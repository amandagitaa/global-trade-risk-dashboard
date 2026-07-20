@extends('layouts.admin')

@section('title', 'Articles Management')
@section('page_title', 'Articles Management')

@section('content')
<!-- Include Summernote CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="panel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="panel-title mb-0">Editorial Articles</h5>
                <div>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createArticleModal" style="background:#FF7A00; border-color:#FF7A00;">
                        <i class="bi bi-pencil-square"></i> Write New Article
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
            <form method="GET" action="{{ route('admin.articles') }}" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search title, category, author..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
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
                        <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Published" {{ request('status') == 'Published' ? 'selected' : '' }}>Published</option>
                        <option value="Archived" {{ request('status') == 'Archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="author" class="form-select">
                        <option value="">All Authors</option>
                        @foreach($authors as $author)
                            <option value="{{ $author }}" {{ request('author') == $author ? 'selected' : '' }}>{{ $author }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-outline-secondary w-100"><i class="bi bi-funnel"></i></button>
                </div>
            </form>

            {{-- Articles Table --}}
            <div class="table-responsive" style="min-height: 400px;">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th style="width: 30%;">Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Last Update</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $index => $article)
                        <tr>
                            <td>{{ $articles->firstItem() + $index }}</td>
                            <td><strong>{{ Str::limit($article->title, 50) }}</strong></td>
                            <td><span class="badge bg-secondary">{{ $article->category }}</span></td>
                            <td>{{ $article->author }}</td>
                            <td>
                                @if($article->status === 'Published')
                                    <span class="badge bg-success">Published</span>
                                @elseif($article->status === 'Draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($article->status === 'Archived')
                                    <span class="badge bg-dark">Archived</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ $article->status }}</span>
                                @endif
                            </td>
                            <td style="font-size: 0.85rem;">{{ $article->created_at->format('d M Y H:i') }}</td>
                            <td style="font-size: 0.85rem;">{{ $article->updated_at->format('d M Y H:i') }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewArticleModal{{ $article->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editArticleModal{{ $article->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteArticleModal{{ $article->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- View Article Detail Modal --}}
                        <div class="modal fade" id="viewArticleModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Article Preview</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4 bg-light">
                                        <div class="card shadow-sm border-0">
                                            <div class="card-body p-5">
                                                <h1 class="mb-3">{{ $article->title }}</h1>
                                                <div class="d-flex gap-3 mb-5 pb-3 border-bottom text-muted">
                                                    <span><i class="bi bi-person-circle"></i> {{ $article->author }}</span>
                                                    <span><i class="bi bi-tag"></i> {{ $article->category }}</span>
                                                    <span><i class="bi bi-calendar3"></i> {{ $article->created_at->format('d F Y, H:i') }}</span>
                                                    @if($article->status === 'Published')
                                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Published</span>
                                                    @endif
                                                </div>
                                                <div class="article-content" style="font-size: 1.1rem; line-height: 1.8;">
                                                    {!! $article->content !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Edit Article Modal --}}
                        <div class="modal fade" id="editArticleModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <form method="POST" action="{{ route('admin.articles.update', $article->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Article</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="row">
                                                <div class="col-md-8 mb-3">
                                                    <label class="form-label">Article Title <span class="text-danger">*</span></label>
                                                    <input type="text" name="title" class="form-control" value="{{ $article->title }}" required>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Category <span class="text-danger">*</span></label>
                                                    <input type="text" name="category" class="form-control" list="categoryOptions" value="{{ $article->category }}" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Author <span class="text-danger">*</span></label>
                                                    <input type="text" name="author" class="form-control" value="{{ $article->author }}" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="Draft" {{ $article->status === 'Draft' ? 'selected' : '' }}>Draft (Hidden)</option>
                                                        <option value="Published" {{ $article->status === 'Published' ? 'selected' : '' }}>Published (Visible)</option>
                                                        <option value="Archived" {{ $article->status === 'Archived' ? 'selected' : '' }}>Archived (Hidden)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Article Content <span class="text-danger">*</span></label>
                                                <textarea name="content" class="form-control summernote" required>{!! $article->content !!}</textarea>
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

                        {{-- Delete Article Modal --}}
                        <div class="modal fade" id="deleteArticleModal{{ $article->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('admin.articles.destroy', $article->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center py-4">
                                            <i class="bi bi-trash text-danger mb-3" style="font-size: 3rem;"></i>
                                            <h5>Delete Article?</h5>
                                            <p class="mb-0 text-muted">Are you sure you want to permanently delete this article? If you just want to hide it, consider changing its status to <strong>Archived</strong> or <strong>Draft</strong> instead.</p>
                                        </div>
                                        <div class="modal-footer justify-content-center border-0 pb-4">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Yes, Delete Article</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No articles found. Start writing!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $articles->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

{{-- Create Article Modal --}}
<div class="modal fade" id="createArticleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form method="POST" action="{{ route('admin.articles.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Write New Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Article Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="E.g. Analisis Risiko Supply Chain Asia 2026" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <input type="text" name="category" class="form-control" list="categoryOptions" placeholder="E.g. Supply Chain" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Author <span class="text-danger">*</span></label>
                            <input type="text" name="author" class="form-control" value="{{ auth()->user()->name ?? 'Admin' }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="Draft">Draft (Hidden)</option>
                                <option value="Published">Published (Visible)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Article Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control summernote" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background:#FF7A00; border-color:#FF7A00;">Save Article</button>
                </div>
            </div>
        </form>
    </div>
</div>

<datalist id="categoryOptions">
    <option value="Supply Chain">
    <option value="Global Trade">
    <option value="Export">
    <option value="Import">
    <option value="Logistics">
    <option value="Shipping">
    <option value="Economy">
    <option value="Risk Analysis">
    <option value="Politics">
    <option value="Technology">
</datalist>

<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            placeholder: 'Start writing your analysis here...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });

        // Fix for summernote inside Bootstrap 5 Modal
        $(document).on('focusin', function(e) {
            if ($(e.target).closest(".note-editor").length) {
                e.stopImmediatePropagation();
            }
        });
    });
</script>
<style>
    /* Styling to ensure HTML content displays correctly inside the view modal */
    .article-content h1, .article-content h2, .article-content h3 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    .article-content p {
        margin-bottom: 1rem;
    }
    .article-content ul, .article-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
</style>
@endsection
