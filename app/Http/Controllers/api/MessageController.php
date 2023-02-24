<?php

namespace App\Http\Controllers\api;

use App\Events\Conversation\ConversationCreate;
use App\Events\Conversation\ConversationHide;
use App\Events\Message\SendMessage;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function sendMessage(Request $request): \Illuminate\Http\JsonResponse
    {

        try {

            $conversation = Conversation::find($request->conversation_id);
            if($conversation->user_id == null){
                $conversation->user_id = auth()->user()->id;
                $conversation->status = 'مستمرة';
                $conversation->save();

                broadcast(new ConversationHide($conversation));

            }


            //send message to WhatsApp
            $params=array(
                'token' => 'ioh2xj5b7nu53gmb',
                'to' => $conversation->chat_ID,
                'body' => $request->body
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance32418/messages/chat",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            return response()->json([
                'status' => true,
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendImage(Request $request): \Illuminate\Http\JsonResponse
    {

        try {

            // Validate the request data
            $this->validate($request, [
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:16384',
            ]);

            $imageName = Str::random(16) . "." . $request->image->getClientOriginalExtension();

            // If an image was uploaded, store it in the file system or cloud storage
            if ($request->hasFile('image')) {
                Storage::disk('public')->put('images/' . $imageName, file_get_contents($request->image));
            }

            $path = 'https://testing.pal-lady.com/storage/app/public/images/' . $imageName;


            $conversation = Conversation::find($request->conversation_id);
            $conversation->user_id = auth()->user()->id;
            $conversation->status = 'مستمرة';
            $conversation->save();

            //send message to WhatsApp
            $params=array(
                'token' => 'ioh2xj5b7nu53gmb',
                'to' => $conversation->chat_ID,
                'image' => $path,
                'caption' => $request->caption
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance32418/messages/image",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            return response()->json([
                'status' => true,
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendVoice(Request $request): \Illuminate\Http\JsonResponse
    {

        try {

            // Validate the request datax
            $this->validate($request, [
                'audio' => 'file|max:16384',
            ]);

            $audioName = Str::random(16) . "." . $request->audio->getClientOriginalExtension();

            // If a video was uploaded, store it in the file system or cloud storage
            if ($request->hasFile('audio')) {
                Storage::disk('public')->put('audio/' . $audioName, file_get_contents($request->audio));
            }

            $path = 'https://testing.pal-lady.com/storage/app/public/audio/' . $audioName;


            $conversation = Conversation::find($request->conversation_id);
            $conversation->user_id = auth()->user()->id;
            $conversation->status = 'مستمرة';
            $conversation->save();

            //send message to WhatsApp
            $params=array(
                'token' => 'ioh2xj5b7nu53gmb',
                'to' => $conversation->chat_ID,
                'audio' => $path,
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance32418/messages/voice",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            return response()->json([
                'status' => true,
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendAudio(Request $request): \Illuminate\Http\JsonResponse
    {

        try {

            // Validate the request data
            $this->validate($request, [
                'audio' => 'file|mimes:mp3,aac,ogg|max:16384',
            ]);

            $audioName = Str::random(16) . "." . $request->audio->getClientOriginalExtension();

            // If a audio was uploaded, store it in the file system or cloud storage
            if ($request->hasFile('audio')) {
                Storage::disk('public')->put('audio/' . $audioName, file_get_contents($request->audio));
            }

            $path = 'https://testing.pal-lady.com/storage/app/public/audio/' . $audioName;


            $conversation = Conversation::find($request->conversation_id);
            $conversation->user_id = auth()->user()->id;
            $conversation->status = 'مستمرة';
            $conversation->save();

            //send message to WhatsApp
            $params=array(
                'token' => 'ioh2xj5b7nu53gmb',
                'to' => $conversation->chat_ID,
                'audio' => $path,
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance32418/messages/audio",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            return response()->json([
                'status' => true,
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendDocument(Request $request): \Illuminate\Http\JsonResponse
    {

        try {

            // Validate the request data
            $this->validate($request, [
                'document' => 'file|mimes:zip,xlsx,csv,txt,pptx,docx,pdf|max:32768',
            ]);

            $originalName = $request->document->getClientOriginalName();

            $documentName = Str::random(16) . "." . $request->document->getClientOriginalExtension();

            // If an image was uploaded, store it in the file system or cloud storage
            if ($request->hasFile('document')) {
                Storage::disk('public')->put('documents/' . $documentName, file_get_contents($request->document));
            }

            $path = 'https://testing.pal-lady.com/storage/app/public/documents/' . $documentName;


            $conversation = Conversation::find($request->conversation_id);
            $conversation->user_id = auth()->user()->id;
            $conversation->status = 'مستمرة';
            $conversation->save();

            //send message to WhatsApp
            $params=array(
                'token' => 'ioh2xj5b7nu53gmb',
                'to' => $conversation->chat_ID,
                'filename' => $originalName,
                'document' => $path,
                'caption' => $request->caption
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance32418/messages/document",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            return response()->json([
                'status' => true,
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendVideo(Request $request): \Illuminate\Http\JsonResponse
    {

        try {

            // Validate the request data
            $this->validate($request, [
                'video' => 'file|mimes:mp4,3gp,mov|max:16384',
            ]);

            $videoName = Str::random(16) . "." . $request->video->getClientOriginalExtension();

            // If a video was uploaded, store it in the file system or cloud storage
            if ($request->hasFile('video')) {
                Storage::disk('public')->put('video/' . $videoName, file_get_contents($request->video));
            }

            $path = 'https://testing.pal-lady.com/storage/app/public/video/' . $videoName;


            $conversation = Conversation::find($request->conversation_id);
            $conversation->user_id = auth()->user()->id;
            $conversation->status = 'مستمرة';
            $conversation->save();

            //send message to WhatsApp
            $params=array(
                'token' => 'ioh2xj5b7nu53gmb',
                'to' => $conversation->chat_ID,
                'video' => $path,
                'caption' => $request->caption,
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance32418/messages/video",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            return response()->json([
                'status' => true,
            ], 200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function webhook(){
        $data = file_get_contents("php://input");
        $event = json_decode($data, true);
        if(isset($event)){
            //Here, you now have event and can process them how you like e.g Add to the database or generate a response
            $file = 'log.txt';
            $data =json_encode($event)."\n";
            file_put_contents($file, $data, FILE_APPEND | LOCK_EX);

            if($event['event_type'] == 'message_received'){
                $conversation = Conversation::where('chat_ID',$event['data']['from'])->first();
            }else{
                $conversation = Conversation::where('chat_ID',$event['data']['to'])->first();
            }

            if(!$conversation){

                if($event['event_type'] == 'message_received'){
                    $us = $event['data']['from'];
                }else{
                    $us = $event['data']['to'];
                }

                //get image of the user
                $params=array(
                    'token' => 'ioh2xj5b7nu53gmb',
                    'chatId' => $us
                );
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.ultramsg.com/instance32418/contacts/image?" .http_build_query($params),
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

                $image = json_decode($response, true);

                $createConversation = Conversation::create([
                    'chat_ID' =>  $event['data']['from'],
                    'name' => $event['data']['pushname'] ?? 'guest',
                    'image' => $image['success'] ?? null,
                    'isReadOnly' => false,
                    'last_time' => $event['data']['time']
                ]);

            }else{
                $conversation->isReadOnly = $event['data']['fromMe'];
                $conversation->last_time = $event['data']['time'];
                $conversation->save();
            }

            if($event['event_type'] == 'message_create' && $event['data']['ack'] == 'server') {
                $user = 1;
            }

            $new_message = Message::create([
                'message_id' => $event['data']['id'],
                'user_id' => $user ?? null,
                'conversation_id' =>  $conversation->id ?? $createConversation->id,
                'from' => $event['data']['from'],
                'to' => $event['data']['to'],
                'body' => $event['data']['body'],
                'media' => $event['data']['media'],
                'fromMe' => $event['data']['fromMe'],
                'type' => $event['data']['type'],
            ]);

            if(!$conversation) {
                broadcast(new ConversationCreate($createConversation));
            }

            broadcast(new SendMessage($new_message));

        }

    }

    public function sendMessageTwo(){
        try{

            $token = 'EAAIK8c4aojYBAEHZBVd7ZABZAsAh0PGZClqYc8jZBkcPGAjPNAXO5LKVH6uKq41PjMbRt9vMeC7EtLHvjwkIGgRZCUuBJkPza9CWYdpPGBbFKS7JbWy4FusR9C0hETjkdLbgEZBZBIZCj7RoxxuEnkK7sq6uo7RUZCi1RwfZBO78rMHCzJBXWcyTG2LH0Eu6ZClsD2KhFicFsYppPNTdrS8U886t';

            $phoneId = '103217839375858';
            $version = 'v15.0';
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => '970567494761',
                'type' => 'text',
                "text" => [
                    "preview_url"=> false,
                    "body"=> "eeewqe"
                ]

            ];

            $message = Http::withToken($token)->post('https://graph.facebook.com/'.$version.'/'.$phoneId.'/messages',
            $payload)->throw()->json();

            return response()->json([
               'success' => true,
               'data' => $message
            ],200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUrl(){
        try{

            $token = 'EAAIK8c4aojYBALcmGhL3jLEEV0MPFYXoKoFtkKAwdtvZAWVM2SIFZApMMmOgZByYpZBnG2zDqOIJaT7jxlMYKpPeZBQMccpZCrFiZCstaZARGp3sLQ9U2Kbm4rwNpikQ8bDZC39Il0TRooPZBlmZC6NnfZCOQmY728hVYSOqTVNGPVW9OcIv3naIqoRHYQidZA3knwCMijSnbBQD2Lqk7YeCyfiZBU';

            $media_id = '753950449645054';
            $version = 'v15.0';
            $payload = [
                'phone_number_id' => '103217839375858',
            ];

            $url = Http::withToken($token)->post('https://graph.facebook.com/'.$version.'/'.$media_id.'/',
                $payload)->throw()->json();

            return response()->json([
                'success' => true,
                'data' => $url
            ],200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadMedia(){
        try{

            $token = 'EAAIK8c4aojYBALcmGhL3jLEEV0MPFYXoKoFtkKAwdtvZAWVM2SIFZApMMmOgZByYpZBnG2zDqOIJaT7jxlMYKpPeZBQMccpZCrFiZCstaZARGp3sLQ9U2Kbm4rwNpikQ8bDZC39Il0TRooPZBlmZC6NnfZCOQmY728hVYSOqTVNGPVW9OcIv3naIqoRHYQidZA3knwCMijSnbBQD2Lqk7YeCyfiZBU';

            $url_media = 'https://lookaside.fbsbx.com/whatsapp_business/attachments/?mid=753950449645054&ext=1677273762&hash=ATuIazCTT9JuX8ZpXhB_F6Utfj_00Tm1BZqn_h_x_DLgeg';
            $version = 'v15.0';

            $download = Http::withToken($token)->get('https://graph.facebook.com/'.$version.'/'.$url_media)->throw()->json();

            return response()->json([
                'success' => true,
                'data' => $download
            ],200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function verifyWebhook(Request $request){
        try {

            $verifyToken = 'wibblewebhook4!';
            $query = $request->query();

            $mode = $query['hub_mode'];
            $token = $query['hub_verify_token'];
            $challenge = $query['hub_challenge'];

            if($mode && $token){
                if($mode === 'subscribe' && $token == $verifyToken){
                    return response($challenge, 200)->header(
                        'Content-Type', 'text/plain'
                    );
                }
            }

            throw new \Exception('Invalid request');

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function processWebhook(Request $request){
        try {

            $bodyContent = json_decode($request->getContent(), true);

            $value = $bodyContent['entry'][0]['changes'][0]['value'];

            if(!empty($value['messages'])){

//                $conversation = Conversation::where('chat_ID',$value['contacts'][0]['wa_jd'])->first();
//                if(!$conversation) {
//                    $createConversation = Conversation::create([
//                        'chat_ID' =>  $value['contacts'][0]['wa-_jd'],
//                        'name' => $value['contacts'][0]['profile']['name'] ?? 'guest',
//                        'image' => null,
//                        'isReadOnly' => false,
//                        'last_time' => $value['messages'][0]['timestamp']
//                    ]);
//                }else{
//                    $conversation->isReadOnly = false;
//                    $conversation->last_time = $value['messages'][0]['timestamp'];
//                    $conversation->save();
//                }
//
//                if($value['messages'][0]['type'] == 'text'){
//                    $body = $value['messages'][0]['text']['body'];
//                }
//
//                $new_message = Message::create([
//                    'message_id' => $value['messages'][0]['id'],
//                    'user_id' => null,
//                    'conversation_id' =>  $conversation->id ?? $createConversation->id,
//                    'from' => $value['messages'][0]['from'],
//                    'to' => $value['metadata']['display_phone_number'],
//                    'body' => $body,
//                    'media' => $event['data']['media'],
//                    'fromMe' => $event['data']['fromMe'],
//                    'type' => $event['data']['type'],
//                ]);


                //Here, you now have event and can process them how you like e.g Add to the database or generate a response
                $file = 'log.txt';
                $data =json_encode($bodyContent)."\n";
                file_put_contents($file, $data, FILE_APPEND | LOCK_EX);

            }

            return response()->json([
                'success' => true
            ],200);

        } catch (\Exception $e) {
            // Return Json Response
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

}
