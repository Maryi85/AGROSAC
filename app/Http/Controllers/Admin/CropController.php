<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCropRequest;
use App\Http\Requests\UpdateCropRequest;
use App\Models\Crop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CropController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('q');
        $crops = Crop::query()
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.crops.index', compact('crops', 'search'));
    }

    public function create(): View
    {
        return view('admin.crops.create');
    }

    public function store(StoreCropRequest $request): RedirectResponse
    {
        Crop::create($request->validated());
        return redirect()->route('admin.crops.index')->with('status', 'Cultivo creado correctamente');
    }

    public function update(UpdateCropRequest $request, Crop $crop): RedirectResponse
    {
        $crop->update($request->validated());
        return redirect()->route('admin.crops.index')->with('status', 'Cultivo actualizado');
    }

    public function destroy(Crop $crop): RedirectResponse
    {
        $crop->delete();
        return redirect()->route('admin.crops.index')->with('status', 'Cultivo eliminado');
    }
}


