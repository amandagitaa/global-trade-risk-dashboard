<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SentimentDictionary;
use Illuminate\Validation\Rule;

class SentimentDictionaryController extends Controller
{
    public function index(Request $request)
    {
        $query = SentimentDictionary::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('word', 'like', "%{$search}%");
        }

        // Filter by Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $words = $query->orderBy('type')->orderBy('word')->paginate(20)->withQueryString();

        return view('admin.sentiment-dictionary.index', compact('words'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'word' => 'required|string|max:100',
            'type' => ['required', Rule::in(['positive', 'negative'])],
        ]);

        $word = strtolower(trim($request->word));

        // Check duplicate
        if (SentimentDictionary::where('word', $word)->where('type', $request->type)->exists()) {
            return redirect()->back()->with('error', 'This word already exists in the ' . $request->type . ' dictionary.');
        }

        SentimentDictionary::create([
            'word' => $word,
            'type' => $request->type
        ]);

        return redirect()->route('admin.sentiment-dictionary')->with('success', 'Word added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'word' => 'required|string|max:100',
            'type' => ['required', Rule::in(['positive', 'negative'])],
        ]);

        $dictionary = SentimentDictionary::findOrFail($id);
        $word = strtolower(trim($request->word));

        // Check duplicate if word or type changed
        if (($dictionary->word !== $word || $dictionary->type !== $request->type) && 
             SentimentDictionary::where('word', $word)->where('type', $request->type)->exists()) {
            return redirect()->back()->with('error', 'This word already exists in the ' . $request->type . ' dictionary.');
        }

        $dictionary->update([
            'word' => $word,
            'type' => $request->type
        ]);

        return redirect()->route('admin.sentiment-dictionary')->with('success', 'Word updated successfully.');
    }

    public function destroy($id)
    {
        $dictionary = SentimentDictionary::findOrFail($id);
        $dictionary->delete();

        return redirect()->route('admin.sentiment-dictionary')->with('success', 'Word deleted successfully.');
    }
}
