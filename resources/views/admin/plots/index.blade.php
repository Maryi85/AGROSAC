@extends('admin.layout')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="text-lg font-semibold text-emerald-700">Lotes</h2>
</div>
@endsection

@section('content')
<div class="bg-white border rounded p-4">

    <!-- Botón para agregar nuevo lote -->
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.plots.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded-lg font-medium transition-colors">
            <i data-lucide="plus" class="w-5 h-5"></i>
            <span>Nuevo Lote</span>
        </a>
        <a href="{{ route('admin.plots.pdf', request()->query()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded-lg font-medium transition-colors">
            <i data-lucide="file-text" class="w-5 h-5"></i>
            <span>Descargar PDF</span>
        </a>
    </div>

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
                            
                            <button type="button" class="inline-flex items-center justify-center w-8 h-8 border border-emerald-200 rounded hover:bg-emerald-50 text-emerald-600" 
                                    data-plot-id="{{ $plot->id }}"
                                    data-plot-name="{{ $plot->name }}"
                                    data-plot-latitude="{{ $plot->latitude ?? '' }}"
                                    data-plot-longitude="{{ $plot->longitude ?? '' }}"
                                    data-plot-boundary="{{ $plot->boundary ? json_encode($plot->boundary) : '' }}"
                                    data-plot-area="{{ $plot->area }}"
                                    data-plot-status="{{ $plot->status }}"
                                    @click="openEditModal($el.dataset.plotId, $el.dataset.plotName, $el.dataset.plotLatitude || null, $el.dataset.plotLongitude || null, $el.dataset.plotBoundary ? JSON.parse($el.dataset.plotBoundary) : null, $el.dataset.plotArea, $el.dataset.plotStatus)" 
                                    title="Editar">
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
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 overflow-y-auto p-4">
            <div class="bg-white border rounded p-6 w-full max-w-4xl my-4" @click.away="closeModal()">
                <h3 class="text-lg font-semibold text-emerald-700 mb-4">Editar Lote</h3>
                <form method="POST" :action="'/admin/plots/' + plotId" class="space-y-4" @submit.prevent="submitForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <div>
                        <label class="block text-sm mb-1 text-emerald-800">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="name" class="w-full border border-emerald-200 rounded px-3 py-2" :class="errors.name ? 'border-red-500' : ''" required />
                        <div x-show="errors.name" class="text-sm text-red-600 mt-1" x-text="errors.name"></div>
                    </div>
                    
                    <!-- Mapa para seleccionar ubicación y delimitar lote -->
                    <div>
                        <label class="block text-sm mb-1 text-emerald-800">Ubicación y Delimitación del Lote</label>
                        <div class="mb-2 flex gap-2 flex-wrap">
                            <button type="button" id="draw-polygon-modal" class="px-3 py-1.5 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 border border-blue-200 rounded">
                                <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i> Delimitar Lote
                            </button>
                            <button type="button" id="delete-polygon-modal" class="px-3 py-1.5 text-xs bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded">
                                <i data-lucide="trash-2" class="w-3 h-3 inline mr-1"></i> Eliminar Polígono
                            </button>
                            <button type="button" id="clear-all-modal" class="px-3 py-1.5 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded">
                                <i data-lucide="x" class="w-3 h-3 inline mr-1"></i> Limpiar Todo
                            </button>
                        </div>
                        <div class="mb-2">
                            <span class="text-xs text-emerald-800">Área calculada: </span>
                            <span id="calculatedAreaDisplayModal" class="text-xs font-semibold text-emerald-700">0.0000 ha</span>
                        </div>
                        <div id="plotMapModal" style="width: 100%; height: 400px; border: 2px solid #10b981; border-radius: 8px; overflow: hidden; position: relative; background-color: #f0f0f0;"></div>
                        <div class="mt-2 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs mb-1 text-emerald-800">Latitud (Centro)</label>
                                <input type="text" name="latitude" id="latitudeModal" x-model="latitude" readonly class="w-full border border-emerald-200 rounded px-3 py-2 bg-gray-50 text-xs" />
                                <div x-show="errors.latitude" class="text-sm text-red-600 mt-1" x-text="errors.latitude"></div>
                            </div>
                            <div>
                                <label class="block text-xs mb-1 text-emerald-800">Longitud (Centro)</label>
                                <input type="text" name="longitude" id="longitudeModal" x-model="longitude" readonly class="w-full border border-emerald-200 rounded px-3 py-2 bg-gray-50 text-xs" />
                                <div x-show="errors.longitude" class="text-sm text-red-600 mt-1" x-text="errors.longitude"></div>
                            </div>
                        </div>
                        <input type="hidden" name="boundary" id="boundaryModal" x-model="boundary" />
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1 text-emerald-800">Área (ha) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0.01" name="area" id="areaModal" x-model="area" class="w-full border border-emerald-200 rounded px-3 py-2" :class="errors.area ? 'border-red-500' : ''" required />
                            <div x-show="errors.area" class="text-sm text-red-600 mt-1" x-text="errors.area"></div>
                        </div>
                        <div>
                            <label class="block text-sm mb-1 text-emerald-800">Estado <span class="text-red-500">*</span></label>
                            <select name="status" x-model="status" class="w-full border border-emerald-200 rounded px-3 py-2" :class="errors.status ? 'border-red-500' : ''" required>
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                            <div x-show="errors.status" class="text-sm text-red-600 mt-1" x-text="errors.status"></div>
                        </div>
                    </div>
                    <!-- Advertencia cuando se selecciona inactivo -->
                    <div x-show="status === 'inactive'" class="p-2 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-700">
                        <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                        <span>Al inhabilitar este lote, asegúrese de que no tenga cultivos activos.</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="px-3 py-2 border rounded inline-flex items-center gap-2" @click="closeModal()"><i data-lucide="x" class="w-4 h-4"></i><span>Cancelar</span></button>
                        <button type="submit" class="px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded inline-flex items-center gap-2 transition-colors"><i data-lucide="save" class="w-4 h-4"></i><span>Actualizar</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $plots->links() }}</div>
</div>

@push('scripts')
<link href="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css" type="text/css">
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.js"></script>
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.css" type="text/css">
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('plotEdit', () => ({
        open: false,
        plotId: null,
        name: '',
        latitude: '',
        longitude: '',
        boundary: '',
        area: '',
        status: 'active',
        errors: {},
        showError: false,
        errorMessage: '',
        showSuccess: false,
        successMessage: '',
        plotMap: null,
        plotMarker: null,
        draw: null,
        geocoder: null,
        
        openEditModal(plotId, name, latitude, longitude, boundary, area, status) {
            this.plotId = plotId;
            this.name = name || '';
            this.latitude = (latitude && latitude !== 'null' && latitude !== '') ? latitude : '';
            this.longitude = (longitude && longitude !== 'null' && longitude !== '') ? longitude : '';
            this.boundary = (boundary && boundary !== null && boundary !== 'null') ? 
                (typeof boundary === 'string' ? boundary : JSON.stringify(boundary)) : '';
            this.area = area || '';
            this.status = status || 'active';
            this.errors = {};
            this.showError = false;
            this.showSuccess = false;
            this.open = true;
            
            // Inicializar mapa después de que el modal esté visible
            this.$nextTick(() => {
                setTimeout(() => {
                    this.initMap();
                }, 200);
            });
        },
        
        closeModal() {
            this.open = false;
            this.errors = {};
            this.showError = false;
            this.showSuccess = false;
            // Limpiar mapa
            if (this.plotMap) {
                this.plotMap.remove();
                this.plotMap = null;
            }
            this.plotMarker = null;
            this.draw = null;
            this.geocoder = null;
        },
        
        initMap() {
            if (typeof mapboxgl === 'undefined') {
                console.error('Mapbox GL JS no está cargado');
                return;
            }

            const mapboxToken = '{{ config('services.mapbox.token') }}';
            
            if (!mapboxToken) {
                console.error('Mapbox token no configurado');
                return;
            }

            const mapContainer = document.getElementById('plotMapModal');
            if (!mapContainer) {
                console.error('Contenedor del mapa no encontrado');
                return;
            }

            // Verificar que el contenedor tenga dimensiones
            const containerRect = mapContainer.getBoundingClientRect();
            if (containerRect.width === 0 || containerRect.height === 0) {
                console.warn('El contenedor del mapa no tiene dimensiones visibles. Esperando...');
                setTimeout(() => {
                    this.initMap();
                }, 500);
                return;
            }

            mapboxgl.accessToken = mapboxToken;
            
            const defaultLocation = [-77.0428, -12.0464]; // Centro de Lima, Perú
            const initialLocation = (this.latitude && this.longitude) ? 
                [parseFloat(this.longitude), parseFloat(this.latitude)] : 
                defaultLocation;
            
            // Polígono de la finca (si existe)
            const farmBoundary = @json($farmSettings->boundary ?? null);
            const farmLat = @json($farmSettings->latitude ?? null);
            const farmLng = @json($farmSettings->longitude ?? null);

            let finalLocation = initialLocation;
            let finalZoom = (this.latitude && this.longitude) ? 15 : 12;
            
            if (!this.latitude && !this.longitude && farmLat !== null && farmLng !== null) {
                finalLocation = [parseFloat(farmLng), parseFloat(farmLat)];
                finalZoom = 15;
            }

            try {
                this.plotMap = new mapboxgl.Map({
                    container: 'plotMapModal',
                    style: 'mapbox://styles/mapbox/streets-v12',
                    center: finalLocation,
                    zoom: finalZoom
                });

                this.plotMap.on('error', (e) => {
                    console.error('Error en el mapa:', e);
                });

                this.plotMap.on('load', () => {
                    if (this.plotMap) {
                        this.plotMap.resize();
                    }

                    // Inicializar Mapbox Draw
                    if (typeof MapboxDraw !== 'undefined') {
                        this.draw = new MapboxDraw({
                            displayControlsDefault: false,
                            controls: {
                                polygon: true,
                                trash: true
                            },
                            defaultMode: 'simple_select'
                        });

                        this.plotMap.addControl(this.draw);
                    }

                    // Agregar el polígono de la finca si existe
                    if (farmBoundary !== null && farmBoundary.type === 'Polygon') {
                        try {
                            this.plotMap.addSource('farm-boundary', {
                                'type': 'geojson',
                                'data': {
                                    'type': 'Feature',
                                    'geometry': farmBoundary
                                }
                            });

                            this.plotMap.addLayer({
                                'id': 'farm-boundary-fill',
                                'type': 'fill',
                                'source': 'farm-boundary',
                                'layout': {},
                                'paint': {
                                    'fill-color': '#10b981',
                                    'fill-opacity': 0.2
                                }
                            });

                            this.plotMap.addLayer({
                                'id': 'farm-boundary-stroke',
                                'type': 'line',
                                'source': 'farm-boundary',
                                'layout': {},
                                'paint': {
                                    'line-color': '#10b981',
                                    'line-width': 3,
                                    'line-dasharray': [2, 2]
                                }
                            });

                            if (!this.latitude && !this.longitude) {
                                const coordinates = farmBoundary.coordinates[0];
                                const bounds = coordinates.reduce((bounds, coord) => {
                                    return bounds.extend(coord);
                                }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
                                
                                this.plotMap.fitBounds(bounds, {
                                    padding: 50
                                });
                            }
                        } catch (e) {
                            console.error('Error agregando polígono de la finca:', e);
                        }
                    }

                    // Agregar marcador de la finca si existe
                    if (farmLat !== null && farmLng !== null) {
                        new mapboxgl.Marker({
                            color: '#10b981'
                        })
                        .setLngLat([parseFloat(farmLng), parseFloat(farmLat)])
                        .setPopup(
                            new mapboxgl.Popup({ offset: 25 })
                                .setHTML(`
                                    <div style="padding: 10px;">
                                        <h3 style="margin: 0 0 5px 0; font-weight: bold; color: #10b981;">{{ $farmSettings->name ?? 'Finca' }}</h3>
                                        @if($farmSettings->address)
                                            <p style="margin: 0; color: #666;">{{ $farmSettings->address }}</p>
                                        @endif
                                    </div>
                                `)
                        )
                        .addTo(this.plotMap);
                    }

                    // Cargar polígono del lote si existe
                    if (this.boundary) {
                        try {
                            const boundaryData = typeof this.boundary === 'string' ? JSON.parse(this.boundary) : this.boundary;
                            if (boundaryData !== null && boundaryData.type === 'Polygon' && this.draw) {
                                const feature = {
                                    type: 'Feature',
                                    geometry: boundaryData
                                };
                                this.draw.add(feature);
                                
                                // Calcular y mostrar área
                                const areaHa = this.calculatePolygonArea(boundaryData.coordinates[0]);
                                document.getElementById('calculatedAreaDisplayModal').textContent = areaHa.toFixed(4) + ' ha';
                                document.getElementById('areaModal').value = areaHa.toFixed(2);
                            }
                        } catch (e) {
                            console.error('Error cargando polígono del lote:', e);
                        }
                    }

                    // Agregar lotes existentes al mapa (excluyendo el que se está editando)
                    const existingPlots = @json($existingPlots ?? []);
                    const filteredPlots = existingPlots.filter(plot => plot.id != this.plotId);
                    
                    if (filteredPlots && filteredPlots.length > 0) {
                        console.log('Agregando lotes existentes al modal:', filteredPlots.length);
                        
                        // Crear una colección de features para todos los lotes existentes
                        const existingPlotsFeatures = filteredPlots
                            .filter(plot => plot.boundary && plot.boundary.type === 'Polygon')
                            .map((plot, index) => {
                                return {
                                    type: 'Feature',
                                    id: `existing-plot-${plot.id}`,
                                    geometry: plot.boundary,
                                    properties: {
                                        id: plot.id,
                                        name: plot.name,
                                        plotId: plot.id
                                    }
                                };
                            });
                        
                        if (existingPlotsFeatures.length > 0) {
                            try {
                                // Agregar source para los lotes existentes
                                this.plotMap.addSource('existing-plots', {
                                    'type': 'geojson',
                                    'data': {
                                        'type': 'FeatureCollection',
                                        'features': existingPlotsFeatures
                                    }
                                });
                                
                                // Agregar capa de relleno para los lotes existentes
                                this.plotMap.addLayer({
                                    'id': 'existing-plots-fill',
                                    'type': 'fill',
                                    'source': 'existing-plots',
                                    'layout': {},
                                    'paint': {
                                        'fill-color': '#ef4444', // Rojo
                                        'fill-opacity': 0.3
                                    }
                                });
                                
                                // Agregar capa de borde para los lotes existentes
                                this.plotMap.addLayer({
                                    'id': 'existing-plots-stroke',
                                    'type': 'line',
                                    'source': 'existing-plots',
                                    'layout': {},
                                    'paint': {
                                        'line-color': '#dc2626', // Rojo más oscuro
                                        'line-width': 2,
                                        'line-dasharray': [4, 2]
                                    }
                                });
                                
                                console.log('Lotes existentes agregados correctamente al modal');
                                
                                // Agregar marcadores para los lotes existentes si tienen coordenadas
                                filteredPlots.forEach((plot) => {
                                    if (plot.latitude && plot.longitude) {
                                        new mapboxgl.Marker({
                                            color: '#ef4444' // Rojo
                                        })
                                        .setLngLat([parseFloat(plot.longitude), parseFloat(plot.latitude)])
                                        .setPopup(
                                            new mapboxgl.Popup({ offset: 25 })
                                                .setHTML(`
                                                    <div style="padding: 10px;">
                                                        <h3 style="margin: 0 0 5px 0; font-weight: bold; color: #ef4444;">Lote: ${plot.name}</h3>
                                                        <p style="margin: 0; color: #666; font-size: 12px;">ID: ${plot.id}</p>
                                                    </div>
                                                `)
                                        )
                                        .addTo(this.plotMap);
                                    }
                                });
                                
                            } catch (e) {
                                console.error('Error agregando lotes existentes al modal:', e);
                            }
                        }
                    }
                    
                    // Agregar controles de navegación
                    this.plotMap.addControl(new mapboxgl.NavigationControl());

                    // Crear marcador si hay coordenadas
                    if (this.latitude && this.longitude) {
                        this.plotMarker = new mapboxgl.Marker({
                            draggable: true,
                            color: '#3b82f6'
                        })
                        .setLngLat(initialLocation)
                        .addTo(this.plotMap);

                        // Listener para cuando se arrastra el marcador
                        this.plotMarker.on('dragend', () => {
                            const lngLat = this.plotMarker.getLngLat();
                            this.updateCoordinates(lngLat);
                        });
                    }

                    // Agregar geocoder
                    if (typeof MapboxGeocoder !== 'undefined') {
                        this.geocoder = new MapboxGeocoder({
                            accessToken: mapboxgl.accessToken,
                            mapboxgl: mapboxgl,
                            marker: false,
                            placeholder: 'Buscar dirección...',
                        });

                        this.plotMap.addControl(this.geocoder, 'top-left');

                        this.geocoder.on('result', (e) => {
                            const lngLat = e.result.center;
                            this.placeMarker([lngLat[0], lngLat[1]]);
                            this.updateCoordinates({ lng: lngLat[0], lat: lngLat[1] });
                        });
                    }

                    // Listener para clics en el mapa
                    this.plotMap.on('click', (e) => {
                        if (this.draw && this.draw.getMode() === 'simple_select') {
                            this.placeMarker([e.lngLat.lng, e.lngLat.lat]);
                            this.updateCoordinates(e.lngLat);
                        }
                    });

                    // Eventos de Mapbox Draw
                    if (this.draw) {
                        this.plotMap.on('draw.create', () => this.updatePlotBoundary());
                        this.plotMap.on('draw.update', () => this.updatePlotBoundary());
                        this.plotMap.on('draw.delete', () => this.updatePlotBoundary());
                    }

                    // Botones de control
                    const drawBtn = document.getElementById('draw-polygon-modal');
                    const deleteBtn = document.getElementById('delete-polygon-modal');
                    const clearBtn = document.getElementById('clear-all-modal');

                    if (drawBtn) {
                        drawBtn.addEventListener('click', () => {
                            if (this.draw) {
                                this.draw.changeMode('draw_polygon');
                            }
                        });
                    }

                    if (deleteBtn) {
                        deleteBtn.addEventListener('click', () => {
                            if (this.draw) {
                                const features = this.draw.getAll();
                                if (features.features.length > 0) {
                                    this.draw.delete(features.features[0].id);
                                    this.updatePlotBoundary();
                                }
                            }
                        });
                    }

                    if (clearBtn) {
                        clearBtn.addEventListener('click', () => {
                            if (this.draw) {
                                this.draw.deleteAll();
                            }
                            if (this.plotMarker) {
                                this.plotMarker.remove();
                                this.plotMarker = null;
                            }
                            const boundaryInput = document.getElementById('boundaryModal');
                            const latInput = document.getElementById('latitudeModal');
                            const lngInput = document.getElementById('longitudeModal');
                            const areaDisplay = document.getElementById('calculatedAreaDisplayModal');
                            const areaInput = document.getElementById('areaModal');

                            if (boundaryInput) boundaryInput.value = '';
                            if (latInput) latInput.value = '';
                            if (lngInput) lngInput.value = '';
                            if (areaDisplay) areaDisplay.textContent = '0.0000 ha';
                            if (areaInput) areaInput.value = '0.00';
                            
                            this.boundary = '';
                            this.latitude = '';
                            this.longitude = '';
                            this.area = '0.00';
                        });
                    }
                });
            } catch (error) {
                console.error('Error inicializando mapa:', error);
            }
        },
        
        updatePlotBoundary() {
            if (!this.draw) return;
            
            const data = this.draw.getAll();
            const areaDisplay = document.getElementById('calculatedAreaDisplayModal');
            const areaInput = document.getElementById('areaModal');

            if (data.features.length > 0) {
                const polygon = data.features[0];
                const boundaryInput = document.getElementById('boundaryModal');
                if (boundaryInput) {
                    boundaryInput.value = JSON.stringify(polygon.geometry);
                    this.boundary = JSON.stringify(polygon.geometry);
                }
                
                // Calcular centro del polígono para actualizar lat/lng
                const coordinates = polygon.geometry.coordinates[0];
                let sumLat = 0, sumLng = 0;
                coordinates.forEach(coord => {
                    sumLng += coord[0];
                    sumLat += coord[1];
                });
                const centerLat = sumLat / coordinates.length;
                const centerLng = sumLng / coordinates.length;
                
                this.updateCoordinates({ lat: centerLat, lng: centerLng });
                if (this.plotMarker) {
                    this.plotMarker.setLngLat([centerLng, centerLat]);
                } else {
                    this.placeMarker([centerLng, centerLat]);
                }

                // Calcular y mostrar área
                const areaHa = this.calculatePolygonArea(polygon.geometry.coordinates[0]);
                if (areaDisplay) areaDisplay.textContent = areaHa.toFixed(4) + ' ha';
                if (areaInput) {
                    areaInput.value = areaHa.toFixed(2);
                    this.area = areaHa.toFixed(2);
                }

            } else {
                const boundaryInput = document.getElementById('boundaryModal');
                if (boundaryInput) {
                    boundaryInput.value = '';
                    this.boundary = '';
                }
                if (areaDisplay) areaDisplay.textContent = '0.0000 ha';
                if (areaInput) {
                    areaInput.value = '0.00';
                    this.area = '0.00';
                }
            }
        },
        
        calculatePolygonArea(coords) {
            if (coords.length < 3) return 0;

            let area = 0;
            const earthRadius = 6378137; // Radio de la Tierra en metros

            for (let i = 0; i < coords.length; i++) {
                const p1 = coords[i];
                const p2 = coords[(i + 1) % coords.length];

                const lat1Rad = p1[1] * Math.PI / 180;
                const lat2Rad = p2[1] * Math.PI / 180;

                const deltaLng = (p2[0] - p1[0]) * Math.PI / 180;

                area += (lat2Rad - lat1Rad) * (2 + Math.sin(lat1Rad) + Math.sin(lat2Rad)) * deltaLng;
            }

            area = Math.abs(area * earthRadius * earthRadius / 2);
            return area / 10000; // Convertir a hectáreas
        },
        
        placeMarker(lngLat) {
            if (this.plotMarker) {
                this.plotMarker.setLngLat(lngLat);
            } else {
                this.plotMarker = new mapboxgl.Marker({
                    draggable: true,
                    color: '#3b82f6'
                })
                .setLngLat(lngLat)
                .addTo(this.plotMap);

                this.plotMarker.on('dragend', () => {
                    const lngLat = this.plotMarker.getLngLat();
                    this.updateCoordinates(lngLat);
                });
            }
        },
        
        updateCoordinates(lngLat) {
            const latInput = document.getElementById('latitudeModal');
            const lngInput = document.getElementById('longitudeModal');
            if (latInput) {
                latInput.value = lngLat.lat.toFixed(8);
                this.latitude = lngLat.lat.toFixed(8);
            }
            if (lngInput) {
                lngInput.value = lngLat.lng.toFixed(8);
                this.longitude = lngLat.lng.toFixed(8);
            }
        },
        
        async submitForm() {
            // Confirmar antes de actualizar
            const confirmResult = await Swal.fire({
                title: '¿Actualizar lote?',
                text: '¿Estás seguro de que deseas guardar los cambios?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, actualizar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-lg bg-white',
                    confirmButton: 'px-4 py-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white border border-emerald-600 transition-colors',
                    cancelButton: 'px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-100 ml-2',
                },
            });
            
            if (!confirmResult.isConfirmed) {
                return;
            }
            
            this.errors = {};
            this.showError = false;
            this.showSuccess = false;
            
            console.log('Enviando formulario...', {
                plotId: this.plotId,
                name: this.name,
                latitude: this.latitude,
                longitude: this.longitude,
                boundary: this.boundary,
                area: this.area,
                status: this.status
            });
            
            const formData = new FormData();
            const token = document.querySelector('input[name="_token"]');
            if (!token) {
                console.error('Token CSRF no encontrado');
                this.errorMessage = 'Error: Token de seguridad no encontrado. Por favor, recarga la página.';
                this.showError = true;
                return;
            }
            
            formData.append('_token', token.value);
            formData.append('_method', 'PUT');
            formData.append('name', this.name || '');
            
            // Generar location automáticamente si hay coordenadas
            let location = '';
            if (this.latitude && this.longitude) {
                location = `Lat: ${parseFloat(this.latitude).toFixed(6)}, Lng: ${parseFloat(this.longitude).toFixed(6)}`;
            }
            formData.append('location', location);
            
            formData.append('latitude', this.latitude || '');
            formData.append('longitude', this.longitude || '');
            formData.append('boundary', this.boundary || '');
            formData.append('area', this.area || '0.00');
            formData.append('status', this.status || 'active');
            
            try {
                console.log('Enviando petición a:', `/admin/plots/${this.plotId}`);
                const response = await fetch(`/admin/plots/${this.plotId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                console.log('Respuesta recibida:', response.status, response.statusText);
                
                const contentType = response.headers.get('content-type');
                let data;
                
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    console.error('Respuesta no es JSON:', text);
                    this.errorMessage = 'Error: Respuesta inesperada del servidor. Por favor, recarga la página e inténtalo de nuevo.';
                    this.showError = true;
                    return;
                }
                
                console.log('Datos recibidos:', data);
                
                if (response.ok || response.status === 200) {
                    this.successMessage = data.message || 'Lote actualizado correctamente';
                    this.showSuccess = true;
                    this.closeModal();
                    
                    setTimeout(() => {
                        this.showSuccess = false;
                    }, 3000);
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    if (data.errors) {
                        this.errors = data.errors;
                        console.error('Errores de validación:', data.errors);
                    } else if (data.message) {
                        this.errorMessage = data.message;
                        this.showError = true;
                        setTimeout(() => {
                            this.showError = false;
                        }, 5000);
                    } else {
                        this.errorMessage = 'Error al actualizar el lote. Por favor, inténtelo de nuevo.';
                        this.showError = true;
                        setTimeout(() => {
                            this.showError = false;
                        }, 5000);
                    }
                }
            } catch (error) {
                console.error('Error en la petición:', error);
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
@endpush
@endsection


