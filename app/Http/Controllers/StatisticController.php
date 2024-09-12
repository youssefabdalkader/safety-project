<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatisticController extends Controller
{
    // Get all statistics
    public function index()
    {
        try {
            $statistics = Statistic::all();
            return response()->json(['status' => true, 'data' => $statistics], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Error fetching statistics'], 500);
        }
    }

    // Create a new statistic
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

    // Show a specific statistic
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

    // Update a specific statistic
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

    // Delete a specific statistic
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
