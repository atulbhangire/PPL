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

class SendLaravelNotificationController extends ZoneBaseClass
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
			return view('Admin.SendLaravelNotification.sendLaravelNotification',compact('user_url'));
		} else {
			return redirect('/');
		}
	}

	public function save($request)
	{
		$inputData = $request->all();
		if(empty($inputData) || empty($inputData['select_usr']) || empty($inputData['laravel_title']) || empty($inputData['laravel_url']) || empty($inputData['laravel_body'])) {
			return redirect()->back()->with('alert_danger', 'Error! Something went wrong..');

		} else {
			$laravel_title = trim($inputData['laravel_title']);
			$laravel_url = trim($inputData['laravel_url']);
			$laravel_body = trim($inputData['laravel_body']);
			
			// Send Notifications For GCM
			$usr_status = $this->getRequestedStatus($inputData['select_usr']);

			// Store the Notifications in Database Table
	        $msg_id = DB::table('laravel_notifications_messages')->insertGetId(['title' => $laravel_title, 'message' => $laravel_body, 'url' => $laravel_url, 'message_type' => 1]);

	        // For All the Users having enabled this notification - we will save this alert in laravel_notifications table
	        if(empty($usr_status)) {
		        $sql_query = "INSERT INTO laravel_notifications (username, message_id, send_browser_push) SELECT user_profiles.usr_username, $msg_id, user_profiles.send_browser_notifications FROM user_profiles";
		    } else {
		        $sql_query = "INSERT INTO laravel_notifications (username, message_id, send_browser_push) SELECT user_profiles.usr_username, $msg_id, user_profiles.send_browser_notifications FROM user_profiles WHERE user_profiles.usr_status = '".$usr_status."'";
		    }
	        DB::statement($sql_query);


            // Call Centralized Notifications Function
            $res_laravel = $this->aws->sendLaravelAlerts_Centralized($msg_id);
			
			return redirect()->back()->with('alert_message', 'Laravel notifications sent successfully.');
		}

	}

    public function checkUserCount($request){
		$inputData = $request->all();
		if(!empty($inputData) && !empty($inputData['select_usr'])) {
			$gcm_res = $this->getRequestedLaravelData($inputData['select_usr']);
			return $gcm_res;
		}
        return 0;
    }

	public function getRequestedStatus($category) {	
		$usr_status = '';
		if($category == 'registered_usr') {
			$usr_status = 'Registered';
		} else if($category == 'subscribed_usr') {
			$usr_status = 'Subscribed';
		} else if($category == 'expired_usr') {
			$usr_status = 'Expired';
		}
		return $usr_status;
	}

	public function getRequestedLaravelData($category) {	
		if($category == 'all_usr') {
			$res = $this->user_profiles::whereNotNull('usr_username')->count();
		} else if($category == 'registered_usr') {
			$res = $this->user_profiles::where('usr_status','Registered')->whereNotNull('usr_username')->count();
		} else if($category == 'subscribed_usr') {
			$res = $this->user_profiles::where('usr_status','Subscribed')->whereNotNull('usr_username')->count();
		} else if($category == 'expired_usr') {
			$res = $this->user_profiles::where('usr_status','Expired')->whereNotNull('usr_username')->count();
		}
		return $res; // removes null and empty elements from array
	}
	
}