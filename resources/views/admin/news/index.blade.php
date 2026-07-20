@extends('layouts.admin')

@section('title', 'News Cache Management')
@section('page_title', 'News Cache Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="panel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="panel-title mb-0">Cached News Intelligence</h5>
                <div>
                    <span class="text-muted small"><i class="bi bi-info-circle"></i> This module only manages the local cache, not the GNews API.</span>
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
            <form method="GET" action="{{ route('admin.news') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search title, category, source..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="Published" {{ request('status') == 'Published' ? 'selected' : '' }}>Published</option>
                        <option value="Unpublished" {{ request('status') == 'Unpublished' ? 'selected' : '' }}>Unpublished</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="country_name" class="form-select">
                        <option value="">All Countries</option>
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ request('country_name') == $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="source" class="form-select">
                        <option value="">All Sources</option>
                        @foreach($sources as $source)
                            <option value="{{ $source }}" {{ request('source') == $source ? 'selected' : '' }}>{{ $source }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-secondary w-100"><i class="bi bi-funnel"></i></button>
                </div>
            </form>

            {{-- News Table --}}
            <div class="table-responsive" style="min-height: 400px;">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th style="width: 35%;">Title</th>
                            <th>Category</th>
                            <th>Country</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Published At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $index => $item)
                        <tr>
                            <td>{{ $news->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ Str::limit($item->title, 60) }}</strong>
                                @if($item->sentiment)
                                    <div class="mt-1">
                                        <span class="badge bg-light text-dark border" style="font-size: 0.7rem;">Sentiment: {{ ucfirst($item->sentiment) }}</span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.8rem;">
                                        {{ $item->category ?: 'Uncategorized' }}
                                    </button>
                                    <div class="dropdown-menu p-3 shadow" style="width: 250px;">
                                        <form method="POST" action="{{ route('admin.news.update_category', $item->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <h6 class="dropdown-header px-0 text-dark">Change Category</h6>
                                            <div class="mb-2">
                                                <input type="text" name="category" class="form-control form-control-sm" value="{{ $item->category }}" required>
                                                <small class="text-muted" style="font-size: 0.7rem;">E.g. Economy, Trade, Shipping, Logistics</small>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary w-100" style="background:#FF7A00; border:none;">Save Category</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->country_name ?: '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $item->source }}</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm dropdown-toggle border-0 
                                        @if($item->status === 'Published') btn-outline-success 
                                        @else btn-outline-secondary @endif
                                        " type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 2px 8px; font-size: 0.8rem;">
                                        {{ $item->status }}
                                    </button>
                                    <ul class="dropdown-menu shadow">
                                        <li>
                                            <form method="POST" action="{{ route('admin.news.update_status', $item->id) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="Published">
                                                <button type="submit" class="dropdown-item text-success"><i class="bi bi-eye-fill small me-2"></i> Published</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.news.update_status', $item->id) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="Unpublished">
                                                <button type="submit" class="dropdown-item text-secondary"><i class="bi bi-eye-slash-fill small me-2"></i> Unpublished</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td style="font-size: 0.85rem;">{{ $item->published_at ? $item->published_at->format('d M Y H:i') : '-' }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewNewsModal{{ $item->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteNewsModal{{ $item->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- View News Detail Modal --}}
                        <div class="modal fade" id="viewNewsModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">News Details (Read Only)</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        @if($item->image_url)
                                            <img src="{{ $item->image_url }}" alt="News Image" class="img-fluid rounded mb-4" style="max-height: 300px; width: 100%; object-fit: cover;">
                                        @endif
                                        <h4 class="mb-3">{{ $item->title }}</h4>
                                        <div class="d-flex gap-2 mb-3">
                                            <span class="badge bg-primary">{{ $item->category ?: 'Uncategorized' }}</span>
                                            <span class="badge bg-secondary">{{ $item->source }}</span>
                                            <span class="badge bg-info text-dark">{{ $item->country_name }}</span>
                                            @if($item->status === 'Published')
                                                <span class="badge bg-success">Published</span>
                                            @else
                                                <span class="badge bg-dark">Unpublished</span>
                                            @endif
                                        </div>
                                        <p class="text-muted small mb-4">
                                            Published: {{ $item->published_at ? $item->published_at->format('d M Y H:i') : '-' }} | 
                                            Fetched: {{ $item->created_at->format('d M Y H:i') }}
                                        </p>
                                        <div class="bg-light p-3 rounded border mb-4">
                                            <strong>Description:</strong><br>
                                            {{ $item->description }}
                                        </div>
                                        <div class="mb-4">
                                            <strong>Content Preview:</strong><br>
                                            {{ $item->content }}
                                        </div>
                                        @if($item->sentiment)
                                            <div class="mb-4">
                                                <strong>AI Sentiment Analysis:</strong>
                                                <span class="badge {{ $item->sentiment == 'positive' ? 'bg-success' : ($item->sentiment == 'negative' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                    {{ ucfirst($item->sentiment) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer justify-content-between">
                                        <a href="{{ $item->url }}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-box-arrow-up-right"></i> Read Original Article</a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Delete News Modal --}}
                        <div class="modal fade" id="deleteNewsModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.news.destroy', $item->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-trash text-danger mb-3" style="font-size: 3rem;"></i>
                                            <h5>Remove from Cache?</h5>
                                            <p class="mb-0 text-muted">Are you sure you want to delete this cached news item? It will be removed from your local database, but it may be fetched again during the next API sync if it is still relevant.</p>
                                        </div>
                                        <div class="modal-footer justify-content-center border-0 pb-4">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Yes, Delete Cache</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No cached news found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $news->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
