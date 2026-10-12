<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Unit\StoreUnitRequest;
use App\Http\Requests\Admin\Unit\UpdateUnitRequest;
use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    public function __construct(
        private UnitService $unitService
    ) {}

    public function index(): View
    {
        return view('admin.units.index', [
            'units' => $this->unitService->paginate(),
        ]);
    }

    public function store(StoreUnitRequest $request): RedirectResponse
    {
        $this->unitService->create(
            $request->validated(),
            Auth::id()
        );

        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Unit berhasil ditambahkan.');
    }

    public function update(
        UpdateUnitRequest $request,
        Unit $unit
    ): RedirectResponse {
        $this->unitService->update(
            $unit,
            $request->validated(),
            Auth::id()
        );

        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $this->unitService->delete(
            $unit,
            Auth::id()
        );

        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Unit berhasil dinonaktifkan.');
    }

    public function restore(Unit $unit): RedirectResponse
    {
        $this->unitService->restore(
            $unit,
            Auth::id()
        );

        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Unit berhasil diaktifkan kembali.');
    }
}
