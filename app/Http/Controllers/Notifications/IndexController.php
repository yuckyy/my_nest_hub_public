<?php

namespace App\Http\Controllers\Notifications;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;

class IndexController extends Controller
{

    public function index(Request $request) {
        $start_id = $request['start_id'];
        if ($start_id > 0){
            $notifications = Auth::user()->notifications()
                ->where('notification_id','<',$start_id)
                ->orderBy('notification_id','DESC')
                ->limit(10) //TODO change to 10 or 20 before launch
                ->get();
        } else {
            $notifications = Auth::user()->notifications()
                ->orderBy('notification_id','DESC')
                ->limit(10) //TODO change to 10 or 20 before launch
                ->get();
        }
        $output = [];
        foreach($notifications as $key => $notification){
            $title = isset($notification->data['title']) ? $notification->data['title'] : '';
            $description = isset($notification->data['description']) ? $notification->data['description'] : '';
            if( strlen( $description) > 50) {
                $description = explode( "\n", wordwrap( $description, 50));
                $description = $description[0] . '...';
            }
            //TODO cut title too
            
            $fromUser = User::find($notification->data['creator_user_id']);
            $outputNotification = (object) [
                "notification_id" => $notification->notification_id,
                "url" => $notification->data['url'],
                "title" => $title,
                "description" => $description,
                "read_at" => $notification->read_at,
                "header" =>
                    notificationIcon($notification) .
                    $notification->created_at->format("m/d/Y") . " " .
                    ( empty($fromUser) ? "" : $fromUser->fullName() )
            ];
            $output[] = $outputNotification;
        }
        return response()->json($output);
    }

    public function markAsReadAction(Request $request) {
        $notification_id = $request['notification_id'];
        $notifications = Auth::user()->notifications()
            ->where('notification_id','=',$notification_id)
            ->get();
        $notifications->markAsRead();
        return response()->json($notifications);
    }

}
