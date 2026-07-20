@extends('layouts.admin')

@section('title', 'Ports Management')
@section('page_title', 'Ports Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="panel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="panel-title mb-0">Master Data: Ports</h5>
                <div>
                    <button class="btn btn-sm btn-outline-success me-2" data-bs-toggle="modal" data-bs-target="#importCsvModal">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Import CSV
                    </button>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createPortModal" style="background:#FF7A00; border-color:#FF7A00;">
                        <i class="bi bi-plus-lg"></i> Add New Port
                    </button>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Filter & Search Form --}}
            <form method="GET" action="{{ route('admin.ports') }}" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search port name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="country_id" class="form-select">
                        <option value="">All Countries</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>{{ $country->country_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="Maintenance" {{ request('status') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
                </div>
            </form>

            {{-- Ports Table --}}
            <div class="table-responsive" style="min-height: 400px;">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Port Name</th>
                            <th>Country</th>
                            <th>Coordinates</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ports as $index => $port)
                        <tr>
                            <td>{{ $ports->firstItem() + $index }}</td>
                            <td><strong>{{ $port->name }}</strong></td>
                            <td>
                                @if($port->country)
                                    @if($port->country->flag)
                                        <img src="{{ $port->country->flag }}" alt="flag" style="width: 20px; border:1px solid #ddd; margin-right:5px;">
                                    @endif
                                    {{ $port->country->country_name }}
                                @else
                                    <span class="text-muted">Unknown</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.8rem;">
                                        {{ number_format($port->latitude, 4) }}, {{ number_format($port->longitude, 4) }}
                                    </button>
                                    <div class="dropdown-menu p-3 shadow" style="width: 250px;">
                                        <form method="POST" action="{{ route('admin.ports.update_coordinates', $port->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <h6 class="dropdown-header px-0 text-dark">Quick Update Coordinates</h6>
                                            <div class="mb-2">
                                                <label class="form-label mb-1" style="font-size:0.8rem;">Latitude</label>
                                                <input type="number" step="any" name="latitude" class="form-control form-control-sm" value="{{ $port->latitude }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label mb-1" style="font-size:0.8rem;">Longitude</label>
                                                <input type="number" step="any" name="longitude" class="form-control form-control-sm" value="{{ $port->longitude }}" required>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary w-100" style="background:#FF7A00; border:none;">Save</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $port->annual_capacity ? number_format($port->annual_capacity) : '-' }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm dropdown-toggle border-0 
                                        @if($port->status === 'Active') btn-outline-success 
                                        @elseif($port->status === 'Maintenance') btn-outline-warning text-dark 
                                        @elseif($port->status === 'Closed') btn-outline-dark 
                                        @else btn-outline-danger @endif
                                        " type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 2px 8px; font-size: 0.8rem;">
                                        {{ $port->status ?? 'Active' }}
                                    </button>
                                    <ul class="dropdown-menu shadow">
                                        <li>
                                            <form method="POST" action="{{ route('admin.ports.update_status', $port->id) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="Active">
                                                <button type="submit" class="dropdown-item text-success"><i class="bi bi-circle-fill small me-2"></i> Active</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.ports.update_status', $port->id) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="Maintenance">
                                                <button type="submit" class="dropdown-item text-warning"><i class="bi bi-circle-fill small me-2"></i> Maintenance</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.ports.update_status', $port->id) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="Inactive">
                                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-circle-fill small me-2"></i> Inactive</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.ports.update_status', $port->id) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="Closed">
                                                <button type="submit" class="dropdown-item text-dark"><i class="bi bi-circle-fill small me-2"></i> Closed</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>{{ $port->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editPortModal{{ $port->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePortModal{{ $port->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Edit Port Modal --}}
                        <div class="modal fade" id="editPortModal{{ $port->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.ports.update', $port->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Port</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3 text-start">
                                                <label class="form-label">Port Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $port->name }}" required>
                                            </div>
                                            <div class="mb-3 text-start">
                                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                                <select name="country_id" class="form-select" required>
                                                    <option value="">Select Country</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->id }}" {{ $port->country_id == $country->id ? 'selected' : '' }}>{{ $country->country_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row text-start">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Latitude <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" name="latitude" class="form-control" value="{{ $port->latitude }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Longitude <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" name="longitude" class="form-control" value="{{ $port->longitude }}" required>
                                                </div>
                                            </div>
                                            <div class="row text-start">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Capacity (Annual)</label>
                                                    <input type="number" name="annual_capacity" class="form-control" value="{{ $port->annual_capacity }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="Active" {{ $port->status === 'Active' ? 'selected' : '' }}>Active</option>
                                                        <option value="Inactive" {{ $port->status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                        <option value="Maintenance" {{ $port->status === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                                        <option value="Closed" {{ $port->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary" style="background:#FF7A00; border-color:#FF7A00;">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Delete Port Modal --}}
                        <div class="modal fade" id="deletePortModal{{ $port->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.ports.destroy', $port->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                                            <h5>Delete {{ $port->name }}?</h5>
                                            <p class="mb-0 text-muted">Are you sure you want to delete this port? If this port is being used by Trade Planner or Map Simulations, the process will safely fail.</p>
                                        </div>
                                        <div class="modal-footer justify-content-center border-0 pb-4">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Yes, Delete Port</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No ports found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $ports->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- Create Port Modal --}}
<div class="modal fade" id="createPortModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.ports.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Port</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Port Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Country <span class="text-danger">*</span></label>
                        <select name="country_id" class="form-select" required>
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude <span class="text-danger">*</span></label>
                            <input type="number" step="any" name="latitude" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude <span class="text-danger">*</span></label>
                            <input type="number" step="any" name="longitude" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Capacity (Annual)</label>
                            <input type="number" name="annual_capacity" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="Active">Active</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#FF7A00; border-color:#FF7A00;">Save Port</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Import CSV Modal --}}
<div class="modal fade" id="importCsvModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.ports.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Ports from CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" style="font-size: 0.9rem;">
                        <i class="bi bi-info-circle me-2"></i>
                        The CSV file must contain the following headers exactly (case-insensitive):
                        <strong>port_name, country, latitude, longitude, capacity, status</strong>.
                        <br><br>
                        <em>Note: The <strong>country</strong> column can be a Country Name or Alpha-2 Code.</em>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload CSV File <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Upload & Import</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
