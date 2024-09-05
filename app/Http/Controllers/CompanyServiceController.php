<?php

namespace App\Http\Controllers;

use App\Models\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyServiceController extends Controller
{
    // Fetch all services
    public function index()
    {
        try {
            $services = CompanyService::all();
            return response()->json([
                'status' => 'success',
                'data' => $services
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching services',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store new service
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyServiceName' => 'required|string|max:255',
            'companyServiceImageUrl' => 'required|url', // URL validation
            'companyServiceDescription' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $service = CompanyService::create($request->all());
            return response()->json([
                'status' => 'success',
                'data' => $service
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error storing service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Show a single service
    public function show($id)
    {
        try {
            $service = CompanyService::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $service
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update a service
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'companyServiceName' => 'sometimes|required|string|max:255',
            'companyServiceImageUrl' => 'sometimes|required|url', // URL validation
            'companyServiceDescription' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $service = CompanyService::findOrFail($id);
            $service->update($request->all());

            return response()->json([
                'status' => 'success',
                'data' => $service
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating service',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a service
    public function destroy($id)
    {
        try {
            $service = CompanyService::findOrFail($id);
            $service->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Service deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting service',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
