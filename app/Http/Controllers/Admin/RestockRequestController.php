<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RestockRequestService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RestockRequestController extends Controller
{
    public function __construct(
        private RestockRequestService $restockRequestService
    ) {}

    public function index(Request $request): View
    {
        $restockRequests = $this->restockRequestService->paginate(
            $request->only([
                'search',
                'status',
            ])
        );

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
}