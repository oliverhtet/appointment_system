<?php

namespace Service\API;

use App\Models\Service as ModelsService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Service
{
    public function getServiceAll()
    {
        $services = ModelsService::all();
        return $services;
    }

    public function createRegister($request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer', 
        ]);
        return $user;
    }
    

}

