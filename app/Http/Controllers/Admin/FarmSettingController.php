<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FarmSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmSettingController extends Controller
{
    public function edit(): View
    {
        $farmSettings = FarmSetting::getFarmSettings();
        return view('admin.farm-settings.edit', compact('farmSettings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'boundary' => ['nullable', 'json'],
        ]);

        $farmSettings = FarmSetting::getFarmSettings();
        
        // Si boundary viene como string JSON, decodificarlo
        if (isset($validated['boundary']) && is_string($validated['boundary'])) {
            $validated['boundary'] = json_decode($validated['boundary'], true);
        }
        
        $farmSettings->update($validated);

        return redirect()->route('admin.farm-settings.edit')
            ->with('status', 'Configuración de la finca actualizada correctamente');
    }
}
