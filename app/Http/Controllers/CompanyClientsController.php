<?php

namespace App\Http\Controllers;

use App\Models\CompanyClients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyClientsController extends Controller
{
    // Fetch all clients
    public function index()
    {
        try {
            $clients = CompanyClients::all();
            return response()->json([
                'status' => 'success',
                'data' => $clients
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching clients',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store a new client
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clientname' => 'required|string|max:255',
            'clientImageUrl' => 'required|url', // URL validation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client = CompanyClients::create([
                'clientname' => $request->clientname,
                'clientImageUrl' => $request->clientImageUrl,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $client
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error storing client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Show a single client
    public function show($id)
    {
        try {
            $client = CompanyClients::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $client
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update a client
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'clientname' => 'sometimes|required|string|max:255',
            'clientImageUrl' => 'sometimes|url', // URL validation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client = CompanyClients::findOrFail($id);
            $client->update($request->only(['clientname', 'clientImageUrl']));

            return response()->json([
                'status' => 'success',
                'data' => $client
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating client',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a client
    public function destroy($id)
    {
        try {
            $client = CompanyClients::findOrFail($id);
            $client->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Client deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting client',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
