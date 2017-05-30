<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\order_details;
use App\user_profiles;
use Session;
use Config;
use URL;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;

class SendAppNotificationController extends ZoneBaseClass
{
	public function __construct() {
		$this->order_details = new order_details;
		$this->user_profiles = new user_profiles;
		$this->aws = new CustomAwsController;
	}

	public function display($zone_code) {
		if(Session::get('is_admin')) {
			Session::set('zone_name', $zone_code);
			$user_url = Config::get('config_path_vars.site_address');;
			return view('Admin.SendAppNotification.sendAppNotification',compact('user_url'));
		} else {
			return redirect('/');
		}
	}

	public function save($request)
	{
		$inputData = $request->all();
		if(empty($inputData) || empty($inputData['select_usr']) || empty($inputData['app_title']) || empty($inputData['app_url']) || empty($inputData['app_body'])) {
			return redirect()->back()->with('alert_danger', 'Error! Something went wrong..');

		} else {
			$app_title = trim($inputData['app_title']);
			$app_url = trim($inputData['app_url']);
			$app_body = trim($inputData['app_body']);
			// Data Payload for FCM
            $fcm_message = array("title" => $app_title,
                                 "body" =>  $app_body,
                                 "link" =>  $app_url,
                                 "alert_type" => "Member");
			// Send Notifications For Android
			$fcm_tokens_android = $this->getRequestedAndroidData($inputData['select_usr']);
			if(!empty($fcm_tokens_android)) {
				$android_notification_msg = array(
					"title" => $app_title,
                    "body" => $app_body,
                    "sound" => "default",
                    "tag" => "",
                    "icon" => "ic_stat_spt",
                    "color" => "#eb6635",
                    "click_action" => "MainActivity",
                    "body_loc_key" => "",
                 	// "body_loc_args" => "", // Need to give proper JSON
                    "title_loc_key" => "",
                 	// "title_loc_args" => "" // Need to give Proper JSON
                );
	            // Call Centralized Notifications Function
	            $res_android = $this->aws->sendMobileAppAlerts_Centralized($fcm_tokens_android,$android_notification_msg,$fcm_message);
			}
			
            // Send Notifications For iOS
			$fcm_tokens_ios = $this->getRequestedIosData($inputData['select_usr']);
			if(!empty($fcm_tokens_ios)) {
				$ios_notification_msg = array(
					"title" => $app_title,
                    "body" => $app_body,
                    "sound" => "default",
                    "badge" => "",
                    "body_loc_key" => "",
                 	// "body_loc_args" => "", // Need to have proper JSON
                    "title_loc_key" => "",
                 	// "title_loc_args" => "" // Need to have proper JSON
                );
	            // Call Centralized Notifications Function
	            $res_android = $this->aws->sendMobileAppAlerts_Centralized($fcm_tokens_ios,$ios_notification_msg,$fcm_message);
			}
			
			return redirect()->back()->with('alert_message', 'App notifications sent successfully.');
		}

	}

    public function checkUserCount($request){
		$inputData = $request->all();
		// dd($inputData);
		if(!empty($inputData) && !empty($inputData['select_usr'])) {
			$A_res = $this->getRequestedAndroidData($inputData['select_usr']);
			$I_res = $this->getRequestedIosData($inputData['select_usr']);
			// return ($res);
			return count($A_res)+count($I_res);
		}
        return 0;
    }

	public function getRequestedAndroidData($category) {	
		if($category == 'all_usr') {
			$res = $this->user_profiles::select('fcm_token_android')->whereNotNull('fcm_token_android')->groupBy('fcm_token_android')->pluck('fcm_token_android')->toArray();
		} else if($category == 'registered_usr') {
			$res = $this->user_profiles::select('fcm_token_android')->where('usr_status','Registered')->whereNotNull('fcm_token_android')->groupBy('fcm_token_android')->pluck('fcm_token_android')->toArray();
		} else if($category == 'subscribed_usr') {
			$res = $this->user_profiles::select('fcm_token_android')->where('usr_status','Subscribed')->whereNotNull('fcm_token_android')->groupBy('fcm_token_android')->pluck('fcm_token_android')->toArray();
		} else if($category == 'expired_usr') {
			$res = $this->user_profiles::select('fcm_token_android')->where('usr_status','Expired')->whereNotNull('fcm_token_android')->groupBy('fcm_token_android')->pluck('fcm_token_android')->toArray();
		}
		return array_values(array_filter($res)); // removes null and empty elements from array
	}
	public function getRequestedIosData($category) {	
		if($category == 'all_usr') {
			$res = $this->user_profiles::select('fcm_token_ios')->whereNotNull('fcm_token_ios')->whereNull('fcm_token_android')->groupBy('fcm_token_ios')->pluck('fcm_token_ios')->toArray();
		} else if($category == 'registered_usr') {
			$res = $this->user_profiles::select('fcm_token_ios')->where('usr_status','Registered')->whereNotNull('fcm_token_ios')->whereNull('fcm_token_android')->groupBy('fcm_token_ios')->pluck('fcm_token_ios')->toArray();
		} else if($category == 'subscribed_usr') {
			$res = $this->user_profiles::select('fcm_token_ios')->where('usr_status','Subscribed')->whereNotNull('fcm_token_ios')->whereNull('fcm_token_android')->groupBy('fcm_token_ios')->pluck('fcm_token_ios')->toArray();
		} else if($category == 'expired_usr') {
			$res = $this->user_profiles::select('fcm_token_ios')->where('usr_status','Expired')->whereNotNull('fcm_token_ios')->whereNull('fcm_token_android')->groupBy('fcm_token_ios')->pluck('fcm_token_ios')->toArray();
		}
		return array_values(array_filter($res)); // removes null and empty elements from array
	}
	
}