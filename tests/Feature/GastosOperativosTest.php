<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class GastosOperativosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Limpiamos la sesión de gastos
        session()->forget('gastos_operativos');

        // Crear zonas y vendedores para que los tests pasen con la base de datos real
        \App\Models\Zone::create(['id' => '1Z - Zona 1', 'name' => 'ZONA 1']);
        \App\Models\Zone::create(['id' => '2Z - Zona 2', 'name' => 'ZONA 2']);
        
        \App\Models\Seller::create(['id' => '3983 - RUTA01', 'name' => 'RUTA01', 'oculto' => 'N']);
        \App\Models\Seller::create(['id' => '4684 - RUTA04', 'name' => 'RUTA04', 'oculto' => 'N']);
    }

    /**
     * Test que el formulario de nuevo gasto requiere autenticación.
     */
    public function test_nuevo_gasto_page_requires_authentication(): void
    {
        $response = $this->get('/nuevo-gasto-op');
        $response->assertRedirect('/login');
    }

    /**
     * Test que la tabla concentradora requiere autenticación.
     */
    public function test_gastos_operativos_page_requires_authentication(): void
    {
        $response = $this->get('/ruta/gastos-operativos');
        $response->assertRedirect('/login');
    }

    /**
     * Test que la página del formulario carga correctamente para usuarios autenticados.
     */
    public function test_nuevo_gasto_page_is_displayed_to_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $responseForm = $this->get('/nuevo-gasto-op');
        $responseForm->assertOk()
            ->assertSeeVolt('ventas.nuevo-gasto-op')
            ->assertSee('Agregar nuevo Gasto Operativo');
    }

    /**
     * Test que la página de la tabla concentradora carga correctamente para usuarios autenticados.
     */
    public function test_gastos_operativos_page_is_displayed_to_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $responseTable = $this->get('/ruta/gastos-operativos');
        $responseTable->assertOk()
            ->assertSeeVolt('rutas.gastos-operativos')
            ->assertSee('Concentrador de Gastos Operativos');
    }

    /**
     * Test que el formulario de nuevo gasto valida campos requeridos.
     */
    public function test_nuevo_gasto_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Volt::test('ventas.nuevo-gasto-op')
            ->call('guardar')
            ->assertSet('errors.zona', 'Campo requerido!')
            ->assertSet('errors.rubro_gasto', 'Campo requerido!')
            ->assertSet('errors.comentario', 'Campo requerido!')
            ->assertSet('errors.cantidad', 'Campo requerido!');
    }

    /**
     * Test que se puede registrar un gasto correctamente y se agrega a la sesión.
     */
    public function test_can_register_gasto_successfully(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simulamos elegir Zona y Vendedor
        $component = Volt::test('ventas.nuevo-gasto-op')
            ->set('zona', '1Z - Zona 1')
            ->set('vendedor', '3983') // RUTA01
            ->set('rubro_gasto', 'Combustible')
            ->set('comentario', 'Gasolina Magna de prueba')
            ->set('fecha', '2026-08-05')
            ->set('cantidad', '500.00')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertRedirect('/ruta/gastos-operativos');

        $gastos = session('gastos_operativos');
        $this->assertNotEmpty($gastos);
        $this->assertEquals('3983', $gastos[0]['ruta_clave']);
        $this->assertEquals(500.00, $gastos[0]['monto']);
        $this->assertEquals('1Z - Zona 1', $gastos[0]['zona']);
    }

    /**
     * Test que la tabla concentradora filtra y elimina gastos.
     */
    public function test_tabla_concentradora_filters_and_deletes_gasto(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Registramos un gasto directamente en sesión
        $this->withSession(['gastos_operativos' => [
            [
                'ruta_clave' => '4684',
                'ruta_nombre' => 'RUTA04',
                'concepto' => 'Combustible',
                'monto' => 950.00,
                'fecha' => '2026-08-05',
                'comprobante' => 'Factura',
                'referencia' => 'TKT-9912',
                'observaciones' => 'Gasto de RUTA04',
                'zona' => '2Z - Zona 2'
            ]
        ]]);

        $component = Volt::test('rutas.gastos-operativos')
            ->assertCount('gastosFiltrados', 1);

        // Probamos filtrar por otra zona donde no hay gastos
        $component->set('filtro_zona', '1Z - Zona 1')
            ->assertCount('gastosFiltrados', 0);

        // Regresamos al filtro y eliminamos el gasto
        $component->set('filtro_zona', 'todos')
            ->call('eliminarGasto', 0)
            ->assertCount('gastosFiltrados', 0);

        $this->assertEmpty(session('gastos_operativos'));
    }
}
