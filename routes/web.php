<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/setup', function(){
    $credentials = [
        'email' => "admin@admin.com",
        'password' => 'password'
    ];

    // Crear usuario si no existe
    if (!Auth::attempt($credentials)) {
        $user = new \App\Models\User();
        $user->name = "Admin";
        $user->email = $credentials['email'];
        $user->password = Hash::make($credentials['password']);
        $user->save();
    }

    // Intentar autenticación
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $adminToken = $user->createToken('admin-token', ['create', 'update', 'delete']);
        $updateToken = $user->createToken('update-token', ['create', 'update']);
        $basicToken = $user->createToken('basic-token');

        return [
            'admin' => $adminToken->plainTextToken,
            'update' => $updateToken->plainTextToken,
            'basic' => $basicToken->plainTextToken,
        ];
    } else {
        return response()->json(['error' => 'Authentication failed'], 401);
    }
});
