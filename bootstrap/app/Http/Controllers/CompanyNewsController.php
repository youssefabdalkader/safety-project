<?php

namespace App\Http\Controllers;

use App\Models\CompanyNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class CompanyNewsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/company-news",
     *     summary="Fetch all news",
     *     tags={"Company News"},
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

    /**
     * @OA\Post(
     *     path="/company-news",
     *     summary="Store new news",
     *     tags={"Company News"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"companyNewTitle", "companyNewUrl", "companyNewImageUrl"},
     *             @OA\Property(property="companyNewTitle", type="string", description="Title of the news"),
     *             @OA\Property(property="companyNewUrl", type="string", description="URL of the news"),
     *             @OA\Property(property="companyNewImageUrl", type="string", description="Image URL of the news")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="News created successfully"
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

    /**
     * @OA\Get(
     *     path="/company-news/{id}",
     *     summary="Show a single news item",
     *     tags={"Company News"},
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
     *         description="News not found"
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

    /**
     * @OA\Post(
     *     path="/company-news/{id}",
     *     summary="Update a news item",
     *     tags={"Company News"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="companyNewTitle", type="string", description="Title of the news"),
     *             @OA\Property(property="companyNewUrl", type="string", description="URL of the news"),
     *             @OA\Property(property="companyNewImageUrl", type="string", description="Image URL of the news")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News updated successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News not found"
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

    /**
     * @OA\Delete(
     *     path="/company-news/{id}",
     *     summary="Delete a news item",
     *     tags={"Company News"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="News deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="News not found"
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
