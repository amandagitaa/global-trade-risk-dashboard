@extends('layouts.admin')

@section('title', 'Sentiment Dictionary')
@section('page_title', 'Sentiment Dictionary Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="panel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="panel-title mb-0">Lexicon Database</h5>
                <div>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createWordModal" style="background:#FF7A00; border-color:#FF7A00;">
                        <i class="bi bi-plus-lg"></i> Add New Word
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
            <form method="GET" action="{{ route('admin.sentiment-dictionary') }}" class="row g-3 mb-4">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search for a word..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="type" class="form-select">
                        <option value="">All Types (Positive & Negative)</option>
                        <option value="positive" {{ request('type') == 'positive' ? 'selected' : '' }}>Positive Words Only</option>
                        <option value="negative" {{ request('type') == 'negative' ? 'selected' : '' }}>Negative Words Only</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100"><i class="bi bi-funnel"></i> Filter</button>
                </div>
            </form>

            {{-- Dictionary Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Word</th>
                            <th>Type</th>
                            <th>Added On</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($words as $index => $item)
                        <tr>
                            <td>{{ $words->firstItem() + $index }}</td>
                            <td><strong>{{ $item->word }}</strong></td>
                            <td>
                                @if($item->type === 'positive')
                                    <span class="badge bg-success"><i class="bi bi-arrow-up-right"></i> Positive</span>
                                @elseif($item->type === 'negative')
                                    <span class="badge bg-danger"><i class="bi bi-arrow-down-right"></i> Negative</span>
                                @endif
                            </td>
                            <td style="font-size: 0.85rem;">{{ $item->created_at->format('d M Y H:i') }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editWordModal{{ $item->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteWordModal{{ $item->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Edit Word Modal --}}
                        <div class="modal fade" id="editWordModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.sentiment-dictionary.update', $item->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Dictionary Word</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label">Word (Lowercase) <span class="text-danger">*</span></label>
                                                <input type="text" name="word" class="form-control" value="{{ $item->word }}" required>
                                                <small class="text-muted">The system will automatically convert this to lowercase.</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Sentiment Type <span class="text-danger">*</span></label>
                                                <select name="type" class="form-select" required>
                                                    <option value="positive" {{ $item->type === 'positive' ? 'selected' : '' }}>Positive (e.g., Growth, Boost)</option>
                                                    <option value="negative" {{ $item->type === 'negative' ? 'selected' : '' }}>Negative (e.g., Crisis, War)</option>
                                                </select>
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

                        {{-- Delete Word Modal --}}
                        <div class="modal fade" id="deleteWordModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.sentiment-dictionary.destroy', $item->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-trash text-danger mb-3" style="font-size: 3rem;"></i>
                                            <h5>Delete '{{ $item->word }}'?</h5>
                                            <p class="mb-0 text-muted">This word will no longer be used for lexicon-based sentiment analysis.</p>
                                        </div>
                                        <div class="modal-footer justify-content-center border-0 pb-4">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No words found in the dictionary.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $words->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- Create Word Modal --}}
<div class="modal fade" id="createWordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.sentiment-dictionary.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Dictionary Word</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label">Word (Lowercase) <span class="text-danger">*</span></label>
                        <input type="text" name="word" class="form-control" placeholder="e.g. inflation" required>
                        <small class="text-muted">The system will automatically convert this to lowercase.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sentiment Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="positive">Positive (e.g., Growth, Boost)</option>
                            <option value="negative">Negative (e.g., Crisis, War)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#FF7A00; border-color:#FF7A00;">Add Word</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
