@extends('layouts.admin')

@section('title', 'Countries Management')
@section('page_title', 'Countries Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="panel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="panel-title mb-0">Master Data: Countries</h5>
                <div>
                    <form method="POST" action="{{ route('admin.countries.sync') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary me-2" onclick="return confirm('Are you sure you want to pull the latest country data from the REST API? This might take a few moments.')">
                            <i class="bi bi-cloud-arrow-down"></i> Update Data
                        </button>
                    </form>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createCountryModal" style="background:#FF7A00; border-color:#FF7A00;">
                        <i class="bi bi-plus-lg"></i> Add New Country
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
            <form method="GET" action="{{ route('admin.countries') }}" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search name, code, region, currency..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="region" class="form-select">
                        <option value="">All Regions</option>
                        @foreach($regions as $region)
                            <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="currency" class="form-select">
                        <option value="">All Currencies</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->currency_code }}" {{ request('currency') == $currency->currency_code ? 'selected' : '' }}>
                                {{ $currency->currency_name }} ({{ $currency->currency_code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">Filter</button>
                </div>
            </form>

            {{-- Countries Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Region</th>
                            <th>Currency</th>
                            <th>Lat / Lng</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($countries as $index => $country)
                        <tr>
                            <td>{{ $countries->firstItem() + $index }}</td>
                            <td>
                                @if($country->flag)
                                    <img src="{{ $country->flag }}" alt="{{ $country->country_code }}" style="width: 24px; height: auto; margin-right: 5px; border:1px solid #ddd;">
                                @endif
                                <strong>{{ $country->country_name }}</strong>
                            </td>
                            <td><span class="badge bg-secondary">{{ $country->country_code }}</span></td>
                            <td><span class="badge bg-info text-dark">{{ $country->region ?: '-' }}</span></td>
                            <td>
                                @if($country->currency_code)
                                    <span class="badge bg-light text-dark border">{{ $country->currency_code }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="font-size: 0.85rem; color:#666;">
                                {{ number_format($country->latitude, 4) }},<br>{{ number_format($country->longitude, 4) }}
                            </td>
                            <td>{{ $country->created_at->format('d M Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editCountryModal{{ $country->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCountryModal{{ $country->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Edit Country Modal --}}
                        <div class="modal fade" id="editCountryModal{{ $country->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.countries.update', $country->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Country</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Country Name <span class="text-danger">*</span></label>
                                                <input type="text" name="country_name" class="form-control" value="{{ $country->country_name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Country Code (Alpha-2) <span class="text-danger">*</span></label>
                                                <input type="text" name="country_code" class="form-control" value="{{ $country->country_code }}" maxlength="10" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Region</label>
                                                <input type="text" name="region" class="form-control" value="{{ $country->region }}">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Currency Name</label>
                                                    <input type="text" name="currency_name" class="form-control" value="{{ $country->currency_name }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Currency Code</label>
                                                    <input type="text" name="currency_code" class="form-control" value="{{ $country->currency_code }}">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Latitude</label>
                                                    <input type="number" step="any" name="latitude" class="form-control" value="{{ $country->latitude }}">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Longitude</label>
                                                    <input type="number" step="any" name="longitude" class="form-control" value="{{ $country->longitude }}">
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

                        {{-- Delete Country Modal --}}
                        <div class="modal fade" id="deleteCountryModal{{ $country->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.countries.destroy', $country->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                                            <h5>Delete {{ $country->country_name }}?</h5>
                                            <p class="mb-0 text-muted">This action will fail if the country is currently linked to any active ports, weather data, or risk analysis reports to preserve database integrity.</p>
                                        </div>
                                        <div class="modal-footer justify-content-center border-0 pb-4">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Yes, Delete Data</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No countries found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $countries->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- Create Country Modal --}}
<div class="modal fade" id="createCountryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.countries.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Country</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Country Name <span class="text-danger">*</span></label>
                        <input type="text" name="country_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Country Code (Alpha-2) <span class="text-danger">*</span></label>
                        <input type="text" name="country_code" class="form-control" maxlength="10" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Region</label>
                        <input type="text" name="region" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Currency Name</label>
                            <input type="text" name="currency_name" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Currency Code</label>
                            <input type="text" name="currency_code" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="number" step="any" name="latitude" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="number" step="any" name="longitude" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#FF7A00; border-color:#FF7A00;">Save Country</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
