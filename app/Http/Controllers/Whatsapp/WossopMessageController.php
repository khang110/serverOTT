<?php

namespace App\Http\Controllers\Whatsapp;

use App\Lib\PusherFactory;
use App\Http\Controllers\Controller;
use App\Events\SendWossopMessage;
use App\Events\SendPrivateWossopMessage;
use App\Models\WossopMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\FileAttempt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;


class WossopMessageController extends Controller
{

    /**
     * Send new WossopMessage
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function sendMessage(Request $request)
    {

        $new_message = new WossopMessage();

       // $receiver = $request->meta['receiver'];
        $receiver = $request->receiver_id;
        $sender = Auth::id();

        /*Thanh*/
        if ($request->hasFile('file')) {
           $stored_file_id = $this->saveFile($request);
           $new_message->message = $stored_file_id;
           $new_message->is_file = 1;
        }
        else {
            $new_message->message = $request->message;
            $new_message->is_file=0;
        }
        if (isset($request->is_group)&&$request->is_group==1) {
            $new_message->is_group = 1;
        }
        else {
            $new_message->is_group = 0;
        }
        

        $new_message->sender = $sender;
        $new_message->receiver = $receiver;
        $new_message->is_read = 0;

        $new_message->save();

        if($new_message->is_group == 0) {
            PusherFactory::make()->trigger('chat', 'send', ['data' => $new_message]);
        }
        else {
            $chanel = 'chat-group-'.$receiver;
            PusherFactory::make()->trigger($chanel, 'send', ['data' => $new_message]);
        }
        //event(new SendWossopMessage($new_message));

        //event(new SendPrivateWossopMessage(($new_message)));
    }


    /**
     * Fetch messages sent and received by authenticated user from a particular user
     * @param user_id the id of the user in the chatlist
     * @return WossopMessage
     */
    public function fetchUserMessages($user_id,$is_group=0)
    {
        $auth_user_id = Auth::id();

        // If message sent to authenticated user is clicked, set 'is_read' to 1
        WossopMessage::where(['sender' => $user_id, 'receiver' => $auth_user_id])->update(['is_read' => 1]);

        return WossopMessage::where(function ($query) use ($user_id, $auth_user_id, $is_group) {
            $query->where('sender', $user_id)->where('receiver', $auth_user_id)->where('is_group', $is_group);
        })->orWhere(function ($query) use ($user_id, $auth_user_id, $is_group) {
            $query->where('sender', $auth_user_id)->where('receiver', $user_id)->where('is_group', $is_group);
        })->get();
    }

    /**
     * Send new file
     * @param \Illuminate\Http\Request  $request
     * @return id
     */
    public function saveFile(Request $request)
    {


        $file = $request->file('file');
        $origin_name = $file->getClientOriginalName();
        $extension = $file->extension();
        $stored_path =  explode("/", $file->store('public'));
        $content_type = $file->getClientMimeType();

        $file_attempt = new FileAttempt();

        $file_attempt->origin_name = $origin_name;
        $file_attempt->extension = $extension;
        $file_attempt->stored_path = $stored_path[1];
        $file_attempt->content_type= $content_type;
        $file_attempt->save();

        return $file_attempt->id;
    }

    /**
     * Fetch file
     * @param id the id of the file_attempt
     * @return file
     */
    public function fetchFile($id)
    {
      $file_info =FileAttempt::find($id);

      $file = Storage::get('public/'.$file_info->stored_path);
      $response = Response::make($file, 200);
      $response->header('Content-Type', $file_info->content_type);

      if(str_contains($file_info->content_type, 'image'))
        {
            $response->header('Content-Disposition', 'inline; filename="'.$file_info->origin_name.'"');
        }
        else
        {
            $response->header('Content-Disposition', 'attachment; filename="'.$file_info->origin_name.'"');
        }
      return $response;
    }

    /**
     * Fetch history list user  message
     * @param id the id of the user
     * @return
     */
    public function fetchListUserMessages()
    {
         $auth_user_id = Auth::id();

           $sender = DB::table('wossop_messages')
            ->leftjoin('users', 'wossop_messages.receiver', '=', 'users.id')
            ->select("wossop_messages.receiver","users.name")
            ->where("wossop_messages.sender",$auth_user_id);

        $receiver = DB::table("wossop_messages")
            ->leftjoin('users', 'wossop_messages.sender', '=', 'users.id')
            ->select("wossop_messages.id","users.name")
            ->where("wossop_messages.receiver",$auth_user_id)
            ->union($sender)
            ->distinct()
            ->get();

        return $receiver;
    }
}
