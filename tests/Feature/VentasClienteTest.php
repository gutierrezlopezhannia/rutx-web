<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class VentasClienteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PruebaSeeder::class);
    }

    /**
     * Test que la ruta de ventas-cliente requiere autenticación.
     */
    public function test_ventas_cliente_page_requires_authentication(): void
    {
        $response = $this->get('/ventas-cliente');
        $response->assertRedirect('/login');
    }

    /**
     * Test que la ruta de ventas-cliente es accesible para usuarios autenticados.
     */
    public function test_ventas_cliente_page_is_displayed_to_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/ventas-cliente');

        $response
            ->assertOk()
            ->assertSeeVolt('ventas.ventas-cliente')
            ->assertSee('Reporte de ventas por Cliente');
    }

    /**
     * Test que el componente Livewire Volt puede buscar un folio inexistente.
     */
    public function test_ventas_cliente_component_can_search(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('ventas.ventas-cliente')
            ->set('search', 'nonexistent-folio-1234')
            ->assertDontSee('No hay Registros para mostrar');

        $this->assertCount(0, $component->get('registrosFiltrados'));
    }


    /**
     * Test que al hacer clic en detalles se abre el modal con la información correcta.
     */
    public function test_ventas_cliente_component_can_open_details_modal(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('ventas.ventas-cliente');
        
        $registros = $component->get('registrosFiltrados');
        $this->assertNotEmpty($registros);
        
        $firstRegistro = $registros[0];
        
        $component->call('verDetalle', $firstRegistro['folio'])
            ->assertSet('mostrarModalDetalle', true)
            ->assertSet('registroSeleccionado.folio', $firstRegistro['folio']);
            
        // Test que al cerrar el modal se limpia el estado
        $component->call('cerrarModal')
            ->assertSet('mostrarModalDetalle', false)
            ->assertSet('registroSeleccionado', null);
    }
}
