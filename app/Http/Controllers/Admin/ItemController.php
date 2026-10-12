<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Item\StoreItemRequest;
use App\Http\Requests\Admin\Item\UpdateItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\Unit;
use App\Services\ItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class ItemController extends Controller
{
    public function __construct(
        private ItemService $itemService
    ) {}

    public function index(Request $request)
    {
        $items = $this->itemService->paginate(
            $request->only([
                'search',
                'category_id',
                'status',
            ]),
            10
        );

        $categories = Category::with('activity')
            ->orderBy('name')
            ->get();

        $units = Unit::with('activity')
            ->orderBy('name')
            ->get();

        return view('admin.items.index', compact(
            'items',
            'categories',
            'units'
        ));
    }

    public function store(
        StoreItemRequest $request
    ): RedirectResponse {
        $this->itemService->create(
            $request->validated(),
            Auth::id()
        );

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(
        UpdateItemRequest $request,
        Item $item
    ): RedirectResponse {
        $this->itemService->update(
            $item,
            $request->validated(),
            Auth::id()
        );

        return redirect()
            ->route('admin.items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(
        Item $item
    ): RedirectResponse {
        $this->itemService->softDelete(
            $item,
            Auth::id()
        );

        return redirect()
            ->route('admin.items.index')
            ->with(
                'success',
                'Barang berhasil dinonaktifkan.'
            );
    }

    public function restore(
        int $id
    ): RedirectResponse {
        $item = Item::query()
            ->with('activity')
            ->findOrFail($id);

        $this->itemService->restore(
            $item,
            Auth::id()
        );

        return redirect()
            ->route('admin.items.index')
            ->with(
                'success',
                'Barang berhasil diaktifkan kembali.'
            );
    }

    public function forceDelete(int $id): RedirectResponse
    {
        $item = Item::withTrashed()->findOrFail($id);

        try {
            $this->itemService->forceDelete($item, Auth::id());

            return redirect()
                ->route('admin.items.index')
                ->with('success', 'Barang berhasil dihapus permanen.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('open_item_modal', true);
        }
    }

    public function show(
        int $id
    ): View {
        $item = Item::query()
            ->with([
                'category',
                'unit',
                'activity.creator',
                'activity.updater',
                'activity.deleter',
            ])
            ->findOrFail($id);

        return view(
            'admin.items.show',
            compact('item')
        );
    }
}
