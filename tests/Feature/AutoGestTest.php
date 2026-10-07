<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AutoGestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function usuario(): Usuario
    {
        return Usuario::where('nombre_usuario', 'demo')->firstOrFail();
    }

    private function clienteData(array $changes = []): array
    {
        return array_replace(['nombre' => 'Luis', 'apellido' => 'Prueba', 'telefono' => '809-555-0199', 'correo' => 'luis@example.com'], $changes);
    }

    private function citaData(array $changes = []): array
    {
        return array_replace($this->clienteData(), [
            'marca' => 'Honda', 'modelo' => 'Civic', 'anio' => 2020, 'placa' => 'TEST-002',
            'id_servicio' => Servicio::where('activo', true)->firstOrFail()->id_servicio,
            'fecha' => now()->addDay()->toDateString(), 'hora' => '10:00', 'comentarios' => 'Prueba funcional.',
        ], $changes);
    }

    public function test_paginas_publicas_y_enlaces_anteriores(): void
    {
        foreach (['/', '/servicios', '/citas', '/login'] as $url) {
            $this->get($url)->assertOk()->assertSee('AutoGest');
        }
        $this->get('/servicios')->assertSee('Precio a consultar')->assertSee('Cambio de aceite');
        $this->get('/servicios.html')->assertRedirect('/servicios');
        $this->get('/citas.html')->assertRedirect('/citas');
        $this->get('/index.html')->assertRedirect('/');
    }

    public function test_rutas_privadas_protegidas_y_binding(): void
    {
        foreach (['/admin', '/admin/clientes', '/admin/clientes/create', '/admin/clientes/1/edit'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
        $this->post('/admin/clientes', $this->clienteData())->assertRedirect('/login');
        $this->put('/admin/clientes/1', $this->clienteData())->assertRedirect('/login');
        $this->delete('/admin/clientes/1')->assertRedirect('/login');
        $this->actingAs($this->usuario())->get('/admin/clientes/99999/edit')->assertNotFound();
        $this->assertDatabaseCount('clientes', 1);
    }

    public function test_login_valido_sesion_y_logout(): void
    {
        $this->assertTrue(Hash::check('AutoGestDemo2026!', $this->usuario()->password_hash));
        $this->withSession(['persistir' => 'sí']);
        $sessionId = session()->getId();
        $this->post('/login', ['usuario' => 'demo', 'password' => 'AutoGestDemo2026!'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->usuario());
        $this->assertNotSame($sessionId, session()->getId());
        $this->get('/admin')->assertOk()->assertSee('Próximas citas')->assertSee('Ana');
        $this->get('/login')->assertRedirect('/admin');
        $token = session()->token();
        $this->post('/logout')->assertRedirect('/login')->assertSessionMissing('persistir');
        $this->assertGuest();
        $this->assertNotSame($token, session()->token());
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/logout')->assertMethodNotAllowed();
    }

    public function test_login_invalido_es_generico_y_limita_intentos(): void
    {
        $this->post('/login', ['usuario' => 'demo', 'password' => 'incorrecta'])->assertSessionHasErrors(['usuario' => 'Usuario o contraseña incorrectos.']);
        $this->post('/login', ['usuario' => 'inexistente', 'password' => 'incorrecta'])->assertSessionHasErrors(['usuario' => 'Usuario o contraseña incorrectos.']);
        $this->assertGuest();
        for ($i = 0; $i < 4; $i++) {
            $this->post('/login', ['usuario' => 'demo', 'password' => 'incorrecta']);
        }
        $this->post('/login', ['usuario' => 'demo', 'password' => 'AutoGestDemo2026!'])->assertSessionHasErrors(['usuario']);
        $this->assertGuest();
        RateLimiter::clear('login:127.0.0.1');
    }

    public function test_crud_clientes_y_validaciones(): void
    {
        $this->actingAs($this->usuario());
        $this->get('/admin/clientes')->assertOk();
        $this->get('/admin/clientes/create')->assertOk();
        $this->post('/admin/clientes', $this->clienteData(['id_cliente' => 99]))->assertRedirect('/admin/clientes');
        $cliente = Cliente::where('correo', 'luis@example.com')->firstOrFail();
        $this->assertNotSame(99, $cliente->id_cliente);
        $this->get('/admin/clientes/'.$cliente->id_cliente.'/edit')->assertOk()->assertSee('Luis');
        $this->put('/admin/clientes/'.$cliente->id_cliente, $this->clienteData(['nombre' => 'Luis Actualizado']))->assertRedirect('/admin/clientes');
        $this->assertDatabaseHas('clientes', ['id_cliente' => $cliente->id_cliente, 'nombre' => 'Luis Actualizado']);
        $this->post('/admin/clientes', $this->clienteData())->assertSessionHasErrors('correo');
        $this->post('/admin/clientes', $this->clienteData(['correo' => 'mal', 'telefono' => 'abc']))->assertSessionHasErrors(['correo', 'telefono']);
        $this->delete('/admin/clientes/'.$cliente->id_cliente)->assertRedirect('/admin/clientes');
        $this->assertDatabaseMissing('clientes', ['id_cliente' => $cliente->id_cliente]);
    }

    public function test_eliminar_cliente_con_citas_se_impide_y_con_vehiculos_sin_citas_funciona(): void
    {
        $demo = Cliente::where('correo', 'cliente@example.com')->firstOrFail();
        $this->actingAs($this->usuario())->from('/admin/clientes')->delete('/admin/clientes/'.$demo->id_cliente)->assertSessionHas('error');
        $this->assertDatabaseCount('clientes', 1);
        $cliente = Cliente::create($this->clienteData());
        Vehiculo::create(['id_cliente' => $cliente->id_cliente, 'marca' => 'Honda', 'modelo' => 'Civic', 'anio' => 2020, 'placa' => 'DELETE-001']);
        $this->get('/admin/clientes?eliminar='.$cliente->id_cliente)->assertSee('Confirmar eliminación');
        $this->delete('/admin/clientes/'.$cliente->id_cliente)->assertRedirect('/admin/clientes');
        $this->assertDatabaseMissing('vehiculos', ['placa' => 'DELETE-001']);
    }

    public function test_cita_persiste_relaciones_y_reutiliza_cliente_y_vehiculo(): void
    {
        $this->post('/citas', $this->citaData())->assertRedirect('/citas')->assertSessionHas('status');
        $cita = Cita::where('comentarios', 'Prueba funcional.')->firstOrFail();
        $this->assertSame('luis@example.com', $cita->cliente->correo);
        $this->assertSame('TEST-002', $cita->vehiculo->placa);
        $this->assertSame($cita->id_cliente, $cita->vehiculo->id_cliente);
        $this->assertNotNull($cita->servicio);
        $this->assertNull($cita->id_usuario);
        $this->actingAs($this->usuario())->post('/citas', $this->citaData(['hora' => '11:00']))->assertRedirect('/citas');
        $this->assertDatabaseCount('clientes', 2);
        $this->assertDatabaseCount('vehiculos', 2);
        $this->assertDatabaseCount('citas', 3);
        $this->assertDatabaseHas('citas', ['id_usuario' => $this->usuario()->id_usuario, 'hora' => '11:00']);
    }

    public function test_cita_invalida_no_guarda_datos_y_restaura_input(): void
    {
        $this->from('/citas')->post('/citas', $this->citaData(['fecha' => '2020-01-01', 'hora' => '25:00', 'correo' => 'mal', 'anio' => 1800, 'telefono' => 'abc']))
            ->assertRedirect('/citas')->assertSessionHasErrors(['fecha', 'hora', 'correo', 'anio', 'telefono'])->assertSessionHasInput('nombre', 'Luis');
        $servicio = $this->citaData()['id_servicio'];
        Servicio::where('id_servicio', $servicio)->update(['activo' => false]);
        $this->post('/citas', $this->citaData(['id_servicio' => $servicio]))->assertSessionHasErrors('id_servicio');
        $this->assertDatabaseCount('clientes', 1);
        $this->assertDatabaseCount('vehiculos', 1);
        $this->assertDatabaseCount('citas', 1);
    }

    public function test_conflicto_de_placa_hace_rollback_y_no_sobrescribe_cliente_publicamente(): void
    {
        $this->post('/citas', $this->citaData(['placa' => 'DEMO-001']))->assertSessionHasErrors('placa');
        $this->assertDatabaseMissing('clientes', ['correo' => 'luis@example.com']);
        $this->post('/citas', $this->citaData(['correo' => 'cliente@example.com']))->assertSessionHasErrors('correo');
        $this->assertDatabaseHas('clientes', ['correo' => 'cliente@example.com', 'nombre' => 'Ana']);
        $this->assertDatabaseCount('vehiculos', 1);
        $this->assertDatabaseCount('citas', 1);
    }

    public function test_blade_escapa_datos_y_seeders_son_repetibles(): void
    {
        Cliente::create($this->clienteData(['nombre' => '<script>alert(1)</script>']));
        $this->actingAs($this->usuario())->get('/admin/clientes')->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->seed();
        $this->assertDatabaseCount('usuarios', 1);
        $this->assertDatabaseCount('servicios', 6);
        $this->assertDatabaseCount('clientes', 2);
        $this->assertDatabaseCount('vehiculos', 1);
        $this->assertDatabaseCount('citas', 1);
    }

    public function test_inputs_de_tipo_incorrecto_se_rechazan_sin_error_de_servidor(): void
    {
        $this->from('/citas')->post('/citas', $this->citaData(['correo' => ['invalido'], 'placa' => ['invalida'], 'id_servicio' => ['invalido'], 'comentarios' => ['invalido']]))
            ->assertRedirect('/citas')->assertSessionHasErrors(['correo', 'placa', 'id_servicio', 'comentarios']);
        $this->get('/citas')->assertOk();
        $this->actingAs($this->usuario())->from('/admin/clientes/create')->post('/admin/clientes', $this->clienteData(['nombre' => ['invalido'], 'correo' => ['invalido']]))
            ->assertSessionHasErrors(['nombre', 'correo']);
        $this->get('/admin/clientes/create')->assertOk();
    }

    public function test_csrf_rechaza_escrituras_sin_token(): void
    {
        // Laravel omite CSRF en testing; cambiar sólo esta bandera permite probar el middleware real.
        $this->app['env'] = 'local';
        foreach (['/login', '/citas', '/admin/clientes', '/logout'] as $url) {
            $this->post($url, [])->assertStatus(419);
        }
    }
}
