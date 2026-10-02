<?php

namespace App\Http\Controllers\Barista;

use App\Http\Controllers\Controller;
use App\Http\Requests\Barista\RestockRequest\StoreRestockRequest;
use App\Http\Requests\Barista\RestockRequest\UpdateRestockRequest;
use App\Models\Item;
use App\Services\RestockRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RestockRequestController extends Controller
{
    public function __construct(
        private RestockRequestService $restockRequestService
    ) {}

    public function index(Request $request): View
    {
        $restockRequests = $this->restockRequestService->paginateByUser(
            Auth::id(),
            [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
            ]
        );

        return view(
            'barista.restock-requests.index',
            compact('restockRequests')
        );
    }

    public function create(): View
    {
        $items = Item::query()
            ->with([
                'category',
                'unit',
                'activity',
            ])
            ->whereDoesntHave('activity', function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->orderBy('name')
            ->get();

        return view(
            'barista.restock-requests.create',
            compact('items')
        );
    }

    public function store(StoreRestockRequest $request): RedirectResponse
    {
        try {
            $this->restockRequestService->create(
                $request->validated(),
                Auth::id()
            );

            return redirect()
                ->route('barista.restock-requests.index')
                ->with(
                    'success',
                    'Ajuan restock berhasil dibuat dan menunggu persetujuan admin.'
                );
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $restockRequest = $this->restockRequestService->findByUser(
            $id,
            Auth::id()
        );

        if ($restockRequest->activity?->deleted_at !== null) {
            abort(404);
        }

        return view(
            'barista.restock-requests.show',
            compact('restockRequest')
        );
    }

    public function edit(int $id): View|RedirectResponse
    {
        $restockRequest = $this->restockRequestService->findByUser(
            $id,
            Auth::id()
        );

        if ($restockRequest->activity?->deleted_at !== null) {
            abort(404);
        }

        if ($restockRequest->status !== 'pending') {
            return redirect()
                ->route('barista.restock-requests.show', [
                    'id' => $restockRequest->id,
                ])
                ->with(
                    'error',
                    'Ajuan yang sudah diproses tidak dapat diubah.'
                );
        }

        $items = Item::query()
            ->with([
                'category',
                'unit',
                'activity',
            ])
            ->whereDoesntHave('activity', function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->orderBy('name')
            ->get();

        return view(
            'barista.restock-requests.edit',
            compact('restockRequest', 'items')
        );
    }

    public function update(
        UpdateRestockRequest $request,
        int $id
    ): RedirectResponse {
        try {
            $restockRequest = $this->restockRequestService->findByUser(
                $id,
                Auth::id()
            );

            $this->restockRequestService->updateByUser(
                $restockRequest,
                $request->validated(),
                Auth::id()
            );

            return redirect()
                ->route('barista.restock-requests.show', [
                    'id' => $restockRequest->id,
                ])
                ->with(
                    'success',
                    'Ajuan restock berhasil diperbarui.'
                );
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $restockRequest = $this->restockRequestService->findByUser(
                $id,
                Auth::id()
            );

            $this->restockRequestService->softDeleteByUser(
                $restockRequest,
                Auth::id()
            );

            return redirect()
                ->route('barista.restock-requests.index')
                ->with(
                    'success',
                    'Ajuan restock berhasil dihapus.'
                );
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
