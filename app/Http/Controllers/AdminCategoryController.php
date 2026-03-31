<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('topics')
            ->latest()
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'slug' => $this->makeUniqueSlug($validated['name']),
        ]);

        AdminLog::create([
            'admin_id' => auth()->id(),
            'action' => 'create_category',
            'details' => "Categorie ID {$category->id} creee ({$category->name})",
        ]);

        return back()->with('success', 'Categorie creee.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'slug' => $this->makeUniqueSlug($validated['name'], $category->id),
        ]);

        AdminLog::create([
            'admin_id' => auth()->id(),
            'action' => 'update_category',
            'details' => "Categorie ID {$category->id} mise a jour ({$category->name})",
        ]);

        return back()->with('success', 'Categorie mise a jour.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $id = $category->id;
        $name = $category->name;

        $category->delete();

        AdminLog::create([
            'admin_id' => auth()->id(),
            'action' => 'delete_category',
            'details' => "Categorie ID {$id} supprimee ({$name})",
        ]);

        return back()->with('success', 'Categorie supprimee.');
    }

    private function makeUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'categorie';
        $slug = $baseSlug;
        $suffix = 1;

        while (Category::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
