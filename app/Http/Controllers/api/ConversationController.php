<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{

    public function index(): JsonResponse
    {
        try {

            if(auth()->user()->role_id == 1){
                $conversations = Conversation::orderBy('last_time', 'desc')->get();
            }elseif (auth()->user()->role_id == 2){
                $conversations = Conversation::where(function ($query){
                    $query->where('user_id', auth()->user()->id)
                        ->orWhere('user_id', null);
                })->where(function ($query){
                    $query->where('status', 'انتظار')
                        ->orWhere('status', 'مستمر');
                })->orderBy('last_time', 'desc')->get();
            }

            return response()->json([
                'status' => true,
                'conversations' => ConversationResource::collection($conversations)
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request): JsonResponse
    {
        try {

            $conversation = Conversation::find($request->conversation_id);

            $cov_messages = Message::where('conversation_id', $conversation->id)->latest()->paginate();

            return response()->json([
                'status' => true,
                'conversation' => ConversationResource::make($conversation),
                'messages' => new MessageResource($cov_messages),
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request): JsonResponse
    {
        try {

            if(auth()->user()->role_id == 1){
                $conversation = Conversation::find($request->conversation_id);

                $conversation->status = 'مكتمل';
                $conversation->save();

                return response()->json([
                    'status' => true,
                    'conversation' => $conversation
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'error_number' => 404,
            ], 401);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function read(Request $request): JsonResponse
    {
        try {

            $conversation = Conversation::find($request->conversation_id);

            $conversation->isReadOnly = true;
            $conversation->save();

            return response()->json([
                'status' => true,
                'conversation' => $conversation
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
