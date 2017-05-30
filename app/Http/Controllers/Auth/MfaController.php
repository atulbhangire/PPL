<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use PragmaRX\Google2FA\Google2FA;
use App\admin_user;
use DB;
use Session;
use Config;
use App\Http\Controllers\Zone\ZoneRenderer;


class MfaController extends Controller
{
	public function __construct()
	{
		$this->zone = new ZoneRenderer;
	}

	public function showMFAWithQR(Request $request)
	{
		if(Session::has('username'))
		{
			return view('mfaauth');
		}
		else
		{
			return redirect('/');
		}
	}

	public function showMFAWithoutQR(Request $request)
	{
		if(Session::has('username'))
		{
			return view('verify');
		}
		else
		{
			return redirect('/');
		}
	}

	public function showAdminIndex()
	{
		if(Session::has('user_name'))
		{
			$adminAccess = Session::get('admin_access');
			if(count($adminAccess) > 0)
			{
				return $this->zone->renderZone($adminAccess[0]['zone_code']);
			}
			else
			{
				return redirect('Admin/AdminDashboard');
			}
		}
		else
		{
			return redirect('/');
		}
	}

	function requestSuperAdminMFA($id, $username){
    	
		$user =   admin_user::select('username','super_secret')->where('id', $id)->first();
		if($user->super_secret == NULL OR $user->super_secret == "")
		{
			$secretURL = $this->generateSuperAdminSecret($user->username,$id);
			//$this->showMFA($secretURL);
			return $secretURL;
		}
		else
		{
			return 1;
		}

	}

	

    function showMFA($user)
	{
		return view('mfaauth');
	}

	function createMFA()
	{
		
	}

	function verifysuadminmfa(Request $request)
	{
		//print_r($request->all()); exit();
		$su_user_id = $request->super_admin_id;
		$user_secrete = $request->user_input_secret;
		
		
		$user =   admin_user::select('super_secret','username')->where('id', $su_user_id)->first();
		$google2fa = new Google2FA();
		$valid = $google2fa->verifyKey($user->super_secret, $user_secrete);
		//echo $valid;
		if($valid < 1 Or $valid == NULL Or $valid == "")
		{
			$request->session()->flash('error_message', 'Invalid Authentication Key! This incident will be reported!');
			$Message = "Unsuccessful MFA attempt for Super Admin \n\n Unsuccessful MFA entered on Admin Portal \n Invalid Timeslot \n Details are :\n Username :".$user->super_username."\n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
			$result = $this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
			return redirect('Admin/verify-MFA');
			//return redirect()->back();
			//return view('verify',['user_id' => $su_user_id, 'username'=> $user->super_username, 'admin_type'=>'superadmin']);
		}
		else
		{
			$request->session()->forget('user_id');
			$request->session()->forget('username');
			$request->session()->forget('admin_type');
			$request->session()->put('is_super_admin',TRUE);
			return redirect('Admin/SuperAdminDashboard');
			//Session::set('super_id', $su_user_id);
		}
		exit();
		//return $valid;
	}

	function loadHome(Request $request){
		return redirect('Admin/index');
	}

	
	function generateSuperAdminSecret($username, $id)
	{
		
		$google2fa = new Google2FA();

		$secret = $google2fa->generateSecretKey();
	
		$super_secret  = array('super_secret' => $secret );
		//$user =  admin_user::select('super_id')->where('super_id', $id)->first();
		$user1 =  admin_user::first()->where(array('id' => $id));
		$updateNow = $user1->update($super_secret);
		
		$google2faurl = $google2fa->getQRCodeGoogleUrl(
    						'ppcorp.org',
   					 		$username,
    						$super_secret['super_secret']
						);
		return $google2faurl;
	}

	function sns_push_message($identifier, $message)
	{
		$targetARN = NULL;
		$Message = "This is test notification";
		trigger_push_notification($targetARN, $Message);
	}
}
