<?php

use function Livewire\Volt\{state, layout, mount};

layout('layouts.app');

$mockRubros = [
    ['id' => 'Combustible', 'name' => 'Combustible'],
    ['id' => 'Casetas', 'name' => 'Casetas'],
    ['id' => 'Estacionamiento', 'name' => 'Estacionamiento'],
    ['id' => 'Viáticos', 'name' => 'Viáticos / Alimentos'],
    ['id' => 'Mantenimiento Menor', 'name' => 'Mantenimiento Menor'],
    ['id' => 'Otros', 'name' => 'Otros Gastos'],
];

state([
    'zonas' => [],
    'rutas' => [],
    'rubros' => $mockRubros,

    'zona' => '',
    'vendedor' => '',
    'rubro_gasto' => '',
    'comentario' => '',
    'fecha' => fn() => date('Y-m-d'),
    'cantidad' => '',

    'vendedoresFiltrados' => [],
    'errors' => [],
    'submitted' => false,
]);

mount(function () {
    // Load Zones from DB
    $this->zonas = \App\Models\Zone::orderBy('id')->get()->map(fn($z) => [
        'id' => $z->id,
        'name' => $z->id
    ])->toArray();
    
    // Load Routes/Sellers from DB
    $this->rutas = \App\Models\Seller::where('oculto', 'N')->get()->map(function ($s) {
        if (str_contains($s->id, ' - ')) {
            $parts = explode(' - ', $s->id);
            $clave = trim($parts[0]);
            $nombre = trim($parts[1]);
        } else {
            $clave = $s->id;
            $nombre = $s->name;
        }
        
        $invoice = \App\Models\Invoice::where('vendedor_id', $s->id)->first();
        $zona = $invoice ? $invoice->zona_id : '1Z - Zona 1';
        
        return [
            'clave' => $clave,
            'nombre' => $nombre,
            'zona' => $zona
        ];
    })->toArray();
});

$updatedZona = function ($value) {
    $this->vendedor = '';
    if ($value) {
        $this->vendedoresFiltrados = collect($this->rutas)->where('zona', $value)->values()->toArray();
    } else {
        $this->vendedoresFiltrados = [];
    }
    $this->validateField('zona');
};

$validateField = function ($field) {
    if (!$this->submitted) return;
    
    unset($this->errors[$field]);
    
    if ($field === 'zona' && empty($this->zona)) {
        $this->errors['zona'] = 'Campo requerido!';
    }
    if ($field === 'vendedor' && empty($this->vendedor)) {
        $this->errors['vendedor'] = 'Campo requerido!';
    }
    if ($field === 'rubro_gasto' && empty($this->rubro_gasto)) {
        $this->errors['rubro_gasto'] = 'Campo requerido!';
    }
    if ($field === 'comentario' && empty($this->comentario)) {
        $this->errors['comentario'] = 'Campo requerido!';
    }
    if ($field === 'fecha' && empty($this->fecha)) {
        $this->errors['fecha'] = 'Campo requerido!';
    }
    if ($field === 'cantidad') {
        if (empty($this->cantidad) || !is_numeric($this->cantidad) || floatval($this->cantidad) <= 0) {
            $this->errors['cantidad'] = 'Campo requerido!';
        }
    }
};

$updatedVendedor = fn() => $this->validateField('vendedor');
$updatedRubroGasto = fn() => $this->validateField('rubro_gasto');
$updatedComentario = fn() => $this->validateField('comentario');
$updatedFecha = fn() => $this->validateField('fecha');
$updatedCantidad = fn() => $this->validateField('cantidad');

$guardar = function () use ($mockRubros) {
    $this->submitted = true;
    $this->errors = [];

    // Validar todos los campos
    if (empty($this->zona)) $this->errors['zona'] = 'Campo requerido!';
    if (empty($this->vendedor)) $this->errors['vendedor'] = 'Campo requerido!';
    if (empty($this->rubro_gasto)) $this->errors['rubro_gasto'] = 'Campo requerido!';
    if (empty($this->comentario)) $this->errors['comentario'] = 'Campo requerido!';
    if (empty($this->fecha)) $this->errors['fecha'] = 'Campo requerido!';
    if (empty($this->cantidad) || !is_numeric($this->cantidad) || floatval($this->cantidad) <= 0) {
        $this->errors['cantidad'] = 'Campo requerido!';
    }

    if (count($this->errors) > 0) {
        return;
    }

    // Obtener ruta seleccionada
    $ruta = collect($this->rutas)->firstWhere('clave', $this->vendedor);
    $nombreRuta = $ruta['nombre'] ?? 'RUTA';
    $rubroSelected = collect($mockRubros)->firstWhere('id', $this->rubro_gasto);
    $concepto = $rubroSelected['name'] ?? $this->rubro_gasto;

    $nuevoGasto = [
        'ruta_clave' => $this->vendedor,
        'ruta_nombre' => $nombreRuta,
        'concepto' => $concepto,
        'monto' => floatval($this->cantidad),
        'fecha' => $this->fecha,
        'comprobante' => 'Nota/Simplificado',
        'referencia' => 'REF-' . strtoupper(substr(uniqid(), -5)),
        'observaciones' => $this->comentario,
        'zona' => $this->zona,
    ];

    $gastos = session('gastos_operativos', []);
    array_unshift($gastos, $nuevoGasto);
    session(['gastos_operativos' => $gastos]);

    // Redirigir con toast de éxito
    session()->flash('success_gasto', "Gasto registrado con éxito para la {$nombreRuta}.");
    return $this->redirectRoute('ruta.gastos-operativos', navigate: true);
};

?>

<div class="h-full bg-[#f4f6f8] dark:bg-gray-900 flex flex-col pt-4 overflow-y-auto">
    <div class="w-full px-6 flex flex-col flex-1 pb-10">
        
        {{-- Breadcrumb --}}
        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mb-6 px-1 select-none">
            <span class="hover:text-gray-700 dark:hover:text-white cursor-pointer">Cpanel</span>
            <span class="mx-2 text-gray-400">/</span>
            <span class="hover:text-gray-700 dark:hover:text-white cursor-pointer">Venta</span>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-[#003859] dark:text-blue-400 font-bold">Nuevo Gasto Operativo</span>
        </div>

        {{-- Form Container --}}
        <div class="flex flex-col items-center justify-center flex-1">
            <div class="w-full max-w-md bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden p-6">
                
                <h2 class="text-sm font-bold text-gray-800 dark:text-white mb-6 uppercase tracking-wider text-left border-b border-gray-100 dark:border-gray-700 pb-3">
                    Agregar nuevo Gasto Operativo
                </h2>

                <form wire:submit.prevent="guardar" class="space-y-5">
                    
                    {{-- Zona --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 {{ isset($errors['zona']) ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                            Zona
                        </label>
                        <div class="relative">
                            <select wire:model.live="zona"
                                class="w-full appearance-none border rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-semibold cursor-pointer {{ isset($errors['zona']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-200 dark:border-gray-700' }}">
                                <option value="">Seleccione Zona ...</option>
                                @foreach($zonas as $z)
                                    <option value="{{ $z['id'] }}">{{ $z['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @if(isset($errors['zona']))
                            <div class="text-red-500 text-[10px] mt-1 font-bold">{{ $errors['zona'] }}</div>
                        @endif
                    </div>

                    {{-- Vendedor (Route) --}}
                    @if ($zona)
                        <div class="animate-fade-in">
                            <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 {{ isset($errors['vendedor']) ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                                Vendedor
                            </label>
                            <div class="relative">
                                <select wire:model.live="vendedor"
                                    class="w-full appearance-none border rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-semibold cursor-pointer {{ isset($errors['vendedor']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-200 dark:border-gray-700' }}">
                                    <option value="">Seleccione Vendedor ...</option>
                                    @foreach($vendedoresFiltrados as $v)
                                        <option value="{{ $v['clave'] }}">{{ $v['clave'] }} - {{ $v['nombre'] }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                            @if(isset($errors['vendedor']))
                                <div class="text-red-500 text-[10px] mt-1 font-bold">{{ $errors['vendedor'] }}</div>
                            @endif
                        </div>
                    @endif

                    {{-- Rubro gasto --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 {{ isset($errors['rubro_gasto']) ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                            Rubro gasto
                        </label>
                        <div class="relative">
                            <select wire:model.live="rubro_gasto"
                                class="w-full appearance-none border rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-semibold cursor-pointer {{ isset($errors['rubro_gasto']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-200 dark:border-gray-700' }}">
                                <option value="">Seleccione Rubro ...</option>
                                @foreach($rubros as $r)
                                    <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @if(isset($errors['rubro_gasto']))
                            <div class="text-red-500 text-[10px] mt-1 font-bold">{{ $errors['rubro_gasto'] }}</div>
                        @endif
                    </div>

                    {{-- Comentario / Gasto --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 {{ isset($errors['comentario']) ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                            Comentario/Gasto
                        </label>
                        <input type="text" wire:model.live="comentario" placeholder="Escriba comentario del gasto ..."
                            class="w-full px-3 py-2 border rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-medium placeholder-gray-400 {{ isset($errors['comentario']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-200 dark:border-gray-700' }}" />
                        @if(isset($errors['comentario']))
                            <div class="text-red-500 text-[10px] mt-1 font-bold">{{ $errors['comentario'] }}</div>
                        @endif
                    </div>

                    {{-- Fecha --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 {{ isset($errors['fecha']) ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                            Fecha
                        </label>
                        <div class="relative">
                            <input type="date" wire:model.live="fecha"
                                class="w-full px-3 py-2 border rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-semibold {{ isset($errors['fecha']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-200 dark:border-gray-700' }}" />
                        </div>
                        @if(isset($errors['fecha']))
                            <div class="text-red-500 text-[10px] mt-1 font-bold">{{ $errors['fecha'] }}</div>
                        @endif
                    </div>

                    {{-- Cantidad (Monto) --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider mb-1.5 {{ isset($errors['cantidad']) ? 'text-red-500' : 'text-gray-500 dark:text-gray-400' }}">
                            Cantidad
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 dark:text-gray-400 font-bold">$</span>
                            <input type="number" step="0.01" min="0.01" wire:model.live="cantidad" placeholder="0.00"
                                class="w-full pl-7 pr-3 py-2 border rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-[#003859] dark:focus:ring-blue-600 bg-white dark:bg-gray-800 text-gray-750 dark:text-gray-200 font-bold placeholder-gray-400 {{ isset($errors['cantidad']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-200 dark:border-gray-700' }}" />
                        </div>
                        @if(isset($errors['cantidad']))
                            <div class="text-red-500 text-[10px] mt-1 font-bold">{{ $errors['cantidad'] }}</div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end pt-3">
                        <button type="submit"
                            class="px-5 py-2 rounded-lg bg-[#003859] hover:bg-[#002d48] dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold shadow-sm transition-all duration-150 cursor-pointer text-xs">
                            Guardar
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
