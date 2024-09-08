<?php 
namespace App\Http\Controllers;

use App\Models\CompanyService;
use App\Models\ServiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyServiceController extends Controller
{
    public function index()
    {
        $services = CompanyService::with('serviceItems')->get();
        return response()->json(['status' => 'success', 'data' => $services], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyServiceName' => 'required|string|max:255',
            'companyServiceImageUrl' => 'required|url',
            'serviceItemIds' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $service = CompanyService::create($request->only(['companyServiceName', 'companyServiceImageUrl']));
            if ($request->has('serviceItemIds')) {
                $service->serviceItems()->sync($request->serviceItemIds);
            }
            return response()->json(['status' => 'success', 'data' => $service], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $service = CompanyService::with('serviceItems')->findOrFail($id);
            return response()->json(['status' => 'success', 'data' => $service], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'companyServiceName' => 'sometimes|required|string|max:255',
            'companyServiceImageUrl' => 'sometimes|required|url',
            'serviceItemIds' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $service = CompanyService::findOrFail($id);
            $service->update($request->only(['companyServiceName', 'companyServiceImageUrl']));
            if ($request->has('serviceItemIds')) {
                $service->serviceItems()->sync($request->serviceItemIds);
            }
            return response()->json(['status' => 'success', 'data' => $service], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $service = CompanyService::findOrFail($id);
            $service->serviceItems()->detach();
            $service->delete();
            return response()->json(['status' => 'success', 'message' => 'Service deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Service not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
