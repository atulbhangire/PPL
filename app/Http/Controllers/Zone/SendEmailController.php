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

class SendEmailController extends ZoneBaseClass
{
	public function __construct() {
		$this->order_details = new order_details;
		$this->user_profiles = new user_profiles;
		$this->aws = new CustomAwsController;
	}

	public function display($zone_code) {
		if(Session::get('is_admin')) {
			Session::set('zone_name', $zone_code);
			return view('Admin.SendEmail.sendEmail');
		} else {
			return redirect('/');
		}
	}

	public function save($request)
	{
		$inputData = $request->all();
		if(empty($inputData) || empty($inputData['select_usr']) || empty($inputData['email_subject']) || empty($inputData['email_body'])) {
			return redirect()->back()->with('alert_danger', 'Error! Something went wrong..');

		} else {
			$email_to = $this->getRequestedData($inputData['select_usr']);
			// dd($email_to);
			$email_subject = trim($inputData['email_subject']);
			$email_body = trim($inputData['email_body']);
			
			$res = $this->aws->sendEmail_Centralized($email_to,$email_subject,$email_body);
			return redirect()->back()->with('alert_message', 'Emails sent successfully.');
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
		if($category == 'all_usr') {
			$res = $this->user_profiles::select('usr_email_id')->groupBy('usr_email_id')->pluck('usr_email_id')->toArray();
		} else if($category == 'registered_usr') {
			$res = $this->user_profiles::select('usr_email_id')->where('usr_status','Registered')->groupBy('usr_email_id')->pluck('usr_email_id')->toArray();
		} else if($category == 'subscribed_usr') {
			$res = $this->user_profiles::select('usr_email_id')->where('usr_status','Subscribed')->groupBy('usr_email_id')->pluck('usr_email_id')->toArray();
		} else if($category == 'expired_usr') {
			$res = $this->user_profiles::select('usr_email_id')->where('usr_status','Expired')->groupBy('usr_email_id')->pluck('usr_email_id')->toArray();
		}
		return array_values(array_filter($res)); // removes null and empty elements from array
	}
	
}