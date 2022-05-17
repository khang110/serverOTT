<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Events\SendWossopMessage;
use App\Events\SendPrivateWossopMessage;
use App\Models\OttMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OttMessageController extends Controller
{

    /**
     * Send new WossopMessage
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function sendMessage(Request $request)
    {
        $receiver = $request->meta['receiver'];
        $sender = Auth::id();
        $message = $request->text;

        $new_message = new OttMessage();
        $new_message->sender = 1;
        $new_message->receiver = $receiver;
        $new_message->message = $message;
        $new_message->is_read = 0;

        $new_message->save();

        // event(new SendWossopMessage($new_message));

        event(new SendPrivateWossopMessage(($new_message)));
    }


    /**
     * Fetch messages sent and received by authenticated user from a particular user
     * @param user_id the id of the user in the chatlist
     * @return WossopMessage
     */
    public function fetchUserMessages($user_id)
    {
        $auth_user_id = 1;

        // If message sent to authenticated user is clicked, set 'is_read' to 1
        OttMessage::where(['sender' => $user_id, 'receiver' => $auth_user_id])->update(['is_read' => 1]);

        $messages =  OttMessage::where(function ($query) use ($user_id, $auth_user_id) {
            $query->where('sender', $user_id)->where('receiver', $auth_user_id);
        })->orWhere(function ($query) use ($user_id, $auth_user_id) {
            $query->where('sender', $auth_user_id)->where('receiver', $user_id);
        })->get();
        return $messages;
    }
}
