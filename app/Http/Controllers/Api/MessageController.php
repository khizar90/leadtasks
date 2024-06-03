<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        if ($request->offer_id) {
            $validator = Validator::make($request->all(), [
                'offer_id' => "required|exists:jobs,id",
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



        if ($request->offer_id) {
            $chat_message = new Message();
            $chat_message->from = $request->from ? : '';
            $chat_message->type = $request->type;
            $chat_message->to = $request->to ? : '';
            $chat_message->offer_id = $request->offer_id;
            $chat_message->message = $request->message;
            $chat_message->time = strtotime(date('Y-m-d H:i:s'));
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $mime = explode('/', $file->getClientMimeType());
                $filename = time() . '-' . uniqid() . '.' . $extension;
                if ($file->move('uploads/user/' . $request->offer_id . '/message/', $filename))
                    $image = '/uploads/user/' .$request->offer_id . '/message/' . $filename;
                $chat_message->attachment = $image;
            }

            $chat_message->save();
            $chat_message = Message::find($chat_message->id);
            if($request->from){
                $chat_message->user = User::find($request->from);
            }
            else{
                $chat_message->user = User::find($request->to);
            }
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

    public function conversation(Request $request, $to_id)
    {
        $user = User::find($request->user()->uuid);


        Message::where('from', $to_id)->where('to', $user->uuid)->where('is_read', 0)->update(['is_read' => 1]);

        $messages = Message::where('offer_id', 0)->where('from_to', $user->uuid . '-' . $to_id)->orWhere('from_to', $to_id . '-' . $user->uuid)->latest()->Paginate(25);
        $user1 = User::where('uuid', $to_id)->first();
        foreach ($messages as $message)
            $message->user = $user1;
        return response()->json([
            'status' => true,
            'action' =>  'Conversation',
            'data' => $messages,
        ]);
    }

    public function inbox(Request $request)
    {

        $user = User::find($request->user()->uuid);
        $get = Message::select('from_to')->where('offer_id', 0)->where('from', $user->uuid)->orWhere('to', $user->uuid)->where('offer_id', 0)->groupBy('from_to')->pluck('from_to');
        $arr = [];
        foreach ($get as $item) {

            $message = Message::where('from_to', $item)->latest()->first();
            if ($message) {
                if ($message->from == $user->uuid) {
                    $user1 = User::where('uuid', $message->to)->first();
                }
                if ($message->to == $user->uuid) {
                    $user1 = User::where('uuid', $message->from)->first();
                }
            }
            $unread_count = Message::where('from_to', $item)->where('to', $user->uuid)->where('is_read', 0)->count();
            $obj = new stdClass();
            $obj->message = $message->message;
            $obj->time = $message->time;
            $obj->type = $message->type;
            $obj->is_read = $message->is_read;
            $obj->user = $user1;
            $obj->unread_count = $unread_count;
            $arr[] = $obj;
        }

        $sorted = collect($arr)->sortByDesc('time');

        // ---COMMENTED FOR FUTURE USE IF NEEDED FOR PAGINATION---
        // $sorted = $sorted->forPage($request->page, 20);

        $arr1 = [];
        $count = 0;
        foreach ($sorted as $item) {
            $arr1[] = $item;
        }
        return response()->json([
            'status' => true,
            'action' =>  'Inbox',
            'data' => $arr1
        ]);
    }
}
