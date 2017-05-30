<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\Feedbacks;
use App\order_details;
use App\user_profiles;
use Session;
use Config;
use URL;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;

class FeedbackController extends ZoneBaseClass
{
	public function __construct()
	{
		$this->feedbacks = new Feedbacks;
		$this->aws = new CustomAwsController;
	}
	public function display($zone_code)
	{
		if(Session::get('is_admin')) {
			$feedbacks=[];
			Session::set('zone_name', $zone_code);
			$feedbacks = $this->feedbacks::select('*')->get();
			if(!empty($feedbacks)) {
				foreach($feedbacks as $feedback) {
					if($feedback->zone==11) {
						$feedback->zone_name = 'Free Zone';
						$feedback->section_name = DB::table('free_zone_sections')->where('sec_id',$feedback->section_id)->pluck('sec_name')->first();
					} else {
						$feedback->zone_name = 'Member Zone';
						$feedback->section_name = DB::table('member_zone_sections')->where('sec_id',$feedback->section_id)->pluck('sec_name')->first();
					}
					// $feedback->zone_name = DB::table('admin_zones')
							// ->where('zn_zone_code',$feedback->zone)->pluck('zn_name')->first();
				}
							// dd($feedbacks);
			}
			return view('Admin.Feedback.viewFeedbacks', compact('feedbacks'));
		} else {
			return redirect('/');
		}
	}

	public function renderTable($zone_code){
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			

			$feedbacks = Feedbacks::select('*');//->orderBy('posted_at','DESC');

        	return \Datatables::of($feedbacks)->editColumn('zone', function ($feedbacks) {
        		if($feedbacks->zone==11) {
            		return 'Free Zone';
        		}else{
        			return 'Member Zone';
        		}
            })->editColumn('section_name', function ($feedbacks) {
        		if($feedbacks->zone==11) {
        			$result = DB::table('free_zone_sections')->where('sec_id',$feedbacks->section_id)->pluck('sec_name')->first();
            		return $result;
        		}else{
        			$result = DB::table('member_zone_sections')->where('sec_id',$feedbacks->section_id)->pluck('sec_name')->first();
        			return $result;
        		}
            })->editColumn('is_accepted', function ($feedbacks) {
        		if($feedbacks->is_accepted) {
            		return "Approved";
        		}else{
        			return '<a class="accept_feedback" id="accept-'.$feedbacks->id.'"> Approve </a>';
        			$result = DB::table('member_zone_sections')->where('sec_id',$feedbacks->section_id)->pluck('sec_name')->first();
        			return $result;
        		}
            })->editColumn('article_id', function ($feedbacks) {
                    return $feedbacks->article_id .'(<a href="'.$feedbacks->article_url.'">view</a>)';
            })->editColumn('posted_at', function ($feedbacks) {
                    return date('M d Y g:i A', strtotime($feedbacks->posted_at));
            })->addColumn('delete', function ($feedbacks) {
            	$ret = "<a data-toggle='modal' href='#deleteFeedback" . $feedbacks->id . "'>Delete</a>";
		        $ret .= "<div class='modal fade' id='deleteFeedback" . $feedbacks->id . "' tabindex='-1' role='basic' aria-hidden='true' style='display: none;'>";
		        $ret .= "<div class='modal-dialog'>";
		        $ret .= "<div class='modal-content'>";
		        $ret .= "<div class='modal-header'>";
		        $ret .= "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'></button>";
		        $ret .= "<h4 class='modal-title'>Delete Feedback ?</h4>";
		        $ret .= "</div>";
		        $ret .= "<div class='modal-body'> Do you want delete comment ID : " . $feedbacks->id . " ?</div>";
		        $ret .= "<div class='modal-footer'>";
		        $ret .= "<button type='button' class='btn dark btn-outline' data-dismiss='modal'>Close</button>";
		        $link = Session::has('zone_name') ? Session::get('zone_name') : '';
		        $ret .= "<a class='btn green' href='" . $link . "/delete/" . $feedbacks->id . "'>Yes</a>";
		        $ret .= "</div>";
		        $ret .= "</div>";
		        $ret .= "</div>";
		        $ret .= "</div>";
				return $ret;
            })->make(true);
			 
		}
		else
		{
			return "FALSE";
		}
	}


    public function approveFeedback($request){
		if(!empty($request->get('id'))) {
			$feedbacks = $this->feedbacks::where('id',$request->get('id'))->update(['is_accepted'=>1]);
		}
        return 'true';
    }
    public function delete($zone_code, $id)
    {
		if($this->feedbacks::first()->where(array('id' => $id))->delete()) {
			return redirect()->back()->with('alert_message', 'Comment deleted successfully.');
		} else {
			return redirect()->back()->with('alert_danger', 'Comment was not deleted.');
		}
    }

}