<?php

namespace App\Http\Controllers;

use App\Models\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CompanyServiceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/company-services",
     *     summary="Get all company services",
     *     tags={"Company Services"},
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index()
    {
        try {
            $Companyservices = CompanyService::all();

            $Companyservice = $Companyservices->map(function ($service) {
                return [
                    'companyServiceId' => $service->companyServiceId,
                    'companyServiceName' => $service->companyServiceName,
                    'companyServiceImageUrl' => $service->companyServiceImageUrl,
                    'companyServices' => $service->serviceItems->map(function ($serviceItem) {
                        return [
                            'serviceItemId' => $serviceItem->serviceItemId,
                            'title' => $serviceItem->title,
                        ];
                    }),
                ];
            });

            return response()->json($Companyservice, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error retrieving services', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/company-services",
     *     summary="Create a company service",
     *     tags={"Company Services"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"companyServiceName", "companyServiceImageUrl"},
     *                 @OA\Property(property="companyServiceName", type="string"),
     *                 @OA\Property(property="companyServiceImageUrl", type="string", format="binary"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyServiceName' => 'required|string|max:255',
            'companyServiceImageUrl' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $uploadResult = Cloudinary::upload($request->file('companyServiceImageUrl')->getRealPath(), [
                'folder' => 'company_services',
            ]);

            $imageUrl = $uploadResult->getSecurePath();

            $service = CompanyService::create([
                'companyServiceName' => $request->companyServiceName,
                'companyServiceImageUrl' => $imageUrl,
            ]);

            if ($request->has('serviceItemIds')) {
                $service->serviceItems()->attach($request->serviceItemIds);
            }

            return response()->json(['status' => 'success', 'data' => [
                'companyServiceId' => $service->companyServiceId,
                'companyServiceName' => $service->companyServiceName,
                'companyServiceImageUrl' => $service->companyServiceImageUrl,
                'companyServices' => $service->serviceItems->map(function ($serviceItem) {
                    return [
                        'serviceItemId' => $serviceItem->serviceItemId,
                        'title' => $serviceItem->title,
                    ];
                }),
            ]], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error creating company service', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/company-services/{id}",
     *     summary="Get company service by ID",
     *     tags={"Company Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show($id)
    {
        try {
            $Companyservices = CompanyService::with('serviceItems')->findOrFail($id);

            return response()->json(['status' => 'success', 'data' => [
                'companyServiceId' => $Companyservices->companyServiceId,
                'companyServiceName' => $Companyservices->companyServiceName,
                'companyServiceImageUrl' => $Companyservices->companyServiceImageUrl,
                'companyServices' => $Companyservices->serviceItems->map(function ($serviceItem) {
                    return [
                        'serviceItemId' => $serviceItem->serviceItemId,
                        'title' => $serviceItem->title,
                    ];
                }),
            ]], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Company service not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error retrieving company service', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/company-services/{id}",
     *     summary="Update a company service",
     *     tags={"Company Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="companyServiceName", type="string"),
     *                 @OA\Property(property="companyServiceImageUrl", type="string", format="binary"),
     *                 @OA\Property(property="serviceItemIds", type="array", @OA\Items(type="integer"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $service = CompanyService::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'companyServiceName' => 'sometimes|required|string|max:255',
                'companyServiceImageUrl' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
                'serviceItemIds' => 'sometimes|array',
                'serviceItemIds.*' => 'integer|exists:service_items,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($request->hasFile('companyServiceImageUrl')) {
                $uploadResult = Cloudinary::upload($request->file('companyServiceImageUrl')->getRealPath(), [
                    'folder' => 'company_services',
                ]);
                $imageUrl = $uploadResult->getSecurePath();
                $service->companyServiceImageUrl = $imageUrl;
            }

            $service->update($request->only('companyServiceName'));

            if ($request->has('serviceItemIds')) {
                $service->serviceItems()->sync($request->serviceItemIds);
            }

            return response()->json(['status' => 'success', 'data' => [
                'companyServiceId' => $service->companyServiceId,
                'companyServiceName' => $service->companyServiceName,
                'companyServiceImageUrl' => $service->companyServiceImageUrl,
                'companyServices' => $service->serviceItems->map(function ($serviceItem) {
                    return [
                        'serviceItemId' => $serviceItem->serviceItemId,
                        'title' => $serviceItem->title,
                    ];
                }),
            ]], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Company service not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error updating company service', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/company-services/{id}",
     *     summary="Delete a company service",
     *     tags={"Company Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy($id)
    {
        try {
            $service = CompanyService::findOrFail($id);
            $service->delete();

            return response()->json(['status' => 'success', 'message' => 'Company service deleted'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Company service not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error deleting company service', 'message' => $e->getMessage()], 500);
        }
    }
}
