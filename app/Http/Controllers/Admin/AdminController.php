<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\admin_user;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
//use App\admin_users;

class AdminController extends Controller
{
	public function __construct(admin_user $admin_user)
	{
		$this->admin_user = $admin_user;	
	}
	public function showAdminDashboard(Request $request)
	{
		if($request->session()->get('is_admin'))
		{
			$admin_user = $this->admin_user->all();
			return view('Admin.dashboard');
		}
		else
		{
			return redirect('/');
		}
	}
}
