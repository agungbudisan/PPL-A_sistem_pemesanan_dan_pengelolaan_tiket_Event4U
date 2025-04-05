<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin')->except(['index', 'show']);
    }

    public function index()
    {
        $categories = Category::withCount('events')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'icon' => 'required|image|max:2048',
            'description' => 'nullable|string'
        ]);

        if ($request->hasFile('icon')) {
            $image = $request->file('icon');
            $imageData = file_get_contents($image->getRealPath());
            $base64Image = base64_encode($imageData);
            $mimeType = $image->getClientMimeType();
            $iconData = 'data:' . $mimeType . ';base64,' . $base64Image;
        }

        Category::create([
            'name' => $request->name,
            'icon' => $iconData ?? null,
            'description' => $request->description
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'icon' => 'required|image|max:2048',
            'description' => 'nullable|string'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description
        ];

        if ($request->hasFile('icon')) {
            $image = $request->file('icon');
            $imageData = file_get_contents($image->getRealPath());
            $base64Image = base64_encode($imageData);
            $mimeType = $image->getClientMimeType();
            $data['icon'] = 'data:' . $mimeType . ';base64,' . $base64Image;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
