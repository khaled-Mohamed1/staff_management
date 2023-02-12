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

            $params=array(
                'token' => 'stesyr4l776ze4xr'
            );
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance31729/chats?" .http_build_query($params),
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
                            'isReadOnly' => $chat['isReadOnly'],
                            'last_time' => $chat['last_time']
                        ]);
                    }
                }
            }

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

            $params=array(
                'token' => 'stesyr4l776ze4xr',
                'chatId' => $conversation->chat_ID,
                'limit' => '10'
            );
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance31729/chats/messages?" .http_build_query($params),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $messages = json_decode($response,true);


            foreach ($messages as $message){
                $first_message = Message::where('message_id', $message['id'])->first();
                if(!$first_message){
                    if($message['type'] == 'chat'){
                        Message::create([
                            'message_id' => $message['id'],
                            'conversation_id' =>  $conversation->id,
                            'from' => $message['from'],
                            'to' => $message['to'],
                            'body' => $message['body'],
                            'fromMe' => $message['fromMe'],
                            'type' => $message['type'],
                        ]);
                    }
                }
            }

            $cov_messages = Message::where('conversation_id', $conversation->id)->paginate();

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

}
