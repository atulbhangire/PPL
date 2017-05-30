<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use Session;
use Config;
use App\gifted_stock_queries;

class GiftedQueriesController extends Controller
{
	public function display($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			return view('Admin.GiftedQueries.indexGiftedQueries');
		}
		else
		{
			return redirect('/');
		}
	}

	public function renderTable($zone_code)
	{
		if(Session::get('is_admin'))
		{
			$gifted_queries = gifted_stock_queries::select('*');

        	return \Datatables::of($gifted_queries)->make(true);
        }
		else
		{
			return "FALSE";
		}
	}
}