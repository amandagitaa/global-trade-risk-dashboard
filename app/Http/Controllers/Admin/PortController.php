<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Port;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Exception;

class PortController extends Controller
{
    public function index(Request $request)
    {
        $query = Port::with('country');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('country', function ($q) use ($search) {
                      $q->where('country_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Country
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $ports = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();
        
        $countries = Country::orderBy('country_name', 'asc')->get();

        return view('admin.ports.index', compact('ports', 'countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'annual_capacity' => 'nullable|numeric',
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Maintenance', 'Closed'])],
        ]);

        Port::create($request->all());

        return redirect()->route('admin.ports')->with('success', 'Port created successfully.');
    }

    public function update(Request $request, Port $port)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'annual_capacity' => 'nullable|numeric',
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Maintenance', 'Closed'])],
        ]);

        $port->update($request->all());

        return redirect()->route('admin.ports')->with('success', 'Port updated successfully.');
    }

    public function updateCoordinates(Request $request, Port $port)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $port->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('admin.ports')->with('success', 'Port coordinates updated successfully.');
    }

    public function updateStatus(Request $request, Port $port)
    {
        $request->validate([
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Maintenance', 'Closed'])],
        ]);

        $port->update(['status' => $request->status]);

        return redirect()->route('admin.ports')->with('success', 'Port status updated successfully.');
    }

    public function destroy(Port $port)
    {
        // Safety Check: Check if this port is linked to shipping routes or other critical tables
        // If your project uses TradeSimulation or Route models, check them here.
        // E.g. $port->shippingRoutes()->exists() if relation is defined.
        
        $hasDependencies = false;

        // Try to delete to see if DB throws constraint violation, or check known relationships.
        // For simplicity and safety, we wrap in try-catch.
        try {
            $port->delete();
            return redirect()->route('admin.ports')->with('success', 'Port deleted successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") { // Integrity constraint violation
                return redirect()->route('admin.ports')->with('error', 'Cannot delete this port because it is currently linked to Trade Planner routes or Map dependencies. Please preserve database integrity.');
            }
            return redirect()->route('admin.ports')->with('error', 'An error occurred while deleting the port.');
        }
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');
        
        // Open file
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            
            // Validate columns minimal: port_name, country, latitude, longitude, capacity, status
            $requiredColumns = ['port_name', 'country', 'latitude', 'longitude', 'capacity', 'status'];
            
            // Map header to indices
            $headerMap = [];
            foreach ($header as $index => $columnName) {
                $headerMap[trim(strtolower($columnName))] = $index;
            }

            // Check if all required columns exist
            foreach ($requiredColumns as $reqCol) {
                if (!isset($headerMap[$reqCol])) {
                    fclose($handle);
                    return redirect()->route('admin.ports')->with('error', 'CSV is missing required column: ' . $reqCol);
                }
            }

            $successCount = 0;
            $updateCount = 0;
            $failCount = 0;

            // Prepare country cache to avoid excessive DB calls
            $countryCache = Country::pluck('id', 'country_name')->mapWithKeys(function ($item, $key) {
                return [strtolower($key) => $item];
            })->toArray();
            
            $countryCodeCache = Country::pluck('id', 'country_code')->mapWithKeys(function ($item, $key) {
                return [strtolower($key) => $item];
            })->toArray();

            DB::beginTransaction();
            try {
                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    // Skip empty rows
                    if (count(array_filter($row)) === 0) continue;

                    $portName = trim($row[$headerMap['port_name']] ?? '');
                    $countryRef = trim(strtolower($row[$headerMap['country']] ?? ''));
                    $latitude = trim($row[$headerMap['latitude']] ?? '');
                    $longitude = trim($row[$headerMap['longitude']] ?? '');
                    $capacity = trim($row[$headerMap['capacity']] ?? '');
                    $status = ucfirst(trim(strtolower($row[$headerMap['status']] ?? 'Active')));

                    // Validate minimal data
                    if (!$portName || !$countryRef || !is_numeric($latitude) || !is_numeric($longitude)) {
                        $failCount++;
                        continue;
                    }
                    
                    if (!in_array($status, ['Active', 'Inactive', 'Maintenance', 'Closed'])) {
                        $status = 'Active';
                    }

                    // Find country ID
                    $countryId = $countryCache[$countryRef] ?? ($countryCodeCache[$countryRef] ?? null);

                    if (!$countryId) {
                        $failCount++;
                        continue; // Skip if country doesn't exist
                    }

                    // Check duplicate
                    $existingPort = Port::where('name', $portName)->where('country_id', $countryId)->first();

                    if ($existingPort) {
                        $existingPort->update([
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'annual_capacity' => is_numeric($capacity) ? $capacity : $existingPort->annual_capacity,
                            'status' => $status,
                        ]);
                        $updateCount++;
                    } else {
                        Port::create([
                            'name' => $portName,
                            'country_id' => $countryId,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'annual_capacity' => is_numeric($capacity) ? $capacity : null,
                            'status' => $status,
                        ]);
                        $successCount++;
                    }
                }
                DB::commit();
                fclose($handle);

                return redirect()->route('admin.ports')->with('success', "Import complete. Added: {$successCount}, Updated: {$updateCount}, Failed/Skipped: {$failCount}.");
            } catch (Exception $e) {
                DB::rollBack();
                fclose($handle);
                return redirect()->route('admin.ports')->with('error', 'Error occurred during import: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.ports')->with('error', 'Cannot read the CSV file.');
    }
}
