<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class TraspasoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PruebaSeeder::class);
    }

    /**
     * Test que la ruta de traspaso requiere autenticación.
     */
    public function test_traspaso_page_requires_authentication(): void
    {
        $response = $this->get('/clientes/traspaso');
        $response->assertRedirect('/login');
    }

    /**
     * Test que la ruta de traspaso es accesible para usuarios autorizados.
     */
    public function test_traspaso_page_is_displayed_to_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/clientes/traspaso');

        $response
            ->assertOk()
            ->assertSeeVolt('clientes.traspaso')
            ->assertSee('Traspaso de Clientes entre Rutas')
            ->assertSee('Ruta de Origen')
            ->assertSee('Ruta de Destino');
    }

    /**
     * Test que el traspaso interactivo funciona correctamente en el componente.
     */
    public function test_traspaso_component_transfers_clients_interactively(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Inicializamos el componente y verificamos que el cliente R1001 esté en el origen (RUTA01) y no en el destino (RUTA03)
        Volt::test('clientes.traspaso')
            ->assertSet('selectedOrigen', '3983 - RUTA01')
            ->assertSet('selectedDestino', '4683 - RUTA03')
            ->set('checkedOrigen', ['R1001'])
            ->call('transferirA')
            // Después del traspaso, el cliente no debería estar marcado como checked
            ->assertSet('checkedOrigen', [])
            // Al guardar, se debe abrir el modal de confirmación primero
            ->call('guardar')
            ->assertSet('mostrarModalConfirmacion', true)
            ->assertSet('notification', '')
            // Confirmamos el traspaso
            ->call('confirmarGuardar')
            ->assertSet('mostrarModalConfirmacion', false)
            ->assertSet('notification', 'El traspaso de clientes ha sido guardado de forma exitosa.');
    }

    /**
     * Test que valida que se pueda cancelar la confirmación de guardado.
     */
    public function test_traspaso_component_can_cancel_confirmation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Volt::test('clientes.traspaso')
            ->set('checkedOrigen', ['R1001'])
            ->call('transferirA')
            ->call('guardar')
            ->assertSet('mostrarModalConfirmacion', true)
            ->call('cancelarGuardar')
            ->assertSet('mostrarModalConfirmacion', false)
            ->assertSet('notification', '');
    }

    /**
     * Test que valida que la ruta de origen y la de destino no sean la misma.
     */
    public function test_traspaso_component_validates_same_route(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Volt::test('clientes.traspaso')
            ->set('selectedOrigen', '3983 - RUTA01')
            ->set('selectedDestino', '3983 - RUTA01') // Seleccionamos la misma ruta
            ->call('guardar')
            ->assertSet('warning', 'La ruta de origen y la ruta de destino no pueden ser la misma.');
    }

    /**
     * Test que valida que no se permita guardar si no hay cambios.
     */
    public function test_traspaso_component_validates_no_changes(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Volt::test('clientes.traspaso')
            ->call('guardar')
            ->assertSet('warning', 'No se han detectado cambios para guardar. Realice un traspaso de cliente primero.');
    }
}
