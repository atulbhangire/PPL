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
use stdClass;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;

class SendBrowserNotificationController extends ZoneBaseClass
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
			return view('Admin.SendBrowserNotification.sendBrowserNotification',compact('user_url'));
		} else {
			return redirect('/');
		}
	}

	public function save($request)
	{
		$inputData = $request->all();
		if(empty($inputData) || empty($inputData['select_usr']) || empty($inputData['browser_title']) || empty($inputData['browser_url']) || empty($inputData['browser_body'])) {
			return redirect()->back()->with('alert_danger', 'Error! Something went wrong..');

		} else {
			$browser_title = trim($inputData['browser_title']);
			$browser_url = trim($inputData['browser_url']);
			$browser_body = trim($inputData['browser_body']);
			// Data Payload for GCM Browser Notification
            $gcm_message = array("title" => $browser_title,
                                 "body" =>  $browser_body,
                                 "link" =>  $browser_url,
                                 "tag" => "Tag");
			// Send Notifications For GCM
			$gcm_tokens_arr = $this->getRequestedGCMData($inputData['select_usr']);
			if(!empty($gcm_tokens_arr)) {
				$gcm_tokens = [];
                foreach ($gcm_tokens_arr as $gcm_token)
                {
					$temp_obj = json_decode($gcm_token);
					if(isset($temp_obj->keys)){
						$temp_obj1 = new stdClass();
						$temp_obj1->endpoint = $temp_obj->endpoint;
						$temp_obj1->auth = $temp_obj->keys->auth;
						$temp_obj1->p256dh = $temp_obj->keys->p256dh;
		            	$gcm_tokens[] = $temp_obj1;
					}
					else{
		            	$gcm_tokens[] = $temp_obj;
					}
                }
	            // Call Centralized Notifications Function
                $res_gcm = $this->aws->sendGCMBrowserAlerts_Centralized($gcm_tokens,$gcm_message);
			}

			// Sending Safari Notifications
            $payload = [];
            $button = "View"; 
            $l1 = explode("://",$browser_url);
			$link = $l1[1];
			$payload = array("title" => $browser_title,
                            "body" => $browser_body,
                            "clicked" => $link,
                            "action" => $button);
            // Send Notifications For Safari Browser Notifications
            $safari_tokens_arr = $this->getRequestedSafariData($inputData['select_usr']);
			if(!empty($safari_tokens_arr)) {
	            // Call Centralized Notifications Function
                $res_safari = $this->aws->sendSafariBrowserAlerts_Centralized($safari_tokens_arr,$payload);
			}
			
			return redirect()->back()->with('alert_message', 'Browser notifications sent successfully.');
		}

	}

    public function checkUserCount($request){
		$inputData = $request->all();
		if(!empty($inputData) && !empty($inputData['select_usr'])) {
			$gcm_res = $this->getRequestedGCMData($inputData['select_usr']);
			$safari_res = $this->getRequestedSafariData($inputData['select_usr']);

			return count($gcm_res)+count($safari_res);
		}
        return 0;
    }

	public function getRequestedGCMData($category) {	
		if($category == 'all_usr') {
			$res = $this->user_profiles::select('gcm_browser_token')->whereNotNull('gcm_browser_token')->groupBy('gcm_browser_token')->pluck('gcm_browser_token')->toArray();
		} else if($category == 'registered_usr') {
			$res = $this->user_profiles::select('gcm_browser_token')->where('usr_status','Registered')->whereNotNull('gcm_browser_token')->groupBy('gcm_browser_token')->pluck('gcm_browser_token')->toArray();
		} else if($category == 'subscribed_usr') {
			$res = $this->user_profiles::select('gcm_browser_token')->where('usr_status','Subscribed')->whereNotNull('gcm_browser_token')->groupBy('gcm_browser_token')->pluck('gcm_browser_token')->toArray();
		} else if($category == 'expired_usr') {
			$res = $this->user_profiles::select('gcm_browser_token')->where('usr_status','Expired')->whereNotNull('gcm_browser_token')->groupBy('gcm_browser_token')->pluck('gcm_browser_token')->toArray();
		}
		return array_values(array_filter($res)); // removes null and empty elements from array
	}

	public function getRequestedSafariData($category) {	
		if($category == 'all_usr') {
			$res = $this->user_profiles::select('safari_browser_token')->whereNotNull('safari_browser_token')->groupBy('safari_browser_token')->pluck('safari_browser_token')->toArray();
		} else if($category == 'registered_usr') {
			$res = $this->user_profiles::select('safari_browser_token')->where('usr_status','Registered')->whereNotNull('safari_browser_token')->groupBy('safari_browser_token')->pluck('safari_browser_token')->toArray();
		} else if($category == 'subscribed_usr') {
			$res = $this->user_profiles::select('safari_browser_token')->where('usr_status','Subscribed')->whereNotNull('safari_browser_token')->groupBy('safari_browser_token')->pluck('safari_browser_token')->toArray();
		} else if($category == 'expired_usr') {
			$res = $this->user_profiles::select('safari_browser_token')->where('usr_status','Expired')->whereNotNull('safari_browser_token')->groupBy('safari_browser_token')->pluck('safari_browser_token')->toArray();
		}
		return array_values(array_filter($res)); // removes null and empty elements from array
	}
	
}