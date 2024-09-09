<?php

namespace App\Http\Controllers;

use App\Models\CompanyClients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyClientsController extends Controller
{
    
    public function index()
    {
        try {
            $clients = CompanyClients::with('category')->get();
            return response()->json([
                'status' => 'success',
                'data' => $clients
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'clientname' => 'required|string|max:255',
            'clientImageUrl' => 'required|url',
            'categoryId' => 'required|exists:_category,categoryId', 
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $client = CompanyClients::create($request->all());
            return response()->json([
                'status' => 'success',
                'data' => $client
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            $client = CompanyClients::with('category')->findOrFail($id);

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
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'clientname' => 'sometimes|required|string|max:255',
            'clientImageUrl' => 'sometimes|required|url', // تأكد أن الصورة هي رابط صحيح
            'categoryId' => 'sometimes|required|exists:_category,categoryId', // تحقق من أن categoryId موجود
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client = CompanyClients::findOrFail($id);
            $client->update($request->all());
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
                'message' => $e->getMessage()
            ], 500);
        }
    }

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
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
