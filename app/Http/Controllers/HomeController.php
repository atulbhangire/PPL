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
		return view('Admin/login');
	}

	public function logout(Request $request)
	{
		if(Session::has('is_admin')){
			$user_id = Session::get('user_id');
			$Logout = array('logout_at'=> Carbon::now() );
			$user_obj = admin_users::first()->where(array('adm_user_id' => $user_id));
			$updateNow = $user_obj->update($Logout);
			//$request->session()->flush();
			$session_id = Session::getId();
			$result = DB::table('sessions')->where('id','=',$session_id)->delete();
			return redirect('/Admin');
		}
		else{
			//$request->session()->flush();
			$session_id = Session::getId();
			$result = DB::table('sessions')->where('id','=',$session_id)->delete();
			return redirect('/Admin');
		}
	}

	public function login(Request $request)
	{
		$username = $request->input('username');
		$password = $request->input('password');

		// dd($request);

		if(!empty($username) and !empty($password))
		{
			$validSuperAdminLogin = $this->super_admin->loginSuperAdmin($username, $password);
			// dd($validSuperAdminLogin);
			if($validSuperAdminLogin != FALSE)
			{
				$ret_mfa = $this->mfa->requestSuperAdminMFA($validSuperAdminLogin, $username);
				if($ret_mfa == 1)
				{
					Session::put('user_id', $validSuperAdminLogin);
					Session::put('username', $username);
					Session::put('admin_type', 'superadmin');
					// dd($ret_mfa);
					return redirect('Admin/verify-MFA');
					//return view('verify',['user_id' => $validSuperAdminLogin, 'username'=> $username, 'admin_type'=>'superadmin']);
				}
				else
				{
					Session::put('showurl', $ret_mfa);
					Session::put('user_id', $validSuperAdminLogin);
					Session::put('username', $username);
					Session::put('admin_type', 'superadmin');
					return redirect('Admin/authenticate-MFA');
					//return view('mfaauth',users_access("id" => $validSuperAdminLogin,"username" => $username);
					//$access_details = ['showurl'=> $ret_mfa, 'user_id' => $validSuperAdminLogin];
					//return view('mfaauth',['showurl'=> $ret_mfa, 'user_id' => $validSuperAdminLogin, 'username'=> $username, 'admin_type'=>'superadmin']);
				}
			}
			else{
				$request->session()->flash('error_message', 'Username or Password is wrong.');
				return redirect()->back();
			}
			/*else
			{
				$validAdminID = $this->admin->loginAdmin($username, $password);
				if($validAdminID)
				{
					$activeAdmin = $this->admin->loginActiveAdmin($validAdminID);
					if($activeAdmin)
					{
						$validAdminIP = $this->admin->loginAdminValidIP($validAdminID);
						if($validAdminIP)
						{
							$validAdminTimeSlot = $this->admin->loginAdminValidTimeSlot($validAdminID);
							if($validAdminTimeSlot)
							{
								$ret_mfa = $this->mfa->requestAdminMFA($validAdminID, $username);
					
								if($ret_mfa == 1)
								{
									Session::set('user_id', $validAdminID);
									Session::set('username', $username);
									Session::set('admin_type', 'admin');
									return redirect('Admin/verify-MFA');
									//return view('verify',['user_id' => $validAdminID, 'username'=> $username, 'admin_type'=>'admin']);
								}
								else
								{
									//return view('mfaauth',users_access("id" => $validSuperAdminLogin,"username" => $username);
									//$access_details = ['showurl'=> $ret_mfa, 'user_id' => $validSuperAdminLogin];
									Session::set('showurl', $ret_mfa);
									Session::set('user_id', $validAdminID);
									Session::set('username', $username);
									Session::set('admin_type', 'admin');
									return redirect('Admin/authenticate-MFA');
									//return view('mfaauth',['showurl'=> $ret_mfa, 'user_id' => $validAdminID, 'username'=> $username, 'admin_type'=>'admin']);
								}
							}
							else
							{
								//show invalid timeslot error and trigger SNS
								$request->session()->flash('error_message', 'Invalid timeslot! This incident will be reported!');
								$Message = "Unsuccessful login attempt on Admin Portal \n\n Unsuccessful login attempt on Admin Portal \n Invalid Timeslot \n Details are :\n Username :".$username."\n Password :".$password."\n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
								$result = $this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
								return redirect()->back();
							}
						}
						else
						{
							//show invalid ip error and trigger SNS
							$request->session()->flash('error_message', 'Invalid IP! This incident will be reported!');
							$Message = "Unsuccessful login attempt on Admin Portal \n\n Unsuccessful login attempt on Admin Portal \n Reason : Invalid IP Address \n Details are :\n Username :".$username."\n Password :".$password."\n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
							$result = $this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
							return redirect()->back();
						}
					}
					else
					{
						//show inactive account error and trigger SNS
						$request->session()->flash('error_message', 'Inactive account! This incident will be reported!');
						$Message = "Unsuccessful login attempt on Admin Portal \n\n Unsuccessful login attempt on Admin Portal \n Reason : Inactive account accessed \n Details are :\n Username :".$username."\n Password :".$password."\n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
						$result = $this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
						return redirect()->back();
					}
				}
				else
				{
					$request->session()->flash('error_message', 'Invalid Credentials! This incident will be reported!');
					$Message = "Unsuccessful login attempt on Admin Portal \n\n Unsuccessful login attempt on Admin Portal \n Wrong Username/Password  \n Details are :\n Username :".$username."\n Password :".$password."\n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
					$result = $this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
					return redirect()->back();
					//return redirect()->route('/');
				}
			}*/
		}
		else
		{
			
			$request->session()->flash('error_message', 'Error! Something went wrong!');
			// dd($request);
			return redirect()->back();
		}
	}	
}
