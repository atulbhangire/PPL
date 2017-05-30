<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\user_alerts;
use Session;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;

class UserAlertController extends ZoneBaseClass	
{
	public function __construct()
	{
		$this->user_alerts = new user_alerts;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}

	public function display($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			return view('Admin.Alert.indexUserAlerts');
		}
		else
		{
			return redirect('/');
		}
	}

	public function getUserAlertData()
	{
		$user_alerts = $this->user_alerts::select('*')->orderBy('created_at', 'DESC')->get();
		$userAlert = '{ "data":'.json_encode($user_alerts). '}';
		return $userAlert;
	}
}
