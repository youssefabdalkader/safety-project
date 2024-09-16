<?php
namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;


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
            $certificatesWithUrls = $certificates->map(function ($certificate) {
                return [
                    'certificateCode' => $certificate->certificateCode,
                    'certificatePhotoUrl' => Storage::url($certificate->certificatePhotoUrl),
                    'startat' => $certificate->startat,
                    'endat' => $certificate->endat,
                    'invalid' => $certificate->invalid
                ];
            });

            return response()->json($certificatesWithUrls);
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
        $request->validate([
            'certificatePhotoUrl' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'startat' => 'required|date',
            'endat' => 'required|date|after:startat',
        ]);

        $path = $request->file('certificatePhotoUrl')->store('certificates', 'public');

        $certificate = Certificate::create([
            'certificateCode' => Str::uuid(),
            'certificatePhotoUrl' => $path,
            'startat' => $request->startat,
            'endat' => $request->endat,
        ]);

        return response()->json([
            'certificateCode' => $certificate->certificateCode,
            'certificatePhotoUrl' => Storage::url($certificate->certificatePhotoUrl),
            'startat' => $certificate->startat,
            'endat' => $certificate->endat,
            'invalid' => $certificate->invalid

        ], 201);
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
            return response()->json([
                'certificateCode' => $certificate->certificateCode,
                'certificatePhotoUrl' => Storage::url($certificate->certificatePhotoUrl),
                'startat' => $certificate->startat,
                'endat' => $certificate->endat,
                'invalid' => $certificate->invalid

            ]);
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
 *     @OA\Response(response=404, description="Not Found"),
 *     @OA\Response(response=500, description="Internal Server Error")
 * )
 */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'startat' => 'sometimes|required|date',
                'endat' => 'sometimes|required|date|after:startat',
                'certificatePhotoUrl' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $certificate = Certificate::findOrFail($id);

            if ($request->hasFile('certificatePhotoUrl')) {
                Storage::disk('public')->delete($certificate->certificatePhotoUrl);
                $path = $request->file('certificatePhotoUrl')->store('certificates', 'public');
                $certificate->certificatePhotoUrl = $path;
            }

            $certificate->update($request->only('startat', 'endat'));

            return response()->json([
                'certificateCode' => $certificate->certificateCode,
                'certificatePhotoUrl' => Storage::url($certificate->certificatePhotoUrl),
                'startat' => $certificate->startat,
                'endat' => $certificate->endat,
                'invalid' => $certificate->invalid

            ]);
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
            $photoPath = $certificate->certificatePhotoUrl;

            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
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
