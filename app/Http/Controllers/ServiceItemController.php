<?php

namespace App\Http\Controllers;

use App\Models\ServiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceItemController extends Controller
{
    /**
     * @OA\Get(
     *     path="/service-items",
     *     summary="Get all service items",
     *     tags={"Service Items"},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="companyServices", type="array", @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     ))
     *                 )
     *             )
     *         )
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
        // Fetch all ServiceItems with related CompanyServices
        $serviceItems = ServiceItem::with('companyServices')->get();

        // Format the data using map
        $formattedServiceItems = $serviceItems->map(function ($serviceItem) {
            return [
                'serviceItemId' => $serviceItem->serviceItemId,
                'title' => $serviceItem->title,
                'companyServices' => $serviceItem->companyServices->map(function ($service) {
                    return [
                        'companyServiceId' => $service->companyServiceId,
                        'companyServiceName' => $service->companyServiceName,
                        'companyServiceImageUrl' => $service->companyServiceImageUrl,
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedServiceItems
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
     *     path="/service-items",
     *     summary="Store a new service item",
     *     tags={"Service Items"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", description="Title of the service item"),
     *             @OA\Property(property="companyServiceIds", type="array", @OA\Items(type="integer"), description="Array of related company service IDs")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service item created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="companyServices", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string")
     *                 ))
     *             )
     *         )
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
            'title' => 'required|string|max:255',
            'companyServiceIds' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $serviceItem = ServiceItem::create($request->only(['title']));

            if ($request->has('companyServiceIds')) {
                $serviceItem->companyServices()->sync($request->companyServiceIds);
            }

            return response()->json(['status' => 'success', 'data' => [
                'serviceItemId' => $serviceItem->serviceItemId,
                'title' => $serviceItem->title,
                'companyServices' => $serviceItem->companyServices->map(function ($service) {
                    return [
                        'companyServiceId' => $service->companyServiceId,
                        'companyServiceName' => $service->companyServiceName,
                        'companyServiceImageUrl' => $service->companyServiceImageUrl,
                    ];
                }),
            ]], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/service-items/{id}",
     *     summary="Show a single service item",
     *     tags={"Service Items"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="companyServices", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service item not found"
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
            $serviceItem = ServiceItem::with('companyServices')->findOrFail($id);
            return response()->json(['status' => 'success', 'data' => [
                'serviceItemId' => $serviceItem->serviceItemId,
                'title' => $serviceItem->title,
                'companyServices' => $serviceItem->companyServices->map(function ($service) {
                    return [
                        'companyServiceId' => $service->companyServiceId,
                        'companyServiceName' => $service->companyServiceName,
                        'companyServiceImageUrl' => $service->companyServiceImageUrl,
                    ];
                }),
            ]], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service item not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/service-items/{id}",
     *     summary="Update a service item",
     *     tags={"Service Items"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", description="Title of the service item"),
     *             @OA\Property(property="companyServiceIds", type="array", @OA\Items(type="integer"), description="Array of related company service IDs")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service item updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="companyServices", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string")
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service item not found"
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
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'companyServiceIds' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $serviceItem = ServiceItem::findOrFail($id);
            $serviceItem->update($request->only(['title']));

            if ($request->has('companyServiceIds')) {
                $serviceItem->companyServices()->sync($request->companyServiceIds);
            }

            return response()->json(['status' => 'success', 'data' => [
                'serviceItemId' => $serviceItem->serviceItemId,
                'title' => $serviceItem->title,
                'companyServices' => $serviceItem->companyServices->map(function ($service) {
                    return [
                        'companyServiceId' => $service->companyServiceId,
                        'companyServiceName' => $service->companyServiceName,
                        'companyServiceImageUrl' => $service->companyServiceImageUrl,
                    ];
                }),
            ]], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service item not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/service-items/{id}",
     *     summary="Delete a service item",
     *     tags={"Service Items"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service item deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Service item deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service item not found"
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
            $serviceItem = ServiceItem::findOrFail($id);
            $serviceItem->companyServices()->detach(); // Detach relations
            $serviceItem->delete();
            return response()->json(['status' => 'success', 'message' => 'Service item deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service item not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
