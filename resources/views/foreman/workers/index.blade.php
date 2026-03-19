@extends('foreman.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Gestión de Trabajadores</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-4">
    {{-- Responsive: wrap buttons on mobile --}}
    <div class="mb-6 flex flex-wrap gap-2 justify-between items-center">

        <a href="{{ route('foreman.workers.pdf', request()->query()) }}" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded-lg font-medium transition-colors">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            <span>Descargar PDF</span>
        </a>
    </div>

    {{-- Responsive: stack on mobile, row on sm+ --}}
    <form method="GET" class="mb-4 flex flex-col sm:flex-row gap-2 sm:items-end">
        <div class="flex-1">
            <label class="block text-sm mb-1 text-emerald-800">Buscar por nombre o email</label>
            <x-search-bar placeholder="Buscar trabajadores..." :with-form="false" />
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Estado</label>
            <select name="status" class="w-full sm:w-auto border border-emerald-200 rounded px-3 py-2">
                <option value="all">Todos los estados</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>
        <button type="submit" class="w-full sm:w-auto px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center justify-center gap-2">
            <i data-lucide="search" class="w-4 h-4"></i>
            <span>Filtrar</span>
        </button>
    </form>

    {{-- Tabla Desktop --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800 border-b">
                    <th class="py-3 pr-4">Foto</th>
                    <th class="py-3 pr-4">Nombre</th>
                    <th class="py-3 pr-4">Email</th>
                    <th class="py-3 pr-4">Teléfono</th>
                    <th class="py-3 pr-4">Estado</th>
                    <th class="py-3 pr-4">Tareas Completadas</th>
                    <th class="py-3 pr-4">Fecha de Registro</th>
                    <th class="py-3 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($workers as $worker)
                <tr class="border-b hover:bg-gray-50" data-worker-id="{{ $worker->id }}">
                    <td class="py-3 pr-4">
                        @if($worker->photo)
                            <img src="{{ storage_asset($worker->photo) }}" alt="Foto" class="h-10 w-10 rounded-full object-cover border border-gray-200">
                        @else
                            <div class="h-10 w-10 rounded-full border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-xs text-gray-400">—</div>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        <div class="font-medium text-gray-900 worker-name">{{ $worker->name }}</div>
                    </td>
                    <td class="py-3 pr-4 worker-email">
                        {{ $worker->email }}
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-800">{{ $worker->phone ?? '—' }}</div>
                    </td>
                    <td class="py-3 pr-4 status-badge">
                        @if($worker->email_verified_at)
                            <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>
                        @endif
                    </td>
                    <td class="py-3 pr-4">
                        @php
                            $completedTasks = \App\Models\Task::where('assigned_to', $worker->id)->where('status', 'completed')->count();
                            $approvedTasks = \App\Models\Task::where('assigned_to', $worker->id)->where('status', 'approved')->count();
                        @endphp
                        <div class="text-sm">
                            <div class="text-gray-900">{{ $completedTasks }} completadas</div>
                            <div class="text-emerald-600">{{ $approvedTasks }} aprobadas</div>
                        </div>
                    </td>
                    <td class="py-3 pr-4">
                        <div class="text-sm text-gray-900">{{ $worker->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="py-3 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            <button class="view-worker-btn inline-flex items-center justify-center w-8 h-8 border border-blue-200 rounded hover:bg-blue-50 text-blue-600" 
                               title="Ver detalles"
                               data-worker-id="{{ $worker->id }}"
                               data-worker-name="{{ $worker->name }}"
                               data-worker-email="{{ $worker->email }}"
                               data-worker-phone="{{ $worker->phone ?? '—' }}"
                               data-worker-status="{{ $worker->email_verified_at ? 'Activo' : 'Inactivo' }}"
                               data-worker-registered="{{ $worker->created_at->format('d/m/Y H:i') }}"
                               data-worker-photo="{{ $worker->photo ? storage_asset($worker->photo) : '' }}">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            <button class="report-worker-btn inline-flex items-center justify-center w-9 h-9 border-2 border-green-500 rounded-lg hover:bg-green-100 text-green-700 bg-green-50 shadow-sm" 
                               title="Generar Reporte"
                               data-worker-id="{{ $worker->id }}"
                               data-worker-name="{{ $worker->name }}">
                                <i data-lucide="file-text" class="w-5 h-5"></i>
                            </button>
                            <button class="edit-worker-btn inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" 
                                    title="Editar"
                                    data-worker-id="{{ $worker->id }}"
                                    data-worker-name="{{ $worker->name }}"
                                    data-worker-email="{{ $worker->email }}"
                                    data-worker-phone="{{ $worker->phone }}"
                                    data-worker-status="{{ $worker->email_verified_at ? 'active' : 'inactive' }}">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <div class="delete-action-container">
                                @if($worker->email_verified_at)
                                    <button disabled class="inline-flex items-center justify-center w-8 h-8 border border-gray-200 rounded bg-gray-100 text-gray-400 cursor-not-allowed opacity-60" title="No se puede eliminar un trabajador activo."><i data-lucide="trash" class="w-4 h-4"></i></button>
                                @else
                                    <form method="POST" action="{{ route('foreman.workers.destroy', $worker) }}" class="inline" data-confirm="true" data-message="¿Eliminar este trabajador?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Eliminar"><i data-lucide="trash" class="w-4 h-4"></i></button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-6 text-center text-emerald-800/70">No hay trabajadores registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-3">
        @forelse ($workers as $worker)
        <div class="bg-white border rounded-lg p-4 shadow-sm" data-worker-id="{{ $worker->id }}">
            <div class="flex items-center gap-3 mb-3">
                @if($worker->photo)
                    <img src="{{ storage_asset($worker->photo) }}" alt="Foto" class="h-12 w-12 rounded-full object-cover border border-gray-200 flex-shrink-0">
                @else
                    <div class="h-12 w-12 rounded-full border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-gray-400 flex-shrink-0"><i data-lucide="user" class="w-6 h-6"></i></div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-gray-900 worker-name truncate">{{ $worker->name }}</div>
                    <div class="text-sm text-gray-500 worker-email truncate">{{ $worker->email }}</div>
                </div>
                <span class="status-badge flex-shrink-0 px-2 py-1 text-xs rounded {{ $worker->email_verified_at ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $worker->email_verified_at ? 'Activo' : 'Inactivo' }}
                </span>
            </div>
            <div class="text-sm text-gray-600 mb-3 space-y-1">
                @if($worker->phone)
                <div class="flex items-center gap-2"><i data-lucide="phone" class="w-4 h-4 text-emerald-500"></i><span>{{ $worker->phone }}</span></div>
                @endif
                <div class="flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4 text-emerald-500"></i><span>{{ $worker->created_at->format('d/m/Y') }}</span></div>
            </div>
            <div class="grid grid-cols-4 border-t border-gray-100 pt-3 gap-2">
                <button class="view-worker-btn flex items-center justify-center gap-1 py-2 text-blue-600 bg-blue-50 rounded-lg text-sm"
                    data-worker-id="{{ $worker->id }}" data-worker-name="{{ $worker->name }}" data-worker-email="{{ $worker->email }}"
                    data-worker-phone="{{ $worker->phone ?? '—' }}" data-worker-status="{{ $worker->email_verified_at ? 'Activo' : 'Inactivo' }}"
                    data-worker-registered="{{ $worker->created_at->format('d/m/Y H:i') }}" data-worker-photo="{{ $worker->photo ? storage_asset($worker->photo) : '' }}">
                    <i data-lucide="eye" class="w-4 h-4"></i><span>Ver</span>
                </button>
                <button class="report-worker-btn flex items-center justify-center gap-1 py-2 text-green-700 bg-green-50 rounded-lg text-sm"
                    data-worker-id="{{ $worker->id }}" data-worker-name="{{ $worker->name }}">
                    <i data-lucide="file-text" class="w-4 h-4"></i><span>Reporte</span>
                </button>
                <button class="edit-worker-btn flex items-center justify-center gap-1 py-2 text-emerald-700 bg-emerald-50 rounded-lg text-sm"
                    data-worker-id="{{ $worker->id }}" data-worker-name="{{ $worker->name }}" data-worker-email="{{ $worker->email }}"
                    data-worker-phone="{{ $worker->phone }}" data-worker-status="{{ $worker->email_verified_at ? 'active' : 'inactive' }}">
                    <i data-lucide="pencil" class="w-4 h-4"></i><span>Editar</span>
                </button>
                {{-- Eliminar --}}
                @if($worker->email_verified_at)
                    <button disabled class="flex items-center justify-center gap-1 py-2 text-gray-400 bg-gray-100 rounded-lg text-sm cursor-not-allowed opacity-60"
                            title="Desactive al trabajador antes de eliminar">
                        <i data-lucide="trash" class="w-4 h-4"></i><span>Borrar</span>
                    </button>
                @else
                    <button type="button"
                            class="delete-worker-card-btn flex items-center justify-center gap-1 py-2 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg text-sm transition-colors"
                            data-action="{{ route('foreman.workers.destroy', $worker) }}"
                            data-name="{{ $worker->name }}">
                        <i data-lucide="trash" class="w-4 h-4"></i><span>Borrar</span>
                    </button>
                @endif
            </div>

        </div>
        @empty
        <div class="py-8 text-center text-gray-500 bg-gray-50 rounded-lg border border-dashed text-sm">No hay trabajadores registrados</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $workers->links() }}</div>
</div>

{{-- editModal: max-h para pantallas pequeñas --}}
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded p-5 sm:p-6 w-full max-w-md mx-4 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-emerald-700">Editar Trabajador</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <form id="editWorkerForm" class="space-y-4">
            @csrf
            @method('PUT')
            
            <!-- Nombre -->
            <div>
                <label for="editName" class="block text-sm font-medium text-emerald-700 mb-2">Nombre Completo *</label>
                <input type="text" name="name" id="editName" required 
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('name') border-red-500 @enderror">
                <div id="editNameError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Email -->
            <div>
                <label for="editEmail" class="block text-sm font-medium text-emerald-700 mb-2">Email *</label>
                <input type="email" name="email" id="editEmail" required 
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('email') border-red-500 @enderror">
                <div id="editEmailError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Teléfono -->
            <div>
                <label for="editPhone" class="block text-sm font-medium text-emerald-700 mb-2">Teléfono</label>
                <input type="tel" name="phone" id="editPhone" 
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('phone') border-red-500 @enderror">
                <div id="editPhoneError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Foto -->
            <div>
                <label for="editPhoto" class="block text-sm font-medium text-emerald-700 mb-2">Foto (Opcional)</label>
                <input type="file" name="photo" id="editPhoto" accept="image/*"
                       class="w-full border border-emerald-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <div id="editPhotoError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <!-- Información del Sistema -->
            <div class="bg-gray-50 rounded p-4">
                <h4 class="text-sm font-semibold text-emerald-700 mb-2">Información del Sistema</h4>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Estado:</span>
                        <select name="status" id="editStatusSelect" class="w-auto border border-emerald-200 rounded px-2 py-1 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Registrado:</span>
                        <span id="editRegistered" class="text-sm text-gray-900"></span>
                    </div>
                </div>
            </div>

            {{-- Responsive: stacked on mobile, row on sm+ --}}
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeEditModal()" class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors text-center">
                    Cancelar
                </button>
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors inline-flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Actualizar Trabajador
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ver Detalles del Trabajador -->
<div id="viewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-semibold text-emerald-700">Detalles del Trabajador</h3>
            <button type="button" onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="space-y-6">
            <!-- Foto y Datos Personales -->
            {{-- Foto e Info apiladas en móvil, lado a lado en sm+ --}}
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6">
                <!-- Foto -->
                <div class="flex-shrink-0">
                    <div id="viewPhotoContainer" class="w-32 h-32 rounded-lg bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center overflow-hidden">
                        <img id="viewPhoto" src="" alt="Foto" class="w-full h-full object-cover" style="display: none;">
                        <i id="viewPhotoIcon" data-lucide="user" class="w-16 h-16 text-emerald-600"></i>
                    </div>
                </div>
                
                <!-- Información Personal -->
                <div class="flex-1">
                    <h4 id="viewName" class="text-2xl font-bold text-gray-900 mb-2"></h4>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <i data-lucide="mail" class="w-4 h-4 text-gray-500"></i>
                            <span id="viewEmail" class="text-gray-700"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="phone" class="w-4 h-4 text-gray-500"></i>
                            <span id="viewPhone" class="text-gray-700"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                            <span class="text-gray-600 text-sm">Registrado: </span>
                            <span id="viewRegistered" class="text-gray-700 text-sm"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="circle-check" class="w-4 h-4 text-gray-500"></i>
                            <span class="text-gray-600 text-sm">Estado: </span>
                            <span id="viewStatus" class="px-2 py-1 rounded text-xs font-medium"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Botón Cerrar -->
        <div class="flex items-center justify-end mt-6 pt-4 border-t">
            <button type="button" onclick="closeViewModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal Reporte del Trabajador -->
<div id="reportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" style="display: none;">
    <div class="bg-white border rounded-lg p-6 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6 pb-4 border-b">
            <h3 class="text-xl font-semibold text-emerald-700">Reporte del Trabajador</h3>
            <button type="button" onclick="closeReportModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div id="reportContent">
            <!-- Loading indicator -->
            <div id="reportLoading" class="flex items-center justify-center py-12">
                <div class="text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600"></div>
                    <p class="mt-4 text-gray-600">Cargando reporte...</p>
                </div>
            </div>
            
            <!-- Report content (hidden initially) -->
            <div id="reportData" style="display: none;">
                <!-- Worker Info -->
                <div class="mb-6 pb-6 border-b">
                    <h4 class="text-lg font-semibold text-emerald-800 mb-4">Información del Trabajador</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <p id="reportWorkerName" class="text-lg text-gray-900 mt-1"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p id="reportWorkerEmail" class="text-lg text-gray-900 mt-1"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            <p id="reportWorkerStatus" class="text-lg mt-1"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                            <p id="reportWorkerRegistered" class="text-lg text-gray-900 mt-1"></p>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="mb-6 pb-6 border-b">
                    <h4 class="text-lg font-semibold text-emerald-800 mb-4">Resumen General</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-blue-700">Total de Tareas</div>
                            <div id="reportTotalTasks" class="text-2xl font-bold text-blue-900 mt-2"></div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-green-700">Total Horas</div>
                            <div id="reportTotalHours" class="text-2xl font-bold text-green-900 mt-2"></div>
                        </div>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-orange-700">Total Kilos</div>
                            <div id="reportTotalKilos" class="text-2xl font-bold text-orange-900 mt-2"></div>
                        </div>
                        <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-emerald-700">Total Acumulado</div>
                            <div id="reportTotalPayment" class="text-2xl font-bold text-emerald-900 mt-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Crop Totals -->
                <div id="reportCropSection" class="mb-6 pb-6 border-b" style="display: none;">
                    <h4 class="text-lg font-semibold text-emerald-800 mb-4">Resumen por Cultivo</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-emerald-800 border-b">
                                    <th class="py-3 pr-4">Cultivo</th>
                                    <th class="py-3 pr-4 text-right">Tareas</th>
                                    <th class="py-3 pr-4 text-right">Horas</th>
                                    <th class="py-3 pr-4 text-right">Kilos</th>
                                    <th class="py-3 pr-4 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody id="reportCropTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tasks Table -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-emerald-800 mb-4">Detalle de Tareas</h4>
                    <div id="reportTasksContainer">
                        <p class="text-center py-8 text-gray-500">No hay tareas para mostrar.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t sticky bottom-0 bg-white">
            <button type="button" onclick="closeReportModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors">
                Cerrar
            </button>
            <button type="button" id="downloadPdfBtn" onclick="downloadReportPDF()" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700 transition-colors">
                <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
                Descargar PDF
            </button>
        </div>
    </div>
</div>

<script>
let currentWorkerId = null;

function openEditModal(id, name, email, phone, status) {
    currentWorkerId = id;
    
    // Llenar el formulario
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editPhone').value = phone || '';
    
    // Actualizar el select de estado
    const statusSelect = document.getElementById('editStatusSelect');
    statusSelect.value = status;
    
    // Obtener fecha de registro del trabajador actual
    const workerRow = document.querySelector(`tr[data-worker-id="${id}"]`);
    const registeredDate = workerRow.querySelector('td:nth-child(7) div').textContent;
    document.getElementById('editRegistered').textContent = registeredDate;
    
    // Limpiar errores anteriores
    clearEditErrors();
    
    // Mostrar modal
    document.getElementById('editModal').style.display = 'flex';
    
    // Actualizar iconos de Lucide
    if (window.lucide) {
        window.lucide.createIcons();
    }
}

// Agregar event listeners para los botones de editar
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.edit-worker-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-worker-id');
            const name = this.getAttribute('data-worker-name');
            const email = this.getAttribute('data-worker-email');
            const phone = this.getAttribute('data-worker-phone') || ''; // Ensure phone exists via data attribute or is empty
            const status = this.getAttribute('data-worker-status');
            
            openEditModal(id, name, email, phone, status);
        });
    });
    
    // Event listeners para botones de ver detalles
    document.querySelectorAll('.view-worker-btn').forEach(button => {
        button.addEventListener('click', function() {
            const name = this.getAttribute('data-worker-name');
            const email = this.getAttribute('data-worker-email');
            const phone = this.getAttribute('data-worker-phone');
            const status = this.getAttribute('data-worker-status');
            const registered = this.getAttribute('data-worker-registered');
            const photo = this.getAttribute('data-worker-photo');
            
            openViewModal(name, email, phone, status, registered, photo);
        });
    });
    
    // Event listeners para botones de reportes
    document.querySelectorAll('.report-worker-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-worker-id');
            const name = this.getAttribute('data-worker-name');
            
            openReportModal(id, name);
        });
    });
    
    // Actualizar iconos de Lucide
    if (window.lucide) {
        window.lucide.createIcons();
    }
});

// Variables para el reporte
let currentReportWorkerId = null;

// Funciones para el modal de reporte
async function openReportModal(workerId, workerName) {
    currentReportWorkerId = workerId;
    
    // Mostrar modal con loading
    document.getElementById('reportModal').style.display = 'flex';
    document.getElementById('reportLoading').style.display = 'flex';
    document.getElementById('reportData').style.display = 'none';
    
    try {
        // Obtener datos del reporte
        const response = await fetch(`/foreman/workers/${workerId}/report-data`);
        if (!response.ok) throw new Error('Error al cargar el reporte');
        
        const data = await response.json();
        
        // Llenar información del trabajador
        document.getElementById('reportWorkerName').textContent = data.worker.name;
        document.getElementById('reportWorkerEmail').textContent = data.worker.email;
        document.getElementById('reportWorkerRegistered').textContent = data.worker.registered;
        
        const statusEl = document.getElementById('reportWorkerStatus');
        if (data.worker.status === 'Activo') {
            statusEl.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>';
        } else {
            statusEl.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>';
        }
        
        // Llenar totales
        document.getElementById('reportTotalTasks').textContent = data.totals.tasks;
        document.getElementById('reportTotalHours').textContent = Number(data.totals.hours).toFixed(0);
        document.getElementById('reportTotalKilos').textContent = Number(data.totals.kilos).toFixed(3);
        document.getElementById('reportTotalPayment').textContent = '$' + Number(data.totals.payment).toFixed(0);
        
        // Llenar resumen por cultivo
        if (data.cropTotals && data.cropTotals.length > 0) {
            document.getElementById('reportCropSection').style.display = 'block';
            const cropTableBody = document.getElementById('reportCropTableBody');
            cropTableBody.innerHTML = '';
            
            data.cropTotals.forEach(crop => {
                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-gray-50';
                row.innerHTML = `
                    <td class="py-3 pr-4 font-medium">${crop.crop}</td>
                    <td class="py-3 pr-4 text-right">${crop.tasks_count}</td>
                    <td class="py-3 pr-4 text-right">${Number(crop.total_hours).toFixed(0)}</td>
                    <td class="py-3 pr-4 text-right">${Number(crop.total_kilos).toFixed(3)}</td>
                    <td class="py-3 pr-4 text-right font-semibold text-emerald-700">$${Number(crop.total_payment).toFixed(0)}</td>
                `;
                cropTableBody.appendChild(row);
            });
        } else {
            document.getElementById('reportCropSection').style.display = 'none';
        }
        
        // Llenar detalle de tareas
        const tasksContainer = document.getElementById('reportTasksContainer');
        if (data.tasks && data.tasks.length > 0) {
            let tasksHTML = '<div class="overflow-x-auto"><table class="min-w-full text-sm"><thead><tr class="text-left text-emerald-800 border-b">';
            tasksHTML += '<th class="py-3 pr-4">Fecha</th>';
            tasksHTML += '<th class="py-3 pr-4">Tipo</th>';
            tasksHTML += '<th class="py-3 pr-4">Descripción</th>';
            tasksHTML += '<th class="py-3 pr-4">Cultivo</th>';
            tasksHTML += '<th class="py-3 pr-4">Lote</th>';
            tasksHTML += '<th class="py-3 pr-4 text-right">Horas</th>';
            tasksHTML += '<th class="py-3 pr-4 text-right">Kilos</th>';
            tasksHTML += '<th class="py-3 pr-4 text-right">Total</th>';
            tasksHTML += '</tr></thead><tbody>';
            
            data.tasks.forEach(task => {
                tasksHTML += '<tr class="border-b hover:bg-gray-50">';
                tasksHTML += `<td class="py-3 pr-4">${task.date}</td>`;
                tasksHTML += `<td class="py-3 pr-4"><span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">${task.type}</span></td>`;
                tasksHTML += `<td class="py-3 pr-4"><div class="text-sm text-gray-900 max-w-xs">${task.description}</div></td>`;
                tasksHTML += `<td class="py-3 pr-4"><span class="font-medium">${task.crop}</span></td>`;
                tasksHTML += `<td class="py-3 pr-4">${task.plot}</td>`;
                tasksHTML += `<td class="py-3 pr-4 text-right">${task.hours > 0 ? Number(task.hours).toFixed(0) : '<span class="text-gray-400">-</span>'}</td>`;
                tasksHTML += `<td class="py-3 pr-4 text-right">${task.kilos > 0 ? Number(task.kilos).toFixed(3) : '<span class="text-gray-400">-</span>'}</td>`;
                tasksHTML += `<td class="py-3 pr-4 text-right font-semibold text-emerald-700">$${Number(task.total).toFixed(0)}</td>`;
                tasksHTML += '</tr>';
            });
            
            tasksHTML += '</tbody><tfoot><tr class="bg-emerald-50 font-semibold">';
            tasksHTML += '<td colspan="7" class="py-3 pr-4 text-right">Total Acumulado:</td>';
            tasksHTML += `<td class="py-3 pr-4 text-right text-emerald-700 text-lg">$${Number(data.totals.payment).toFixed(0)}</td>`;
            tasksHTML += '</tr></tfoot></table></div>';
            
            tasksContainer.innerHTML = tasksHTML;
        } else {
            tasksContainer.innerHTML = '<p class="text-center py-8 text-gray-500">No hay tareas aprobadas para este trabajador.</p>';
        }
        
        // Ocultar loading y mostrar datos
        document.getElementById('reportLoading').style.display = 'none';
        document.getElementById('reportData').style.display = 'block';
        
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('reportLoading').innerHTML = `
            <div class="text-center text-red-600">
                <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-4"></i>
                <p>Error al cargar el reporte. Por favor, intenta nuevamente.</p>
            </div>
        `;
    }
    
    // Actualizar iconos de Lucide
    if (window.lucide) {
        window.lucide.createIcons();
    }
}

function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
    currentReportWorkerId = null;
}

function downloadReportPDF() {
    if (currentReportWorkerId) {
        // Descargar PDF directamente
        window.location.href = `/foreman/workers/${currentReportWorkerId}/report-pdf`;
    }
}

// Funciones para el modal de detalles
function openViewModal(name, email, phone, status, registered, photo) {
    // Llenar información
    document.getElementById('viewName').textContent = name;
    document.getElementById('viewEmail').textContent = email;
    document.getElementById('viewPhone').textContent = phone;
    document.getElementById('viewRegistered').textContent = registered;
    
    // Estado
    const statusElement = document.getElementById('viewStatus');
    statusElement.textContent = status;
    if (status === 'Activo') {
        statusElement.className = 'px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700';
    } else {
        statusElement.className = 'px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-700';
    }
    
    // Foto
    const photoImg = document.getElementById('viewPhoto');
    const photoIcon = document.getElementById('viewPhotoIcon');
    if (photo) {
        photoImg.src = photo;
        photoImg.style.display = 'block';
        photoIcon.style.display = 'none';
    } else {
        photoImg.style.display = 'none';
        photoIcon.style.display = 'block';
    }
    
    // Mostrar modal
    document.getElementById('viewModal').style.display = 'flex';
    
    // Actualizar iconos de Lucide
    if (window.lucide) {
        window.lucide.createIcons();
    }
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    currentWorkerId = null;
    clearEditErrors();
}

function clearEditErrors() {
    document.getElementById('editNameError').classList.add('hidden');
    document.getElementById('editEmailError').classList.add('hidden');
    document.getElementById('editNameError').textContent = '';
    document.getElementById('editEmailError').textContent = '';
}

// Manejar envío del formulario
document.getElementById('editWorkerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!currentWorkerId) return;
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Add _method PUT and status manually if needed, though FormData captures inputs
    formData.append('_method', 'PUT');
    // Ensure status is captured correctly from the select
    formData.set('status', document.getElementById('editStatusSelect').value);
    
    try {
        // Mostrar estado de carga
        submitButton.disabled = true;
        submitButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i>Actualizando...';
        
        const response = await fetch(`/foreman/workers/${currentWorkerId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                // 'Content-Type': 'multipart/form-data', // Do NOT set Content-Type header manually when using FormData
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            sessionStorage.setItem('toastMessage', data.message);
            window.location.reload();
        } else {
            // Mostrar errores de validación
            if (data.errors) {
                if (data.errors.name) {
                    showEditError('editNameError', data.errors.name[0]);
                }
                if (data.errors.email) {
                    showEditError('editEmailError', data.errors.email[0]);
                }
            } else {
                showNotification(data.message || 'Error al actualizar el trabajador', 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión. Inténtalo de nuevo.', 'error');
    } finally {
        // Restaurar botón
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    }
});

function showEditError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    errorElement.textContent = message;
    errorElement.classList.remove('hidden');
}

function updateWorkerRow(workerId, workerData) {
    const workerRow = document.querySelector(`tr[data-worker-id="${workerId}"]`);
    if (workerRow) {
        // Actualizar foto
        const photoCell = workerRow.querySelector('td:first-child');
        if (workerData.photo) {
            photoCell.innerHTML = `<img src="${workerData.photo}" alt="Foto" class="h-10 w-10 rounded-full object-cover border border-gray-200">`;
        } else {
            photoCell.innerHTML = `<div class="h-10 w-10 rounded-full border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-xs text-gray-400">—</div>`;
        }

        // Actualizar nombre
        workerRow.querySelector('.worker-name').textContent = workerData.name;
        
        // Actualizar email
        workerRow.querySelector('.worker-email').textContent = workerData.email;

        // Actualizar teléfono
        const phoneCell = workerRow.querySelector('td:nth-child(4) div');
        if (phoneCell) {
            phoneCell.textContent = workerData.phone || '—';
        }
        
        // Actualizar estado
        const statusBadge = workerRow.querySelector('.status-badge');
        if (workerData.status === 'active') {
            statusBadge.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">Activo</span>';
        } else {
            statusBadge.innerHTML = '<span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Inactivo</span>';
        }

        // --- ACTUALIZAR ATRIBUTOS DATA PARA FUTURAS ACCIONES (VITAL) ---
        
        // Botón Editar
        const editBtn = workerRow.querySelector('.edit-worker-btn');
        if (editBtn) {
            editBtn.setAttribute('data-worker-name', workerData.name);
            editBtn.setAttribute('data-worker-email', workerData.email);
            editBtn.setAttribute('data-worker-phone', workerData.phone || '');
            editBtn.setAttribute('data-worker-status', workerData.status);
        }

        // --- ACTUALIZAR BOTÓN DE ELIMINAR (NUEVO) ---
        updateDeleteButton(workerRow, workerData.status, `/foreman/workers/${workerId}`);

        // Botón Ver Detalles
        const viewBtn = workerRow.querySelector('.view-worker-btn');
        if (viewBtn) {
            viewBtn.setAttribute('data-worker-name', workerData.name);
            viewBtn.setAttribute('data-worker-email', workerData.email);
            viewBtn.setAttribute('data-worker-phone', workerData.phone || '—');
            viewBtn.setAttribute('data-worker-status', workerData.status === 'active' ? 'Activo' : 'Inactivo');
            if (workerData.photo) {
                viewBtn.setAttribute('data-worker-photo', workerData.photo);
            }
        }

        // Botón Reporte
        const reportBtn = workerRow.querySelector('.report-worker-btn');
        if (reportBtn) {
            reportBtn.setAttribute('data-worker-name', workerData.name);
        }
    }
}

function showNotification(message, type = 'info') {
    // Usar la función global de SweetAlert2 toast
    if (window.showSuccessAlert && type === 'success') {
        window.showSuccessAlert(message);
    } else if (window.showErrorAlert && type === 'error') {
        window.showErrorAlert(message);
    } else if (window.showSuccessAlert) {
        window.showSuccessAlert(message);
    }
}

// Cerrar modal al hacer clic fuera de él
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Función para actualizar el botón de eliminar dinámicamente
function updateDeleteButton(row, status, destroyUrl) {
    const container = row.querySelector('.delete-action-container');
    if (!container) return;

    if (status === 'active') {
        container.innerHTML = `
            <button disabled class="inline-flex items-center justify-center w-8 h-8 border border-gray-200 rounded bg-gray-100 text-gray-400 cursor-not-allowed opacity-60" title="No se puede eliminar un trabajador activo. Debe desactivarlo primero.">
                <i data-lucide="trash" class="w-4 h-4"></i>
            </button>
        `;
    } else {
        container.innerHTML = `
            <form method="POST" action="${destroyUrl}" class="inline" data-confirm="true" data-message="¿Eliminar este trabajador? Esta acción no se puede deshacer.">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <input type="hidden" name="_method" value="DELETE">
                <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Eliminar">
                    <i data-lucide="trash" class="w-4 h-4"></i>
                </button>
            </form>
        `;
    }
    
    // Reinicializar iconos de Lucide
    if (window.lucide) {
        window.lucide.createIcons();
    }
}
// Handler para botones Eliminar en tarjetas móviles
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-worker-card-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const action = this.getAttribute('data-action');
            const name = this.getAttribute('data-name');
            Swal.fire({
                title: '¿Eliminar trabajador?',
                text: `Se eliminará a "${name}". Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
            }).then(result => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = action;
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
    // Restaurar Toast por SessionStorage (después de una edición exitosa de trabajador)
    if (sessionStorage.getItem('toastMessage')) {
        showNotification(sessionStorage.getItem('toastMessage'), 'success');
        sessionStorage.removeItem('toastMessage');
    }
});
</script>
@endsection


