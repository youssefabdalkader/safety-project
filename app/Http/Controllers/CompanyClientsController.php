<?php

namespace App\Http\Controllers;

use App\Models\CompanyClients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CompanyClientsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/company-clients",
     *     summary="Get all company clients",
     *     tags={"Company Clients"},
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function index()
    {
        try {
            $clientdata = CompanyClients::with('category')->get();
           $clients = $clientdata->map(function ($client) {
            return [ // تأكد من وجود "return" هنا
                'clientId' => $client->clientId,
                'clientImageUrl' => $client->clientImageUrl,
                'clientName' => $client->clientname, // تأكد من استخدام أسماء الحقول الصحيحة
                'categoryId' => $client->categoryId, // إزالة المسافة الزائدة بعد "categoryId"
                'categoryName' => $client->category->title,
            ];
        });
              
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

    /**
     * @OA\Post(
     *     path="/company-clients",
     *     summary="Create a new company client",
     *     tags={"Company Clients"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"clientname", "clientImageUrl", "categoryId"},
     *             @OA\Property(property="clientname", type="string", description="Name of the client"),
     *             @OA\Property(property="clientImageUrl", type="string", description="URL of the client image"),
     *             @OA\Property(property="categoryId", type="integer", description="Category ID of the client")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/company-clients/{id}",
     *     summary="Get a company client by ID",
     *     tags={"Company Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function show($id)
    {
        try {
  
            $client = CompanyClients::with('category')->findOrFail($id);
           

            return response()->json([
                'status' => 'success',
                'data' => [
                'clientId' => $client->clientId,
                'clientImageUrl' => $client->clientImageUrl,
                'clientName' => $client->clientname, // تأكد من استخدام أسماء الحقول الصحيحة
                'categoryId' => $client->categoryId, // إزالة المسافة الزائدة بعد "categoryId"
                'categoryName' => $client->category->title,]
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

/**
 * @OA\Post(
 *     path="/company-clients/{id}",
 *     summary="Update a company client",
 *     tags={"Company Clients"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"clientname", "clientImageUrl", "categoryId"},
 *             @OA\Property(property="clientname", type="string", description="Name of the client", example="John Doe"),
 *             @OA\Property(property="clientImageUrl", type="string", description="URL of the client image", example="http://example.com/image.jpg"),
 *             @OA\Property(property="categoryId", type="integer", description="Category ID of the client", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object", 
 *                 @OA\Property(property="clientId", type="integer", example=1),
 *                 @OA\Property(property="clientname", type="string", example="John Doe"),
 *                 @OA\Property(property="clientImageUrl", type="string", example="http://example.com/image.jpg"),
 *                 @OA\Property(property="categoryId", type="integer", example=1)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Client not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Client not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="An error occurred")
 *         )
 *     )
 * )
 */


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'clientname' => 'sometimes|required|string|max:255',  
            'clientImageUrl' => 'sometimes|required|url', 
            'categoryId' => 'sometimes|required|exists:_category,categoryId', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $client = CompanyClients::findOrFail($id);
            $client->update($request->only(['clientname', 'clientImageUrl', 'categoryId']));
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

    /**
     * @OA\Delete(
     *     path="/company-clients/{id}",
     *     summary="Delete a company client",
     *     tags={"Company Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
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
