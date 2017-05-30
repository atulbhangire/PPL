<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use Session;
use App\notifications;
use App\member_zone_sections;
use App\user_profiles;
use GuzzleHttp\Client;
use Config;
use Minishlink\WebPush\WebPush;

class NotificationController extends ZoneBaseClass
{
	public function __construct()
	{
		$this->notifications = new notifications;
		$this->member_zone_sections = new member_zone_sections;
		$this->user_profiles = new user_profiles;
		$this->GCM_SEND_ENDPOINT_URL = Config::get('config_path_vars.GCM_SEND_ENDPOINT_URL');
		$this->GCM_API_KEY = Config::get('config_path_vars.GCM_API_KEY');
	}

	public function display($zone_code)
    {
        if(Session::get('is_admin'))
        {
            Session::set('zone_name', $zone_code);
            $member_zone_sections = $this->member_zone_sections::select('sec_id', 'sec_name')->get();
            return view('Admin.Notification.addNotifications', compact('member_zone_sections'));
        }
        else
        {
            return redirect('/');
        }
    }

    public function save($request)
	{
		$section_id = $request->input('section');
		$message = $request->input('message');
		$data = array(
			'section_id' => $section_id,
			'message' => $message
		);
		$data = json_encode($data);
		$this->notifications->data = $data;
		if($this->notifications->save())
		{
			// php web push library
			/*$users = $this->user_profiles::select('gcm_browser_token')->where('m' . $section_id . '_alerts', 1)->where('usr_status', 'Registered')->get();
			$gcm = array(
				'GCM' => $this->GCM_API_KEY
			);
			$data1 = array(
				'title' => $message,
				'body' => $message
			);
			$webPush = new WebPush($gcm);
			$webPush->setAutomaticPadding(false);
			foreach ($users as $user)
			{
				$gcm_browser_token = json_decode($user->gcm_browser_token);
				$userPublicKey = $gcm_browser_token->keys->p256dh;
				$userAuthToken = $gcm_browser_token->keys->auth;
				$notificationArray = array(
					'endpoint' => $gcm_browser_token->endpoint,
					'payload' => json_encode($data1),
					'userPublicKey' => $userPublicKey,
					'userAuthToken' => $userAuthToken
				);
				$webPush->sendNotification(
					$notificationArray['endpoint'],
					$notificationArray['payload'],
					$notificationArray['userPublicKey'],
					$notificationArray['userAuthToken'],
					true // optional (defaults false)
				);
				$webPush->flush();
			}*/
			// laravel socket.io
			/*$notification_id = $this->notifications->id;
			$client = new Client();
			$url = "http://localhost:8001/notify/" . $notification_id;
			$response = $client->post($url);*/
			return redirect()->back();
		}
		else
		{
			return FALSE;
		}
	}
}
