<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlotRequest;
use App\Http\Requests\UpdatePlotRequest;
use App\Models\Plot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlotController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $plots = Plot::query()
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.plots.index', compact('plots', 'search'));
    }

    public function create(): View
    {
        return view('admin.plots.create');
    }

    public function store(StorePlotRequest $request): RedirectResponse
    {
        Plot::create($request->validated());
        return redirect()->route('admin.plots.index')->with('status', 'Lote creado correctamente');
    }

    public function edit(Plot $plot): View
    {
        return view('admin.plots.edit', compact('plot'));
    }

    public function update(UpdatePlotRequest $request, Plot $plot): RedirectResponse
    {
        $plot->update($request->validated());
        return redirect()->route('admin.plots.index')->with('status', 'Lote actualizado');
    }

    public function destroy(Plot $plot): RedirectResponse
    {
        $plot->delete();
        return redirect()->route('admin.plots.index')->with('status', 'Lote eliminado');
    }
}


