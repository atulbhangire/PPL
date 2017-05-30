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
use App\free_zone_sections;

class FreeZoneController extends ZoneBaseClass
{
   public function __construct()
	{
		$this->free_zone_sections = new free_zone_sections;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}
	public function display($zone_code){
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$free_zone_sections = $this->free_zone_sections::select('*')->orderBy('sec_id','ASC')->get();
			return view('Admin.FreeZone.indexFreeZone', compact('free_zone_sections'));
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
		return "Freezone Section Edit Page";
	}

	public function update($request){

	}

	public function delete($zone_code, $id){

	}
}
