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
use App\databank_sections;


class DatabankController extends Controller
{
   public function __construct()
	{
		$this->databank_sections = new databank_sections;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}
	public function display($zone_code){
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$databank_sections = $this->databank_sections::select('*')->orderBy('sec_id','ASC')->get();
			return view('Admin.Databank.indexDataBank', compact('databank_sections'));
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
		return "Databank Section Edit Page";
	}

	public function update($request){

	}

	public function delete($zone_code, $id){

	}
}
