<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Database\QueryException;

class ClienteController extends Controller
{
    public function index()
    {
        return view('clientes.index', ['clientes' => Cliente::withCount(['vehiculos', 'citas'])->orderByDesc('id_cliente')->paginate(10)]);
    }

    public function create()
    {
        return view('clientes.form', ['cliente' => new Cliente]);
    }

    public function store(ClienteRequest $request)
    {
        Cliente::create($request->validated());

        return redirect()->route('clientes.index')->with('status', 'Cliente registrado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.form', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());

        return redirect()->route('clientes.index')->with('status', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->citas()->exists()) {
            return back()->with('error', 'Este cliente tiene citas relacionadas y no puede eliminarse.');
        }
        try {
            $cliente->delete();
        } catch (QueryException $e) {
            if (in_array((string) $e->getCode(), ['23000', '23503'])) {
                return back()->with('error', 'No se puede eliminar el cliente porque tiene registros relacionados.');
            }
            throw $e;
        }

        return redirect()->route('clientes.index')->with('status', 'Cliente eliminado correctamente.');
    }
}
