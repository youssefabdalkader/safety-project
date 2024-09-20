<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatisticController extends Controller
{
    /**
     * @OA\Get(
     *     path="/statistics",
     *     summary="Get all statistics",
     *     tags={"Statistics"},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="number", type="integer")
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
            $statistics = Statistic::all();
            return response()->json(['status' => true, 'data' => $statistics], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error fetching statistics'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/statistics",
     *     summary="Create a new statistic",
     *     tags={"Statistics"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "number"},
     *             @OA\Property(property="title", type="string", description="Title of the statistic"),
     *             @OA\Property(property="number", type="integer", description="Number associated with the statistic")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Statistic created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="number", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
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
            'number' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }

        try {
            $statistic = Statistic::create($request->all());
            return response()->json(['status' => true, 'data' => $statistic], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error creating statistic'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/statistics/{id}",
     *     summary="Show a specific statistic",
     *     tags={"Statistics"},
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
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="number", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Statistic not found"
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
            $statistic = Statistic::findOrFail($id);
            return response()->json(['status' => true, 'data' => $statistic], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Statistic not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error fetching statistic'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/statistics/{id}",
     *     summary="Update a specific statistic",
     *     tags={"Statistics"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", description="Title of the statistic"),
     *             @OA\Property(property="number", type="integer", description="Number associated with the statistic")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistic updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="number", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Statistic not found"
     *     ),
     *     @OA\Response(
     *         response=400,
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
            'number' => 'sometimes|required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }

        try {
            $statistic = Statistic::findOrFail($id);
            $statistic->update($request->all());
            return response()->json(['status' => true, 'data' => $statistic], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Statistic not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error updating statistic'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/statistics/{id}",
     *     summary="Delete a specific statistic",
     *     tags={"Statistics"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistic deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Statistic deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Statistic not found"
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
            $statistic = Statistic::findOrFail($id);
            $statistic->delete();
            return response()->json(['status' => true, 'message' => 'Statistic deleted'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => false, 'message' => 'Statistic not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error deleting statistic'], 500);
        }
    }
}
