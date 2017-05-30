<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
// use App\admin_users;
// use App\admin_zones;
use Session;
use Crypt;
use Config;
use Carbon\Carbon;
// use App\Http\Controllers\AWS\CustomAwsController;

class SuperAdminController extends Controller
{
	public function __construct()
	{
		/*$this->admin_users = $admin_users;
		$this->admin_zones = $admin_zones;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');*/
	}
	public function showSuperAdminDashboard(Request $request)
	{
		if($request->session()->get('is_super_admin'))
		{
			//$admin_users = $this->admin_users->orderBy('created_at','desc')->get();
			/*$admin_users = $this->admin_users->all();
			$admin_zones = $this->admin_zones::select('zn_zone_code', 'zn_name', 'zn_description')->get();*/
			return view('SuperAdmin.dashboard');
		}
		else
		{
			return redirect('/');
		}
	}
	/*public function showAddAdminView(Request $request)
	{
		if($request->session()->get('is_super_admin'))
		{
			$admin_zones = $this->admin_zones->select('zn_id')->exists();
			if($admin_zones)
			{
				$admin_zones = $this->admin_zones->select('zn_id', 'zn_name', 'zn_zone_code')->get();
			}
			else
			{
				$admin_zones = NULL;
			}
			return view('SuperAdmin.addAdmin', compact('admin_zones'));
		}
		else
		{
			return redirect('/');
		}
	}
	public function resetAdmin($id)
	{
		$data = array(
			'admin_secret' => NULL
		);
		$admin_username = $this->admin_users::select('adm_username')->where(array('adm_user_id' => $id))->first();
		$admin_update = $this->admin_users::first()->where(array('adm_user_id' => $id));
		$updateNow = $admin_update->update($data);
		if($updateNow)
		{
			$Message = "Admin: " . $admin_username->adm_username . " MFA secret key reset successful \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
			$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
			return redirect('/Admin/SuperAdminDashboard')->with('admin_message', 'MFA key for ' . $admin_username->adm_username . ' reset successful.');
		}
		else
		{
			$Message = "Admin: " . $admin_username->adm_username . " MFA secret key reset unsuccessful \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
			$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
			return redirect('/Admin/SuperAdminDashboard')->with('admin_danger', 'MFA key for ' . $admin_username->adm_username . ' reset unsuccessful.');
		}
	}
	public function getAdmin(Request $request)
	{
		$username = $request->input('username');
		$password = $request->input('password');
		$change_password = $request->input('change_password');
		$permissible_ip = $request->input('permissible_ip');
		$checkbox_permissible_days = $request->input('checkbox');
		$permissible_days = implode(",", $checkbox_permissible_days);
		$permissible_time_start = $request->input('permissible_time_start');
		$permissible_time_end = $request->input('permissible_time_end');
		$permissible_timerange = $permissible_time_start . ", " . $permissible_time_end;
		$admin_status = $request->input('admin_status');
		$checkbox_zone = $request->input('checkbox_zone');
		if(!empty($username) and !empty($password) and isset($change_password) and ($change_password >= 0) and !empty($permissible_ip) and isset($permissible_days) and !empty($permissible_timerange) and isset($admin_status))
		{
			if ($this->admin_users::where('adm_username', '=', $username)->exists())
			{
				return redirect('/Admin/SuperAdminDashboard')->with('admin_danger', 'Error! ' . $username . ' already exists.');
			}
		}
		else
		{
			return redirect('/Admin/SuperAdminDashboard')->with('admin_danger', 'Error! Something went wrong.');
		}
		$data = array(
			'username' => $username,
			'password' => $password,
			'change_password' => $change_password,
			'permissible_ip' => $permissible_ip,
			'permissible_days' => $permissible_days,
			'permissible_timerange' => $permissible_timerange,
			'is_active' => $admin_status
		);
		$saved = $this->saveAdmin($data, $checkbox_zone);
		if($saved)
		{
			$Message = "Admin: " . $request->input('username') . " added successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
			$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
			return redirect('/Admin/SuperAdminDashboard')->with('admin_message', $request->input('username') . ' added successfully!');
		}
		else
		{
			return redirect('/Admin/SuperAdminDashboard')->with('admin_danger', $request->input('username') . ' was not added!');
		}
	}
	public function saveAdmin($data, $checkbox_zone)
	{
		$this->admin_users->adm_username = $data['username'];
		$this->admin_users->adm_password = Crypt::encrypt($data['password']);
		$this->admin_users->change_password = $data['change_password'];
		$this->admin_users->last_password_changed = Date('Y-m-d H:i:s');
		$this->admin_users->permissible_ip = $data['permissible_ip'];
		$this->admin_users->permissible_days = $data['permissible_days'];
		$this->admin_users->permissible_timerange = $data['permissible_timerange'];
		$this->admin_users->is_active = $data['is_active'];
		if(!empty($checkbox_zone))
		{
			foreach ($checkbox_zone as $key => $value)
			{
				$this->admin_users->$key = $value;
			}
		}
		if($this->admin_users->save())
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function editAdmin($id)
	{
		$result = $this->admin_users::select('*')->where(array('adm_user_id' => $id))->first();
		if(!empty($result))
		{
			$admin_zones = $this->admin_zones->select('zn_id')->exists();
			if($admin_zones)
			{
				$admin_zones = $this->admin_zones->select('zn_id', 'zn_name', 'zn_zone_code')->get();
				foreach ($admin_zones as $key => $value)
				{
					$zone = 'Zone_' . $value->zn_zone_code;
					Session::flash('Zone_' . $value->zn_zone_code, $result->$zone);
				}
			}
			else
			{
				$admin_zones = NULL;
			}
			Session::flash('edit_admin', 'Edit');
			Session::flash('edit_id', $result->adm_user_id);
			Session::flash('edit_username', $result->adm_username);
			Session::flash('edit_password', Crypt::decrypt($result->adm_password));
			Session::flash('edit_change_password', $result->change_password);
			Session::flash('edit_permissible_ip', $result->permissible_ip);
			Session::flash('edit_permissible_days', $result->permissible_days);
			$permissible_timerange = explode(", ", $result->permissible_timerange);
			Session::flash('edit_permissible_starttime', $permissible_timerange[0]);
			Session::flash('edit_permissible_endtime', $permissible_timerange[1]);
			Session::flash('edit_is_active', $result->is_active);
			Session::flash('edit_last_password_changed', $result->last_password_changed);
			Session::flash('edit_login_ip', $result->login_ip);
			return view('SuperAdmin.addAdmin', compact('admin_zones'));
		}
		else
		{
			return redirect('/Admin/SuperAdminDashboard')->with('admin_danger', 'Error! Something went wrong.');
		}
	}
	public function updateAdmin(Request $request)
	{
		$admin_id = $request->input('admin_id');
		$username_hidden_old = $request->input('username_hidden_old');
		$username = $request->input('username');
		$password = $request->input('password');
		$change_password = $request->input('change_password');
		$permissible_ip = $request->input('permissible_ip');
		$checkbox_permissible_days = $request->input('checkbox');
		$permissible_days = implode(",", $checkbox_permissible_days);
		$permissible_time_start = $request->input('permissible_time_start');
		$permissible_time_end = $request->input('permissible_time_end');
		$permissible_timerange = $permissible_time_start . ", " . $permissible_time_end;
		$admin_status = $request->input('admin_status');
		$checkbox_zone = $request->input('checkbox_zone');
		if(!empty($admin_id) and !empty($username_hidden_old) and !empty($username) and !empty($password) and isset($change_password) and !empty($permissible_ip) and isset($permissible_days) and !empty($permissible_timerange) and isset($admin_status))
		{
			if($username != $username_hidden_old)
			{
				if ($this->admin_users::where('adm_username', '=', $username)->exists())
				{
					return redirect('/Admin/SuperAdminDashboard')->with('admin_danger', 'Error! ' . $username . ' already exists.');
				}
			}
			$data = array(
				'adm_username' => $username,
				'adm_password' => Crypt::encrypt($password),
				'change_password' => $change_password,
				'permissible_ip' => $permissible_ip,
				'permissible_days' => $permissible_days,
				'permissible_timerange' => $permissible_timerange,
				'is_active' => $admin_status
			);
			if(!empty($checkbox_zone))
			{
				$data = array_merge($data, $checkbox_zone);
			}
			$admin_update = admin_users::first()->where(array('adm_user_id' => $admin_id));
			$updateNow = $admin_update->update($data);
			if($updateNow)
			{
				$Message = "Admin: " . $request->input('username') . " updated successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
				$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
				return redirect('/Admin/SuperAdminDashboard')->with('admin_message', $request->input('username') . ' updated successfully!');
			}
			else
			{
				return redirect('/Admin/SuperAdminDashboard')->with('admin_danger', $request->input('username') . ' was not updated.');
			}
		}
		else
		{
			return redirect('/Admin/SuperAdminDashboard')->with('admin_danger', 'Error! Something went wrong.');
		}
	}
	public function deleteAdmin($id)
	{
		$admin_username = $this->admin_users::select('adm_username')->where(array('adm_user_id' => $id))->first();
		if(!empty($admin_username))
		{
			if($this->admin_users::first()->where(array('adm_user_id' => $id))->delete())
			{
				$Message = "Admin: " . $admin_username->adm_username . " deleted successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
				$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
				return redirect('/Admin/SuperAdminDashboard')->with('admin_message', $admin_username->adm_username . ' deleted successfully!');
			}
			else
			{
				return redirect()->route('/Admin/SuperAdminDashboard')->with('admin_danger', $admin_username->adm_username . ' was not deleted.');
			}
		}
		else
		{
			return redirect()->route('/Admin/SuperAdminDashboard')->with('admin_danger', 'Error! Something went wrong.');
		}
	}*/
}