<?php 

namespace App\Http\Controllers;

use App\Models\CompanyService;
use App\Models\ServiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CompanyServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/company-services",
     *     summary="Fetch all company services",
     *     tags={"Company Service"},
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
        // جلب البيانات مع العلاقة
        $services = CompanyService::with('serviceItems')->get();
        
        // استخدام map لتنسيق البيانات قبل الإرجاع
        $formattedServices = $services->map(function ($service) {
            return [
                'companyServiceId' => $service->companyServiceId,
                'companyServiceName' => $service->companyServiceName,
                'companyServiceImageUrl' => $service->companyServiceImageUrl,
                'serviceItems' => $service->serviceItems->map(function ($item) {
                    return [
                        'serviceItemId' => $item->serviceItemId,
                        'title' => $item->title,
                    ];
                }),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $formattedServices
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
     *     path="/company-services",
     *     summary="Store a new company service",
     *     tags={"Company Service"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"companyServiceName", "companyServiceImageUrl"},
     *             @OA\Property(property="companyServiceName", type="string", description="Name of the company service"),
     *             @OA\Property(property="companyServiceImageUrl", type="string", description="Image URL of the company service"),
     *             @OA\Property(property="serviceItemIds", type="array", @OA\Items(type="integer"), description="Array of service item IDs")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Company service created successfully"
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
            'companyServiceName' => 'required|string|max:255',
            'companyServiceImageUrl' => 'required|url',
            'serviceItemIds' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $service = CompanyService::create($request->only(['companyServiceName', 'companyServiceImageUrl']));
            if ($request->has('serviceItemIds')) {
                $service->serviceItems()->sync($request->serviceItemIds);
            }
            return response()->json(['status' => 'success', 'data' => $service], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/company-services/{id}",
     *     summary="Show a single company service",
     *     tags={"Company Service"},
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
     *         description="Company service not found"
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
            $service = CompanyService::with('serviceItems')->findOrFail($id);
            return response()->json(
               [
                'status' => 'success',
                'data' =>[
                    'companyServiceId' => $service->companyServiceId,
                    'companyServiceName' => $service->companyServiceName,
                    'companyServiceImageUrl' => $service->companyServiceImageUrl,
                    'serviceItems' => $service->serviceItems->map(function ($item) {
                        return [
                            'serviceItemId' => $item->serviceItemId,
                            'title' => $item->title,
                        ];
                    }),
                ]], status: 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/company-services/{id}",
     *     summary="Update a company service",
     *     tags={"Company Service"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="companyServiceName", type="string", description="Name of the company service"),
     *             @OA\Property(property="companyServiceImageUrl", type="string", description="Image URL of the company service"),
     *             @OA\Property(property="serviceItemIds", type="array", @OA\Items(type="integer"), description="Array of service item IDs")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company service updated successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company service not found"
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
            'companyServiceName' => 'sometimes|required|string|max:255',
            'companyServiceImageUrl' => 'sometimes|required|url',
            'serviceItemIds' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $service = CompanyService::findOrFail($id);
            $service->update($request->only(['companyServiceName', 'companyServiceImageUrl']));
            if ($request->has('serviceItemIds')) {
                $service->serviceItems()->sync($request->serviceItemIds);
            }
            return response()->json(
                [
                 'status' => 'success',
                 'data' =>[
                     'companyServiceId' => $service->companyServiceId,
                     'companyServiceName' => $service->companyServiceName,
                     'companyServiceImageUrl' => $service->companyServiceImageUrl,
                     'serviceItems' => $service->serviceItems->map(function ($item) {
                         return [
                             'serviceItemId' => $item->serviceItemId,
                             'title' => $item->title,
                         ];
                     }),
                 ]], status: 200);
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/company-services/{id}",
     *     summary="Delete a company service",
     *     tags={"Company Service"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company service deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company service not found"
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
            $service = CompanyService::findOrFail($id);
            $service->serviceItems()->detach();
            $service->delete();
            return response()->json(['status' => 'success', 'message' => 'Service deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
