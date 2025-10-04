@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Lotes</h2>
    <a href="{{ route('admin.plots.create') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">
        <i data-lucide="plus" class="w-4 h-4"></i>
        <span>Nuevo Lote</span>
    </a>
  </div>
@endsection

@section('content')
<div class="bg-white border rounded p-4">

    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por nombre" class="border border-emerald-200 rounded px-3 py-2 w-full" />
        <button class="px-3 py-2 border border-emerald-300 rounded text-emerald-700 hover:bg-emerald-100 inline-flex items-center gap-2">
            <i data-lucide="search" class="w-4 h-4"></i>
            <span>Buscar</span>
        </button>
    </form>

    <div class="overflow-x-auto" x-data="plotEdit()" @close-edit.window="open=false">
        <!-- Notificación de error -->
        <div x-show="showError" x-cloak class="fixed top-4 right-4 z-50 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg" x-transition>
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                <span x-text="errorMessage"></span>
                <button @click="showError = false" class="ml-4 text-red-500 hover:text-red-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        
        <!-- Notificación de éxito -->
        <div x-show="showSuccess" x-cloak class="fixed top-4 right-4 z-50 bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded shadow-lg" x-transition>
            <div class="flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                <span x-text="successMessage"></span>
                <button @click="showSuccess = false" class="ml-4 text-emerald-500 hover:text-emerald-700">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800">
                    <th class="py-2 pr-4">Nombre</th>
                    <th class="py-2 pr-4">Ubicación</th>
                    <th class="py-2 pr-4">Área (ha)</th>
                    <th class="py-2 pr-4">Cultivo</th>
                    <th class="py-2 pr-4">Estado</th>
                    <th class="py-2 pr-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($plots as $plot)
                <tr class="border-t">
                    <td class="py-2 pr-4">{{ $plot->name }}</td>
                    <td class="py-2 pr-4">{{ $plot->location ?? '—' }}</td>
                    <td class="py-2 pr-4">{{ $plot->area ?? '—' }}</td>
                    <td class="py-2 pr-4">
                        @if($plot->crops->count() > 0)
                            @foreach($plot->crops as $crop)
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">
                                        {{ $crop->name }}
                                    </span>
                                    @if($crop->variety)
                                        <span class="text-xs text-gray-500">({{ $crop->variety }})</span>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <span class="text-gray-400 text-sm">Sin cultivo</span>
                        @endif
                    </td>
                    <td class="py-2 pr-4">
                        <span class="px-2 py-0.5 text-xs rounded {{ $plot->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $plot->status === 'active' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="py-2 pr-4 text-right">
                        <div class="flex items-center gap-1 justify-end">
                            @php
                                $activeCrops = $plot->crops->where('status', 'active')->count();
                                $canDisable = $activeCrops === 0;
                                $canDelete = $canDisable && $plot->status === 'inactive';
                            @endphp
                            
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" @click="open=true; plotId={{ $plot->id }}; name='{{ $plot->name }}'; location='{{ $plot->location }}'; area='{{ $plot->area }}'; status='{{ $plot->status }}'" title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            
                            @if($canDelete)
                                <form method="POST" action="{{ route('admin.plots.destroy', $plot) }}" class="inline" data-confirm="true" data-message="¿Eliminar lote?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center justify-center w-8 h-8 border border-red-200 rounded hover:bg-red-50 text-red-600" title="Eliminar">
                                        <i data-lucide="trash" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            @else
                                <button class="inline-flex items-center justify-center w-8 h-8 border border-gray-200 rounded opacity-50 cursor-not-allowed bg-gray-100 text-gray-400" 
                                        title="{{ $activeCrops > 0 ? "No se puede eliminar: tiene {$activeCrops} cultivo(s) activo(s)" : 'No se puede eliminar un lote activo' }}" 
                                        disabled>
                                    <i data-lucide="trash" class="w-4 h-4"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-6 text-center text-emerald-800/70">Sin resultados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Modal de edición -->
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white border rounded p-6 w-full max-w-2xl" @click.away="open=false">
                <h3 class="text-lg font-semibold text-emerald-700 mb-4">Editar Lote</h3>
                <form method="POST" :action="'/admin/plots/' + plotId" class="space-y-4" @submit.prevent="submitForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <div>
                        <label class="block text-sm mb-1 text-emerald-800">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="name" class="w-full border border-emerald-200 rounded px-3 py-2" :class="errors.name ? 'border-red-500' : ''" required />
                        <div x-show="errors.name" class="text-sm text-red-600 mt-1" x-text="errors.name"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1 text-emerald-800">Ubicación <span class="text-red-500">*</span></label>
                            <input type="text" name="location" x-model="location" class="w-full border border-emerald-200 rounded px-3 py-2" :class="errors.location ? 'border-red-500' : ''" required />
                            <div x-show="errors.location" class="text-sm text-red-600 mt-1" x-text="errors.location"></div>
                        </div>
                        <div>
                            <label class="block text-sm mb-1 text-emerald-800">Área (ha) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="1" name="area" x-model="area" class="w-full border border-emerald-200 rounded px-3 py-2" :class="errors.area ? 'border-red-500' : ''" required />
                            <div x-show="errors.area" class="text-sm text-red-600 mt-1" x-text="errors.area"></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm mb-1 text-emerald-800">Estado <span class="text-red-500">*</span></label>
                        <select name="status" x-model="status" class="w-full border border-emerald-200 rounded px-3 py-2" :class="errors.status ? 'border-red-500' : ''" required>
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                        <div x-show="errors.status" class="text-sm text-red-600 mt-1" x-text="errors.status"></div>
                        <!-- Advertencia cuando se selecciona inactivo -->
                        <div x-show="status === 'inactive'" class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-700">
                            <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                            <span>Al inhabilitar este lote, asegúrese de que no tenga cultivos activos.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="px-3 py-2 border rounded inline-flex items-center gap-2" @click="open=false; errors={}; showError=false; showSuccess=false"><i data-lucide="x" class="w-4 h-4"></i><span>Cancelar</span></button>
                        <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2"><i data-lucide="save" class="w-4 h-4"></i><span>Actualizar</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $plots->links() }}</div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('plotEdit', () => ({
        open: false,
        plotId: null,
        name: '',
        location: '',
        area: '',
        status: 'active',
        errors: {},
        showError: false,
        errorMessage: '',
        showSuccess: false,
        successMessage: '',
        
        async submitForm() {
            this.errors = {};
            this.showError = false;
            this.showSuccess = false;
            
            const formData = new FormData();
            formData.append('_token', document.querySelector('input[name="_token"]').value);
            formData.append('_method', 'PUT');
            formData.append('name', this.name);
            formData.append('location', this.location);
            formData.append('area', this.area);
            formData.append('status', this.status);
            
            try {
                const response = await fetch(`/admin/plots/${this.plotId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    // Éxito - mostrar mensaje y cerrar modal
                    const data = await response.json();
                    this.successMessage = data.message || 'Lote actualizado correctamente';
                    this.showSuccess = true;
                    this.open = false;
                    
                    // Auto-ocultar después de 3 segundos
                    setTimeout(() => {
                        this.showSuccess = false;
                    }, 3000);
                    
                    // Recargar la página después de un breve delay para mostrar el mensaje
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Error de validación o lógica de negocio
                    const data = await response.json();
                    if (data.errors) {
                        this.errors = data.errors;
                    } else if (data.message) {
                        // Mostrar mensaje de error general (como cultivos activos)
                        this.errorMessage = data.message;
                        this.showError = true;
                        // Auto-ocultar después de 5 segundos
                        setTimeout(() => {
                            this.showError = false;
                        }, 5000);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                this.errorMessage = 'Ocurrió un error al actualizar el lote. Por favor, inténtelo de nuevo.';
                this.showError = true;
                setTimeout(() => {
                    this.showError = false;
                }, 5000);
            }
        }
    }));
});
</script>
@endsection


