<?php

namespace App\Services;

use App\User;
use App\Models\UserNotification;
use ExponentPhpSDK\Expo;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $expo;

    public function __construct()
    {
        $this->expo = Expo::normalSetup();
        $this->expo->setAccessToken('N9Di_yznIcRR-ojBht-5YpzUyt3l2eqHXH0yUlDV');
    }

    /**
     * @param $user
     * @param string $title
     * @param string $content
     * @param array $data
     * @param int $type
     * @param null $orderId
     */
    public function pushNotification($user, $title = '', $content = '', $data = [], $type =1)
    {
        try{
            $tokenList = $user->notificationTokens;
            $tokens = [];
            foreach ($tokenList as $token){
                if($token -> status ==  1){
                    if(!empty($token->user_id)){
                        $tokens[] = $token->token;
                        $this->expo->subscribe($token->token ,$token->token);
                    }
                }
            }
            //Save to db
            $userNotification = new UserNotification([
                'user_id' => $user->id,
                'title' => $title,
                'content' => $content,
                'data' => json_encode($data),
                'type' => $type,
                'is_read' => false
            ]);
            $userNotification->save();

            //Chekc if token empty -> returns
            if(empty($tokens)){
                Log::channel('notification')->error('Tokens empty =>'. $user->id);
                return;
            }

            // Build the notification data
            $data = array_merge($data, [
                'title' => $title,
                'content' => $content,
                'type' => $type,
                'id' => $userNotification->id
            ]);

            //Get unread number
            $unreadCount = UserNotification::where('user_id', $user->id)->where('is_read', false)->count();

            $notification = [
                'title'=> strip_tags($title),
                'body' => strip_tags($content),
                'data' => json_encode($data),
                'sound' =>  'default',
                'badge' => $unreadCount,
                'channelId' => 'default'
            ];

            // Subscribe the recipient to the server
            $this->expo->notify($tokens, $notification);
        } catch (\Exception $ex){
            Log::channel('notification')->error('Can not push notification', ['message' =>$ex->getMessage()]);
        }
    }
}
