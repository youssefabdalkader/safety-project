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
    public function index()
    {
        try {
            // Retrieve all certificates
            $certificates = Certificate::all();
    
            // Map through each certificate to include the full URL of the image
            $certificatesWithUrls = $certificates->map(function ($certificate) {
                return [
                    'certificateCode' => $certificate->certificateCode,
                    'certificatePhotoUrl' => Storage::url($certificate->certificatePhotoUrl),
                    'startat' => $certificate->startat,
                    'endat' => $certificate->endat,
                ];
            });
    
            return response()->json($certificatesWithUrls);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error retrieving certificates', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'certificatePhotoUrl' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'startat' => 'required|date',
            'endat' => 'required|date|after:startat',
        ]);

        // Handle the uploaded image
        $path = $request->file('certificatePhotoUrl')->store('certificates', 'public');

        // Create the certificate with the path stored in the database
        $certificate = Certificate::create([
            'certificateCode' => Str::uuid(),
            'certificatePhotoUrl' => $path, // Store the relative path
            'startat' => $request->startat,
            'endat' => $request->endat,
        ]);

        // Return the certificate with the full URL of the image
        return response()->json([
            'certificateCode' => $certificate->certificateCode,
            'certificatePhotoUrl' => Storage::url($certificate->certificatePhotoUrl),
            'startat' => $certificate->startat,
            'endat' => $certificate->endat,
        ], 201);
    }

    // Show a certificate
    public function show($id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            return response()->json([
                'certificateCode' => $certificate->certificateCode,
                'certificatePhotoUrl' => Storage::url($certificate->certificatePhotoUrl),
                'startat' => $certificate->startat,
                'endat' => $certificate->endat,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Certificate not found'], 404);
        }
    }

    // Update a certificate
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'startat' => 'sometimes|required|date',
                'endat' => 'sometimes|required|date|after:startat',
                'certificatePhotoUrl' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $certificate = Certificate::findOrFail($id);

            // If a new photo is uploaded
            if ($request->hasFile('certificatePhotoUrl')) {
                // Delete the old photo
                Storage::disk('public')->delete($certificate->certificatePhotoUrl);

                // Store the new photo
                $path = $request->file('certificatePhotoUrl')->store('certificates', 'public');
                $certificate->certificatePhotoUrl = $path;
            }

            // Update the certificate with new data
            $certificate->update($request->only('startat', 'endat'));

            // Return the updated certificate with the full URL of the image
            return response()->json([
                'certificateCode' => $certificate->certificateCode,
                'certificatePhotoUrl' => Storage::url($certificate->certificatePhotoUrl),
                'startat' => $certificate->startat,
                'endat' => $certificate->endat,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Certificate not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error updating certificate', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a certificate
    public function destroy($id)
    {
        try {
            $certificate = Certificate::findOrFail($id);
            // Get the path of the photo
            $photoPath = $certificate->certificatePhotoUrl;

            // Delete the file from storage
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            // Delete the certificate record from the database
            $certificate->delete();

            return response()->json(['message' => 'Certificate deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Certificate not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error deleting certificate', 'message' => $e->getMessage()], 500);
        }
    }
}
