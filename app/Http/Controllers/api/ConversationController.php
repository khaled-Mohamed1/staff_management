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
                $conversations = Conversation::where('user_id', auth()->user()->id)
                        ->orWhere('user_id', null)->orderBy('last_time', 'desc')->get();
                $conversations = Conversation::where(function ($query){
                    $query->where('user_id', auth()->user()->id);
                    $query->orWhere('user_id', null);
                })->where('status', '!=', 'مكتملة')->get();
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

            if(auth()->user()->role_id == 1){
                return response()->json([
                    'status' => true,
                    'conversation' => ConversationResource::make($conversation),
                    'messages' => new MessageResource($cov_messages),
                ], 200);
            }
            if($conversation->user_id == auth()->user()->id || $conversation->user_id == null){
                if($conversation->status != 'مكتملة' || $conversation->status != 'فاشلة'){

                    return response()->json([
                        'status' => true,
                        'conversation' => ConversationResource::make($conversation),
                        'messages' => new MessageResource($cov_messages),
                    ], 200);
                }
            }

            return response()->json([
                'status' => false,
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

                $conversation = Conversation::find($request->conversation_id);

                $conversation->status = $request->status;
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
