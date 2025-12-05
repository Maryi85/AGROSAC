@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Configuración de la Finca</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-6 max-w-4xl">
    <form method="POST" action="{{ route('admin.farm-settings.update') }}" class="space-y-4" id="farm-settings-form">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Nombre de la Finca <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $farmSettings->name) }}" class="w-full border border-emerald-200 rounded px-3 py-2" required />
            @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Dirección</label>
            <input type="text" name="address" id="address" value="{{ old('address', $farmSettings->address) }}" class="w-full border border-emerald-200 rounded px-3 py-2" />
            @error('address')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        
        <!-- Mapa para seleccionar ubicación de la finca -->
        <div>
            <label class="block text-sm mb-1 text-emerald-800">Ubicación y Delimitación de la Finca</label>
            <p class="text-xs text-gray-600 mb-2">
                <strong>Instrucciones:</strong> 
                1. Haz clic en el mapa para seleccionar el punto central de la finca (marcador). 
                2. Usa las herramientas de dibujo para delimitar el área de la finca (polígono).
            </p>
            
            <!-- Controles de dibujo -->
            <div class="mb-2 flex gap-2 flex-wrap">
                <button type="button" id="draw-polygon" class="px-3 py-1 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 border border-emerald-200 rounded text-sm">
                    <i data-lucide="square" class="w-4 h-4 inline"></i> Dibujar Área
                </button>
                <button type="button" id="delete-polygon" class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 border border-red-200 rounded text-sm">
                    <i data-lucide="trash-2" class="w-4 h-4 inline"></i> Eliminar Área
                </button>
                <button type="button" id="clear-all" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-200 rounded text-sm">
                    <i data-lucide="x" class="w-4 h-4 inline"></i> Limpiar Todo
                </button>
            </div>
            
            <div id="farmMap" style="width: 100%; height: 500px; border: 2px solid #10b981; border-radius: 8px; overflow: hidden;"></div>
            <div class="mt-2 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs mb-1 text-emerald-800">Latitud (Centro)</label>
                    <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $farmSettings->latitude) }}" readonly class="w-full border border-emerald-200 rounded px-3 py-2 bg-gray-50" />
                    @error('latitude')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs mb-1 text-emerald-800">Longitud (Centro)</label>
                    <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $farmSettings->longitude) }}" readonly class="w-full border border-emerald-200 rounded px-3 py-2 bg-gray-50" />
                    @error('longitude')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <input type="hidden" name="boundary" id="boundary" value="{{ old('boundary', $farmSettings->boundary ? json_encode($farmSettings->boundary) : '') }}" />
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.index') }}" class="px-3 py-2 border rounded inline-flex items-center gap-2"><i data-lucide="arrow-left" class="w-4 h-4"></i><span>Volver</span></a>
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
    mapboxgl.accessToken = '{{ config('services.mapbox.token') }}';
    
    let farmMap;
    let farmMarker;
    let geocoder;
    let draw;

    const farmLat = {{ $farmSettings->latitude ?? 'null' }};
    const farmLng = {{ $farmSettings->longitude ?? 'null' }};
    const oldLat = {{ old('latitude', 'null') }};
    const oldLng = {{ old('longitude', 'null') }};
    const existingBoundary = @json($farmSettings->boundary ?? null);
    
    const defaultLocation = [-77.0428, -12.0464];
    const initialLocation = (oldLat && oldLng) ? [parseFloat(oldLng), parseFloat(oldLat)] :
                           (farmLat && farmLng) ? [parseFloat(farmLng), parseFloat(farmLat)] :
                           defaultLocation;

    // Inicializar mapa
    farmMap = new mapboxgl.Map({
        container: 'farmMap',
        style: 'mapbox://styles/mapbox/streets-v12',
        center: initialLocation,
        zoom: (farmLat && farmLng) ? 15 : 12
    });

    // Agregar controles de navegación
    farmMap.addControl(new mapboxgl.NavigationControl());

    // Inicializar Mapbox Draw
    draw = new MapboxDraw({
        displayControlsDefault: false,
        controls: {
            polygon: true,
            trash: true
        },
        defaultMode: 'simple_select'
    });

    farmMap.addControl(draw);

    // Cargar polígono existente si existe
    if (existingBoundary && existingBoundary.type === 'Polygon') {
        const feature = {
            type: 'Feature',
            geometry: existingBoundary
        };
        draw.add(feature);
        
        // Ajustar el mapa para mostrar el polígono
        const coordinates = existingBoundary.coordinates[0];
        const bounds = coordinates.reduce((bounds, coord) => {
            return bounds.extend(coord);
        }, new mapboxgl.LngLatBounds(coordinates[0], coordinates[0]));
        
        farmMap.fitBounds(bounds, {
            padding: 50
        });
    }

    // Crear marcador si hay coordenadas
    if (oldLat && oldLng || (farmLat && farmLng)) {
        farmMarker = new mapboxgl.Marker({
            draggable: true,
            color: '#10b981'
        })
        .setLngLat(initialLocation)
        .addTo(farmMap);

        document.getElementById('latitude').value = (oldLat && oldLng) ? oldLat : (farmLat ? farmLat : '');
        document.getElementById('longitude').value = (oldLat && oldLng) ? oldLng : (farmLng ? farmLng : '');

        // Listener para cuando se arrastra el marcador
        farmMarker.on('dragend', () => {
            const lngLat = farmMarker.getLngLat();
            updateCoordinates(lngLat);
            reverseGeocode(lngLat);
        });
    }

    // Agregar geocoder (búsqueda de direcciones)
    geocoder = new MapboxGeocoder({
        accessToken: mapboxgl.accessToken,
        mapboxgl: mapboxgl,
        marker: false,
        placeholder: 'Buscar dirección...',
    });

    farmMap.addControl(geocoder, 'top-left');

    // Listener para cuando se selecciona una dirección del geocoder
    geocoder.on('result', (e) => {
        const lngLat = e.result.center;
        placeMarker([lngLat[0], lngLat[1]]);
        updateCoordinates({ lng: lngLat[0], lat: lngLat[1] });
        const addressInput = document.getElementById('address');
        if (addressInput) {
            addressInput.value = e.result.place_name;
        }
    });

    // Listener para clics en el mapa (solo si no está en modo draw)
    farmMap.on('click', (e) => {
        if (draw.getMode() === 'simple_select') {
            placeMarker([e.lngLat.lng, e.lngLat.lat]);
            updateCoordinates(e.lngLat);
            reverseGeocode(e.lngLat);
        }
    });

    // Eventos de Mapbox Draw
    farmMap.on('draw.create', updateBoundary);
    farmMap.on('draw.update', updateBoundary);
    farmMap.on('draw.delete', updateBoundary);

    function updateBoundary() {
        const data = draw.getAll();
        if (data.features.length > 0) {
            const polygon = data.features[0];
            document.getElementById('boundary').value = JSON.stringify(polygon.geometry);
        } else {
            document.getElementById('boundary').value = '';
        }
    }

    // Botones de control
    document.getElementById('draw-polygon').addEventListener('click', () => {
        draw.changeMode('draw_polygon');
    });

    document.getElementById('delete-polygon').addEventListener('click', () => {
        const features = draw.getAll();
        if (features.features.length > 0) {
            draw.delete(features.features[0].id);
            updateBoundary();
        }
    });

    document.getElementById('clear-all').addEventListener('click', () => {
        draw.deleteAll();
        if (farmMarker) {
            farmMarker.remove();
            farmMarker = null;
        }
        document.getElementById('boundary').value = '';
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
    });

    function placeMarker(lngLat) {
        if (farmMarker) {
            farmMarker.setLngLat(lngLat);
        } else {
            farmMarker = new mapboxgl.Marker({
                draggable: true,
                color: '#10b981'
            })
            .setLngLat(lngLat)
            .addTo(farmMap);

            farmMarker.on('dragend', () => {
                const lngLat = farmMarker.getLngLat();
                updateCoordinates(lngLat);
                reverseGeocode(lngLat);
            });
        }
    }

    function updateCoordinates(lngLat) {
        document.getElementById('latitude').value = lngLat.lat.toFixed(8);
        document.getElementById('longitude').value = lngLat.lng.toFixed(8);
    }

    function reverseGeocode(lngLat) {
        fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${lngLat.lng},${lngLat.lat}.json?access_token=${mapboxgl.accessToken}`)
            .then(response => response.json())
            .then(data => {
                if (data.features && data.features.length > 0) {
                    const addressInput = document.getElementById('address');
                    if (addressInput) {
                        addressInput.value = data.features[0].place_name;
                    }
                }
            })
            .catch(error => console.error('Error en geocodificación inversa:', error));
    }

    // Búsqueda por dirección en el campo de texto
    const addressInput = document.getElementById('address');
    let addressTimeout;
    if (addressInput) {
        addressInput.addEventListener('input', () => {
            clearTimeout(addressTimeout);
            addressTimeout = setTimeout(() => {
                if (addressInput.value.length > 3) {
                    geocodeAddress(addressInput.value);
                }
            }, 500);
        });
    }

    function geocodeAddress(address) {
        fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(address)}.json?access_token=${mapboxgl.accessToken}&country=pe`)
            .then(response => response.json())
            .then(data => {
                if (data.features && data.features.length > 0) {
                    const lngLat = data.features[0].center;
                    farmMap.flyTo({
                        center: lngLat,
                        zoom: 15
                    });
                    placeMarker(lngLat);
                    updateCoordinates({ lng: lngLat[0], lat: lngLat[1] });
                }
            })
            .catch(error => console.error('Error en geocodificación:', error));
    }
</script>
@endpush
