<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Usuarios;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UsuariosController extends Controller
{
    public function index()
    {
        return Usuarios::all();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'curp' => 'required|string|max:18|unique:usuarios',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $usuario = Usuarios::create([
            'name' => $request->name,
            'curp' => $request->curp,
            'age' => $this->calculateAgeFromCurp($request->curp),
            'registration_date' => now(),
            'photo_path' => $photoPath,
            'email' => $request->email,
        ]);

        Mail::to($usuario->email)->send(new Mailable($usuario));

        return response()->json(['message' => 'Usuario registrado correctamente', 'usuario' => $usuario], 201);
    }

    private function calculateAgeFromCurp($curp)
    {
        $birthYear = substr($curp, 4, 2);
        $birthMonth = substr($curp, 6, 2);
        $birthDay = substr($curp, 8, 2);

        $birthYear = $birthYear >= date('y') ? '19'.$birthYear : '20'.$birthYear;

        $birthDate = Carbon::createFromFormat('Y-m-d', "$birthYear-$birthMonth-$birthDay");
        return $birthDate->age;
    }

    public function show(string $id)
    {
        $usuario = Usuarios::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email,'.$id,
            // Añadir otras validaciones según los campos
        ]);

        $usuario = Usuarios::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->update($request->all());

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'usuario' => $usuario
        ]);
    }

    public function destroy(string $id)
    {
        $usuario = Usuarios::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        $usuario->delete();

        return response()->json(['message' => 'Usuario borrado exitosamente']);
    }
}
