<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::query()
            ->when(request()->filled('q'), fn ($query) => $query->where('name', 'like', '%'.request('q').'%'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('tags.index', compact('tags'));
    }

    public function store(StoreTagRequest $request): RedirectResponse
    {
        Tag::firstOrCreate([
            'name' => $request->name,
        ], [
            'color' => $request->color,
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag created.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('tags.index')->with('success', 'Tag deleted.');
    }
}
