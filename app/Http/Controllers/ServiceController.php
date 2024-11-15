<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Service\API\Service as APIService;

class ServiceController extends Controller
{
    private $apiservice;

    public function __construct(APIService $apiservice)
    {
        $this->apiservice = $apiservice;
    }
    // Get all services
    public function index()
    {
        $services = $this->apiservice->getServiceAll();
        return response()->json($services);
    }

    // Create a new service
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'service_provider_id' => 'required|exists:service_providers,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        // Create the service
        $service = Service::create($validatedData);

        // Return a success response
        return response()->json([
            'message' => 'Service created successfully',
            'data' => $service,
        ], 201);
    }


    // Get a specific service by ID
    public function show($id)
    {
        $service = Service::find($id);
        
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        return response()->json($service);
    }

    // Update a service
    public function update(Request $request, $id)
    {
        $service = Service::find($id);
        
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'duration' => 'sometimes|integer',
            'price' => 'sometimes|numeric',
        ]);

        $service->update($validated);

        return response()->json([
            'message' => 'Service updated successfully',
            'data' => $service,
        ]);
    }

    public function destroy($id)
    {
        $service = Service::find($id);
        
        if (!$service) {
            return response()->json(['message' => 'Service not found'], 404);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
}
