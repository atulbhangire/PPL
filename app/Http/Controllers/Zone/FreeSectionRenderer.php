<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Config;
use App\free_zone_sections;

class FreeSectionRenderer extends Controller
{
    public function __construct()
	{	
		$this->freeSectionNamespace = Config::get('config_path_vars.freeSectionNamespace');
	}
    public function renderSection($section_id){
    	$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $section_id)->first();
		$zoneController = $this->freeSectionNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->display($section_id);

   	}
}
