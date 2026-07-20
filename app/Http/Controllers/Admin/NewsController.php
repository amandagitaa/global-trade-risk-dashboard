<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsCache;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsCache::withoutGlobalScopes();

        // Search (Title, Category, Country, Source)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('country_name', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%");
            });
        }

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by Status Publish
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Country
        if ($request->filled('country_name')) {
            $query->where('country_name', $request->country_name);
        }
        
        // Filter by Source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $news = $query->orderBy('published_at', 'desc')->paginate(15)->withQueryString();

        // Distinct lists for filters
        $categories = NewsCache::withoutGlobalScopes()->select('category')->whereNotNull('category')->distinct()->pluck('category');
        $countries = NewsCache::withoutGlobalScopes()->select('country_name')->whereNotNull('country_name')->distinct()->pluck('country_name');
        $sources = NewsCache::withoutGlobalScopes()->select('source')->whereNotNull('source')->distinct()->pluck('source');

        return view('admin.news.index', compact('news', 'categories', 'countries', 'sources'));
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'category' => 'required|string|max:100',
        ]);

        $news = NewsCache::withoutGlobalScopes()->findOrFail($id);
        $news->update(['category' => $request->category]);

        return back()->with('success', 'News category updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(['Published', 'Unpublished'])],
        ]);

        $news = NewsCache::withoutGlobalScopes()->findOrFail($id);
        $news->update(['status' => $request->status]);

        return back()->with('success', "News status updated to {$request->status}.");
    }

    public function destroy($id)
    {
        $news = NewsCache::withoutGlobalScopes()->findOrFail($id);
        $news->delete();

        return back()->with('success', 'News cache deleted successfully.');
    }
}
