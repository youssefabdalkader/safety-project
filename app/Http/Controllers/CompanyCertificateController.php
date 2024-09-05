<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyCertificate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyCertificateController extends Controller
{
    // Fetch all certificates
    public function index()
    {
        try {
            $certificates = CompanyCertificate::all()->map(function ($certificate) {
                $certificate->certificatePhotoUrl = Storage::url($certificate->certificatePhotoUrl);
                return $certificate;
            });

            return response()->json([
                'status' => 'success',
                'data' => $certificates
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching certificates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store a new certificate
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'certificatePhotoUrl' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Validate image
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle file upload
            $file = $request->file('certificatePhotoUrl');
            $fileName = time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('public/certificates', $fileName);

            $certificate = CompanyCertificate::create([
                'certificateCode' => Str::uuid()->toString(), // Generate UUID or other unique code
                'certificatePhotoUrl' => Storage::url($filePath), // Store URL path
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $certificate
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while storing the certificate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Show a single certificate
    public function show($id)
    {
        try {
            $certificate = CompanyCertificate::findOrFail($id);
            $certificate->certificatePhotoUrl = Storage::url($certificate->certificatePhotoUrl);

            return response()->json([
                'status' => 'success',
                'data' => $certificate
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the certificate',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show2($code)
    {
        try {
            $certificate = CompanyCertificate::where('certificateCode', $code)->firstOrFail();

            $certificate->certificatePhotoUrl = Storage::url($certificate->certificatePhotoUrl);

            return response()->json([
                'status' => 'success',
                'data' => $certificate
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the certificate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update a certificate
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'certificatePhotoUrl' => 'sometimes|required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $certificate = CompanyCertificate::findOrFail($id);

            if ($request->hasFile('certificatePhotoUrl')) {
                // Handle file upload
                $file = $request->file('certificatePhotoUrl');
                $fileName = time() . '-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('public/certificates', $fileName);

                // Update photo URL
                $certificate->certificatePhotoUrl = Storage::url($filePath);
            }

            $certificate->save();

            return response()->json([
                'status' => 'success',
                'data' => $certificate
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the certificate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a certificate
    public function destroy($id)
    {
        try {
            $certificate = CompanyCertificate::findOrFail($id);
            $certificate->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Certificate deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certificate not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the certificate',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
