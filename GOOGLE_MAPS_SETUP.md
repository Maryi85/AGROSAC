# Configuración de Google Maps

## Requisitos

Para usar la funcionalidad de Google Maps en el sistema, necesitas obtener una API Key de Google Maps JavaScript API.

## Pasos para Configurar

### 1. Obtener API Key de Google Maps

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita la **Google Maps JavaScript API**:
   - Ve a "APIs & Services" > "Library"
   - Busca "Maps JavaScript API"
   - Haz clic en "Enable"
4. Crea credenciales:
   - Ve a "APIs & Services" > "Credentials"
   - Haz clic en "Create Credentials" > "API Key"
   - Copia la API Key generada

### 2. Configurar en el Proyecto

1. Abre tu archivo `.env` en la raíz del proyecto
2. Agrega la siguiente línea:
   ```env
   GOOGLE_MAPS_API_KEY=tu_api_key_aqui
   ```
3. Guarda el archivo
4. Ejecuta:
   ```bash
   php artisan config:clear
   ```

### 3. Restricciones de la API Key (Recomendado)

Para mayor seguridad, configura restricciones en tu API Key:

1. En Google Cloud Console, ve a "APIs & Services" > "Credentials"
2. Haz clic en tu API Key
3. En "Application restrictions", selecciona "HTTP referrers (web sites)"
4. Agrega los dominios permitidos:
   - `http://localhost:8000/*` (para desarrollo)
   - `http://127.0.0.1:8000/*` (para desarrollo)
   - Tu dominio de producción (ej: `https://tudominio.com/*`)
5. En "API restrictions", selecciona "Restrict key"
6. Selecciona solo "Maps JavaScript API"
7. Guarda los cambios

## Funcionalidades Implementadas

### 1. Dashboard del Administrador
- Muestra un mapa interactivo con la ubicación de la finca
- El mapa se muestra solo si se ha configurado la ubicación
- Incluye un botón para configurar la ubicación

### 2. Configuración de la Finca
- Ruta: `/admin/farm-settings/edit`
- Permite configurar:
  - Nombre de la finca
  - Dirección
  - Latitud y Longitud (seleccionables desde el mapa)

### 3. Crear/Editar Lote
- Mapa interactivo para seleccionar la ubicación del lote
- Funcionalidades:
  - Clic en el mapa para seleccionar ubicación
  - Arrastrar el marcador para ajustar posición
  - Búsqueda por dirección (geocodificación)
  - Geocodificación inversa (obtener dirección desde coordenadas)
  - Campos de latitud y longitud se llenan automáticamente

## Notas Importantes

- La API Key es necesaria para que los mapas funcionen
- Si no se configura la API Key, se mostrará un mensaje de advertencia
- Los mapas son responsivos y funcionan en dispositivos móviles
- Las coordenadas se guardan con 8 decimales de precisión





