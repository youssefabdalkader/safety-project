<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUsMail;
use Illuminate\Support\Facades\Validator;

class ContactUsMailController extends Controller
{
    /**
     * @OA\Get(
     *     path="/contact-us-mails",
     *     summary="Get all messages",
     *     tags={"Contact Us Mails"},
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

    /**
     * @OA\Post(
     *     path="/contact-us-mails",
     *     summary="Store a new message",
     *     tags={"Contact Us Mails"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"userName", "userPhone", "userEmail", "message"},
     *             @OA\Property(property="userName", type="string", description="User's name"),
     *             @OA\Property(property="userPhone", type="string", description="User's phone number"),
     *             @OA\Property(property="userEmail", type="string", format="email", description="User's email"),
     *             @OA\Property(property="message", type="string", description="User's message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message created successfully"
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

    /**
     * @OA\Get(
     *     path="/contact-us-mails/{id}",
     *     summary="Show a single message",
     *     tags={"Contact Us Mails"},
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
     *         description="Message not found"
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

    /**
     * @OA\Post(
     *     path="/contact-us-mails/{id}",
     *     summary="Update a message",
     *     tags={"Contact Us Mails"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="userName", type="string", description="User's name"),
     *             @OA\Property(property="userPhone", type="string", description="User's phone number"),
     *             @OA\Property(property="userEmail", type="string", format="email", description="User's email"),
     *             @OA\Property(property="message", type="string", description="User's message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Message not found"
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

    /**
     * @OA\Delete(
     *     path="/contact-us-mails/{id}",
     *     summary="Delete a message",
     *     tags={"Contact Us Mails"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Message not found"
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
