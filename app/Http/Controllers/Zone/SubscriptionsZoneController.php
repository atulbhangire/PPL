<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\subscription_plans;
use Session;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;

class SubscriptionsZoneController extends ZoneBaseClass
{
	public function __construct()
	{
		$this->subscription_plans = new subscription_plans;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}
	public function display($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$subscription_plans = $this->subscription_plans::select('*')
					->orderBy('is_active','DESC')
					->orderBy('subscription_plan_code')
					->get();
			return view('Admin.Subscription.viewSubscription', compact('subscription_plans'));
		}
		else
		{
			return redirect('/');
		}
	}
	public function addNew($zone_code)
	{
		if(Session::get('is_admin'))
		{
			return view('Admin.Subscription.addSubscription');
		}
		else
		{
			return redirect('/');
		}
	}
	public function save($request)
	{
		$subscription_code = $request->input('subscription_code');
		$checkSubscriptionCode = $this->checkSubscriptionCode($subscription_code);
		if($checkSubscriptionCode == 0)
		{
			Session::flash('subscription_danger', 'Error! Subscription code already exists.');
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
			return redirect($zone_name);
		}
		else if($checkSubscriptionCode == FALSE)
		{
			Session::flash('subscription_danger', 'Error! Something went wrong.');
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
			return redirect($zone_name);
		}
		$subscription_name = $request->input('subscription_name');
		$checkSubscriptionName = $this->checkSubscriptionName($subscription_name);
		if($checkSubscriptionName == 0)
		{
			Session::flash('subscription_danger', 'Error! Subscription name already exists.');
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
			return redirect($zone_name);
		}
		else if($checkSubscriptionName == FALSE)
		{
			Session::flash('subscription_danger', 'Error! Something went wrong.');
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
			return redirect($zone_name);
		}
		$subscription_description = $request->input('subscription_description');
		$subscription_duration = $request->input('subscription_duration');
		$subscription_renewal_date = $request->input('subscription_renewal_date');
		$subscription_base_price = $request->input('subscription_base_price');
		$subscription_service_tax = $request->input('subscription_service_tax');
		$subscription_total_price = $request->input('subscription_total_price');
		$stock_query_limit_alloted = $request->input('stock_query_limit_alloted');
		$subscription_status = $request->input('subscription_status');
		if(!empty($subscription_description) and !empty($subscription_duration) and !empty($subscription_renewal_date) and !empty($subscription_base_price) and !empty($subscription_service_tax) and !empty($subscription_total_price) and isset($subscription_status))
		{
			$data = array(
				'subscription_plan_code' => $subscription_code,
				'subscription_plan_name' => $subscription_name,
				'subscription_plan_description' => $subscription_description,
				'subscription_plan_duration' => $subscription_duration,
				'subscription_renew_allowed_before_n_days' => $subscription_renewal_date,
				'subscription_base_price_INR' => $subscription_base_price,
				'subscription_service_tax_INR' => $subscription_service_tax,
				'subscription_total_price_INR' => $subscription_total_price,
				'stock_query_limit_alloted' => $stock_query_limit_alloted,
				'is_active' => $subscription_status
			);
			$saved = $this->saveSubscription($data);
			if($saved)
			{
				$Message = "Subscription Plan: " . $subscription_name . " added successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
				$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
				Session::flash('subscription_message', $subscription_name . ' plan added successfully.');
				$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
				return redirect($zone_name);
			}
			else
			{
				Session::flash('subscription_danger', $subscription_name . ' plan added not added.');
				$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
				return redirect($zone_name);
			}
		}
		else
		{
			Session::flash('subscription_danger', 'Error! Something went wrong.');
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
			return redirect($zone_name);
		}
	}
	public function checkSubscriptionCode($subscription_code)
	{
		if(!empty($subscription_code))
		{
			if ($this->subscription_plans::where('subscription_plan_code', '=', $subscription_code)->exists())
			{
				return 0;
			}
		}
		else
		{
			return FALSE;
		}
		return TRUE;
	}
	public function checkSubscriptionName($subscription_name)
	{
		if(!empty($subscription_name))
		{
			if ($this->subscription_plans::where('subscription_plan_name', '=', $subscription_name)->exists())
			{
				return 0;
			}
		}
		else
		{
			return FALSE;
		}
		return TRUE;
	}
	public function saveSubscription($data)
	{
		$this->subscription_plans->subscription_plan_code = $data['subscription_plan_code'];
		$this->subscription_plans->subscription_plan_name = $data['subscription_plan_name'];
		$this->subscription_plans->subscription_plan_description = $data['subscription_plan_description'];
		$this->subscription_plans->subscription_plan_duration = $data['subscription_plan_duration'];
		$this->subscription_plans->subscription_renew_allowed_before_n_days = $data['subscription_renew_allowed_before_n_days'];
		$this->subscription_plans->subscription_base_price_INR = $data['subscription_base_price_INR'];
		$this->subscription_plans->subscription_service_tax_INR = $data['subscription_service_tax_INR'];
		$this->subscription_plans->subscription_total_price_INR = $data['subscription_total_price_INR'];
		$this->subscription_plans->stock_query_limit_alloted = $data['stock_query_limit_alloted'];
		$this->subscription_plans->is_active = $data['is_active'];
		if($this->subscription_plans->save())
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	public function edit($zone_code, $id)
	{
		if(Session::get('is_admin'))
		{
			$subscription_plans = $this->subscription_plans::select('*')->where('subscription_plan_id', $id)->first();
			Session::flash('edit_subscription', TRUE);
			Session::flash('edit_subscription_id', $id);
			Session::flash('edit_subscription_code', $subscription_plans->subscription_plan_code);
			Session::flash('edit_subscription_name', $subscription_plans->subscription_plan_name);
			Session::flash('edit_subscription_description', $subscription_plans->subscription_plan_description);
			Session::flash('edit_subscription_duration', $subscription_plans->subscription_plan_duration);
			Session::flash('edit_subscription_renew_days', $subscription_plans->subscription_renew_allowed_before_n_days);
			Session::flash('edit_subscription_base_price_INR', $subscription_plans->subscription_base_price_INR);
			Session::flash('edit_subscription_service_tax_INR', $subscription_plans->subscription_service_tax_INR);
			Session::flash('edit_subscription_total_price_INR', $subscription_plans->subscription_total_price_INR);
			Session::flash('edit_stock_query_limit_alloted', $subscription_plans->stock_query_limit_alloted);
			Session::flash('edit_is_active', $subscription_plans->is_active);
			return view('Admin.Subscription.addSubscription');
		}
		else
		{
			return redirect('/');
		}
	}
	public function update($request)
	{
		if(Session::get('is_admin'))
		{
			$subscription_id = $request->input('subscription_id');
			$subscription_code = $request->input('subscription_code');
			$subscription_code_hidden = $request->input('subscription_code_hidden');
			if($subscription_code != $subscription_code_hidden)
			{
				$checkSubscriptionCode = $this->checkSubscriptionCode($subscription_code);
				if($checkSubscriptionCode == 0)
				{
					Session::flash('subscription_danger', 'Error! Subscription plan code already exists.');
					$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
					return redirect($zone_name);
				}
				else if($checkSubscriptionCode == FALSE)
				{
					Session::flash('subscription_danger', 'Error! Something went wrong.');
					$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
					return redirect($zone_name);
				}
			}
			$subscription_name = $request->input('subscription_name');
			$subscription_name_hidden = $request->input('subscription_name_hidden');
			// echo $subscription_name_hidden . "asdsad";
			if($subscription_name != $subscription_name_hidden)
			{
				$checkSubscriptionName = $this->checkSubscriptionName($subscription_name);
				if($checkSubscriptionName == 0)
				{
					Session::flash('subscription_danger', 'Error! ' . $subscription_name . ' plan name already exists.');
					$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
					return redirect($zone_name);
				}
				else if($checkSubscriptionName == FALSE)
				{
					Session::flash('subscription_danger', 'Error! Something went wrong.');
					$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
					return redirect($zone_name);
				}
			}
			$data = array(
				'subscription_plan_code' => $subscription_code,
				'subscription_plan_name' => $subscription_name,
				'subscription_plan_description' => $request->input('subscription_description'),
				'subscription_plan_duration' => $request->input('subscription_duration'),
				'subscription_renew_allowed_before_n_days' => $request->input('subscription_renewal_date'),
				'subscription_base_price_INR' => $request->input('subscription_base_price'),
				'subscription_service_tax_INR' => $request->input('subscription_service_tax'),
				'subscription_total_price_INR' => $request->input('subscription_total_price'),
				'stock_query_limit_alloted' => $request->input('stock_query_limit_alloted'),
				'is_active' => $request->input('subscription_status'),
			);
			$subscription_update = $this->subscription_plans::first()->where(array('subscription_plan_id' => $subscription_id));
			$updateNow = $subscription_update->update($data);
			if($updateNow)
			{
				$Message = "Subscription Plan: " . $subscription_name . " updated successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
				$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
				Session::flash('subscription_message', $subscription_name. ' plan updated successfully.');
				$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
				return redirect($zone_name);
			}
			else
			{
				Session::flash('subscription_danger', 'Error! '. $subscription_name . ' plan was not updated.');
				$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
				return redirect($zone_name);
			}
		}
		else
		{
			return redirect('/');
		}
	}
	public function delete($zone_code, $id)
    {
    	$subscription_plan_name = $this->subscription_plans::select('subscription_plan_name')->where(array('subscription_plan_id' => $id))->first();
		if($this->subscription_plans::first()->where(array('subscription_plan_id' => $id))->delete())
		{
			$Message = "Subscription Plan: " . $subscription_plan_name->subscription_plan_name . " deleted successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
			$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
			Session::flash('subscription_message', $subscription_plan_name->subscription_plan_name . ' plan deleted successfully.');
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
			return redirect($zone_name);
		}
		else
		{
			Session::flash('subscription_danger', $subscription_plan_name->subscription_plan_name . ' plan was not deleted.');
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
			return redirect($zone_name);
		}
    }
}