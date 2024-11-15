<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function index()
    {
        $serviceProviders = ServiceProvider::all();
        return response()->json($serviceProviders);
    }

    // Create a new service provider
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:service_providers,email',
            'phone' => 'required|string|max:15',
        ]);

        $serviceProvider = ServiceProvider::create($request->all());

        return response()->json([
            'message' => 'Service provider created successfully',
            'data' => $serviceProvider,
        ], 201);
    }

    public function show($id)
    {
        $serviceProvider = ServiceProvider::find($id);
    
        if (!$serviceProvider) {
            return response()->json(['message' => 'Service provider not found'], 404);
        }
        
        return response()->json([
            'data' => $serviceProvider,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
        ]);

        $serviceProvider = ServiceProvider::findOrFail($id);
        $serviceProvider->update($request->all());

        return response()->json([
            'message' => 'Service provider updated successfully',
            'data' => $serviceProvider,
        ], 201);
    }

    // Delete a service provider
    public function destroy($id)
    {
        ServiceProvider::destroy($id);
        return response()->json(['message' => 'Service Provider deleted successfully']);
    }
}
