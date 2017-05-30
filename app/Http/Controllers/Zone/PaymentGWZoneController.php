<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;
use App\Http\Controllers\ZoneBaseClass;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\payment_gateways;
use Session;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;
// use App\Http\Controllers\Zone\ZoneRenderer;

class PaymentGWZoneController extends ZoneBaseClass
{
	public function __construct()
	{
		$this->payment_gateways = new payment_gateways;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}
    public function display($zone_code){
    	if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$payment_gateways = $this->payment_gateways::select('*')
					->orderBy('pgw_is_active','DESC')
					->orderBy('pgw_pg_code')
					->get();
	   		return view('Admin.PaymentGW.indexPaymentGW',compact('payment_gateways'));
	   	}
	   	else
	   	{
	   		return redirect('/');
	   	}
    }

    public function addNew($zone_code)
    {
   		return view('Admin.PaymentGW.addPaymentGW');
    }

    public function save($request){
    	if (payment_gateways::where('pgw_pg_code', '=', $request->input('payment_code'))->exists()) 
    	{
    		Session::flash('error_message_danger', 'Unable to add new gateway! Gateway code Exists');
		}
		else
		{
			$data = array(
				'pgw_pg_code' => $request->input('payment_code'),
				'pgw_pg_name' => $request->input('payment_name'),
				'pgw_pg_description' => $request->input('payment_description'),
				'pgw_pg_currency' => $request->input('payment_currency'),
				'pgw_currency_exchange_rate' => $request->input('payment_exchange'),
				'pgw_access_details' => $request->input('payment_access_details'),
				'pgw_is_active' => $request->input('payment_status')
			);
			$result = $this->saveGateway($data);
			if($result)
			{	
				Session::flash('error_message', 'Gateway added successfully!');
				$Message = "Payment Gateway: " . $request->input('payment_name') . " added successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
				$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
			}
			else
			{
				Session::flash('error_message_danger', 'Unable to add new Gateway!');
			}
		}
		$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
		return redirect($zone_name);
	}

	public function saveGateway($data)
	{
		$this->payment_gateways->pgw_pg_code = $data['pgw_pg_code'];
		$this->payment_gateways->pgw_pg_name = $data['pgw_pg_name'];
		$this->payment_gateways->pgw_pg_description = $data['pgw_pg_description'];
		$this->payment_gateways->pgw_pg_currency = $data['pgw_pg_currency'];
		$this->payment_gateways->pgw_currency_exchange_rate = $data['pgw_currency_exchange_rate'];
		$this->payment_gateways->pgw_access_details = $data['pgw_access_details'];
		$this->payment_gateways->pgw_is_active = $data['pgw_is_active'];
		
		
		if($this->payment_gateways->save()){
			return TRUE;
		}
		else{
			return FALSE;
		}
	}

    public function edit($zone_code, $id)
    {
    	if(Session::get('is_admin'))
		{
			$payment_obj = payment_gateways::select('pgw_pg_code','pgw_pg_name','pgw_pg_description','pgw_pg_currency', 'pgw_currency_exchange_rate', 'pgw_access_details','pgw_is_active')->where('pgw_id', $id)->first();
			Session::flash('edit_payment', TRUE);
			Session::flash('payment_id', $id);
			Session::flash('edit_payment_code', $payment_obj->pgw_pg_code);
			Session::flash('edit_payment_name', $payment_obj->pgw_pg_name);
			Session::flash('edit_payment_description', $payment_obj->pgw_pg_description);
			Session::flash('edit_payment_currency', $payment_obj->pgw_pg_currency);
			Session::flash('edit_payment_exchange_rate', $payment_obj->pgw_currency_exchange_rate);
			Session::flash('edit_payment_access', $payment_obj->pgw_access_details);
			Session::flash('edit_payment_active', $payment_obj->pgw_is_active);
			return view('Admin.PaymentGW.addPaymentGW');
		}
		else
		{
			return redirect('/');
		}
	}

	public function update($request)
	{
		if($request->session()->get('is_admin'))
		{
			$data = array(
				'pgw_pg_code' => $request->input('payment_code'),
				'pgw_pg_name' => $request->input('payment_name'),
				'pgw_pg_description' => $request->input('payment_description'),
				'pgw_pg_currency' => $request->input('payment_currency'),
				'pgw_currency_exchange_rate' => $request->input('payment_exchange'),
				'pgw_access_details' => $request->input('payment_access_details'),
				'pgw_is_active' => $request->input('payment_status')
			);
		
			if($request->input('payment_code_hidden') != $request->input('payment_code'))
			{
				if (payment_gateways::where('pgw_pg_code', '=', $request->input('payment_code'))->exists()) 
				{
					Session::flash('error_message_danger', 'Unable to add new gateway! Gateway code Exists');
				}
				else
				{
					$payment_obj = payment_gateways::first()->where(array('pgw_id' => $request->input('payment_id')));
					$updateNow = $payment_obj->update($data);
					if($updateNow){
						Session::flash('error_message', 'Gateway Updated successfully!');
						$Message = "Payment Gateway: " . $request->input('payment_name') . " updated successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
						$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
					}else{
						Session::flash('error_message_danger', 'Unable to edit Gateway!');
					}
				}

			}
			else
			{
				$payment_obj = payment_gateways::first()->where(array('pgw_id' => $request->input('payment_id')));
				$updateNow = $payment_obj->update($data);
				if($updateNow){
					Session::flash('error_message', 'Gateway Updated successfully!');
				}else{
					Session::flash('error_message_danger', 'Unable to edit Gateway!');
				}
			}
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
			return redirect($zone_name);
		}
		else
		{
			return redirect('/');
		}
	}

	public function delete($zone_code, $id)
	{
		$payment_obj = payment_gateways::first()->where(array('pgw_id' => $id));
		$payment_obj1 = payment_gateways::select('pgw_pg_name')->where('pgw_id', $id)->first();
		$delete = $payment_obj->delete();
		if($delete)
		{
			Session::flash('error_message', 'Gateway '.$payment_obj1->pgw_pg_name.' deleted successfully!');
			$Message = "Payment Gateway: " . $payment_obj1->pgw_pg_name . " deleted successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
			$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
		}else
		{
			Session::flash('error_message_danger', 'Unable to delete '.$payment_obj1->pgw_pg_name.' gateway!');
		}
		$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
		return redirect($zone_name);
	}

   public function get_sub_menu()
   {
   		return "PAYMENT GATEWAY MENU VIEW";
   }
}
