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
            ->assertSee('Ranking de Clientes con Mayor Venta')
            ->assertSee('Total Ventas (Período)')
            ->assertSee('Pedidos / Tickets')
            ->assertSee('Ticket Promedio General');
    }

    /**
     * Test that the component correctly shows seeded top clients.
     */
    public function test_top_clientes_shows_seeded_sales(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Component has the default date range from setup matching the seeder
        Volt::test('ventas.top-clientes')
            ->assertSee('CLIENTE PRUEBA 01')
            ->assertSee('CLIENTE PRUEBA 02')
            ->assertSee('99-PRUEBA');
    }

    /**
     * Test that the top limit successfully restricts results.
     */
    public function test_top_limit_restricts_results(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Default top_limit is 10. Let's set it to 2 and check that we only see top 2.
        // The seeder has 4 CLIENTE PRUEBA. Let's make sure it contains CLIENTE PRUEBA 01 (highest sales) 
        // and doesn't show others when limited to 1 (or 2).
        Volt::test('ventas.top-clientes')
            ->set('top_limit', 2)
            ->assertSee('CLIENTE PRUEBA 01') // total total: ~55100 in seeder
            ->assertSee('CLIENTE PRUEBA 02') // total total: ~53500 in seeder
            ->assertDontSee('CLIENTE PRUEBA 03') // total total: ~10000
            ->assertDontSee('CLIENTE PRUEBA 04'); // total total: ~14000
    }

    /**
     * Test that text search filters the ranking list.
     */
    public function test_search_filters_results(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Volt::test('ventas.top-clientes')
            ->set('search', 'CLIENTE PRUEBA 01')
            ->assertSee('CLIENTE PRUEBA 01')
            ->assertDontSee('CLIENTE PRUEBA 02');
    }
}
