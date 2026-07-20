<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class CreditoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PruebaSeeder::class);
    }

    /**
     * Test que la ruta de crédito requiere autenticación.
     */
    public function test_credito_page_requires_authentication(): void
    {
        $response = $this->get('/clientes/credito');
        $response->assertRedirect('/login');
    }

    /**
     * Test que la ruta de crédito es accesible para usuarios autorizados.
     */
    public function test_credito_page_is_displayed_to_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/clientes/credito');

        $response
            ->assertOk()
            ->assertSeeVolt('clientes.credito')
            ->assertSee('Panel de Control de Cartera y Crédito')
            ->assertSee('Cartera Total')
            ->assertSee('Límite Autorizado');
    }

    /**
     * Test que el componente de crédito filtra los clientes por búsqueda de texto.
     */
    public function test_credito_component_filters_by_search_query(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Buscar un cliente específico
        Volt::test('clientes.credito')
            ->set('search', 'CLIENTE PRUEBA 01')
            ->assertSee('CLIENTE PRUEBA 01')
            ->assertDontSee('CLIENTE PRUEBA 02');
    }

    /**
     * Test que el componente de crédito filtra por estado de crédito.
     */
    public function test_credito_component_filters_by_status(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Si filtramos por "Al corriente" en el seeder inicial donde todos tienen saldos vencidos de 2018,
        // no debería mostrarse ningún cliente como "Al corriente".
        Volt::test('clientes.credito')
            ->set('filtro_estado', 'Al corriente')
            ->assertDontSee('CLIENTE PRUEBA 01')
            ->assertDontSee('CLIENTE PRUEBA 02')
            ->assertSee('No se encontraron registros de cartera que coincidan');
    }
}
