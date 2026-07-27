<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class TopClientesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PruebaSeeder::class);
    }

    /**
     * Test that the top clients page requires authentication.
     */
    public function test_top_clientes_page_requires_authentication(): void
    {
        $response = $this->get('/top-clientes');
        $response->assertRedirect('/login');
    }

    /**
     * Test that the top clients page is accessible to authenticated users.
     */
    public function test_top_clientes_page_is_displayed_to_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/top-clientes');

        $response
            ->assertOk()
            ->assertSeeVolt('ventas.top-clientes')
            ->assertSee('Clientes con mayor Venta')
            ->assertSee('Zona')
            ->assertSee('Vendedor')
            ->assertSee('Fecha inicial')
            ->assertSee('Fecha final')
            ->assertSee('Consultar');
    }

    /**
     * Test that the component correctly shows seeded top clients.
     */
    public function test_top_clientes_shows_seeded_sales(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Seeded invoices are dated '2018-09-19'
        Volt::test('ventas.top-clientes')
            ->set('fecha_inicio', '2018-01-01')
            ->set('fecha_fin', '2026-12-31')
            ->call('consultar')
            ->assertSee('CLIENTE PRUEBA 01')
            ->assertSee('CLIENTE PRUEBA 02')
            // The route label combines code and name
            ->assertSee('999001 VENDEDOR PRUEBA');
    }

    /**
     * Test that the multi-select vendor filter correctly limits results.
     */
    public function test_seller_filter_applies(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Filter by the seeded seller
        Volt::test('ventas.top-clientes')
            ->set('fecha_inicio', '2018-01-01')
            ->set('fecha_fin', '2026-12-31')
            ->set('vendedores_seleccionados', ['999001 - VENDEDOR PRUEBA'])
            ->call('consultar')
            ->assertSee('CLIENTE PRUEBA 01');

        // Filter by a non-existent seller, should show empty
        Volt::test('ventas.top-clientes')
            ->set('fecha_inicio', '2018-01-01')
            ->set('fecha_fin', '2026-12-31')
            ->set('vendedores_seleccionados', ['nonexistent-seller'])
            ->call('consultar')
            ->assertDontSee('CLIENTE PRUEBA 01');
    }

    /**
     * Test that text search filters the ranking list.
     */
    public function test_search_filters_results(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Volt::test('ventas.top-clientes')
            ->set('fecha_inicio', '2018-01-01')
            ->set('fecha_fin', '2026-12-31')
            ->call('consultar')
            ->set('search', 'CLIENTE PRUEBA 01')
            ->assertSee('CLIENTE PRUEBA 01')
            ->assertDontSee('CLIENTE PRUEBA 02');
    }
}
