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

    <div class="overflow-x-auto" x-data="{ open: false, plotId: null, name: '', location: '', area: '', status: 'active' }" @close-edit.window="open=false">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-emerald-800">
                    <th class="py-2 pr-4">Nombre</th>
                    <th class="py-2 pr-4">Ubicación</th>
                    <th class="py-2 pr-4">Área (ha)</th>
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
                        <span class="px-2 py-0.5 text-xs rounded {{ $plot->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $plot->status === 'active' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="py-2 pr-4 text-right">
                        <button type="button" class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-emerald-50" @click="open=true; plotId={{ $plot->id }}; name='{{ $plot->name }}'; location='{{ $plot->location }}'; area='{{ $plot->area }}'; status='{{ $plot->status }}'"><i data-lucide="pencil" class="w-4 h-4"></i>Editar</button>
                        <form method="POST" action="{{ route('admin.plots.destroy', $plot) }}" class="inline" data-confirm="true" data-message="¿Eliminar lote?">
                            @csrf
                            @method('DELETE')
                            <button class="inline-flex items-center gap-1 px-2 py-1 border rounded hover:bg-red-50 text-red-600"><i data-lucide="trash" class="w-4 h-4"></i>Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-emerald-800/70">Sin resultados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <!-- Modal de edición -->
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white border rounded p-6 w-full max-w-2xl" @click.away="open=false">
                <h3 class="text-lg font-semibold text-emerald-700 mb-4">Editar Lote</h3>
                <form method="POST" :action="'/admin/plots/' + plotId" class="space-y-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <div>
                        <label class="block text-sm mb-1 text-emerald-800">Nombre</label>
                        <input type="text" name="name" x-model="name" class="w-full border border-emerald-200 rounded px-3 py-2" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1 text-emerald-800">Ubicación</label>
                            <input type="text" name="location" x-model="location" class="w-full border border-emerald-200 rounded px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-sm mb-1 text-emerald-800">Área (ha)</label>
                            <input type="number" step="0.01" min="0" name="area" x-model="area" class="w-full border border-emerald-200 rounded px-3 py-2" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm mb-1 text-emerald-800">Estado</label>
                        <select name="status" x-model="status" class="w-full border border-emerald-200 rounded px-3 py-2">
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="px-3 py-2 border rounded inline-flex items-center gap-2" @click="open=false"><i data-lucide="x" class="w-4 h-4"></i><span>Cancelar</span></button>
                        <button class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded inline-flex items-center gap-2"><i data-lucide="save" class="w-4 h-4"></i><span>Actualizar</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $plots->links() }}</div>
</div>
@endsection


