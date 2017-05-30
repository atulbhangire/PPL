<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Controllers\ZoneBaseClass;
use Session;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;
use App\member_zone_sections;


class MemberZoneController extends ZoneBaseClass
{
    public function __construct()
	{
		$this->member_zone_sections = new member_zone_sections;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}
	public function display($zone_code){
		if(Session::get('is_admin'))
		{
			if(Session::has('child_section')){
				Session::forget('child_section');
			}
			Session::set('zone_name', $zone_code);
			$member_zone_sections = $this->member_zone_sections::select('*')->orderBy('sec_ordering','ASC')->get();
			return view('Admin.MemberZone.indexMemberZone', compact('member_zone_sections'));
		}
		else
		{
			return redirect('/');
		}
	}

	public function get_sub_menu(){

	}

	public function addNew($zone_code){

	}

	public function save($request){

	}

	public function edit($zone_code, $id){
		return "MemberZone Section Edit Page";
	}

	public function update($request){

	}

	public function delete($zone_code, $id){

	}
}
