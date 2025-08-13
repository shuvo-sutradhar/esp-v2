<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->string('search');
        $tags = Tag::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('settings/tags/Index', [
            'tags' => $tags,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('settings/tags/Create');
    }

    public function store(StoreTagRequest $request): RedirectResponse
    {
        Tag::create($request->validated());
        return to_route('settings.tags.index');
    }

    public function edit(int $id)
    {
        $tag = Tag::findOrFail($id);
        return Inertia::render('settings/tags/Edit', ['tag' => $tag]);
    }

    public function update(UpdateTagRequest $request, int $id): RedirectResponse
    {
        $tag = Tag::findOrFail($id);
        $tag->update($request->validated());
        return to_route('settings.tags.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();
        return back();
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:tags,id'],
        ]);

        Tag::whereIn('id', $validated['ids'])->delete();
        return back();
    }
}


