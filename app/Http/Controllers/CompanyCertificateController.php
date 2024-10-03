<?php

namespace App\Http\Controllers;

use Exception;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Certificate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class CompanyCertificateController extends Controller
{
    /**
     * @OA\Get(
     *     path="/company-certificates",
     *     summary="Get all certificates",
     *     tags={"Certificates"},
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function index()
    {
        try {
            $certificates = Certificate::all();
            return response()->json($certificates);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error retrieving certificates', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/company-certificates",
     *     summary="Create a certificate",
     *     tags={"Certificates"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"certificatePhotoUrl", "startat", "endat"},
     *                 @OA\Property(property="certificatePhotoUrl", type="string", format="binary"),
     *                 @OA\Property(property="startat", type="string", format="date"),
     *                 @OA\Property(property="endat", type="string", format="date")
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
        // Validator for request data
        $validator = Validator::make($request->all(), [
            'certificatePhotoUrl' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'startat' => 'required|date',
            'endat' => 'required|date|after:startat',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Upload image to Cloudinary
            $uploadResult = Cloudinary::upload($request->file('certificatePhotoUrl')->getRealPath(), [
                'folder' => 'certificates',
            ]);

            $imageUrl = $uploadResult->getSecurePath();

            // Create certificate entry in the database
            $certificate = Certificate::create([
                'certificateCode' => Str::uuid(),
                'certificatePhotoUrl' => $imageUrl,
                'startat' => $request->startat,
                'endat' => $request->endat,
            ]);

            return response()->json([
                'certificateCode' => $certificate->certificateCode,
                'certificatePhotoUrl' => $imageUrl,
                'startat' => $certificate->startat,
                'endat' => $certificate->endat,
                'invalid' => $certificate->invalid,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'An error occurred while storing the certificate.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/company-certificates/showbycode/{id}",
     *     summary="Get certificate by ID",
     *     tags={"Certificates"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show($id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            return response()->json($certificate);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Certificate not found'], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/company-certificates/{id}",
     *     summary="Update certificate",
     *     tags={"Certificates"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="startat", type="string", format="date"),
     *                 @OA\Property(property="endat", type="string", format="date"),
     *                 @OA\Property(property="certificatePhotoUrl", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function update(Request $request, $id)
    {
        // Validator for update data
        $validator = Validator::make($request->all(), [
            'startat' => 'sometimes|required|date',
            'endat' => 'sometimes|required|date|after:startat',
            'certificatePhotoUrl' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $certificate = Certificate::findOrFail($id);

            // If a new photo is uploaded, replace the existing one
            if ($request->hasFile('certificatePhotoUrl')) {
                // Upload new image to Cloudinary
                $uploadResult = Cloudinary::upload($request->file('certificatePhotoUrl')->getRealPath(), [
                    'folder' => 'certificates',
                ]);
                $imageUrl = $uploadResult->getSecurePath();
                $certificate->certificatePhotoUrl = $imageUrl;
            }

            $certificate->update($request->only('startat', 'endat'));

            return response()->json($certificate);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Certificate not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error updating certificate', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/company-certificates/{id}",
     *     summary="Delete certificate",
     *     tags={"Certificates"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Deleted"),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */
    public function destroy($id)
    {
        try {
            $certificate = Certificate::findOrFail($id);

            // Delete certificate photo from Cloudinary
            $photoUrl = $certificate->certificatePhotoUrl;
            if ($photoUrl) {
                Cloudinary::destroy($photoUrl);
            }

            $certificate->delete();
            return response()->json(['message' => 'Certificate deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Certificate not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error deleting certificate', 'message' => $e->getMessage()], 500);
        }
    }
}
