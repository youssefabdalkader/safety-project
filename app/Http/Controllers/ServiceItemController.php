<?php
namespace App\Http\Controllers;

use App\Models\ServiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceItemController extends Controller
{
    public function index()
    {
        // Fetch all ServiceItems with related CompanyServices
        $serviceItems = ServiceItem::with('companyServices')->get();
        return response()->json(['status' => 'success', 'data' => $serviceItems], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'companyServiceIds' => 'sometimes|array', // Array of related company services
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $serviceItem = ServiceItem::create($request->only(['title']));

            // Attach related CompanyServices if provided
            if ($request->has('companyServiceIds')) {
                $serviceItem->companyServices()->sync($request->companyServiceIds);
            }

            return response()->json(['status' => 'success', 'data' => $serviceItem], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            // Find a ServiceItem by ID with related CompanyServices
            $serviceItem = ServiceItem::with('companyServices')->findOrFail($id);
            return response()->json(['status' => 'success', 'data' => $serviceItem], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service item not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'companyServiceIds' => 'sometimes|array', // Array of related company services
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            // Find ServiceItem by ID
            $serviceItem = ServiceItem::findOrFail($id);
            $serviceItem->update($request->only(['title']));

            // Update related CompanyServices if provided
            if ($request->has('companyServiceIds')) {
                $serviceItem->companyServices()->sync($request->companyServiceIds);
            }

            return response()->json(['status' => 'success', 'data' => $serviceItem], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service item not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $serviceItem = ServiceItem::findOrFail($id);
            $serviceItem->companyServices()->detach(); // Detach relations
            $serviceItem->delete();
            return response()->json(['status' => 'success', 'message' => 'Service item deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service item not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
