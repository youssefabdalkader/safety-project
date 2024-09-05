<?php

namespace App\Http\Controllers;

use App\Models\CompanyNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyNewsController extends Controller
{
    // Fetch all news
    public function index()
    {
        try {
            $news = CompanyNews::all();
            return response()->json([
                'status' => 'success',
                'data' => $news
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching news',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store new news
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyNewTitle' => 'required|string|max:255',
            'companyNewUrl' => 'required|url',
            'companyNewImageUrl' => 'required|url', // URL validation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $news = CompanyNews::create([
                'companyNewTitle' => $request->companyNewTitle,
                'companyNewUrl' => $request->companyNewUrl,
                'companyNewImageUrl' => $request->companyNewImageUrl,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $news
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error storing news',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Show a single news item
    public function show($id)
    {
        try {
            $news = CompanyNews::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $news
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'News not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching news',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update a news item
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'companyNewTitle' => 'sometimes|required|string|max:255',
            'companyNewUrl' => 'sometimes|url',
            'companyNewImageUrl' => 'sometimes|url', // URL validation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $news = CompanyNews::findOrFail($id);
            $news->update($request->only(['companyNewTitle', 'companyNewUrl', 'companyNewImageUrl']));

            return response()->json([
                'status' => 'success',
                'data' => $news
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'News not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating news',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a news item
    public function destroy($id)
    {
        try {
            $news = CompanyNews::findOrFail($id);
            $news->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'News deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'News not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting news',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
