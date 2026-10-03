<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RestockRequest\ApproveRestockRequest;
use App\Http\Requests\Admin\RestockRequest\RejectRestockRequest;
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
        $restockRequests = $this->restockRequestService->paginate([
            'search' => $request->input('search'),
            'status' => $request->input('status'),
        ]);

        return view(
            'admin.restock-requests.index',
            compact('restockRequests')
        );
    }

    public function show(int $id): View
    {
        $restockRequest = $this->restockRequestService->find($id);

        return view(
            'admin.restock-requests.show',
            compact('restockRequest')
        );
    }

    public function approve(
        ApproveRestockRequest $request,
        int $id
    ): RedirectResponse {
        try {
            $this->restockRequestService->approve(
                $id,
                Auth::id()
            );

            return redirect()
                ->route('admin.restock-requests.index')
                ->with(
                    'success',
                    'Ajuan restock berhasil disetujui dan stok telah ditambahkan.'
                );
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.restock-requests.index')
                ->with('error', $e->getMessage());
        }
    }

    public function reject(
        RejectRestockRequest $request,
        int $id
    ): RedirectResponse {
        try {
            $this->restockRequestService->reject(
                $id,
                Auth::id(),
                $request->validated('rejection_reason')
            );

            return redirect()
                ->route('admin.restock-requests.index')
                ->with(
                    'success',
                    'Ajuan restock berhasil ditolak.'
                );
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.restock-requests.index')
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $restockRequest = $this->restockRequestService->find($id);

        $this->restockRequestService->softDelete(
            $restockRequest,
            Auth::id()
        );

        return redirect()
            ->route('admin.restock-requests.index')
            ->with(
                'success',
                'Ajuan restock berhasil dihapus.'
            );
    }

    public function restore(int $id): RedirectResponse
    {
        $restockRequest = $this->restockRequestService->find($id);

        $this->restockRequestService->restore(
            $restockRequest,
            Auth::id()
        );

        return redirect()
            ->route('admin.restock-requests.index')
            ->with(
                'success',
                'Ajuan restock berhasil dipulihkan.'
            );
    }

    public function forceDestroy(int $id): RedirectResponse
    {
        $restockRequest = $this->restockRequestService->find($id);

        $this->restockRequestService->forceDelete(
            $restockRequest
        );

        return redirect()
            ->route('admin.restock-requests.index')
            ->with(
                'success',
                'Ajuan restock berhasil dihapus permanen.'
            );
    }
}
