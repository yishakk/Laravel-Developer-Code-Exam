<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::paginate(10);
        return response()->json($vehicles);
    }

    // Add a new vehicle
    public function store(Request $request)
    {
        $validated = $request->validate([
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer|min:1900',
            'license_plate' => 'required|string|unique:vehicles',
        ]);

        $vehicle = Vehicle::create($validated);
        return response()->json($vehicle, 201);
    }

    // Show a specific vehicle
    public function show($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return response()->json($vehicle);
    }

    // Update a specific vehicle
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'make' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer|min:1900',
            'license_plate' => 'required|string|unique:vehicles,license_plate,' . $vehicle->id,
        ]);

        $vehicle->update($validated);
        return response()->json($vehicle);
    }

    // Delete a specific vehicle
    public function destroy($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->delete();
        return response()->json(null, 204);
    }
}
