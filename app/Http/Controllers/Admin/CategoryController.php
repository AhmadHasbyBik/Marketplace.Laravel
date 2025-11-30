<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = Category::orderBy('order')->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        if ($image = $this->handleImageUpload($request)) {
            $payload['image'] = $image;
        }

        Category::create($payload);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $payload = $request->validated();
        if ($image = $this->handleImageUpload($request, $category)) {
            $payload['image'] = $image;
        }

        $category->update($payload);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori dihapus');
    }

    private function handleImageUpload(CategoryRequest $request, ?Category $category = null): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        if ($category?->image) {
            Storage::disk('public')->delete($category->image);
        }

        return $request->file('image')->store('categories', 'public');
    }
}
