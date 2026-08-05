<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ReportePreventaEntregaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PruebaSeeder::class);
    }

    /**
     * Test that the route requires authentication.
     */
    public function test_preventa_entrega_page_requires_authentication(): void
    {
        $response = $this->get('/reporte-preventa-entrega');
        $response->assertRedirect('/login');
    }

    /**
     * Test that authenticated users can see the page and Volt component.
     */
    public function test_preventa_entrega_page_is_displayed_to_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/reporte-preventa-entrega');

        $response
            ->assertOk()
            ->assertSeeVolt('ventas.reporte-preventa-entrega')
            ->assertSee('Reporte de Preventa y Entrega');
    }

    /**
     * Test component initial state and logic of dynamic route fetching.
     */
    public function test_preventa_entrega_component_initial_state(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('ventas.reporte-preventa-entrega');
        
        $component->assertSet('filtro_zona', '1Z - Zona 1')
            ->assertSet('filtro_ruta', 'todos')
            ->assertSet('consultado', false);
            
        // Test obtaining routes for the test sandbox zone
        $component->set('filtro_zona', '99-PRUEBA');
        $rutas = $component->instance()->obtenerRutas();
        $this->assertNotEmpty($rutas);
        $this->assertEquals('999001 - VENDEDOR PRUEBA', $rutas[0]['id']);
    }

    /**
     * Test executing a search / consult query.
     */
    public function test_preventa_entrega_component_can_consult_and_calculate_comparisons(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // We consult Zone '99-PRUEBA' and Route '999001 - VENDEDOR PRUEBA'
        $component = Volt::test('ventas.reporte-preventa-entrega')
            ->set('filtro_zona', '99-PRUEBA')
            ->set('filtro_ruta', '999001 - VENDEDOR PRUEBA')
            ->set('fecha_inicio', '2026-08-03')
            ->set('fecha_fin', '2026-08-05')
            ->call('consultar')
            ->assertSet('consultado', true)
            ->assertSee('Detalle de Comparación');
            
        $datos = $component->get('datosTabla');
        $this->assertNotEmpty($datos);
        
        // Assert KPIs are calculated and non-zero
        $this->assertGreaterThan(0, $component->get('totalPreventa'));
        $this->assertGreaterThan(0, $component->get('totalEntrega'));
    }

    /**
     * Test changing 'Agrupar por' logic between Producto, Pedido and Producto por Pedido.
     */
    public function test_preventa_entrega_component_can_change_grouping(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('ventas.reporte-preventa-entrega')
            ->set('filtro_zona', '99-PRUEBA')
            ->set('filtro_ruta', '999001 - VENDEDOR PRUEBA')
            ->set('fecha_inicio', '2026-08-03')
            ->set('fecha_fin', '2026-08-05')
            ->call('consultar');
            
        // Default is Producto grouping: keys should contain 'producto_id'
        $datosProd = $component->get('datosTabla');
        $this->assertArrayHasKey('producto_id', $datosProd[0]);
        
        // Change to Pedido: keys should contain 'pedido_folio' and 'estado_carga'
        $component->set('agrupar_por', 'Pedido');
        $datosPed = $component->get('datosTabla');
        $this->assertArrayHasKey('pedido_folio', $datosPed[0]);
        $this->assertArrayHasKey('estado_carga', $datosPed[0]);
        
        // Change to Producto por Pedido: keys should contain both 'pedido_folio' and 'producto_id'
        $component->set('agrupar_por', 'Producto por Pedido');
        $datosBoth = $component->get('datosTabla');
        $this->assertArrayHasKey('pedido_folio', $datosBoth[0]);
        $this->assertArrayHasKey('producto_id', $datosBoth[0]);
    }

    /**
     * Test rendering with the exact filters from user screenshot.
     */
    public function test_preventa_entrega_component_can_filter_by_multi_lines_and_family(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('ventas.reporte-preventa-entrega')
            ->set('filtro_zona', '99-PRUEBA')
            ->set('filtro_ruta', '999001 - VENDEDOR PRUEBA')
            ->set('fecha_inicio', '2026-08-03')
            ->set('fecha_fin', '2026-08-05')
            ->set('filtro_lineas', ['ALIMENTOS', 'DULCERIA', 'FARMACIA', 'HIG Y DESECHABLE'])
            ->set('filtro_linea_familia', 'GRANOS')
            ->call('consultar')
            ->assertSet('consultado', true);
            
        $html = $component->html();
        $this->assertTrue(
            str_contains($html, 'Detalle de Comparación') || str_contains($html, 'No se encontraron'),
            "HTML did not contain results or empty state"
        );
    }
}
