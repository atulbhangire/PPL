<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\support_incoming;
use App\support_replies;
use App\admin_users;
use Session;
use Config;
use URL;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;

class CustomerSupportStatsController extends ZoneBaseClass
{
	public function __construct()
	{
		$this->support_incoming = new support_incoming;
		$this->support_replies = new support_replies;
		$this->admin_users = new admin_users;
		$this->aws = new CustomAwsController;
		// $this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}
	public function display($zone_code)
	{
		// dd($request->all());
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			// $admin_stats=$this->getCaseStatsDataInner('all');
			// dd($admin_stats);
			return view('Admin.SupportStats.index');
		}
		else
		{
			return redirect('/');
		}
	}

	public function getCaseStatsData($request){
		if(!empty($request->get('period'))) {
			$admin_stats=$this->getCaseStatsDataInner($request->get('period'));
			$admin_stats = '{ "data":'.json_encode($admin_stats). '}';
			return $admin_stats;
		}
        return 'false';
    }

	public function getAdminStatsData($request){
		if(!empty($request->get('period'))) {
			$admin_stats=$this->getAdminStatsDataInner($request->get('period'));
			$admin_stats = '{ "data":'.json_encode($admin_stats). '}';
			return $admin_stats;
		}
        return 'false';
    }

	public function getCaseStatsDataInner($period)
	{
		$case_category_arr = $this->support_incoming::select('case_category')->whereNotNull('case_category')->groupBy('case_category')->pluck('case_category')->toArray();
		$case_stats=[]; $cnt=0;
		if($period=='1d') $newTime = strtotime('-1 day');
		else if($period=='1w') $newTime = strtotime('-1 week');
		else if($period=='1m') $newTime = strtotime('-1 month');
		else if($period=='1y') $newTime = strtotime('-1 year');

		if(!empty($case_category_arr)) {
			foreach ($case_category_arr as $case_category) {
				$case_stats[$cnt]['category'] = ucwords(str_replace('-', ' ', $case_category)); //$case_category;
				// Received cases count
				$received_query = DB::table('support_incoming')
				            ->where('case_category',$case_category);
				if (isset($newTime))
				    $received_query->where('created_at','>=',date('Y-m-d H:i:s', $newTime));
				$case_stats[$cnt]['received'] = $received_query->count();

				// Replied cases count
				$replied_query = DB::table('support_incoming')
				            ->where('case_category',$case_category)
				            ->where('case_replied',1);
				if (isset($newTime))
				    $replied_query->where('created_at','>=',date('Y-m-d H:i:s', $newTime));
				$case_stats[$cnt]['replied'] = $replied_query->count();

				// Closed cases count
				$closed_query = DB::table('support_incoming')
				            ->where('case_category',$case_category)
				            ->where('case_closed',1);
				if (isset($newTime))
				    $closed_query->where('created_at','>=',date('Y-m-d H:i:s', $newTime));
				$case_stats[$cnt]['closed'] = $closed_query->count();

				// Flagged cases count
				$flagged_query = DB::table('support_incoming')
				            ->where('case_category',$case_category)
				            ->where('case_flagged',1);
				if (isset($newTime))
				    $flagged_query->where('created_at','>=',date('Y-m-d H:i:s', $newTime));
				$case_stats[$cnt]['flagged'] = $flagged_query->count();

				// Rating
				$rating_query = DB::table('support_incoming')->select('case_ratings_by_user')
				            ->where('case_category',$case_category)
				            ->whereNotNull('case_ratings_by_user')
				            ->where('case_ratings_by_user','!=',0);
				if (isset($newTime))
				    $rating_query->where('support_incoming.created_at','>=',date('Y-m-d H:i:s', $newTime));
				$rating_arr = $rating_query->pluck('case_ratings_by_user')->toArray();
				if(!empty($rating_arr)) {
					$case_stats[$cnt]['rating'] = round( array_sum($rating_arr) / count($rating_arr) ,1)."  (".count($rating_arr).") ";
				} else {
					$case_stats[$cnt]['rating'] = '-';
				}

				$cnt++;
			}
		}
		// dd($case_stats);
		return $case_stats;
	}

	public function getAdminStatsDataInner($period)
	{
		$admin_list = $this->admin_users::select('adm_username')->where('is_active',1)->pluck('adm_username');
		$admin_stats=[]; $cnt=0;
		if($period=='1d') $newTime = strtotime('-1 day');
		else if($period=='1w') $newTime = strtotime('-1 week');
		else if($period=='1m') $newTime = strtotime('-1 month');
		else if($period=='1y') $newTime = strtotime('-1 year');

		if(!empty($admin_list)) {
			foreach ($admin_list as $admin) {
				$admin_stats[$cnt]['admin'] = $admin;
				$admin_stats[$cnt]['updated_at'] = 'TBD';
				// Replied cases count
				$replied_query = DB::table('support_incoming')
				            ->leftJoin('support_replies', 'support_incoming.id', '=', 'support_replies.support_incoming_id')
				            ->where('support_replies.reply_by',$admin)
				            ->where('support_incoming.case_replied',1);
				if (isset($newTime))
				    $replied_query->where('support_incoming.created_at','>=',date('Y-m-d H:i:s', $newTime));
				$admin_stats[$cnt]['replied'] = $replied_query->count();

				// Closed cases count
				$closed_query = DB::table('support_incoming')
				            ->where('closed_by',$admin)
				            ->where('case_closed',1);
				if (isset($newTime))
				    $closed_query->where('created_at','>=',date('Y-m-d H:i:s', $newTime));
				$admin_stats[$cnt]['closed'] = $closed_query->count();

				// Flagged cases count
				$flagged_query = DB::table('support_incoming')
				            ->where('flagged_by',$admin)
				            ->where('case_flagged',1);
				if (isset($newTime))
				    $flagged_query->where('created_at','>=',date('Y-m-d H:i:s', $newTime));
				$admin_stats[$cnt]['flagged'] = $flagged_query->count();

				// Rating
				$rating_query = DB::table('support_incoming')->select('support_incoming.case_ratings_by_user')
				            ->leftJoin('support_replies', 'support_incoming.id', '=', 'support_replies.support_incoming_id')
				            ->where('support_replies.reply_by',$admin)
				            ->whereNotNull('support_incoming.case_ratings_by_user')
				            ->where('support_incoming.case_ratings_by_user','!=',0)
				            ->where('support_incoming.case_replied',1);
				if (isset($newTime))
				    $rating_query->where('support_incoming.created_at','>=',date('Y-m-d H:i:s', $newTime));
				// $admin_stats[$cnt]['rating'] = $rating_query->pluck('support_incoming.case_ratings_by_user')->toArray();
				$rating_arr = $rating_query->pluck('case_ratings_by_user')->toArray();
				if(!empty($rating_arr)) {
					$admin_stats[$cnt]['rating'] = round( array_sum($rating_arr) / count($rating_arr) ,1)."  (".count($rating_arr).") ";
				} else {
					$admin_stats[$cnt]['rating'] = '-';
				}

				// Child Table
				$childData = $this->getAdminStatsChildData($period,$admin);
				$temp = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
				$temp .= '</tr>';
                $temp .= '<td><strong>Category</strong></td>';
                $temp .= '<td><strong>Replied</strong></td>';
                $temp .= '<td><strong>Closed</strong></td>';
                $temp .= '<td><strong>Flagged</strong></td>';
                $temp .= '<td><strong>Rating</strong></td>';
                $temp .= '</tr>';
				foreach ($childData as $child) {
	                $temp .= '</tr>';
	                $temp .= '<td>'.$child['category'].'</td>';
	                $temp .= '<td>'.$child['replied'].'</td>';
	                $temp .= '<td>'.$child['closed'].'</td>';
	                $temp .= '<td>'.$child['flagged'].'</td>';
	                $temp .= '<td>'.$child['rating'].'</td>';
	                $temp .= '</tr>';
				}
	                $temp .= '</table>';
				$admin_stats[$cnt]['childTbl'] = $temp;
				// dd($childData);
				// dd($childData);
				$cnt++;
			}
		}
		return $admin_stats;
	}

	public function getAdminStatsChildData($period,$admin)
	{
		$case_category_arr = $this->support_incoming::select('case_category')->whereNotNull('case_category')->groupBy('case_category')->pluck('case_category')->toArray();
		// $admin_list = $this->admin_users::select('adm_username')->where('is_active',1)->pluck('adm_username');
		$case_stats=[]; $cnt=0;
		if($period=='1d') $newTime = strtotime('-1 day');
		else if($period=='1w') $newTime = strtotime('-1 week');
		else if($period=='1m') $newTime = strtotime('-1 month');
		else if($period=='1y') $newTime = strtotime('-1 year');

		if(!empty($case_category_arr)) {
			foreach ($case_category_arr as $case_category) {
				$case_stats[$cnt]['category'] = ucwords(str_replace('-', ' ', $case_category)); //$case_category;
				// $case_stats[$cnt]['updated_at'] = 'TBD';
				// Replied cases count
				$replied_query = DB::table('support_incoming')
				            ->leftJoin('support_replies', 'support_incoming.id', '=', 'support_replies.support_incoming_id')
				            ->where('support_replies.reply_by',$admin)
				            ->where('support_incoming.case_category',$case_category)
				            ->where('support_incoming.case_replied',1);
				if (isset($newTime))
				    $replied_query->where('support_incoming.created_at','>=',date('Y-m-d H:i:s', $newTime));
				$case_stats[$cnt]['replied'] = $replied_query->count();

				// Closed cases count
				$closed_query = DB::table('support_incoming')
				            ->where('closed_by',$admin)
				            ->where('case_category',$case_category)
				            ->where('case_closed',1);
				if (isset($newTime))
				    $closed_query->where('created_at','>=',date('Y-m-d H:i:s', $newTime));
				$case_stats[$cnt]['closed'] = $closed_query->count();

				// Flagged cases count
				$flagged_query = DB::table('support_incoming')
				            ->where('flagged_by',$admin)
				            ->where('case_category',$case_category)
				            ->where('case_flagged',1);
				if (isset($newTime))
				    $flagged_query->where('created_at','>=',date('Y-m-d H:i:s', $newTime));
				$case_stats[$cnt]['flagged'] = $flagged_query->count();

				// Rating
				$rating_query = DB::table('support_incoming')->select('support_incoming.case_ratings_by_user')
				            ->leftJoin('support_replies', 'support_incoming.id', '=', 'support_replies.support_incoming_id')
				            ->where('support_incoming.case_category',$case_category)
				            ->where('support_replies.reply_by',$admin)
				            ->whereNotNull('support_incoming.case_ratings_by_user')
				            ->where('support_incoming.case_ratings_by_user','!=',0)
				            ->where('support_incoming.case_replied',1);
				if (isset($newTime))
				    $rating_query->where('support_incoming.created_at','>=',date('Y-m-d H:i:s', $newTime));
				// $case_stats[$cnt]['rating'] = $rating_query->pluck('support_incoming.case_ratings_by_user')->toArray();
				$rating_arr = $rating_query->pluck('case_ratings_by_user')->toArray();
				if(!empty($rating_arr)) {
					$case_stats[$cnt]['rating'] = round( array_sum($rating_arr) / count($rating_arr) ,1)."  (".count($rating_arr).") ";
				} else {
					$case_stats[$cnt]['rating'] = '-';
				}

				
				$cnt++;
			}
		}
		return $case_stats;
	}
	
}