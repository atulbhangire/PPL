<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\admin_zones;
use Session;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;

class ZoneController extends Controller
{
	public function __construct(admin_zones $admin_zones)
	{
		$this->admin_zones = $admin_zones;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}
	public function showZone(Request $request)
	{
		if($request->session()->get('is_super_admin'))
		{
			$admin_zones = $this->admin_zones->select('*')->orderBy('zn_zone_code','ASC')->get();
			return view('SuperAdmin.zone',compact('admin_zones'));
		}
		else
		{
			return redirect('/');
		}
		
	}

	public function showAddZoneView(Request $request)
	{
		if($request->session()->get('is_super_admin'))
		{
			return view('SuperAdmin.addZone');
		}
		else
		{
			return redirect('/');
		}
	}

	public function createZone(Request $request)
	{
		if($request->session()->get('is_super_admin'))
		{
			if (admin_zones::where('zn_zone_code', '=', $request->input('zone_code'))->exists()) {
			   Session::flash('error_message_danger', 'Unable to add new zone! Zone code Exists');
			}
			elseif(admin_zones::where('zn_name', '=', $request->input('zone_name'))->exists()){
				Session::flash('error_message_danger', 'Unable to add new zone! Zone name Exists');
			}
			elseif(admin_zones::where('zn_controller', '=', $request->input('zone_controller'))->exists()){
				Session::flash('error_message_danger', 'Unable to add new zone! Zone controller Exists');
			}else{
					$data = array(
					'zone_code' => $request->input('zone_code'),
					'zone_name' => $request->input('zone_name'),
					'zone_description' => $request->input('zone_description'),
					'zone_controller' => $request->input('zone_controller')
				);
				$success = $this->addZoneColumn($request->input('zone_code'));
				if($success){
					$result = $this->saveZone($data);
					if($result){
						Session::flash('error_message', 'Zone added successfully!');
						$Message = "Zone: " . $request->input('zone_name') . " added successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
						$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
					}
					else{
						Session::flash('error_message_danger', 'Unable to add new zone!');
					}
				}
				else{
					Session::flash('error_message_danger', 'Unable to add new zone!');
				}
				
			}
			return redirect('/Admin/SuperAdminZone');
		}
		else
		{
			return redirect('/');
		}
	}

	public function addZoneColumn($zoneCode){
		$result = DB::statement( 'ALTER TABLE admin_users ADD Zone_'.$zoneCode.' tinyint(1) default 0' );
		if($result){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function updateZoneColumn($zoneCode,$newCode){
		$result = DB::statement( 'ALTER TABLE admin_users change Zone_'.$zoneCode.' Zone_'.$newCode.' tinyint(1) default 0' );
		if($result){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function deleteZoneColumn($zoneCode){
		$result = DB::statement( 'ALTER TABLE admin_users DROP Zone_'.$zoneCode);
		if($result){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function saveZone($data)
	{
		$this->admin_zones->zn_zone_code = $data['zone_code'];
		$this->admin_zones->zn_name = $data['zone_name'];
		$this->admin_zones->zn_description = $data['zone_description'];
		$this->admin_zones->zn_controller = $data['zone_controller'];
		
		if($this->admin_zones->save()){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

	public function updateZone(Request $request,$id)
	{
		if(!empty($request))
		{
			if($request->session()->get('is_super_admin'))
			{
				$zone_obj = admin_zones::select('zn_id','zn_zone_code','zn_name','zn_controller','zn_description')->where('zn_id', $id)->first();
				Session::flash('edit_zone', TRUE);
				Session::flash('edit_zone_id', $id);
				Session::flash('edit_zone_code', $zone_obj->zn_zone_code);
				Session::flash('edit_zone_name', $zone_obj->zn_name);
				Session::flash('edit_zone_controller', $zone_obj->zn_controller);
				Session::flash('edit_zone_description', $zone_obj->zn_description);
				return view('SuperAdmin.addZone');
			}
			else
			{
				return redirect('/');
			}
		}else
		{
			Session::flash('error_message_danger', 'Something went wrong! Please try again');
			return view('SuperAdmin.addZone');
		}
		

		
	}

 	public function is_code_available($code){
 		
 		if (admin_zones::where('zn_zone_code', '=', $code)->exists()) {
				 return TRUE;
		}
		else{
			return FALSE;
		}
 	}

 	public function is_name_available($name){
 		if (admin_zones::where('zn_name', '=', $name)->exists()) {
				 return TRUE;
		}
		else{
			return FALSE;
		}
 	}

 	public function is_controller_available($controller){
 		if (admin_zones::where('zn_controller', '=', $controller)->exists()) {
				 return TRUE;
		}
		else{
			return FALSE;
		}
 	}

	public function modifyZone(Request $request)
	{
		if($request->session()->get('is_super_admin'))
		{
		
			$code ;
			$name ;
			$controller ;

			if($request->input('zone_code_old') != $request->input('zone_code')){
				if ($this->is_code_available($request->input('zone_code'))) {
					$code = FALSE;
				   Session::flash('error_message_danger', 'Unable to edit zone! Zone code Exists');
				}else{
					$code = TRUE;
				}
			}else{
				$code = TRUE;
			}

			if($request->input('zone_name_old') != $request->input('zone_name')){
				if ($this->is_name_available($request->input('zone_name'))) {
					$name = FALSE;
				   Session::flash('error_message_danger', 'Unable to edit zone! Zone name Exists');
				}else{
					$name = TRUE;
				}
			}else{
				$name = TRUE;
			}

			if ($request->input('zone_controller_old') != $request->input('zone_controller')) {
				if ($this->is_controller_available($request->input('zone_controller'))) {
					$controller = FALSE;
				   Session::flash('error_message_danger', 'Unable to edit zone! Zone controller Exists');
				}else{
					$controller = TRUE;
				}
			}else{
				$controller = TRUE;
			}

			if($code and $name and $controller){
				$data = array(
						'zn_zone_code' => $request->input('zone_code'),
						'zn_name' => $request->input('zone_name'),
						'zn_description' => $request->input('zone_description'),
						'zn_controller' => $request->input('zone_controller')
					);

					$alterAdmin = $this->updateZoneColumn($request->input('zone_code_old'),$request->input('zone_code'));
					if($alterAdmin){
						$zone_obj1 = admin_zones::first()->where(array('zn_id' => $request->input('zone_id')));
						$updateNow = $zone_obj1->update($data);
						if($updateNow){
							Session::flash('error_message', 'Zone Updated successfully!');
							$Message = "Zone: " . $request->input('zone_name') . " updated successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
							$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
							$admin_zones = $this->admin_zones->select('*')->orderBy('zn_zone_code','ASC')->get();
							return redirect('/Admin/SuperAdminZone');
						}else{
							Session::flash('error_message_danger', 'Unable to edit zone!');
							$admin_zones = $this->admin_zones->select('*')->orderBy('zn_zone_code','ASC')->get();
							return redirect('/Admin/SuperAdminZone');
						}
					}else{
						Session::flash('error_message_danger', 'Unable to edit zone!');
						$admin_zones = $this->admin_zones->select('*')->orderBy('zn_zone_code','ASC')->get();
						return redirect('/Admin/SuperAdminZone');
					}
			}else{
				return redirect('/Admin/SuperAdminZone');
			}
			
		}
		else
		{
			return redirect('/');
		}
	}

	public function deleteZone($id)
	{
		$zone_obj = admin_zones::first()->where(array('zn_id' => $id));
		$zone_obj1 = admin_zones::select('zn_zone_code','zn_name')->where('zn_id', $id)->first();
		$alterAdmin = $this->deleteZoneColumn($zone_obj1->zn_zone_code);
		if($alterAdmin){
			$delete = $zone_obj->delete();
			Session::flash('error_message', 'Zone '.$zone_obj1->zn_name.' deleted successfully!');
			$Message = "Zone: " . $zone_obj1->zn_name . " deleted successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
			$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
			//return redirect('SuperAdminZone');
			return redirect('/Admin/SuperAdminZone');
		}else{
			Session::flash('error_message', 'Unable to delete '.$zone_obj1->zn_name.' Please try again!');
			//return redirect('SuperAdminZone');
			return redirect('/Admin/SuperAdminZone');
		}
		
	}
}
