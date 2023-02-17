<?php

namespace App\Http\Controllers\api;

use App\Events\Message\SendMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function sendMessage(Request $request): \Illuminate\Http\JsonResponse
    {

        try {

            $conversation = Conversation::find($request->conversation_id);

            //send message to WhatsApp
            $params=array(
                'token' => 'av1cil01p9exr1l0',
                'to' => $conversation->chat_ID,
                'body' => $request->body
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance32116/messages/chat",
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

            //send message to WhatsApp
            $params=array(
                'token' => 'av1cil01p9exr1l0',
                'to' => $conversation->chat_ID,
                'image' => $path,
                'caption' => $request->caption
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance32116/messages/image",
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

            // Validate the request data
            $this->validate($request, [
                'audio' => 'file|max:16384',
            ]);

            $audioName = Str::random(16) . "." . $request->audio->getClientOriginalExtension();

            // If an audio was uploaded, store it in the file system or cloud storage
            if ($request->hasFile('audio')) {
                Storage::disk('public')->put('audio/' . $audioName, file_get_contents($request->audio));
            }

            $path = 'https://testing.pal-lady.com/storage/app/public/audio/' . $audioName;


            $conversation = Conversation::find($request->conversation_id);

            //send message to WhatsApp
            $params=array(
                'token' => 'av1cil01p9exr1l0',
                'to' => $conversation->chat_ID,
                'audio' => $path,
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance32116/messages/voice",
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

            //send message to WhatsApp
            $params=array(
                'token' => 'av1cil01p9exr1l0',
                'to' => $conversation->chat_ID,
                'filename' => $originalName,
                'document' => $path,
                'caption' => $request->caption
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance32116/messages/document",
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

            //send message to WhatsApp
            $params=array(
                'token' => 'av1cil01p9exr1l0',
                'to' => $conversation->chat_ID,
                'video' => $videoName,
                'document' => $path,
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/instance32116/messages/video",
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
                $createConversation = Conversation::create([
                    'chat_ID' =>  $event['data']['from'],
                    'name' => $event['data']['pushname'],
                    'isReadOnly' => false,
                    'last_time' => $event['data']['time']
                ]);
            }else{
                $conversation->isReadOnly = $event['data']['fromMe'];
                $conversation->last_time = $event['data']['time'];
                $conversation->save();
            }



            if($event['event_type'] = 'message_create' && $event['data']['ack'] == 'server') {
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

            broadcast(new SendMessage($new_message));

        }

    }

}
