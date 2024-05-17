<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
     public function sendMessage(Request $request)
    {
        if ($request->ticket_id) {
            $validator = Validator::make($request->all(), [
                'task_id' => "required|exists:jobs,id",
                'from' => "required|exists:users,uuid",
                'message' => 'required'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'to' => "required|exists:users,uuid",
                'from' => "required|exists:users,uuid",
                'message' => 'required_without:attachment',
            ]);
        }

        $errorMessage = implode(', ', $validator->errors()->all());

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'action' =>  $errorMessage,
            ]);
        }



        if ($request->task_id) {
            $chat_message = new Message();
            $chat_message->from = $request->from;
            $chat_message->type = $request->type;
            $chat_message->to = 0;
            $chat_message->task_id = $request->task_id;
            $chat_message->message = $request->message;
            $chat_message->time = strtotime(date('Y-m-d H:i:s'));
            $chat_message->save();
            $chat_message = Message::find($chat_message->id);
        } else {
            $chat_message = new Message();

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $mime = explode('/', $file->getClientMimeType());
                $filename = time() . '-' . uniqid() . '.' . $extension;
                if ($file->move('uploads/user/' . $request->from . '-' . $request->to . '/message/', $filename))
                    $image = '/uploads/user/' . $request->from  . '-' . $request->to . '/message/' . $filename;
                $chat_message->attachment = $image;
            }

            $chat_message->from = $request->from;
            $chat_message->to = $request->to;
            $chat_message->type = $request->type;
            $chat_message->message = $request->message ?: '';
            $chat_message->time = strtotime(date('Y-m-d H:i:s'));
            $find = Message::where('from_to', $request->from . '-' . $request->to)->orWhere('from_to', $request->to . '-' . $request->from)->first();
            $channel = '';
            if ($find) {
                $channel = $find->from_to;
                $chat_message->from_to = $find->from_to;
                Message::where('from_to', $chat_message->from_to)->where('to', $request->from)->where('is_read', 0)->update(['is_read' => 1]);
            } else {
                $channel = '';
                $chat_message->from_to = $request->from . '-' . $request->to;
            }
            $chat_message->save();
            $chat_message = Message::find($chat_message->id);

            $find = Message::find($chat_message->id);
            $user = User::find($request->from);

            $chat_message->user = $user;
           
            // $tokens = UserDevice::where('user_id', $request->to)->where('token', '!=', '')->groupBy('token')->pluck('token')->toArray();
            // FirebaseNotification::handle($tokens, $user->name . ' has send you a message', 'New Message', ['data_id' => $request->from, 'data_image' => $user->image,  'data_name' => $user->name,  'type' => 'message']);

            // $pusher = new Pusher('ec2175fecd86a44cbf83', 'dc0ea8f8a27a34389e7c', 1682390, [
            //     'cluster' => 'us2',
            //     'useTLS' => true,
            // ]);
    
            // $pusher->trigger($chat_message->from_to, 'new-message', $chat_message);

        }



        return response()->json([
            'status' => true,
            'action' => "Message send",
            'data' => $chat_message
        ]);
    }
}
