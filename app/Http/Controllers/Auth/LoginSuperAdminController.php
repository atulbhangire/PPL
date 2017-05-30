<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\admin_user;
use Session;

class LoginSuperAdminController extends Controller
{
	public function loginSuperAdmin($username, $password)
	{
		$su_admin_users =  admin_user::select('id')->where('username', $username)->where('password', md5($password))->first();
		if(isset($su_admin_users->id))
		{
			return $su_admin_users->id;
		}
		else
		{
			return FALSE;
		}
	}

	function verifymfa(Request $iprequest){
		return " checking values";
	}
}
