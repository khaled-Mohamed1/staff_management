<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{

    public function index(): JsonResponse
    {
        try {

            $params=array(
                'token' => 'cir37r145y4w0051'
            );
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance31446/chats?" .http_build_query($params),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $chats = json_decode($response,true);

            foreach ($chats as $chat){
                if(!$chat['isGroup']){
                    $conversation = Conversation::where('chat_id', $chat['id'])->first();
                    if(!$conversation){
                        Conversation::create([
                            'chat_ID' =>  $chat['id'],
                            'name' => $chat['name'],
                        ]);
                    }
                }
            }

            if(auth()->user()->role_id == 1){
                $conversations = Conversation::latest()->get();
            }elseif (auth()->user()->role_id == 2){
                $conversations = Conversation::where(function ($query){
                   $query->where('user_id', auth()->user()->id)
                       ->orWhere('user_id', null);
                })->where(function ($query){
                    $query->where('status', 'انتظار')
                        ->orWhere('status', 'مستمر');
                })->latest()->get();
            }

            return response()->json([
                'status' => true,
                'conversations' => $conversations
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

}
