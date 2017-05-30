<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Config;
use App\member_zone_sections;

class MemberSectionRenderer extends Controller
{
   	public function __construct()
	{	
		$this->memberSectionNamespace = Config::get('config_path_vars.memberSectionNamespace');
	}
    public function renderSection($section_id){
    	$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->display($section_id);

   	}
}
