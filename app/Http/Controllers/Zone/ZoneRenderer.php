<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Config;
use Session;

class ZoneRenderer extends Controller
{
	public function __construct()
	{	
	}
    /*public function renderZone($zone_code){
    	$admin_access = Session::get('admin_access');
    	if(empty($admin_access)){
            Session::flash('error_message','Invalid Session! Please login to access this zone');
            return redirect('/Admin');
        }
		$zone_codes  = array_column($admin_access, 'zone_code');
		if(in_array($zone_code, $zone_codes)){

	    	Session::set('zone_name', $zone_code);
	    	$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
	    	if(!empty($zone_obj->zn_controller))
	    	{
	    		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
				$zoneController = new $zoneController;
				return $zoneController->display($zone_code);
	    	}
	    	else
	    	{
	    		return redirect()->back();
	    	}
		}else{
			return redirect()->back();
		}
   	}

   	public function renderTable($zone_code){
    	$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->renderTable($zone_code);

   	}

   	public function renderTable2($zone_code){
    	$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->renderTable2($zone_code);

   	}

   	public function renderTable3($zone_code){
    	$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->renderTable3($zone_code);

   	}

   	public function renderTable4($zone_code){
    	$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->renderTable4($zone_code);

   	}

   	public function addNew($zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->addNew($zone_code);
   	}


   	public function save(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->save($request);
   	}


   	public function edit($zone_code, $id){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->edit($zone_code, $id);
   	}

   	public function editStkName($zone_code, $id){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->editStkName($zone_code, $id);
   	}

   	public function editFree($zone_code, $id){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->editFree($zone_code, $id);
   	}

   	public function update(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->update($request);
   	}

   	public function updateStkName(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->updateStkName($request);
   	}

   	public function updateFree(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->updateFree($request);
   	}

   	public function delete($zone_code, $id){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->delete($zone_code, $id);
   	}

   	public function delete2($zone_code, $id){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->delete2($zone_code, $id);
   	}

   	public function delete3($zone_code, $id){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->delete3($zone_code, $id);
   	}

   	public function delete4($zone_code, $id){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->delete4($zone_code, $id);
   	}

   	public function memberDisplay($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->display($zone_code,$section_id);
		}
		
   	}

   	public function memberAddFutureDisplay($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->memberAddFutureDisplay($zone_code,$section_id);
		}
		
   	}

   	public function memberArchiveDisplay($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->ArchiveDisplay($zone_code,$section_id);
		}
		
   	}

   	public function memberOldArchiveDisplay($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->oldArchiveDisplay($zone_code,$section_id);
		}
		
   	}

   	public function freeArchiveDisplay($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->FreeZoneArchiveDisplay($zone_code,$section_id);
		}
		
   	}

   	public function getFutureStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->getAllFutureStocks($zone_code,$section_id);
		}
		
   	}

   	public function getCurrentStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->getAllCurrentStocks($zone_code,$section_id);
		}
		
   	}


   	public function dateAI($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->dateAI($zone_code,$section_id);
		}
		
   	}

   	public function getInternalRatioinale($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->getInternalRatioinale($zone_code,$section_id);
		}
		
   	}


   	public function getPastStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->getAllArchivedStocks($zone_code,$section_id);
		}
		
   	}

   	public function getOldPastStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->getAllOldArchivedStocks($zone_code,$section_id);
		}
		
   	}

   	public function getPas20tStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->get20AllArchivedStocks($zone_code,$section_id);
		}
		
   	}

   	public function postFutureStocks(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->postFutureStocks($request,$zone_code,$section_id);
		}
   	}

   	public function makeLive(Request $request,$zone_code, $section_id, $past_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->makeLive($request,$zone_code,$section_id, $past_id);
		}
   	}

   	public function editFutureStocks(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->editFutureStocks($request,$zone_code,$section_id);
		}
   	}

   	public function getDescription(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->getDescription($request,$zone_code,$section_id);
		}
   	}

   	public function getDescriptionOld(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->getDescriptionOld($request,$zone_code,$section_id);
		}
   	}

   	public function getDescriptionFree(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->getDescriptionFree($request,$zone_code,$section_id);
		}
   	}

   	public function getFutureStockRow(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->getFutureStockRow($request,$zone_code,$section_id);
		}
   	}

   	public function getUnloadDateParam(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->getUnloadDateParam($request,$zone_code,$section_id);
		}
   	}

   	public function editCurrentStocks(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->editCurrentStocks($request,$zone_code,$section_id);
		}
   	}

   	public function getCurrentStockRow(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->getCurrentStockRow($request,$zone_code,$section_id);
		}
   	}

   	public function postCurrentStocks(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->postCurrentStocks($request,$zone_code,$section_id);
		}
   	}

   	public function deleteFutureStocks(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->deleteFutureStocks($request,$zone_code,$section_id);
		}
   	}



   	public function deleteCurrentStocks(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->deleteCurrentStocks($request,$zone_code,$section_id);
		}
   	}

   	public function exitcall(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->exitcall($request,$zone_code,$section_id);
		}
   	}

   	public function editPastStocks(Request $request,$zone_code, $section_id, $past_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->editPastStocks($request,$zone_code,$section_id,$past_id);
		}
   	}	

   	public function editCurrStocks(Request $request,$zone_code, $section_id, $past_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->editCurrStocks($request,$zone_code,$section_id,$past_id);
		}
   	}	


   	public function updatePastStocks(Request $request,$zone_code, $section_id, $past_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->updatePastStocks($request,$zone_code,$section_id,$past_id);
		}
   	}

   	public function updateCurrStocks(Request $request,$zone_code, $section_id, $past_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->updateCurrStocks($request,$zone_code,$section_id,$past_id);
		}
   	}

   	public function autoCompleteStockName(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->autoCompleteStockName($request,$zone_code,$section_id);
		}
   	}

   	public function checkStockName(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->checkStockName($request,$zone_code,$section_id);
		}
   	}

   	public function autoCompleteUserName(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->autoCompleteUserName($request);
   	}

   	public function checkUserName(Request $request,$zone_code){
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->checkUserName($request);
   	}

   	public function getUserInfo(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getUserInfo($request);
	}

   	public function autoCompleteStockNameIA(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->autoCompleteStockNameIA($request);
	}

   	public function checkStockNameIA(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->checkStockNameIA($request);
	}

   	public function postSearch(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->postSearch($request);
	}

   	public function getList(Request $request,$zone_code,$stock_id){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getList($request,$zone_code,$stock_id);
	}

   	public function addNewInfo(Request $request,$zone_code,$stock_id){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->addNewInfo($request,$zone_code,$stock_id);
	}

   	public function getStockDataForEdit(Request $request,$zone_code,$stock_id){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getStockDataForEdit($request,$zone_code,$stock_id);
	}
		
   	public function setFlagToCase(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->setFlagToCase($request);
   	}
		
   	public function saveTemplate(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->saveTemplate($request);
   	}
		
   	public function getUserDetails(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getUserDetails($request);
   	}
		
   	public function getOrderDetails(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getOrderDetails($request);
   	}
		
   	public function unsubscribeUser(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->unsubscribeUser($request);
   	}
		
   	public function saveUsernameForCase(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->saveUsernameForCase($request);
   	}
		
   	public function checkUserCount(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->checkUserCount($request);
   	}
		
   	public function approveFeedback(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->approveFeedback($request);
   	}
		
   	public function getCaseStatsData(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getCaseStatsData($request);
   	}
		
   	public function getAdminStatsData(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getAdminStatsData($request);
   	}
//////////////////////////////////////////////
//////////// FREE ZONE SECTIONS //////////////
//////////////////////////////////////////////   	
   	
   	public function freeDisplay($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->display($zone_code,$section_id);
		}
		
   	}

   	public function dateAIFreeZone($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->dateAIFreeZone($zone_code,$section_id);
		}
   	}

   	public function SubmitImage(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->SubmitImage($request,$zone_code,$section_id);
		}
   	}

   	public function CancelImage(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->CancelImage($request,$zone_code,$section_id);
		}
   	}


   	public function getFreeZoneFutureStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->getFreeZoneFutureStocks($zone_code,$section_id);
		}
		
   	}

   	public function getFreeZoneCurrentStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->getFreeZoneCurrentStocks($zone_code,$section_id);
		}
		
   	}

   	public function getFreeZoneAllCurrentStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->getFreeZoneAllCurrentStocks($zone_code,$section_id);
		}
		
   	}

   	public function getFreeZonePastStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->getFreeZonePastStocks($zone_code,$section_id);
		}
		
   	}

   	public function getFreeZonePas20tStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->getFreeZonePas20tStocks($zone_code,$section_id);
		}
		
   	}

   	public function getTVSchedule($zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."TVScheduleController";
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->getTVSchedule($zone_code);
   	}

   	public function addTVSchedule($zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."TVScheduleController";
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->addTVSchedule($zone_code);
   	}

   	public function saveTVSchedule(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."TVScheduleController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->saveTVSchedule($request);
   	}

   	public function editTVSchedule($zone_code, $id){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."TVScheduleController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->editTVSchedule($zone_code, $id);
   	}

   	public function updateTVSchedule(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."TVScheduleController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->updateTVSchedule($request);
   	}

   	public function deleteTVSchedule($zone_code, $id){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."TVScheduleController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->deleteTVSchedule($zone_code, $id);
   	}

   	public function getUpcomingEvent($zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."UpcomingEventController";
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->getUpcomingEvent($zone_code);
   	}

   	public function addUpcomingEvent($zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."UpcomingEventController";
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->addUpcomingEvent($zone_code);
   	}

   	public function saveUpcomingEvent(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."UpcomingEventController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->saveUpcomingEvent($request);
   	}

   	public function editUpcomingEvent($zone_code, $id){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."UpcomingEventController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->editUpcomingEvent($zone_code, $id);
   	}

   	public function updateUpcomingEvent(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."UpcomingEventController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->updateUpcomingEvent($request);
   	}

   	public function deleteUpcomingEvent($zone_code, $id){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."UpcomingEventController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->deleteUpcomingEvent($zone_code, $id);
	}

   	public function getVideo($zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."VideoController";
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->getVideo($zone_code);
   	}

   	public function addVideo($zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."VideoController";
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->addVideo($zone_code);
   	}

   	public function saveVideo(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."VideoController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->saveVideo($request);
   	}

   	public function editVideo($zone_code, $id){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."VideoController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->editVideo($zone_code, $id);
   	}

   	public function updateVideo(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."VideoController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->updateVideo($request);
   	}

   	public function deleteVideo($zone_code, $id){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."VideoController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->deleteVideo($zone_code, $id);
   	}

   	public function makeLiveBlog($zone_code, $id){
   		Session::set('zone_name', $zone_code);
   		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->makeLiveBlog($zone_code,$id);
   	}

   	public function Blogs($zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->Blogs($zone_code);
   	}

   	public function addBlogFuture($zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->addBlogFuture($zone_code);
   	}

   	public function saveBlogFuture(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->saveBlogFuture($request,$zone_code);
   	}

   	public function editBlogFuture($zone_code, $id){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->editBlogFuture($zone_code, $id);
   	}

   	public function updateBlogFuture(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->updateBlogFuture($request,$zone_code);
   	}

   	public function deleteBlogFuture(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->deleteBlogFuture($request,$zone_code);
   	}

   	public function addBlogCurrent($zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->addBlogCurrent($zone_code);
   	}

   	public function saveBlogCurrent(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->saveBlogCurrent($request,$zone_code);
   	}

   	public function editBlogCurrent($zone_code, $id){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->editBlogCurrent($zone_code, $id);
   	}

   	public function updateBlogCurrent(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->updateBlogCurrent($request,$zone_code);
   	}

   	public function deleteBlogCurrent($zone_code, $id){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->deleteBlogCurrent($zone_code, $id);
   	}

   	public function getBlogCurrent($zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->getBlogCurrent($zone_code);
   	}

   	public function getBlogFuture($zone_code){
   		Session::set('zone_name', $zone_code);
		$freezoneSectionController = $this->freeSectionNamespace."BlogsController";
		$freezoneSectionController = new $freezoneSectionController;
		return $freezoneSectionController->getBlogFuture($zone_code);
   	}

   	public function addFreeZoneFutureStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->addFreeZoneFutureStocks($zone_code,$section_id);
		}
		
   	}

   	public function addFutureStockReco($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->addFutureStockReco($zone_code,$section_id);
		}
		
   	}

   	public function addCurrentStockReco($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->memberSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->addCurrentStockReco($zone_code,$section_id);
		}
		
   	}

   	public function addFreeZoneCurrentStocks($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->addFreeZoneCurrentStocks($zone_code,$section_id);
		}
		
   	}

   	public function saveFutureFreeZone(Request $request, $zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->saveFutureFreeZone($request,$zone_code,$section_id);
		}
		
   	}

   	public function saveFutureStockReco(Request $request, $zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->freeSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->saveFutureStockReco($request,$zone_code,$section_id);
		}	
   	}

   	public function editFutureStockReco($zone_code, $section_id,$id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->freeSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->editFutureStockReco($zone_code,$section_id,$id);
		}	
   	}

   	public function editCurrentStockReco($zone_code, $section_id,$id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->freeSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->editCurrentStockReco($zone_code,$section_id,$id);
		}	
   	}

   	public function updateFutureStockReco(Request $request, $zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->freeSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->updateFutureStockReco($request,$zone_code,$section_id);
		}	
   	}
   	public function updateCurrentStockReco(Request $request, $zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('member_zone', $section_id);
   		$member_zone_sections = member_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$memberzoneSectionController = $this->memberSectionNamespace.$member_zone_sections->sec_controller;
		if(empty($memberzoneSectionController) or $memberzoneSectionController == $this->freeSectionNamespace or $memberzoneSectionController == "" or $memberzoneSectionController == null){
			return "Section controller not set.";
		}else{
			$memberzoneSectionController = new $memberzoneSectionController;
			return $memberzoneSectionController->updateCurrentStockReco($request,$zone_code,$section_id);
		}	
   	}

   	public function editFutureFreeZone(Request $request, $zone_code, $section_id, $id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->editFutureFreeZone($request,$zone_code,$section_id, $id);
		}
		
   	}

   	public function makeLiveFutureFreeZone(Request $request, $zone_code, $section_id, $id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->makeLiveFutureFreeZone($request,$zone_code,$section_id, $id);
		}
		
   	}


   	public function updateFutureFreeZone(Request $request, $zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->updateFutureFreeZone($request,$zone_code,$section_id);
		}
		
   	}

   	public function saveCurrentFreeZone(Request $request, $zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->saveCurrentFreeZone($request,$zone_code,$section_id);
		}
		
   	}

   	public function editCurrentFreeZone(Request $request, $zone_code, $section_id, $id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->editCurrentFreeZone($request,$zone_code,$section_id,$id);
		}
		
   	}

   	public function updateCurrentFreeZone(Request $request, $zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->updateCurrentFreeZone($request,$zone_code,$section_id);
		}
		
   	}


   	public function autoCompleteStockNameFreeZone($zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->autoCompleteStockNameFreeZone($zone_code,$section_id);
		}
		
   	}

   	public function deleteFreeZoneFutureStocks(Request $request,$zone_code, $section_id){
   		Session::set('zone_name', $zone_code);
		Session::set('free_zone', $section_id);
   		$free_zone_sections = free_zone_sections::select('sec_controller')->where('sec_id', $section_id)->first();
		$freezoneSectionController = $this->freeSectionNamespace.$free_zone_sections->sec_controller;
		if(empty($freezoneSectionController) or $freezoneSectionController == $this->freeSectionNamespace or $freezoneSectionController == "" or $freezoneSectionController == null){
			return "Section controller not set.";
		}else{
			$freezoneSectionController = new $freezoneSectionController;
			return $freezoneSectionController->deleteFreeZoneFutureStocks($request,$zone_code,$section_id);
		}
   	}

   	////////////////////////////////////////






   	public function getStockData($zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getStockData();
   	}

   	public function getAdminAlertData($zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getAdminAlertData();
   	}

   	public function toggleAdminAlertStatus(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->toggleAdminAlertStatus($request);
   	}

   	public function deleteAdminAlert(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->deleteAdminAlert($request);
   	}

   	public function getUserAlertData($zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getUserAlertData();
   	}

   	public function toggleUserAlertStatus(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->toggleUserAlertStatus($request);
   	}

   	public function deleteUserAlert(Request $request, $zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->deleteUserAlert($request);
   	}

   	public function getInvalidEmailComplaintsData($zone_code)
   	{
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getInvalidEmailComplaintsData();
   	}


   	public function autoCompleteOrderId(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->autoCompleteOrderId($request);
   	}

   	public function checkOrderId(Request $request,$zone_code){
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->checkOrderId($request);
   	}

   	public function getOrderInfo(Request $request,$zone_code){
   		Session::set('zone_name', $zone_code);
   		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->getOrderInfo($request);
	}

	public function downloadActiveUsersInfo(Request $request, $zone_code)
	{
		Session::set('zone_name', $zone_code);
		$zone_obj = admin_zones::select('zn_controller')->where('zn_zone_code', $zone_code)->first();
		$zoneController = $this->zoneNamespace.$zone_obj->zn_controller;
		$zoneController = new $zoneController;
		return $zoneController->downloadActiveUsersInfo($request);
	}*/
}
