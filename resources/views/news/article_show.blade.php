@extends('layouts.app')

@section('title', $article->title . ' - Official Article')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('news.index') }}" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Back to News</a>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="mb-3">
                        <span class="badge bg-primary px-3 py-2 fs-6">{{ strtoupper($article->category) }}</span>
                    </div>

                    <h1 class="fw-bold mb-3" style="color: var(--primary-color);">{{ $article->title }}</h1>

                    <div class="d-flex align-items-center text-muted mb-4 pb-4 border-bottom">
                        <div class="me-4">
                            <i class="bi bi-person-circle fs-5 me-2 align-middle"></i> 
                            <strong>{{ $article->author }}</strong>
                        </div>
                        <div>
                            <i class="bi bi-calendar-event fs-5 me-2 align-middle"></i> 
                            {{ $article->created_at ? $article->created_at->format('d M Y, H:i') : 'Unknown Date' }}
                        </div>
                    </div>

                    <div class="article-content fs-5" style="line-height: 1.8;">
                        {!! $article->content !!}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
