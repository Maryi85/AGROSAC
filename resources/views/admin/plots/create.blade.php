@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Nuevo Lote</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-6 max-w-4xl">
    <form method="POST" action="{{ route('admin.plots.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Nombre <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-emerald-200 rounded px-3 py-2" required />
            @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <!-- Mapa para seleccionar ubicación y delimitar lote -->
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Ubicación y Delimitación del Lote</label>
            <p class="text-xs text-gray-600 mb-2">
                <strong>Instrucciones:</strong> 
                1. Haz clic en el mapa para seleccionar el punto central del lote (marcador). 
                2. Usa "Dibujar Área" para delimitar el área del lote dentro de los límites de la finca.
                @if($farmSettings->boundary)
                    <span class="text-emerald-600 font-semibold">El área verde muestra los límites de la finca.</span>
                @endif
                @if(isset($existingPlots) && $existingPlots->count() > 0)
                    <span class="text-red-600 font-semibold">Las áreas rojas muestran los lotes ya creados.</span>
                @endif
            </p>
            
            <!-- Controles de dibujo -->
            <div class="mb-2 flex gap-2 flex-wrap">
                <button type="button" id="draw-polygon" class="px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 border border-blue-200 rounded text-sm">
                    <i data-lucide="square" class="w-4 h-4 inline"></i> Dibujar Área del Lote
                </button>
                <button type="button" id="delete-polygon" class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded text-sm">
                    <i data-lucide="trash-2" class="w-4 h-4 inline"></i> Eliminar Área
                </button>
                <button type="button" id="clear-all" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded text-sm">
                    <i data-lucide="x" class="w-4 h-4 inline"></i> Limpiar Todo
                </button>
            </div>
            
            <div id="plotMap" style="width: 100%; height: 500px; border: 2px solid #10b981; border-radius: 8px; overflow: hidden; position: relative; background-color: #f0f0f0;"></div>
            <div class="mt-2 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs mb-1 text-emerald-800">Latitud (Centro)</label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}" readonly class="w-full border border-emerald-200 rounded px-3 py-2 bg-gray-50" />
                    @error('latitude')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            <div>
                    <label class="block text-xs mb-1 text-emerald-800">Longitud (Centro)</label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}" readonly class="w-full border border-emerald-200 rounded px-3 py-2 bg-gray-50" />
                    @error('longitude')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="mt-2">
                <label class="block text-xs mb-1 text-emerald-800">Área Calculada del Lote</label>
                <div id="calculated-area" class="w-full border border-emerald-200 rounded px-3 py-2 bg-emerald-50 text-emerald-700 font-semibold">
                    <span id="area-display">0.00 ha</span>
                    <span class="text-xs text-gray-500 ml-2">(Se calculará automáticamente al delimitar el área)</span>
                </div>
            </div>
            <input type="hidden" name="boundary" id="boundary" value="{{ old('boundary', '') }}" />
        </div>
        
            <div>
                <label class="block text-sm mb-1 text-emerald-800">Área (ha) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="1" name="area" value="{{ old('area') }}" class="w-full border border-emerald-200 rounded px-3 py-2" required />
                @error('area')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Estado <span class="text-red-500">*</span></label>
            <select name="status" class="w-full border border-emerald-200 rounded px-3 py-2" required>
                <option value="active" {{ old('status', 'active')==='active' ? 'selected' : '' }}>Activo</option>
                <option value="inactive" {{ old('status')==='inactive' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('status')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.plots.index') }}" class="px-3 py-2 border rounded inline-flex items-center gap-2"><i data-lucide="arrow-left" class="w-4 h-4"></i><span>Cancelar</span></a>
            <button class="px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded inline-flex items-center gap-2 transition-colors"><i data-lucide="save" class="w-4 h-4"></i><span>Guardar</span></button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<link href="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js"></script>
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css" type="text/css">
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.js"></script>
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.css" type="text/css">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Esperar a que las librerías de Mapbox estén cargadas
        if (typeof mapboxgl === 'undefined') {
            console.error('Mapbox GL JS no está cargado');
            const mapContainer = document.getElementById('plotMap');
            if (mapContainer) {
                mapContainer.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded"><p class="text-red-800">Error: No se pudo cargar la librería de Mapbox. Verifica tu conexión a internet.</p></div>';
            }
            return;
        }

        const mapboxToken = '{{ config('services.mapbox.token') }}';
        const mapContainer = document.getElementById('plotMap');
        
        if (!mapboxToken) {
            console.error('Mapbox token no configurado');
            if (mapContainer) {
                mapContainer.innerHTML = '<div class="p-4 bg-yellow-50 border border-yellow-200 rounded"><p class="text-yellow-800">Error: No se ha configurado el token de Mapbox. Verifica tu archivo .env</p></div>';
            }
            return;
        }

        if (!mapContainer) {
            console.error('Contenedor del mapa no encontrado');
            return;
        }

        // Verificar que el contenedor tenga dimensiones
        const containerRect = mapContainer.getBoundingClientRect();
        if (containerRect.width === 0 || containerRect.height === 0) {
            console.warn('El contenedor del mapa no tiene dimensiones visibles. Esperando...');
            // Esperar un poco y reintentar
            setTimeout(() => {
                const retryRect = mapContainer.getBoundingClientRect();
                if (retryRect.width === 0 || retryRect.height === 0) {
                    console.error('El contenedor del mapa sigue sin dimensiones');
                    mapContainer.innerHTML = '<div class="p-4 bg-yellow-50 border border-yellow-200 rounded"><p class="text-yellow-800">El contenedor del mapa no es visible. Por favor, recarga la página.</p></div>';
                    return;
                }
            }, 500);
        }

        mapboxgl.accessToken = mapboxToken;
        
        let plotMap;
        let plotMarker;
        let geocoder;
        let draw;

        // Coordenadas por defecto (centro de Lima, Perú)
        const defaultLocation = [-77.0428, -12.0464];
        
        // Si hay valores antiguos, usarlos
        const oldLat = @json(old('latitude'));
        const oldLng = @json(old('longitude'));
        const oldBoundary = @json(old('boundary'));
        const initialLocation = (oldLat !== null && oldLng !== null) ? [parseFloat(oldLng), parseFloat(oldLat)] : defaultLocation;
        
        // Polígono de la finca (si existe)
        const farmBoundary = @json($farmSettings->boundary ?? null);
        const farmLat = @json($farmSettings->latitude ?? null);
        const farmLng = @json($farmSettings->longitude ?? null);
        
        // Lotes existentes para mostrarlos en el mapa
        const existingPlots = @json($existingPlots ?? []);

        // Si hay coordenadas de la finca, usarlas como ubicación inicial si no hay coordenadas antiguas
        let finalLocation = initialLocation;
        let finalZoom = (oldLat !== null && oldLng !== null) ? 15 : 12;
        
        if (oldLat === null && oldLng === null && farmLat !== null && farmLng !== null) {
            finalLocation = [parseFloat(farmLng), parseFloat(farmLat)];
            finalZoom = 15;
        }

        // Inicializar mapa
        try {
            console.log('Inicializando mapa...');
            console.log('Token:', mapboxToken ? 'Configurado' : 'No configurado');
            console.log('Contenedor:', mapContainer ? 'Encontrado' : 'No encontrado');
            console.log('Ubicación inicial:', finalLocation);
            console.log('Farm boundary:', farmBoundary);
            
            plotMap = new mapboxgl.Map({
                container: 'plotMap',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: finalLocation,
                zoom: finalZoom
            });

            plotMap.on('error', (e) => {
                console.error('Error en el mapa:', e);
                if (mapContainer) {
                    mapContainer.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded"><p class="text-red-800">Error al cargar el mapa: ' + (e.error ? e.error.message : 'Error desconocido') + '</p></div>';
                }
            });

            // Forzar resize del mapa después de un pequeño delay para asegurar que se renderice
            setTimeout(() => {
                if (plotMap) {
                    plotMap.resize();
                    console.log('Mapa redimensionado');
                }
            }, 100);

            console.log('Mapa inicializado correctamente');

            // Inicializar Mapbox Draw
            if (typeof MapboxDraw !== 'undefined') {
                draw = new MapboxDraw({
                    displayControlsDefault: false,
                    controls: {
                        polygon: true,
                        trash: true
                    },
                    defaultMode: 'simple_select'
                });

                plotMap.addControl(draw);
            } else {
                console.warn('Mapbox Draw no está disponible');
            }

            // Agregar controles de navegación
            plotMap.addControl(new mapboxgl.NavigationControl());

            // Agregar marcador de la finca si existe (antes del evento load)
            if (farmLat !== null && farmLng !== null) {
                plotMap.on('load', () => {
                    const farmMarker = new mapboxgl.Marker({
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
                    .addTo(plotMap);
                });
            }

            // Agregar el polígono de la finca si existe
            plotMap.on('load', () => {
                console.log('Evento load del mapa disparado');
                
                if (farmBoundary && farmBoundary.type === 'Polygon') {
                    console.log('Agregando polígono de la finca');
                    try {
                        // Verificar si el source ya existe
                        if (plotMap.getSource('farm-boundary')) {
                            plotMap.removeLayer('farm-boundary-fill');
                            plotMap.removeLayer('farm-boundary-stroke');
                            plotMap.removeSource('farm-boundary');
                        }

                        plotMap.addSource('farm-boundary', {
                            'type': 'geojson',
                            'data': {
                                'type': 'Feature',
                                'geometry': farmBoundary
                            }
                        });

                        plotMap.addLayer({
                            'id': 'farm-boundary-fill',
                            'type': 'fill',
                            'source': 'farm-boundary',
                            'layout': {},
                            'paint': {
                                'fill-color': '#10b981',
                                'fill-opacity': 0.2
                            }
                        });

                        plotMap.addLayer({
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

                        // Ajustar el mapa para mostrar el polígono si no hay coordenadas específicas
                        if (oldLat === null && oldLng === null) {
                            const coordinates = farmBoundary.coordinates[0];
                            const bounds = coordinates.reduce((bounds, coord) => {
                                return bounds.extend(coord);
                            }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
                            
                            plotMap.fitBounds(bounds, {
                                padding: 50
                            });
                        }
                        console.log('Polígono de la finca agregado correctamente');
                    } catch (e) {
                        console.error('Error agregando polígono de la finca:', e);
                    }
                } else {
                    console.log('No hay polígono de la finca para mostrar. farmBoundary:', farmBoundary);
                }

                // Cargar polígono del lote si existe
                if (oldBoundary !== null && oldBoundary !== undefined) {
                    try {
                        const boundaryData = typeof oldBoundary === 'string' ? JSON.parse(oldBoundary) : oldBoundary;
                        if (boundaryData !== null && boundaryData.type === 'Polygon') {
                            const feature = {
                                type: 'Feature',
                                geometry: boundaryData
                            };
                            draw.add(feature);
                        }
                    } catch (e) {
                        console.error('Error parsing boundary:', e);
                    }
                }
                
                // Agregar lotes existentes al mapa
                if (existingPlots && existingPlots.length > 0) {
                    console.log('Agregando lotes existentes:', existingPlots.length);
                    
                    // Crear una colección de features para todos los lotes existentes
                    const existingPlotsFeatures = existingPlots
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
                            plotMap.addSource('existing-plots', {
                                'type': 'geojson',
                                'data': {
                                    'type': 'FeatureCollection',
                                    'features': existingPlotsFeatures
                                }
                            });
                            
                            // Agregar capa de relleno para los lotes existentes
                            plotMap.addLayer({
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
                            plotMap.addLayer({
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
                            
                            console.log('Lotes existentes agregados correctamente');
                            
                            // Agregar marcadores para los lotes existentes si tienen coordenadas
                            existingPlots.forEach((plot) => {
                                if (plot.latitude && plot.longitude) {
                                    const marker = new mapboxgl.Marker({
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
                                    .addTo(plotMap);
                                }
                            });
                            
                            // Agregar interacción para mostrar información al hacer hover
                            plotMap.on('mouseenter', 'existing-plots-fill', (e) => {
                                plotMap.getCanvas().style.cursor = 'pointer';
                                const plotName = e.features[0].properties.name || 'Lote sin nombre';
                                const plotId = e.features[0].properties.id || e.features[0].properties.plotId;
                                
                                // Mostrar información en un tooltip o en la consola
                                console.log(`Hover sobre lote: ${plotName} (ID: ${plotId})`);
                            });
                            
                            plotMap.on('mouseleave', 'existing-plots-fill', () => {
                                plotMap.getCanvas().style.cursor = '';
                            });
                            
                            // Agregar click para mostrar popup con información del lote
                            plotMap.on('click', 'existing-plots-fill', (e) => {
                                const plotName = e.features[0].properties.name || 'Lote sin nombre';
                                const plotId = e.features[0].properties.id || e.features[0].properties.plotId;
                                
                                new mapboxgl.Popup()
                                    .setLngLat(e.lngLat)
                                    .setHTML(`
                                        <div style="padding: 10px;">
                                            <h3 style="margin: 0 0 5px 0; font-weight: bold; color: #ef4444;">Lote Existente</h3>
                                            <p style="margin: 0; color: #666;"><strong>Nombre:</strong> ${plotName}</p>
                                            <p style="margin: 0; color: #666; font-size: 12px;"><strong>ID:</strong> ${plotId}</p>
                                        </div>
                                    `)
                                    .addTo(plotMap);
                            });
                            
                        } catch (e) {
                            console.error('Error agregando lotes existentes:', e);
                        }
                    } else {
                        console.log('No hay lotes existentes con boundary válido para mostrar');
                    }
                } else {
                    console.log('No hay lotes existentes para mostrar');
                }
            });


            // Crear marcador si hay coordenadas antiguas
            if (oldLat !== null && oldLng !== null) {
                plotMarker = new mapboxgl.Marker({
                    draggable: true,
                    color: '#3b82f6'
                })
                .setLngLat(initialLocation)
                .addTo(plotMap);

                document.getElementById('latitude').value = oldLat;
                document.getElementById('longitude').value = oldLng;

                // Listener para cuando se arrastra el marcador
                plotMarker.on('dragend', () => {
                    const lngLat = plotMarker.getLngLat();
                    updateCoordinates(lngLat);
                    reverseGeocode(lngLat);
                });
            }

            // Agregar geocoder (búsqueda de direcciones)
            if (typeof MapboxGeocoder !== 'undefined') {
                geocoder = new MapboxGeocoder({
                    accessToken: mapboxgl.accessToken,
                    mapboxgl: mapboxgl,
                    marker: false,
                    placeholder: 'Buscar dirección...',
                });

                plotMap.addControl(geocoder, 'top-left');
            } else {
                console.warn('Mapbox Geocoder no está disponible');
            }

            // Listener para cuando se selecciona una dirección del geocoder
            geocoder.on('result', (e) => {
                const lngLat = e.result.center;
                placeMarker([lngLat[0], lngLat[1]]);
                updateCoordinates({ lng: lngLat[0], lat: lngLat[1] });
            });

            // Listener para clics en el mapa (solo si no está en modo draw)
            plotMap.on('click', (e) => {
                if (draw.getMode() === 'simple_select') {
                    placeMarker([e.lngLat.lng, e.lngLat.lat]);
                    updateCoordinates(e.lngLat);
                    reverseGeocode(e.lngLat);
                }
            });

            // Eventos de Mapbox Draw
            plotMap.on('draw.create', updatePlotBoundary);
            plotMap.on('draw.update', updatePlotBoundary);
            plotMap.on('draw.delete', updatePlotBoundary);

            // Función para calcular el área de un polígono en hectáreas usando la fórmula de Shoelace
            function calculatePolygonArea(coordinates) {
                if (!coordinates || coordinates.length < 3) return 0;
                
                let area = 0;
                const n = coordinates.length;
                
                for (let i = 0; i < n; i++) {
                    const j = (i + 1) % n;
                    area += coordinates[i][0] * coordinates[j][1];
                    area -= coordinates[j][0] * coordinates[i][1];
                }
                
                // El área está en grados cuadrados, necesitamos convertir a metros cuadrados
                // y luego a hectáreas
                area = Math.abs(area) / 2;
                
                // Aproximación: 1 grado de latitud ≈ 111,320 metros
                // 1 grado de longitud ≈ 111,320 * cos(latitud) metros
                // Usamos el centro del polígono para una mejor aproximación
                const centerLat = coordinates.reduce((sum, coord) => sum + coord[1], 0) / coordinates.length;
                const latMeters = 111320; // metros por grado de latitud
                const lngMeters = 111320 * Math.cos(centerLat * Math.PI / 180); // metros por grado de longitud
                
                // Convertir a metros cuadrados
                const areaM2 = area * latMeters * lngMeters;
                
                // Convertir a hectáreas (1 hectárea = 10,000 m²)
                const areaHa = areaM2 / 10000;
                
                return areaHa;
            }

            function updatePlotBoundary() {
                const data = draw.getAll();
                if (data.features.length > 0) {
                    const polygon = data.features[0];
                    const boundaryInput = document.getElementById('boundary');
                    if (boundaryInput) {
                        boundaryInput.value = JSON.stringify(polygon.geometry);
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
                    
                    updateCoordinates({ lat: centerLat, lng: centerLng });
                    if (plotMarker) {
                        plotMarker.setLngLat([centerLng, centerLat]);
                    } else {
                        placeMarker([centerLng, centerLat]);
                    }
                    
                    // Calcular y mostrar el área
                    const areaHa = calculatePolygonArea(coordinates);
                    const areaDisplay = document.getElementById('area-display');
                    if (areaDisplay) {
                        areaDisplay.textContent = areaHa.toFixed(4) + ' ha';
                    }
                    
                    // Actualizar el campo de área del formulario si existe
                    const areaInput = document.querySelector('input[name="area"]');
                    if (areaInput) {
                        areaInput.value = areaHa.toFixed(2);
                    }
                } else {
                    const boundaryInput = document.getElementById('boundary');
                    if (boundaryInput) {
                        boundaryInput.value = '';
                    }
                    
                    // Limpiar el área mostrada
                    const areaDisplay = document.getElementById('area-display');
                    if (areaDisplay) {
                        areaDisplay.textContent = '0.00 ha';
                    }
                    
                    // Limpiar el campo de área del formulario
                    const areaInput = document.querySelector('input[name="area"]');
                    if (areaInput) {
                        areaInput.value = '';
                    }
                }
            }

            // Botones de control
            const drawBtn = document.getElementById('draw-polygon');
            const deleteBtn = document.getElementById('delete-polygon');
            const clearBtn = document.getElementById('clear-all');

            if (drawBtn) {
                drawBtn.addEventListener('click', () => {
                    draw.changeMode('draw_polygon');
                });
            }

            if (deleteBtn) {
                deleteBtn.addEventListener('click', () => {
                    const features = draw.getAll();
                    if (features.features.length > 0) {
                        draw.delete(features.features[0].id);
                        updatePlotBoundary();
                    }
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    draw.deleteAll();
                    if (plotMarker) {
                        plotMarker.remove();
                        plotMarker = null;
                    }
                    const boundaryInput = document.getElementById('boundary');
                    const latInput = document.getElementById('latitude');
                    const lngInput = document.getElementById('longitude');
                    const areaDisplay = document.getElementById('area-display');
                    const areaInput = document.querySelector('input[name="area"]');
                    
                    if (boundaryInput) boundaryInput.value = '';
                    if (latInput) latInput.value = '';
                    if (lngInput) lngInput.value = '';
                    if (areaDisplay) areaDisplay.textContent = '0.00 ha';
                    if (areaInput) areaInput.value = '';
                });
            }

            function placeMarker(lngLat) {
                if (plotMarker) {
                    plotMarker.setLngLat(lngLat);
                } else {
                    plotMarker = new mapboxgl.Marker({
                        draggable: true,
                        color: '#3b82f6'
                    })
                    .setLngLat(lngLat)
                    .addTo(plotMap);

                    plotMarker.on('dragend', () => {
                        const lngLat = plotMarker.getLngLat();
                        updateCoordinates(lngLat);
                        reverseGeocode(lngLat);
                    });
                }
            }

            function updateCoordinates(lngLat) {
                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');
                if (latInput) latInput.value = lngLat.lat.toFixed(8);
                if (lngInput) lngInput.value = lngLat.lng.toFixed(8);
            }

            function reverseGeocode(lngLat) {
                // Función para obtener la dirección desde coordenadas (opcional, no se muestra en el formulario)
                const url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' + lngLat.lng + ',' + lngLat.lat + '.json?access_token=' + mapboxgl.accessToken;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        // La dirección se puede usar para otros propósitos si es necesario
                        console.log('Dirección encontrada:', data.features && data.features.length > 0 ? data.features[0].place_name : 'No encontrada');
                    })
                    .catch(error => console.error('Error en geocodificación inversa:', error));
            }

            function geocodeAddress(address) {
                const url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' + encodeURIComponent(address) + '.json?access_token=' + mapboxgl.accessToken + '&country=pe';
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.features && data.features.length > 0) {
                            const lngLat = data.features[0].center;
                            plotMap.flyTo({
                                center: lngLat,
                                zoom: 15
                            });
                            placeMarker(lngLat);
                            updateCoordinates({ lng: lngLat[0], lat: lngLat[1] });
                        }
                    })
                    .catch(error => console.error('Error en geocodificación:', error));
            }

        } catch (error) {
            console.error('Error inicializando mapa:', error);
            const mapContainer = document.getElementById('plotMap');
            if (mapContainer) {
                mapContainer.innerHTML = '<div class="p-4 bg-red-50 border border-red-200 rounded"><p class="text-red-800">Error al cargar el mapa. Por favor, recarga la página.</p></div>';
            }
        }
    });
</script>
@endpush
