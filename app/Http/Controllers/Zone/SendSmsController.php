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

class SendSmsController extends ZoneBaseClass
{
	public function __construct() {
		$this->order_details = new order_details;
		$this->user_profiles = new user_profiles;
		$this->aws = new CustomAwsController;
	}

	public function display($zone_code) {
		if(Session::get('is_admin')) {
			Session::set('zone_name', $zone_code);
			return view('Admin.SendSms.sendSms');
		} else {
			return redirect('/');
		}
	}

	public function save($request)
	{
		$inputData = $request->all();
		if(empty($inputData) || empty($inputData['select_usr']) || empty($inputData['sms_body'])) {
			return redirect()->back()->with('alert_danger', 'Error! Something went wrong..');

		} else {
			$mobile_nums = $this->getRequestedData($inputData['select_usr']);
			$processes_mobile_nums = [];
			if(!empty($mobile_nums)) {
				foreach ($mobile_nums as $mobile) {
					$mobile = trim($mobile);
					$processes_mobile_nums[] = str_replace('+', '', $mobile);
				}
				$sms_body = trim($inputData['sms_body']);
				
				$res = $this->aws->sendSMS_Centralized($processes_mobile_nums,$sms_body);
			}
			return redirect()->back()->with('alert_message', 'SMS sent successfully.');
		}

	}

    public function checkUserCount($request){
		$inputData = $request->all();
		// dd($inputData);
		if(!empty($inputData) && !empty($inputData['select_usr'])) {
			$res = $this->getRequestedData($inputData['select_usr']);
			return count($res);
		}
        return 0;
    }

	public function getRequestedData($category) {	

		$res = array();
		if($category == 'all_usr') {
			$res = $this->user_profiles::select(DB::raw('CONCAT(usr_mobile_country_code, "", usr_mobile_number) AS mobile'))->groupBy('mobile')->pluck('mobile')->toArray();
		} else if($category == 'registered_usr') {
			$res = $this->user_profiles::select(DB::raw('CONCAT(usr_mobile_country_code, "", usr_mobile_number) AS mobile'))->where('usr_status','Registered')->groupBy('mobile')->pluck('mobile')->toArray();
		} else if($category == 'subscribed_usr') {
			$res = $this->user_profiles::select(DB::raw('CONCAT(usr_mobile_country_code, "", usr_mobile_number) AS mobile'))->where('usr_status','Subscribed')->groupBy('mobile')->pluck('mobile')->toArray();
		} else if($category == 'expired_usr') {
			$res = $this->user_profiles::select(DB::raw('CONCAT(usr_mobile_country_code, "", usr_mobile_number) AS mobile'))->where('usr_status','Expired')->groupBy('mobile')->pluck('mobile')->toArray();
		}
		return array_values(array_filter($res)); // removes null and empty elements from array
	}
	
}