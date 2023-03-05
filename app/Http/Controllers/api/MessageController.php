<?php

namespace App\Http\Controllers\api;

use App\Events\Conversation\ConversationCreate;
use App\Events\Conversation\ConversationHide;
use App\Events\Message\SendMessage;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{

    public $token = 'EAAIK8c4aojYBANu7qjevt6vjEZAVSP3BYea2Xj44LrYK4EVZCk3fOOeDxRzrVcPfKGfYAl4whdUWZAkMZBZBxjiM35Iq2n7ridMw6TfT4OmQCYtR6Pwz0X9bdfCKyVYCKNuy9F3wusE676E28nO2Djj27RZB5mEs8WFCUmvyj2ykL1ZCsPsSljL1u68NNI9cftuzidaQfl8TgZDZD';

    public function sendMessage(Request $request): \Illuminate\Http\JsonResponse
    {

        try {

            $setting = Setting::find(1);

            $conversation = Conversation::find($request->conversation_id);
            if($conversation->user_id == null){
                $conversation->user_id = auth()->user()->id;
                $conversation->status = 'مستمرة';
                $conversation->save();
                broadcast(new ConversationHide($conversation));
            }

            $new_message = Message::create([
                'message_id' => null,
                'user_id' => auth()->user()->id,
                'conversation_id' =>  $conversation->id,
                'from' => $setting->company_phone_NO,
                'to' => $conversation->chat_ID,
                'body' => $request->body,
                'media' => null,
                'fromMe' => true,
                'type' => 'text',
            ]);

            broadcast(new SendMessage($new_message));

            //send message to WhatsApp

            $phoneId = '103217839375858';
            $version = 'v15.0';
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $conversation->chat_ID,
                'type' => 'text',
                "text" => [
                    "preview_url"=> false,
                    "body"=> $request->body
                ]
            ];

            $message = Http::withToken($this->token)->post('https://graph.facebook.com/'.$version.'/'.$phoneId.'/messages',
                $payload)->throw()->json();

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

            $setting = Setting::find(1);

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
            if($conversation->user_id == null){
                $conversation->user_id = auth()->user()->id;
                $conversation->status = 'مستمرة';
                $conversation->save();
                broadcast(new ConversationHide($conversation));
            }

            $new_message = Message::create([
                'message_id' => null,
                'user_id' => auth()->user()->id,
                'conversation_id' =>  $conversation->id,
                'from' => $setting->company_phone_NO,
                'to' => $conversation->chat_ID,
                'body' => $request->caption,
                'media' => $path,
                'fromMe' => true,
                'type' => 'image',
            ]);

            broadcast(new SendMessage($new_message));

            //send message to WhatsApp

            $phoneId = '103217839375858';
            $version = 'v15.0';
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $conversation->chat_ID,
                'type' => 'image',
                "image" => [
                    "link" => $path,
                    "caption"=> $request->caption ?? null
                ]
            ];

            $message = Http::withToken($this->token)->post('https://graph.facebook.com/'.$version.'/'.$phoneId.'/messages',
                $payload)->throw()->json();

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

            $setting = Setting::find(1);

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
            if($conversation->user_id == null){
                $conversation->user_id = auth()->user()->id;
                $conversation->status = 'مستمرة';
                $conversation->save();
                broadcast(new ConversationHide($conversation));
            }

            $new_message = Message::create([
                'message_id' => null,
                'user_id' => auth()->user()->id,
                'conversation_id' =>  $conversation->id,
                'from' => $setting->company_phone_NO,
                'to' => $conversation->chat_ID,
                'body' => $request->caption,
                'media' => $path,
                'fromMe' => true,
                'type' => 'audio',
            ]);

            broadcast(new SendMessage($new_message));

            //send message to WhatsApp

            $phoneId = '103217839375858';
            $version = 'v15.0';
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $conversation->chat_ID,
                'type' => 'audio',
                "audio" => [
                    "link" => $path,
                ]
            ];

            $message = Http::withToken($this->token)->post('https://graph.facebook.com/'.$version.'/'.$phoneId.'/messages',
                $payload)->throw()->json();

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

            $setting = Setting::find(1);

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
            if($conversation->user_id == null){
                $conversation->user_id = auth()->user()->id;
                $conversation->status = 'مستمرة';
                $conversation->save();
                broadcast(new ConversationHide($conversation));
            }

            $new_message = Message::create([
                'message_id' => null,
                'user_id' => auth()->user()->id,
                'conversation_id' =>  $conversation->id,
                'from' => $setting->company_phone_NO,
                'to' => $conversation->chat_ID,
                'body' => $request->caption,
                'media' => $path,
                'fromMe' => true,
                'type' => 'document',
            ]);

            broadcast(new SendMessage($new_message));

            //send message to WhatsApp

            $phoneId = '103217839375858';
            $version = 'v15.0';
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $conversation->chat_ID,
                'type' => 'document',
                "document" => [
                    "link" => $path,
                    "caption"=> $request->caption ?? null,
                    "filename" => $originalName
                ]
            ];

            $message = Http::withToken($this->token)->post('https://graph.facebook.com/'.$version.'/'.$phoneId.'/messages',
                $payload)->throw()->json();

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

            $setting = Setting::find(1);

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
            if($conversation->user_id == null){
                $conversation->user_id = auth()->user()->id;
                $conversation->status = 'مستمرة';
                $conversation->save();

                broadcast(new ConversationHide($conversation));

            }

            //send message to WhatsApp
            $new_message = Message::create([
                'message_id' => null,
                'user_id' => auth()->user()->id,
                'conversation_id' =>  $conversation->id,
                'from' => $setting->company_phone_NO,
                'to' => $conversation->chat_ID,
                'body' => $request->caption,
                'media' => $path,
                'fromMe' => true,
                'type' => 'video',
            ]);

            broadcast(new SendMessage($new_message));

            //send message to WhatsApp

            $phoneId = '103217839375858';
            $version = 'v15.0';
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $conversation->chat_ID,
                'type' => 'video',
                "video" => [
                    "link" => $path,
                    "caption"=> $request->caption ?? null
                ]

            ];

            $message = Http::withToken($this->token)->post('https://graph.facebook.com/'.$version.'/'.$phoneId.'/messages',
                $payload)->throw()->json();

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
            $output_filename =  Str::random(16);
            $output_document = Str::random(1);


            $bodyContent = json_decode($request->getContent(), true);


            $value = $bodyContent['entry'][0]['changes'][0]['value'];

            if(!empty($value['messages'])){

                $conversation = Conversation::where('chat_ID',$value['contacts'][0]['wa_id'])->first();


                if(!$conversation) {
                    $createConversation = Conversation::create([
                        'chat_ID' =>  $value['contacts'][0]['wa_id'],
                        'name' => $value['contacts'][0]['profile']['name'] ?? 'guest',
                        'image' => null,
                        'isReadOnly' => false,
                        'last_time' => $value['messages'][0]['timestamp']
                    ]);
                }else{
                    $conversation->isReadOnly = false;
                    $conversation->last_time = $value['messages'][0]['timestamp'];
                    $conversation->save();
                }

                if($value['messages'][0]['type'] == 'text'){
                    $body = $value['messages'][0]['text']['body'];
                }elseif($value['messages'][0]['type'] == 'image'){
                    $body = $value['messages'][0]['image']['caption'] ?? null;
                    $media = 'https://testing.pal-lady.com/public/images/' . $output_filename . '.jpeg';
                }elseif($value['messages'][0]['type'] == 'video'){
                    $body = $value['messages'][0]['video']['caption'] ?? null;
                    $media = 'https://testing.pal-lady.com/public/images/' . $output_filename . '.mp4';
                }elseif($value['messages'][0]['type'] == 'document'){
                    $body = $value['messages'][0]['document']['caption'] ?? null;
                    $media = 'https://testing.pal-lady.com/public/documents/' . $output_document . $value['messages'][0]['document']['filename'];
                }elseif($value['messages'][0]['type'] == 'audio'){
                    $media = 'https://testing.pal-lady.com/public/audios/' . $output_filename . '.ogg';
                }

                $new_message = Message::create([
                    'message_id' => $value['messages'][0]['id'],
                    'user_id' => null,
                    'conversation_id' =>  $conversation->id ?? $createConversation->id,
                    'from' => $value['messages'][0]['from'],
                    'to' => $value['metadata']['display_phone_number'],
                    'body' => $body ?? null,
                    'media' => $media ?? null,
                    'fromMe' => false,
                    'type' => $value['messages'][0]['type'],
                ]);

                if(!$conversation) {
                    broadcast(new ConversationCreate($createConversation));
                }

                broadcast(new SendMessage($new_message));

                if($value['messages'][0]['type'] == 'image'){
                    $media_id = $value['messages'][0]['image']['id'];
                }elseif ($value['messages'][0]['type'] == 'video') {
                    $media_id = $value['messages'][0]['video']['id'];
                }elseif ($value['messages'][0]['type'] == 'document') {
                    $media_id = $value['messages'][0]['document']['id'];
                }elseif ($value['messages'][0]['type'] == 'audio') {
                    $media_id = $value['messages'][0]['audio']['id'];
                }

                $version = 'v15.0';
                $payload = [
                    'phone_number_id' => '103217839375858',
                ];

                $url = Http::withToken($this->token)->get('https://graph.facebook.com/'.$version.'/'.$media_id.'/',
                    $payload)->throw()->json();

                //download media
                $media_url =json_encode($url)."\n";
                $url_media = $url['url'];

                $ch = curl_init($url_media);

                if($value['messages'][0]['type'] == 'image'){
                    $fp = fopen('images/'.$output_filename . '.jpeg', 'wb');
                }elseif ($value['messages'][0]['type'] == 'video') {
                    $fp = fopen('videos/'.$output_filename . '.mp4', 'wb');
                }elseif ($value['messages'][0]['type'] == 'document') {
                    $output_filename = $output_document . $value['messages'][0]['document']['filename'];
                    $fp = fopen('documents/'.$output_filename, 'wb');
                }elseif ($value['messages'][0]['type'] == 'audio') {
                    $fp = fopen('audios/'.$output_filename . '.ogg', 'wb');
                }

                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($ch, CURLOPT_TIMEOUT, 400);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST , "GET");
                curl_setopt($ch,CURLOPT_ENCODING , "");
                curl_setopt($ch,CURLOPT_FILE , $fp);

                $headers    = [];
                $headers[]  = "Authorization: Bearer " . $this->token;
                $headers[]  = "Accept-Language:en-US,en;q=0.5";
                $headers[]  = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36";
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $raw = curl_exec($ch);

                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);

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
