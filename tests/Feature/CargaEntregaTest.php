<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CargaEntregaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PruebaSeeder::class);
    }

    /**
     * Test que la ruta de carga-entrega requiere autenticación.
     */
    public function test_carga_entrega_page_requires_authentication(): void
    {
        $response = $this->get('/inventario/carga-entrega');
        $response->assertRedirect('/login');
    }

    /**
     * Test que la ruta de carga-entrega es accesible para usuarios autenticados.
     */
    public function test_carga_entrega_page_is_displayed_to_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/inventario/carga-entrega');

        $response
            ->assertOk()
            ->assertSeeVolt('inventario.carga-entrega')
            ->assertSee('Carga Inventario Móvil Entrega');
    }

    /**
     * Test que el componente se inicializa correctamente y se puede consultar.
     */
    public function test_carga_entrega_component_can_consult_and_filter(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('inventario.carga-entrega')
            ->assertSet('consultado', false)
            ->call('consultar')
            ->assertSet('consultado', true)
            ->assertSet('toast_type', 'success');

        $registros = $component->get('registrosFiltrados');
        $this->assertNotEmpty($registros);
    }

    /**
     * Test que el stock insuficiente bloquea la validación final.
     */
    public function test_insufficient_stock_blocks_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('inventario.carga-entrega')
            ->call('consultar');

        // Modificar cantidad de AL-201 a 50 (existencia_almacen es 20)
        $registros = $component->get('registrosFiltrados');
        $index = collect($registros)->search(fn($r) => $r['clave'] === 'AL-201');

        $component->call('actualizarCantidad', $index, 50)
            ->call('validarYGuardar')
            ->assertSet('show_validation_modal', false)
            ->assertSet('toast_type', 'error');
    }

    /**
     * Test que una carga conciliada (sin desajustes) se guarda directamente en el modal sin justificación.
     */
    public function test_fully_conciliated_load_can_save_directly(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('inventario.carga-entrega')
            ->call('consultar');

        // AL-200: requerido=120, movil=45, cargar=75 -> concilia (120)
        // AL-201: requerido=30, movil=10, cargar=20 -> concilia (30)
        // AL-202: requerido=15, movil=15, cargar=0 -> concilia (15)
        // BEB-100: requerido=100, movil=10, cargar=15 -> desajuste (25 != 100)
        // Ajustamos BEB-100 para conciliar. Pero BEB-100 tiene existencia_almacen = 15.
        // Así que para conciliar todo sin advertencias, cambiamos los productos con desajustes
        // o ajustamos sus cantidades a cargar.
        // Por ejemplo, para BEB-100, requerido es 100, movil es 10. Si cargamos 90, concilia, pero supera existencia_almacen.
        // Vamos a modificar unidades_requeridas de los productos en el state para simular una carga perfecta.
        
        $productos = $component->get('productos');
        foreach ($productos as $key => $p) {
            // Hacemos que todas las unidades requeridas coincidan con movil + cargar
            $productos[$key]['unidades_requeridas'] = $p['existencia_movil'] + $p['cantidad_a_cargar'];
        }
        $component->set('productos', $productos);
        
        // Volvemos a consultar para refrescar registrosFiltrados
        $component->call('consultar')
            ->call('validarYGuardar')
            ->assertSet('show_validation_modal', true);

        // Confirmar carga
        $component->call('confirmarCarga')
            ->assertSet('show_validation_modal', false)
            ->assertSet('consultado', false)
            ->assertSet('toast_type', 'success');
    }

    /**
     * Test que una carga con discrepancias (incompleta/excedente) requiere justificación obligatoria.
     */
    public function test_discrepancy_requires_justification(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Por defecto, BEB-100 tiene discrepancias (requerido=100, movil=10, cargar=15)
        $component = Volt::test('inventario.carga-entrega')
            ->call('consultar')
            ->call('validarYGuardar')
            ->assertSet('show_validation_modal', true);

        // Intentar guardar sin justificación
        $component->call('confirmarCarga')
            ->assertSet('show_validation_modal', true) // sigue abierto
            ->assertSet('toast_type', 'warning');

        // Rellenar justificación y reintentar
        $component->set('justificacion', 'Falta de stock del producto energético en almacén central.')
            ->call('confirmarCarga')
            ->assertSet('show_validation_modal', false) // se cierra
            ->assertSet('consultado', false) // se reinicia el formulario
            ->assertSet('toast_type', 'success');
    }
}
