<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\LoginSuperAdminController;
use App\Http\Controllers\Auth\MfaController;
use Session;
use Config;
use DB;
use Illuminate\Support\Facades\Redirect;
use App\admin_user;

class HomeController extends Controller
{
	public function __construct()
	{
		$this->super_admin = new LoginSuperAdminController;
		$this->mfa = new MfaController;
	}
	public function index()
	{
		if(Session::get('is_super_admin'))
		{
			// dd(Session::get('_previous.url'));
			return redirect(Session::get('_previous.url'));
		}
		else{
			return view('Admin/login');
		}
	}

	public function logout(Request $request)
	{
		$session_id = Session::getId();
		$result = DB::table('sessions')->where('id','=',$session_id)->delete();
		return redirect('/Admin');
	}

	public function login(Request $request)
	{
		$username = $request->input('username');
		$password = $request->input('password');

		if(!empty($username) and !empty($password))
		{
			$validSuperAdminLogin = $this->super_admin->loginSuperAdmin($username, $password);
			if($validSuperAdminLogin != FALSE)
			{
				$ret_mfa = $this->mfa->requestSuperAdminMFA($validSuperAdminLogin, $username);
				if($ret_mfa == 1)
				{
					Session::put('user_id', $validSuperAdminLogin);
					Session::put('username', $username);
					Session::put('admin_type', 'superadmin');
					return redirect('Admin/verify-MFA');
				}
				else
				{
					Session::put('showurl', $ret_mfa);
					Session::put('user_id', $validSuperAdminLogin);
					Session::put('username', $username);
					Session::put('admin_type', 'superadmin');
					return redirect('Admin/authenticate-MFA');
				}
			}
			else{
				$request->session()->flash('error_message', 'Username or Password is wrong.');
				return redirect()->back();
			}
		}
		else
		{
			
			$request->session()->flash('error_message', 'Error! Something went wrong!');
			return redirect()->back();
		}
	}	
}
