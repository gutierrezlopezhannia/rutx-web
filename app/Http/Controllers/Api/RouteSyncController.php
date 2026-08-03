<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * RouteSyncController
 *
 * Endpoint de sincronización matutina para la app móvil RUTX.
 * Devuelve los clientes de la zona del vendedor (con datos de crédito)
 * y el catálogo de productos activos.
 *
 * GET /api/v1/routes/sync/{vendedor_id}
 */
class RouteSyncController extends Controller
{
    /**
     * Sincronización matutina completa.
     * Retorna: vendedor, clientes (con crédito), productos.
     */
    public function sync(Request $request, string $vendedorId): JsonResponse
    {
        // 1. Verificar que el vendedor existe
        $vendedor = Seller::find($vendedorId);

        if (! $vendedor) {
            return response()->json([
                'error'   => 'Vendedor no encontrado.',
                'codigo'  => 'VENDEDOR_NOT_FOUND',
            ], 404);
        }

        // 2. Obtener la zona del vendedor desde sus clientes asignados
        //    (los clientes del vendedor se determinan por zona_id en invoices o por zona directa)
        $zonaId = $this->resolverZonaDelVendedor($vendedorId);

        // 3. Consultar clientes de la ruta con datos de crédito
        $clientes = $this->obtenerClientesDeRuta($vendedorId, $zonaId);

        // 4. Consultar productos activos del catálogo
        $productos = $this->obtenerProductos();

        return response()->json([
            'vendedor' => [
                'id'      => $vendedor->id,
                'nombre'  => $vendedor->name,
                'zona_id' => $zonaId,
            ],
            'clientes' => $clientes,
            'productos' => $productos,
            'sincronizado_en' => now()->toISOString(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers privados
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resuelve la zona_id del vendedor consultando sus últimas ventas.
     * Si no tiene historial, devuelve null (se devolverán todos los clientes).
     */
    private function resolverZonaDelVendedor(string $vendedorId): ?string
    {
        return DB::table('invoices')
            ->where('vendedor_id', $vendedorId)
            ->orderByDesc('fecha')
            ->value('zona_id');
    }

    /**
     * Obtiene la lista de clientes asignados al vendedor/zona.
     *
     * Lógica de crédito:
     *   - tiene_credito = 1  si  limite > 0
     *   - tiene_credito = 0  si  limite = 0 (contado puro)
     *
     * Tipo de venta:
     *   - 'credito'  si plazo > 0
     *   - 'contado'  si plazo = 0
     */
    private function obtenerClientesDeRuta(string $vendedorId, ?string $zonaId): array
    {
        $query = DB::table('customers as c')
            ->leftJoin('zones as z', 'z.id', '=', 'c.zona_id')
            ->select([
                'c.id             as cliente_id',
                'c.clave          as clave_cliente',
                'c.nombre         as nombre_cliente',
                'c.telefono',
                'c.direccion      as calle',
                DB::raw("'' as colonia"),           // Microsip no separa colonia; se deja vacío
                'z.nombre         as poblacion',
                'c.zona_id',
                DB::raw('CASE WHEN c.limite > 0 THEN 1 ELSE 0 END as tiene_credito'),
                'c.limite         as limite_credito',
                'c.saldo          as saldo_credito',
                DB::raw("CASE WHEN c.plazo > 0 THEN 'credito' ELSE 'contado' END as tipo_venta"),
            ]);

        // Filtrar por zona si está disponible
        if ($zonaId) {
            $query->where('c.zona_id', $zonaId);
        } else {
            // Sin zona resuelta: buscar clientes que el vendedor haya visitado
            $clientesIds = DB::table('invoices')
                ->where('vendedor_id', $vendedorId)
                ->distinct()
                ->pluck('customer_id');
            $query->whereIn('c.id', $clientesIds);
        }

        $rows = $query
            ->where('c.limite', '>=', 0)  // Excluir clientes marcados como inactivos
            ->orderBy('c.nombre', 'ASC')
            ->get();

        return $rows->map(function ($row) {
            return [
                'cliente_id'     => (string) $row->cliente_id,
                'clave_cliente'  => $row->clave_cliente ?? '',
                'nombre_cliente' => $row->nombre_cliente,
                'telefono'       => $row->telefono ?? '0',
                'calle'          => $row->calle ?? '',
                'colonia'        => $row->colonia ?? '',
                'poblacion'      => $row->poblacion ?? '',
                'codigo_postal'  => '',
                'zona_id'        => $row->zona_id,
                'tiene_credito'  => (bool) $row->tiene_credito,
                'limite_credito' => (float) ($row->limite_credito ?? 0.0),
                'saldo_credito'  => (float) ($row->saldo_credito ?? 0.0),
                'tipo_venta'     => $row->tipo_venta,
            ];
        })->toArray();
    }

    /**
     * Obtiene el catálogo de productos activos con precios e impuestos.
     * Se mapea directamente desde la tabla de Microsip (sin filtro de vendedor).
     */
    private function obtenerProductos(): array
    {
        // Intentar con tabla estandarizada del sistema (adapt según esquema real de Microsip)
        $rows = DB::table('articulos as a')
            ->select([
                'a.articulo_id',
                'a.nombre',
                DB::raw("'A' as estatus"),
                'a.clave',
                'a.precio_1  as precio',
                DB::raw('16            as porcentaje_impuesto'),
                DB::raw('622           as impuesto_id'),
            ])
            ->where('a.estatus', 'A')
            ->orderBy('a.nombre', 'ASC')
            ->limit(2000)
            ->get();

        return $rows->map(function ($row) {
            return [
                'articulo_id'          => (int) $row->articulo_id,
                'nombre'               => $row->nombre,
                'estatus'              => $row->estatus,
                'clave'                => $row->clave ?? '',
                'precio'               => (float) ($row->precio ?? 0.0),
                'porcentaje_impuesto'  => (int) ($row->porcentaje_impuesto ?? 16),
                'impuesto_id'          => (int) ($row->impuesto_id ?? 622),
            ];
        })->toArray();
    }
}
