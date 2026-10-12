<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => $this->categoryService->paginate(),
        ]);
    }

    public function store(
        StoreCategoryRequest $request
    ): RedirectResponse {
        $this->categoryService->create(
            $request->validated(),
            Auth::id()
        );

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ): RedirectResponse {
        $this->categoryService->update(
            $category,
            $request->validated(),
            Auth::id()
        );

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(
        Category $category
    ): RedirectResponse {
        $this->categoryService->delete(
            $category,
            Auth::id()
        );

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dinonaktifkan.');
    }

    public function restore(
        int $id
    ): RedirectResponse {
        $category = Category::withTrashed()->findOrFail($id);

        if (!$category->trashed()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('info', 'Kategori sudah aktif.');
        }

        $this->categoryService->restore(
            $category,
            Auth::id()
        );

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diaktifkan kembali.');
    }
}
