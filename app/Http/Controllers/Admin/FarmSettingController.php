<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\FarmSetting;
use App\Models\LedgerEntry;
use App\Models\Loan;
use App\Models\Plot;
use App\Models\Supply;
use App\Models\Task;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmSettingController extends Controller
{
    public function edit(): View
    {
        $farmSettings = FarmSetting::getFarmSettings();

        $dataChecks = [
            Plot::count()        => 'lotes',
            Crop::count()        => 'cultivos',
            Task::count()        => 'tareas',
            Supply::count()      => 'insumos',
            Tool::count()        => 'herramientas',
            Loan::count()        => 'préstamos',
            LedgerEntry::count() => 'entradas contables',
            User::where('role', '!=', 'admin')->count() => 'trabajadores/mayordomos',
        ];

        $hasData = false;
        $hasDataLabel = '';
        foreach ($dataChecks as $count => $label) {
            if ($count > 0) {
                $hasData = true;
                $hasDataLabel = $label;
                break;
            }
        }

        return view('admin.farm-settings.edit', compact('farmSettings', 'hasData', 'hasDataLabel'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'address'   => ['nullable', 'string', 'max:500'],
            'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'boundary'  => ['nullable', 'json'],
        ]);

        $farmSettings = FarmSetting::getFarmSettings();

        // Si boundary viene como string JSON, decodificarlo
        if (isset($validated['boundary']) && is_string($validated['boundary'])) {
            $validated['boundary'] = json_decode($validated['boundary'], true);
        }

        // Impedir borrar la delimitación si hay lotes u otros datos registrados
        $boundaryIsBeingCleared = empty($validated['boundary']) && $farmSettings->boundary;
        if ($boundaryIsBeingCleared && Plot::count() > 0) {
            return redirect()->route('admin.farm-settings.edit')
                ->withInput()
                ->with('error', 'No se puede eliminar la delimitación de la finca porque existen lotes registrados en el sistema.');
        }

        $farmSettings->update($validated);

        return redirect()->route('admin.farm-settings.edit')
            ->with('status', 'Configuración de la finca actualizada correctamente');
    }

    public function destroy(): RedirectResponse
    {
        $checks = [
            Plot::count()          => 'lotes registrados',
            Crop::count()          => 'cultivos registrados',
            Task::count()          => 'tareas registradas',
            Supply::count()        => 'insumos registrados',
            Tool::count()          => 'herramientas registradas',
            Loan::count()          => 'préstamos registrados',
            LedgerEntry::count()   => 'entradas contables registradas',
            User::where('role', '!=', 'admin')->count() => 'trabajadores o mayordomos registrados',
        ];

        foreach ($checks as $count => $label) {
            if ($count > 0) {
                return redirect()->route('admin.farm-settings.edit')
                    ->with('error', "No se puede eliminar la configuración de la finca porque existen {$label} en el sistema. Elimine primero todos los datos asociados.");
            }
        }

        FarmSetting::truncate();

        return redirect()->route('admin.farm-settings.edit')
            ->with('status', 'Configuración de la finca eliminada correctamente.');
    }
}
