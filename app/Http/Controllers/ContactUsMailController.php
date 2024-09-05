<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUsMail;
use Illuminate\Support\Facades\Validator;

class ContactUsMailController extends Controller
{
    // Fetch all messages
    public function index()
    {
        try {
            $mails = ContactUsMail::all();
            return response()->json([
                'status' => 'success',
                'data' => $mails
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store a new message
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userName' => 'required|string|max:255',
            'userPhone' => 'required|string|max:20',
            'userEmail' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mail = ContactUsMail::create($request->all());
            return response()->json([
                'status' => 'success',
                'data' => $mail
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Show a single message
    public function show($id)
    {
        try {
            $mail = ContactUsMail::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $mail
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update a message
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'userName' => 'sometimes|required|string|max:255',
            'userPhone' => 'sometimes|required|string|max:20',
            'userEmail' => 'sometimes|required|email|max:255',
            'message' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mail = ContactUsMail::findOrFail($id);
            $mail->update($request->all());
            return response()->json([
                'status' => 'success',
                'data' => $mail
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a message
    public function destroy($id)
    {
        try {
            $mail = ContactUsMail::findOrFail($id);
            $mail->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Message deleted successfully'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete message',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
