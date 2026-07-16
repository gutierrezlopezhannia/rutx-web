<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CobranzaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PruebaSeeder::class);
    }

    /**
     * Test que la ruta de cobranza requiere autenticación.
     */
    public function test_cobranza_page_requires_authentication(): void
    {
        $response = $this->get('/cobranza');
        $response->assertRedirect('/login');
    }

    /**
     * Test que la ruta de cobranza es accesible para usuarios autorizados.
     */
    public function test_cobranza_page_is_displayed_to_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/cobranza');

        $response
            ->assertOk()
            ->assertSeeVolt('cobranza.index')
            ->assertSee('Cobranza')
            ->assertSee('Pedidos de crédito');
    }

    /**
     * Test que al cambiar de cliente se actualizan sus datos y pedidos de crédito.
     */
    public function test_cobranza_component_updates_when_changing_client(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('cobranza.index')
            ->set('filtro_cliente', '999002 - CLIENTE PRUEBA 02')
            ->assertSet('filtro_zona', '99-PRUEBA');
            
        $pedidos = $component->get('pedidos');
        $this->assertNotEmpty($pedidos);
        $this->assertEquals('PRU000004', $pedidos[0]['folio']);
    }

    /**
     * Test de selección completa (Select All)
     */
    public function test_cobranza_select_all_checkbox_calculates_total(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('cobranza.index')
            ->set('selectAll', true);
            
        $pedidos = $component->get('pedidos');
        foreach ($pedidos as $p) {
            $this->assertTrue($p['checked']);
            $this->assertEquals($p['saldo'], $p['cobranza']);
        }
        
        $this->assertEquals(52400.00, $component->get('monto'));
    }

    /**
     * Test de validación: Se requiere vendedor para enviar cobranza.
     */
    public function test_cobranza_requires_vendedor_to_submit(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('cobranza.index')
            ->set('monto', 2000.00)
            ->set('filtro_vendedor', 'todos')
            ->call('enviarCobranza')
            ->assertSet('mensaje_error', 'Debe seleccionar un Vendedor para procesar la cobranza.');
    }

    /**
     * Test de flujo completo exitoso.
     */
    public function test_cobranza_submits_successfully_and_deducts_balances(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('cobranza.index')
            ->set('filtro_vendedor', '999001 - VENDEDOR PRUEBA')
            ->set('selectAll', true)
            ->call('enviarCobranza')
            ->assertSet('monto', '')
            ->assertSet('mensaje_error', '');

        $this->assertNotEmpty($component->get('mensaje_exito'));
            
        $cliente = \App\Models\Customer::find('999001 - CLIENTE PRUEBA 01');
        $this->assertEquals(0.00, $cliente->saldo);
    }
}
