<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Validation\Rule;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::withoutGlobalScopes();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Author
        if ($request->filled('author')) {
            $query->where('author', $request->author);
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $categories = Article::withoutGlobalScopes()->select('category')->distinct()->pluck('category');
        $authors = Article::withoutGlobalScopes()->select('author')->distinct()->pluck('author');

        return view('admin.articles.index', compact('articles', 'categories', 'authors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'content' => 'required|string',
            'author' => 'required|string|max:100',
            'status' => ['required', Rule::in(['Draft', 'Published', 'Archived'])],
        ]);

        Article::create([
            'title' => $request->title,
            'category' => $request->category,
            'content' => $request->content,
            'author' => $request->author,
            'status' => $request->status,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.articles')->with('success', 'Article created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'content' => 'required|string',
            'author' => 'required|string|max:100',
            'status' => ['required', Rule::in(['Draft', 'Published', 'Archived'])],
        ]);

        $article = Article::withoutGlobalScopes()->findOrFail($id);
        
        $article->update([
            'title' => $request->title,
            'category' => $request->category,
            'content' => $request->content,
            'author' => $request->author,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.articles')->with('success', 'Article updated successfully.');
    }

    public function destroy($id)
    {
        $article = Article::withoutGlobalScopes()->findOrFail($id);
        $article->delete();

        return redirect()->route('admin.articles')->with('success', 'Article deleted successfully.');
    }
}
