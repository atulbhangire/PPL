<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\Http\Controllers\HomeController;
use App\internal_analysis;
use App\stock_index_table;
use Session;
use Config;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;

class InternalAnalysisController extends ZoneBaseClass
{
	public function __construct()
	{
		$this->internal_analysis = new internal_analysis;
		$this->stock_index_table = new stock_index_table;
		$this->HomeController = new HomeController;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
		$this->Bucket = Config::get('config_path_vars.Image_Bucket');
		
		$this->isFullAccess = 0;
		if(Session::has('zone_name')) {
			$zone_name = DB::table('admin_zones')->where('zn_zone_code', Session::get('zone_name'))->pluck('zn_name')->first();
			if (strpos($zone_name, 'Full') !== false) {
			    $this->isFullAccess = 1;
			}
		}
	}

	public function display($zone_code)
	{
    	if(Session::get('is_admin')) {
			Session::set('zone_name', $zone_code);
			/*$stocks = $this->stock_index_table::select('*')->get();*/
			$isFullAccess = $this->isFullAccess;
	   		return view('Admin.InternalAnalysis.index', compact('isFullAccess'));
	   	
		} else {
	   		return redirect('/');
	   	}
    }

    public function addNew($zone_code)
    {
    	if(Session::get('is_admin')) {
   			return view('Admin.InternalAnalysis.add');
		} else {
	   		return redirect('/');
	   	}
    }

    public function addNewInfo($request,$zone_code,$stock_id)
    {
    	if(Session::get('is_admin')) {
    		if(!$this->isFullAccess) return redirect("/Admin/" . Session::get('zone_name'));
    		$stock = $this->stock_index_table::select('unique_identifier')->where('id', '=', $stock_id)->first();
			$stock_name = '';
			if(!empty($stock)) {
				$stock_name = $stock->unique_identifier;
			}
   			return view('Admin.InternalAnalysis.add',compact('stock_name'));
		} else {
	   		return redirect('/');
	   	}
    }

    public function save($request)
    {
    	if($request->session()->get('is_admin')) {
			$postedData = $request->all();
    		if(!empty($postedData)) {
    			$stock_index_id = $this->stock_index_table::select('id')->where('unique_identifier', '=', $postedData['stock_name'])->first();
    			if(empty($stock_index_id)) {
					return redirect()->back()->with('error_message_danger', 'Error! Please provide correct stock name.');
    			}
    			$stock_code_price_arr = $this->HomeController->getStockPrices([$postedData['stock_name']],1,1);
    			// dd($stock_code_price_arr);
    			if(!empty($stock_code_price_arr)) {
    				$cmp = ( $stock_code_price_arr[$postedData['stock_name']]['cmp'] ) ? $stock_code_price_arr[$postedData['stock_name']]['cmp'] : 0;
    				$change = ( $stock_code_price_arr[$postedData['stock_name']]['change'] ) ? $stock_code_price_arr[$postedData['stock_name']]['change'] : 0;
    				if($change > 0) $change='+'.$change;

    				$cmp_at_add = ($cmp)?$cmp.(($change)?' ('.$change.')':''):'';

	    			$data = array(
						'stock_index_id' => $stock_index_id->id,
						'sector' => $postedData['sector'],
						'type' => $postedData['type'],
						'event_datetime' => ($postedData['event_datetime']) ? $postedData['event_datetime']:NULL,
						'content' => $postedData['content'],
						'cmp_at_add' => $cmp_at_add,
						'created_by' => Session::get('user_name'),
						'updated_by' => Session::get('user_name'),
					);
					// dd($data);
					try {
						$res = $this->internal_analysis->insert($data);
					} catch(\Exception $e) {
					    return redirect()->back()->with('error_message', 'Stock info was not added. Stock and Event Datetime already present.'); 
					}
					if($res) {
						return redirect('/Admin/'.Session::get('zone_name'))->with('success_message', 'Stock info added successfully.');
					} 
	    		}
    		}
			return redirect()->back()->withInputs()->with('error_message', 'Stock info was not added.');
		} else {
	   		return redirect('/');
	   	}
    }

    public function autoCompleteStockNameIA($request){
		
        $stock_index_table = stock_index_table::select('unique_identifier')->where('unique_identifier', 'like', '%' . $request['query'] . '%')->limit(100)->get();

        //stock_index_table::select('unique_identifier')->get();
        $data = array();
        foreach ($stock_index_table as $stock_index_table){
        	array_push($data, $stock_index_table->unique_identifier);
        }

        //$data = json_encode($data);
        //$request['query']
        $suggestions = array('suggestions' => $data );

        $data = json_encode($suggestions);
        return $data;
    }
    public function checkStockNameIA($request){
		
        $stock_index_table = stock_index_table::select('unique_identifier')->where('unique_identifier', $request['query'])->get();
		$data = count($stock_index_table); 
        return $data;
    }
    public function postSearch($request){
		if(Session::get('is_admin'))
		{
			if(!$this->isFullAccess) return redirect("/Admin/" . Session::get('zone_name'));
			$postedData = $request->all();
			if(!empty($postedData)) {
				$stock_index_id = $this->stock_index_table::select('id')->where('unique_identifier', '=', $postedData['stock_name'])->first();
				if(empty($stock_index_id)) {
					return redirect()->back()->with('error_message', 'Error! Please provide correct stock name.');
				}
				$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') . "/list/".$stock_index_id->id : "/Home";
				return redirect($zone_name);
	        }
        }
		return redirect('/');
    }
    public function getList($request,$zone_code,$stock_id){
		if(Session::get('is_admin'))
		{
			if(!$this->isFullAccess) return redirect("/Admin/" . Session::get('zone_name'));
			$stock = $this->stock_index_table::select('unique_identifier')->where('id', '=', $stock_id)->first();
			// dd(($stock));
			if(!empty($stock)) {
				$stock_code_price_arr = $this->HomeController->getStockPrices([$stock->unique_identifier],1,1);
    			// dd($stock_code_price_arr);
    			$cmp = 0;
    			$change = 0;
    			if(!empty($stock_code_price_arr) && !empty($stock_code_price_arr[$stock->unique_identifier])) {
    				$cmp = ( $stock_code_price_arr[$stock->unique_identifier]['cmp'] ) ? $stock_code_price_arr[$stock->unique_identifier]['cmp'] : 0;
    				$change = ( $stock_code_price_arr[$stock->unique_identifier]['change'] ) ? $stock_code_price_arr[$stock->unique_identifier]['change'] : 0;
    				if($change > 0) $change='+'.$change;

    				$cmp_now = ($cmp)?$cmp.(($change)?' ('.$change.')':''):'';
    			}	
    			$stk_data = ['stock_name' => $stock->unique_identifier,
    					 'cmp' => $cmp,
    					 'change' => $change ];
				return view('Admin.InternalAnalysis.index_edit', compact('stock_id','stk_data'));
			}	
			return redirect( Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/" );
        } else {
			return redirect('/');
        }
    }

	public function edit($zone_code, $id)
	{
		if(Session::get('is_admin'))
		{
			if(!$this->isFullAccess) return redirect("/Admin/" . Session::get('zone_name'));
			$internal_analysis = $this->internal_analysis::select('*')->where('id', $id)->first();
			if(!empty($internal_analysis)) {
				$stock_name = $this->stock_index_table::select('unique_identifier')->where('id', '=', $internal_analysis->stock_index_id)->first();
				
				if(!empty($stock_name)) {
					Session::flash('is_edit', 1);
					Session::flash('id', $internal_analysis->id);
					Session::flash('stock_name', $stock_name->unique_identifier);
					Session::flash('sector', $internal_analysis->sector);
					Session::flash('type', $internal_analysis->type);
					Session::flash('event_datetime', $internal_analysis->event_datetime);
					Session::flash('content', $internal_analysis->content);
					return view('Admin.InternalAnalysis.add');
				}
			}
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') . "/" : "/";
			return redirect($zone_name);
		}	
		return redirect('/');
	}

	public function update($request)
	{
		if(!$this->isFullAccess) return redirect("/Admin/" . Session::get('zone_name'));
		if($request->session()->get('is_admin')) {
			$postedData = $request->all();
    		if(!empty($postedData)) {
    			$stock_index_id = $this->stock_index_table::select('id')->where('unique_identifier', '=', $postedData['stock_name'])->first();
    			if(empty($stock_index_id)) {
					return redirect()->back()->with('error_message_danger', 'Error! Please provide correct stock name.');
    			}
    			$data = array(
					'stock_index_id' => $stock_index_id->id,
					'sector' => $postedData['sector'],
					'type' => $postedData['type'],
					'event_datetime' => ($postedData['event_datetime']) ? $postedData['event_datetime']:NULL,
					'content' => $postedData['content'],
					'updated_by' => Session::get('user_name'),
				);
				// dd($postedData);
				try {
					$res = $this->internal_analysis->where('id',$postedData['id'])->update($data);
				} catch(\Exception $e) {
				    return redirect()->back()->with('error_message', 'Stock info was not updated. Stock and Event Datetime already present.'); 
				}
				if($res) {
					return redirect('/Admin/'.Session::get('zone_name').'/list/'.$stock_index_id->id)->with('success_message', 'Stock info updated successfully.');
				} 
    		}
			return redirect()->back()->with('error_message', 'Stock info was not updated.');
		} else {
	   		return redirect('/');
	   	}
	}

	public function delete($zone_code, $id)
	{
		if(Session::get('is_admin'))
		{
			if(!$this->isFullAccess) return redirect("/Admin/" . Session::get('zone_name'));
			$stock = $this->internal_analysis::select('*')->where(array('id' => $id))->first();
			if($this->internal_analysis::first()->where(array('id' => $id))->delete())
			{
				Session::flash('success_message', $stock->stock_index_id . ' stock info deleted successfully.');
			}
			else
			{
				Session::flash('error_message', $stock->stock_index_id . ' stock info was not deleted.');
			}
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
			return redirect($zone_name);
		}
		else
		{
			return redirect('/');
		}
	}

	public function getStockData()
	{
		if(Session::get('is_admin'))
		{
			// $internal_analysis = $this->internal_analysis::select('*')->get();
			$internal_analysis = DB::table('internal_analysis')
            ->join('stock_index_table', 'internal_analysis.stock_index_id', '=', 'stock_index_table.id')
            ->select('internal_analysis.*', 'stock_index_table.unique_identifier')
            ->limit(100)
            ->orderBy('id','desc')
            ->get();
            // dd($internal_analysis);
			$zone_name = Session::has('zone_name') ? Session::get('zone_name') : NULL;
			foreach($internal_analysis as $stock)
			{
				$stock_code_price_arr = $this->HomeController->getStockPrices([$stock->unique_identifier],1,1);
    			// dd($stock_code_price_arr);
    			$cmp_now = '';
    			if(!empty($stock_code_price_arr)) {
    				$cmp = ( $stock_code_price_arr[$stock->unique_identifier]['cmp'] ) ? $stock_code_price_arr[$stock->unique_identifier]['cmp'] : 0;
    				$change = ( $stock_code_price_arr[$stock->unique_identifier]['change'] ) ? $stock_code_price_arr[$stock->unique_identifier]['change'] : 0;
    				if($change > 0) $change='+'.$change;

    				$cmp_now = ($cmp)?$cmp.(($change)?' ('.$change.')':''):'';
    			}	
    			$stock->cmp_now = $cmp_now;
				$stock->sector = ($stock->sector)?'Yes':'No';
				$stock->event_datetime = $this->timeInReadableFormat($stock->event_datetime);
				$stock->created_at_readable = $this->timeInReadableFormat($stock->created_at);
				$stock->updated_at_readable = $this->timeInReadableFormat($stock->updated_at);
				// $edit="<div class='text-center'><a class='btn btn-danger' id='RemoveFutureBtn_1' href='/Admin/" . $zone_name . "/edit/" . $stock->id . "' ><i class='glyphicon glyphicon-edit'></i></a>";
				// $delete = "<a class='btn btn-danger' data-toggle='modal' href='#deleteZone" . $stock->id . "'><i class='glyphicon glyphicon-trash'></i></a></div>";
				if($this->isFullAccess) {
					$delete = "<a data-toggle='modal' href='#deleteZone" . $stock->id . "'>Delete</a>";
					$delete .= "<div class='modal fade' id='deleteZone" . $stock->id . "' tabindex='-1' role='basic' aria-hidden='true' style='display: none;'>";
					$delete .= "<div class='modal-dialog'>";
					$delete .= "<div class='modal-content'>";
					$delete .= "<div class='modal-header'>";
					$delete .= "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'></button>";
					$delete .= "<h4 class='modal-title'>Delete Stock</h4>";
					$delete .= "</div>";
					$delete .= "<div class='modal-body'> Do you want delete " . $stock->unique_identifier . "? </div>";
					$delete .= "<div class='modal-footer'>";
					$delete .= " <button type='button' class='btn dark btn-outline' data-dismiss='modal'>Close</button>";
					$delete .= "<button type='button' class='btn green' onclick=\"window.location.href='/Admin/" . $zone_name . "/delete/" . $stock->id . "'\">Yes</button>";
					$delete .= "</div>";
					$delete .= "</div>";
					$delete .= "</div>";
					$delete .= "</div>";
					$stock->delete = $delete;
					$stock->unique_identifier = "<a class='edit' href='/Admin/" . $zone_name . "/list/" . $stock->stock_index_id . "'> ". $stock->unique_identifier ." </a>";
				} 
			}
			$stockData = '{ "data":'.json_encode($internal_analysis). '}';
			return $stockData;
		}
		else
		{
			return redirect('/');
		}
	}

	public function getStockDataForEdit($request,$zone_code,$stock_id)
	{
		if(Session::get('is_admin') && $this->isFullAccess)
		{
			if(!$this->isFullAccess) return false; 
			// $internal_analysis = $this->internal_analysis::select('*')->where('stock_index_id', $stock_id)->get();
			$internal_analysis = DB::table('internal_analysis')
            ->join('stock_index_table', 'internal_analysis.stock_index_id', '=', 'stock_index_table.id')
            ->select('internal_analysis.*', 'stock_index_table.unique_identifier')
            ->where('stock_index_id', $stock_id)
            ->get();
			$zone_name = Session::has('zone_name') ? Session::get('zone_name') : NULL;
			foreach($internal_analysis as $stock)
			{
				$stock->sector = ($stock->sector)?'Yes':'No';
				$stock->event_datetime = $this->timeInReadableFormat($stock->event_datetime);
				$stock->created_at_readable = $this->timeInReadableFormat($stock->created_at);
				$stock->updated_at_readable = $this->timeInReadableFormat($stock->updated_at);
				$stock->added_by = $stock->created_by." on ".$stock->created_at_readable;
				if(!empty($stock->updated_by) && $stock->created_by != $stock->updated_by) {
					$stock->added_by .= "<br/> (Modified by ".$stock->updated_by." on ".$stock->updated_at_readable.")";
				}
				$stock->content = $stock->content."<br/>CMP at add:<b> ".$stock->cmp_at_add."</b>";
				$edit="<div class='text-center'><a class='btn btn-danger' id='RemoveFutureBtn_1' href='/Admin/" . $zone_name . "/edit/" . $stock->id . "' style='margin-top: 2px;' ><i class='glyphicon glyphicon-edit'></i></a>";
				$delete = "<a class='btn btn-danger' data-toggle='modal' href='#deleteZone" . $stock->id . "' style='margin-top: 2px;'><i class='glyphicon glyphicon-trash'></i></a></div>";
				$delete .= "<div class='modal fade' id='deleteZone" . $stock->id . "' tabindex='-1' role='basic' aria-hidden='true' style='display: none;'>";
				$delete .= "<div class='modal-dialog'>";
				$delete .= "<div class='modal-content'>";
				$delete .= "<div class='modal-header'>";
				$delete .= "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'></button>";
				$delete .= "<h4 class='modal-title'>Delete Stock</h4>";
				$delete .= "</div>";
				$delete .= "<div class='modal-body'> Do you want delete " . $stock->unique_identifier . "? </div>";
				$delete .= "<div class='modal-footer'>";
				$delete .= " <button type='button' class='btn dark btn-outline' data-dismiss='modal'>Close</button>";
				$delete .= "<button type='button' class='btn green' onclick=\"window.location.href='/Admin/" . $zone_name . "/delete/" . $stock->id . "'\">Yes</button>";
				$delete .= "</div>";
				$delete .= "</div>";
				$delete .= "</div>";
				$delete .= "</div>";
				$stock->edit_delete = $edit.$delete;
			}
			$stockData = '{ "data":'.json_encode($internal_analysis). '}';
			return $stockData;
		}
		else
		{
			return redirect('/');
		}
	}

	function timeInReadableFormat($date_time)
    {
    	if(!empty($date_time) && $date_time!='0000-00-00 00:00:00') {
	    	$date = date_create($date_time);
	        $d = date_format($date, "d");
	        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
	        if((($d % 100) >= 11) && (($d % 100) <= 13))
	        {
	            $d = $d . 'th ';
	        }
	        else
	        {
	            $d = $d . $ends[$d % 10] . " ";
	        }
	        return $d . date_format($date, "M") . " at " . date_format($date, "h:i a");
	    }
	    return '';
    }
}