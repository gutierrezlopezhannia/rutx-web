<?php

use function Livewire\Volt\state;
use function Livewire\Volt\layout;

layout('layouts.app');

state([
    // Estructura mock basada en la tabla DIRS_CLIENTES, pero incluyendo lat/lng para que el mapa funcione ahora mismo sin Geocoding.
    // Detalle tecnico: para que el mapa funcione se necesitan las coordenadas (lat, lng) de los clientes.
    // Los datos se obtienen de la tabla DIRS_CLIENTES.
    // La tabla DIRS_CLIENTES tiene las siguientes columnas:
    // cliente_id, clave, nombre, tipo, calle, num_exterior, colonia, poblacion, codigo_postal
    // En todo caso, resolver el problema de la falta de estos campos en la BD o usar otro metodo, por lo pronto funciona con estos datos.
    'clientes' => [
        ['cliente_id' => 1001, 'clave' => 'CLI-001', 'nombre' => 'Abarrotes La Esperanza', 'tipo' => 'A', 'calle' => 'Av. Central', 'num_exterior' => '123', 'colonia' => 'Centro', 'poblacion' => 'Tuxtla Gutiérrez', 'codigo_postal' => '29000', 'lat' => 16.7385, 'lng' => -92.6385],
        ['cliente_id' => 1002, 'clave' => 'CLI-002', 'nombre' => 'Miscelánea El Sol', 'tipo' => 'A', 'calle' => 'Calle 5 de Mayo', 'num_exterior' => '45', 'colonia' => 'Barrio de Guadalupe', 'poblacion' => 'San Cristóbal de las Casas', 'codigo_postal' => '29200', 'lat' => 16.7360, 'lng' => -92.6360],
        ['cliente_id' => 1003, 'clave' => 'CLI-003', 'nombre' => 'Tienda Don Pepe', 'tipo' => 'B', 'calle' => 'Blvd. Belisario Domínguez', 'num_exterior' => '890', 'colonia' => 'San José', 'poblacion' => 'Tuxtla Gutiérrez', 'codigo_postal' => '29040', 'lat' => 16.7375, 'lng' => -92.6365],
        ['cliente_id' => 1004, 'clave' => 'CLI-004', 'nombre' => 'Kiosco Central', 'tipo' => 'C', 'calle' => '1ra Sur', 'num_exterior' => 'S/N', 'colonia' => 'Centro', 'poblacion' => 'Chiapa de Corzo', 'codigo_postal' => '29160', 'lat' => 16.7390, 'lng' => -92.6350],
    ],
]);

?>

{{--
    Los datos se pasan como atributos data-* en el div raíz para evitar el uso
    de @js() dentro de <script>, lo cual confunde al compilador de caché de Volt.
--}}
<div
    x-data="{}"
    x-init="$nextTick(() => window.RutxMap.init())"
    data-clientes="{{ json_encode($clientes) }}"
    data-gmaps-key="{{ env('GOOGLE_MAPS_API_KEY') }}"
    id="rutx-root"
    class="font-sans flex flex-col h-[calc(100vh-130px)]"
>
    <!-- Header -->
    <div class="flex justify-between items-center mb-4 shrink-0">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Mapa de Clientes</h2>
            <p class="text-sm text-gray-400">Ubicación de clientes con información de dirección</p>
        </div>
    </div>

    <!-- Contenedor del Mapa — wire:ignore previene que Livewire destruya el DOM del mapa -->
    <div wire:ignore class="flex-1 bg-white border border-gray-200 rounded-xl shadow-sm relative overflow-hidden min-h-[400px]">
        <div id="rutx-map" class="absolute inset-0 w-full h-full"></div>
    </div>
</div>

<script>
    (function () {
        // Leer datos desde atributos data-* del div raíz (evita la directiva js de blade en el script)
        const root        = document.getElementById('rutx-root');
        const clientes    = JSON.parse(root.dataset.clientes || '[]');
        const gmapsKey    = root.dataset.gmapsKey || '';

        window.RutxMap = {
            instance:      null,
            infoWindow:    null,
            clientes:      clientes,
            COLOR_CLIENTE_A: '#003859',
            COLOR_CLIENTE_B: '#4a90d9',
            COLOR_CLIENTE_C: '#ff9800',

            init() {
                if (typeof google !== 'undefined' && google.maps) {
                    this.setupMap();
                    return;
                }
                window._rutxMapCallback = () => window.RutxMap.setupMap();
                const script = document.createElement('script');
                script.src   = `https://maps.googleapis.com/maps/api/js?key=${gmapsKey}&callback=_rutxMapCallback`;
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
            },

            setupMap() {
                const el = document.getElementById('rutx-map');
                if (!el || this.instance) return;

                // Inicializar mapa centrado en el primer cliente (o por defecto en Chiapas)
                const centerLat = this.clientes.length > 0 ? parseFloat(this.clientes[0].lat) : 16.7370;
                const centerLng = this.clientes.length > 0 ? parseFloat(this.clientes[0].lng) : -92.6376;

                this.instance = new google.maps.Map(el, {
                    center: { lat: centerLat, lng: centerLng },
                    zoom: 14,
                    disableDefaultUI: true,
                    zoomControl: true,
                    styles: [
                        { featureType: 'poi',     stylers: [{ visibility: 'off' }] },
                        { featureType: 'transit', stylers: [{ visibility: 'off' }] },
                    ],
                });

                this.infoWindow = new google.maps.InfoWindow();
                this.plotClientes();
            },

            plotClientes() {
                this.clientes.forEach((c) => {
                    // 1. Calcular el color por jerarquía
                    const color = c.tipo === 'B' ? this.COLOR_CLIENTE_B
                                : c.tipo === 'C' ? this.COLOR_CLIENTE_C
                                : this.COLOR_CLIENTE_A;

                    // 2. Colocar el marcador usando lat y lng directos
                    const marker = new google.maps.Marker({
                        position: { lat: parseFloat(c.lat), lng: parseFloat(c.lng) },
                        map:      this.instance,
                        title:    `${c.nombre} (Tipo ${c.tipo})`,
                        icon: {
                            path:        google.maps.SymbolPath.CIRCLE,
                            scale:       8,
                            fillColor:   color,
                            fillOpacity: 1,
                            strokeColor: '#ffffff',
                            strokeWeight: 2,
                        },
                    });

                    // 3. Agregar el popup al hacer clic (usando la información de dirección para visualización)
                    marker.addListener('click', () => {
                        const content = `
                            <div style="font-family:sans-serif; color:#333; min-width: 180px;">
                                <h3 style="margin:0 0 5px 0; font-size:14px; font-weight:bold; color:${color};">
                                    ${c.nombre}
                                    <span style="font-size:11px; font-weight:normal; background:#eee; color:#666; padding:2px 4px; border-radius:3px; margin-left:4px;">Tipo ${c.tipo}</span>
                                </h3>
                                <p style="margin:0; font-size:12px;"><strong>Clave:</strong> ${c.clave}</p>
                                <p style="margin:4px 0 0 0; font-size:12px;"><strong>Dirección:</strong><br> ${c.calle} #${c.num_exterior}</p>
                                <p style="margin:0; font-size:12px;"><strong>Colonia:</strong> ${c.colonia}</p>
                                <p style="margin:0; font-size:12px;"><strong>Población:</strong> ${c.poblacion}, CP: ${c.codigo_postal}</p>
                            </div>
                        `;
                        this.infoWindow.setContent(content);
                        this.infoWindow.open(this.instance, marker);
                    });
                });
            }
        };
    })();
</script>
