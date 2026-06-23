<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tags = Tag::query()
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        if ($request->ajax()) {
            return view('tags.partials.results', compact('tags'));
        }

        return view('tags.index', compact('tags'));
    }

    public function store(StoreTagRequest $request): RedirectResponse
    {
        Gate::authorize('create', Project::class);

        Tag::firstOrCreate([
            'name' => $request->name,
        ], [
            'color' => $request->color,
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag created.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        Gate::authorize('create', Project::class);

        $tag->delete();

        return redirect()->route('tags.index')->with('success', 'Tag deleted.');
    }
}
