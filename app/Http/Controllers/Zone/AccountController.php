<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\order_details;
use App\user_profiles;
use App\payment_gateways;
use Session;
use Config;
use URL;
use DB;
use stdClass;
use Response;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;
use App\Http\Controllers\Zone\OrderDetailsController;

class AccountController extends ZoneBaseClass
{
	public function __construct() {
		$this->order_details = new order_details;
		$this->user_profiles = new user_profiles;
        $this->order_details_cntr = new OrderDetailsController;
		$this->aws = new CustomAwsController;
		$this->Alert_Admin = Config::get('config_path_vars.Alert_Admin');
	}

	public function display($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$user_url = Config::get('config_path_vars.user_spt_base');

			/*TABLE 2 STARTS*/
			$payment_gateways = payment_gateways::select('pgw_pg_name')->orderBy('pgw_pg_code')->get()->toArray();
			$last_seven_days_order_details = order_details::select('order_start_date', 'order_subscription_total_price_INR', 'order_pg_fees', 'order_pg_name')->where('order_status', 'Subscribed')->whereRaw("date(order_start_date) >= date(CURRENT_DATE - INTERVAL 7 DAY)")->where('order_is_complimentary', 0)->get()->toArray();
			$this_month_order_details = order_details::select('order_start_date', 'order_subscription_total_price_INR', 'order_pg_fees', 'order_pg_name')->where('order_status', 'Subscribed')->whereRaw("YEAR(order_start_date) = YEAR(CURRENT_DATE) AND MONTH(order_start_date) = MONTH(CURRENT_DATE)")->where('order_is_complimentary', 0)->get()->toArray();
			$last_month_order_details = order_details::select('order_start_date', 'order_subscription_total_price_INR', 'order_pg_fees', 'order_pg_name')->where('order_status', 'Subscribed')->whereRaw("YEAR(order_start_date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(order_start_date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)")->where('order_is_complimentary', 0)->get()->toArray();
			$dm_date = [];
			$ymd_date = [];
			// Initialize all values to 0 - START
			for($i = 0; $i <= 7; $i++)
			{
				$dm_date[$i] = date('d/m',strtotime("-$i days"));
				$ymd_date[$i] = date('Y-m-d',strtotime("-$i days"));
				$subscription_stats['Total']['Order Count'][$dm_date[$i]] = 0;
				$subscription_stats['Total']["Gross Revenue (in '000)"][$dm_date[$i]] = 0;
				$subscription_stats['Total']['Fees (Incl. Service Tax)'][$dm_date[$i]] = 0;
			}
			$subscription_stats['Total']['Order Count']['This Month'] = 0;
			$subscription_stats['Total']["Gross Revenue (in '000)"]['This Month'] = 0;
			$subscription_stats['Total']['Fees (Incl. Service Tax)']['This Month'] = 0;

			$subscription_stats['Total']['Order Count']['Last Month'] = 0;
			$subscription_stats['Total']["Gross Revenue (in '000)"]['Last Month'] = 0;
			$subscription_stats['Total']['Fees (Incl. Service Tax)']['Last Month'] = 0;

			foreach ($payment_gateways as $payment_gateway)
			{
				for($i = 0; $i <= 7; $i++)
				{
					$subscription_stats[$payment_gateway['pgw_pg_name']]['Order Count'][$dm_date[$i]] = 0;
					$subscription_stats[$payment_gateway['pgw_pg_name']]["Gross Revenue (in '000)"][$dm_date[$i]] = 0;
					$subscription_stats[$payment_gateway['pgw_pg_name']]["Fees (Incl. Service Tax)"][$dm_date[$i]] = 0;

					$subscription_stats['Other']['Order Count'][$dm_date[$i]] = 0;
					$subscription_stats['Other']["Gross Revenue (in '000)"][$dm_date[$i]] = 0;
					$subscription_stats['Other']['Fees (Incl. Service Tax)'][$dm_date[$i]] = 0;
				}

				$subscription_stats[$payment_gateway['pgw_pg_name']]['Order Count']['This Month'] = 0;
				$subscription_stats[$payment_gateway['pgw_pg_name']]["Gross Revenue (in '000)"]['This Month'] = 0;
				$subscription_stats[$payment_gateway['pgw_pg_name']]['Fees (Incl. Service Tax)']['This Month'] = 0;

				$subscription_stats[$payment_gateway['pgw_pg_name']]['Order Count']['Last Month'] = 0;
				$subscription_stats[$payment_gateway['pgw_pg_name']]["Gross Revenue (in '000)"]['Last Month'] = 0;
				$subscription_stats[$payment_gateway['pgw_pg_name']]['Fees (Incl. Service Tax)']['Last Month'] = 0;
			}

			$subscription_stats['Other']['Order Count']['This Month'] = 0;
			$subscription_stats['Other']["Gross Revenue (in '000)"]['This Month'] = 0;
			$subscription_stats['Other']['Fees (Incl. Service Tax)']['This Month'] = 0;

			$subscription_stats['Other']['Order Count']['Last Month'] = 0;
			$subscription_stats['Other']["Gross Revenue (in '000)"]['Last Month'] = 0;
			$subscription_stats['Other']['Fees (Incl. Service Tax)']['Last Month'] = 0;
			// Initialize all values to 0 - END

			//PROCESS TOTAL LAST SEVEN DAYS - START
			for($i = 0; $i <= 7; $i++)
			{
				$every_day_total_order_count = 0;
				$every_day_total_gross_revenue = 0;
				$every_day_total_fees = 0;
				
				$every_day_pg_total_order_count[$dm_date[$i]] = 0;
				$every_day_pg_total_gross_revenue[$dm_date[$i]] = 0;
				$every_day_pg_total_fees[$dm_date[$i]] = 0;
				foreach ($last_seven_days_order_details as $value)
				{
					if(date('Y-m-d', strtotime($value['order_start_date'])) == $ymd_date[$i])
					{
						$every_day_total_order_count++;
						$every_day_total_gross_revenue = $value['order_subscription_total_price_INR'] / 1000;
						$every_day_total_fees = round($value['order_pg_fees']);

						$subscription_stats['Total']['Order Count'][$dm_date[$i]] = $every_day_total_order_count;
						$subscription_stats['Total']["Gross Revenue (in '000)"][$dm_date[$i]] = $subscription_stats['Total']["Gross Revenue (in '000)"][$dm_date[$i]] + $every_day_total_gross_revenue;
						$subscription_stats['Total']['Fees (Incl. Service Tax)'][$dm_date[$i]] = $subscription_stats['Total']['Fees (Incl. Service Tax)'][$dm_date[$i]] + $every_day_total_fees;

						foreach ($payment_gateways as $payment_gateway)
						{
							if($payment_gateway['pgw_pg_name'] == $value['order_pg_name'])
							{
								$every_day_pg_total_order_count[$dm_date[$i]]++;
								$subscription_stats[$payment_gateway['pgw_pg_name']]['Order Count'][$dm_date[$i]]++;

								$every_day_pg_total_gross_revenue[$dm_date[$i]] = $every_day_pg_total_gross_revenue[$dm_date[$i]] + $value['order_subscription_total_price_INR'] / 1000;
								$subscription_stats[$payment_gateway['pgw_pg_name']]["Gross Revenue (in '000)"][$dm_date[$i]] = $subscription_stats[$payment_gateway['pgw_pg_name']]["Gross Revenue (in '000)"][$dm_date[$i]] + $value['order_subscription_total_price_INR'] / 1000;;

								$every_day_pg_total_fees[$dm_date[$i]] = $every_day_pg_total_fees[$dm_date[$i]] + round($value['order_pg_fees']);
								$subscription_stats[$payment_gateway['pgw_pg_name']]["Fees (Incl. Service Tax)"][$dm_date[$i]] = $subscription_stats[$payment_gateway['pgw_pg_name']]["Fees (Incl. Service Tax)"][$dm_date[$i]] + round($value['order_pg_fees']);
							}
						}
					}
				}
				$subscription_stats['Other']['Order Count'][$dm_date[$i]] = $subscription_stats['Total']['Order Count'][$dm_date[$i]] - $every_day_pg_total_order_count[$dm_date[$i]];
				$subscription_stats['Other']["Gross Revenue (in '000)"][$dm_date[$i]] = $subscription_stats['Total']["Gross Revenue (in '000)"][$dm_date[$i]] - $every_day_pg_total_gross_revenue[$dm_date[$i]];
				$subscription_stats['Other']["Fees (Incl. Service Tax)"][$dm_date[$i]] = $subscription_stats['Total']["Fees (Incl. Service Tax)"][$dm_date[$i]] - $every_day_pg_total_fees[$dm_date[$i]];
			}
			//PROCESS TOTAL LAST SEVEN DAYS - END

			//PROCESS TOTAL THIS MONTH - START
			$this_month_total_gross_revenue = 0;
			$this_month_total_fees = 0;
			foreach ($this_month_order_details as $value)
			{
				$this_month_total_gross_revenue = $value['order_subscription_total_price_INR'] / 1000;
				$this_month_total_fees = round($value['order_pg_fees']);

				$subscription_stats['Total']['Order Count']['This Month']++;
				$subscription_stats['Total']["Gross Revenue (in '000)"]['This Month'] = $subscription_stats['Total']["Gross Revenue (in '000)"]['This Month'] + $this_month_total_gross_revenue;
				$subscription_stats['Total']['Fees (Incl. Service Tax)']['This Month'] = $subscription_stats['Total']['Fees (Incl. Service Tax)']['This Month'] + $this_month_total_fees;
			}
			$this_month_break_point = 0;
			$this_month_pg_total_order_count_add = 0;
			$this_month_pg_total_gross_revenue = 0;
			$this_month_pg_total_fees = 0;
			foreach ($payment_gateways as $payment_gateway)
			{
				if($this_month_break_point == count($this_month_order_details))
				{
					break;
				}
				$subscription_stats[$payment_gateway['pgw_pg_name']]['Order Count']['This Month'] = 0;
				$subscription_stats[$payment_gateway['pgw_pg_name']]["Gross Revenue (in '000)"]['This Month'] = 0;
				$subscription_stats[$payment_gateway['pgw_pg_name']]['Fees (Incl. Service Tax)']['This Month'] = 0;
				foreach ($this_month_order_details as $value)
				{
					if($payment_gateway['pgw_pg_name'] == $value['order_pg_name'])
					{
						$this_month_pg_total_order_count_add++;
						$subscription_stats[$payment_gateway['pgw_pg_name']]['Order Count']['This Month']++;

						$this_month_pg_total_gross_revenue = $this_month_pg_total_gross_revenue + $value['order_subscription_total_price_INR'] / 1000;
						$subscription_stats[$payment_gateway['pgw_pg_name']]["Gross Revenue (in '000)"]['This Month'] = $subscription_stats[$payment_gateway['pgw_pg_name']]["Gross Revenue (in '000)"]['This Month'] + $value['order_subscription_total_price_INR'] / 1000;

						$this_month_pg_total_fees = $this_month_pg_total_fees + round($value['order_pg_fees']);
						$subscription_stats[$payment_gateway['pgw_pg_name']]["Fees (Incl. Service Tax)"]['This Month'] = $subscription_stats[$payment_gateway['pgw_pg_name']]["Fees (Incl. Service Tax)"]['This Month'] + round($value['order_pg_fees']);
						$this_month_break_point++;
					}
				}
			}
			$subscription_stats['Other']['Order Count']['This Month'] = $subscription_stats['Total']['Order Count']['This Month'] - $this_month_pg_total_order_count_add;
			$subscription_stats['Other']["Gross Revenue (in '000)"]['This Month'] = $subscription_stats['Total']["Gross Revenue (in '000)"]['This Month'] - $this_month_pg_total_gross_revenue;
			$subscription_stats['Other']["Fees (Incl. Service Tax)"]['This Month'] = $subscription_stats['Total']["Fees (Incl. Service Tax)"]['This Month'] - $this_month_pg_total_fees;
			//PROCESS TOTAL THIS MONTH - END

			//PROCESS TOTAL LAST MONTH - START
			$last_month_total_gross_revenue = 0;
			$last_month_total_fees = 0;
			foreach ($last_month_order_details as $value)
			{
				$last_month_total_gross_revenue = $value['order_subscription_total_price_INR'] / 1000;
				$last_month_total_fees = round($value['order_pg_fees']);

				$subscription_stats['Total']['Order Count']['Last Month']++;
				$subscription_stats['Total']["Gross Revenue (in '000)"]['Last Month'] = $subscription_stats['Total']["Gross Revenue (in '000)"]['Last Month'] + $last_month_total_gross_revenue;
				$subscription_stats['Total']['Fees (Incl. Service Tax)']['Last Month'] = $subscription_stats['Total']['Fees (Incl. Service Tax)']['Last Month'] + $last_month_total_fees;
			}
			$last_month_break_point = 0;
			$last_month_pg_total_order_count_add = 0;
			$last_month_pg_total_gross_revenue = 0;
			$last_month_pg_total_fees = 0;
			foreach ($payment_gateways as $payment_gateway)
			{
				if($last_month_break_point == count($last_month_order_details))
				{
					break;
				}
				$subscription_stats[$payment_gateway['pgw_pg_name']]['Order Count']['Last Month'] = 0;
				$subscription_stats[$payment_gateway['pgw_pg_name']]["Gross Revenue (in '000)"]['Last Month'] = 0;
				$subscription_stats[$payment_gateway['pgw_pg_name']]['Fees (Incl. Service Tax)']['Last Month'] = 0;
				foreach ($last_month_order_details as $value)
				{
					if($payment_gateway['pgw_pg_name'] == $value['order_pg_name'])
					{
						$last_month_pg_total_order_count_add++;
						$subscription_stats[$payment_gateway['pgw_pg_name']]['Order Count']['Last Month']++;

						$last_month_pg_total_gross_revenue = $last_month_pg_total_gross_revenue + $value['order_subscription_total_price_INR'] / 1000;
						$subscription_stats[$payment_gateway['pgw_pg_name']]["Gross Revenue (in '000)"]['Last Month'] = $subscription_stats[$payment_gateway['pgw_pg_name']]["Gross Revenue (in '000)"]['Last Month'] + $value['order_subscription_total_price_INR'] / 1000;

						$last_month_pg_total_fees = $last_month_pg_total_fees + round($value['order_pg_fees']);
						$subscription_stats[$payment_gateway['pgw_pg_name']]["Fees (Incl. Service Tax)"]['Last Month'] = $subscription_stats[$payment_gateway['pgw_pg_name']]["Fees (Incl. Service Tax)"]['Last Month'] + round($value['order_pg_fees']);
						$last_month_break_point++;
					}
				}
			}
			$subscription_stats['Other']['Order Count']['Last Month'] = $subscription_stats['Total']['Order Count']['Last Month'] - $last_month_pg_total_order_count_add;
			$subscription_stats['Other']["Gross Revenue (in '000)"]['Last Month'] = $subscription_stats['Total']["Gross Revenue (in '000)"]['Last Month'] - $last_month_pg_total_gross_revenue;
			$subscription_stats['Other']["Fees (Incl. Service Tax)"]['Last Month'] = $subscription_stats['Total']["Fees (Incl. Service Tax)"]['Last Month'] - $last_month_pg_total_fees;
			//PROCESS TOTAL LAST MONTH - END

			$temp = $subscription_stats['Other'];
			unset($subscription_stats['Other']);
			$subscription_stats['Other'] = $temp;

			unset($last_seven_days_order_details);
			unset($this_month_order_details);
			unset($last_month_order_details);
			unset($dm_date);
			unset($ymd_date);
			//dd($subscription_stats);
			/*TABLE 2 ENDS*/

			return view('Admin.Account.account', compact('user_url', 'subscription_stats'));
		}
		else
		{
			return redirect('/');
		}
	}

	public function save($request)
	{
		/*array:3 [▼
		  "_token" => "izwqUYdKxM1wd04bZZKvrV6IOjattQzq5jOcPWTD"
		  "month" => "9"
		  "year" => "2016"
		]*/
		$inputData = $request->all();
		if(empty($inputData) || empty($inputData['month']) || empty($inputData['year'])) {
			return redirect()->back()->with('alert_danger', 'Error! Something went wrong..');

		} else {
			$month = $inputData['month'];
			$year = $inputData['year'];
			$orderData = $this->order_details::select(
					'order_id',
					'order_username',
					'order_email_id',
					'order_mobile_country_code',
					'order_mobile_number',
					'order_subscription_plan_name',
					'order_subscription_duration',
					'order_subscription_base_price_INR',
					'order_subscription_service_tax_INR',
					'order_subscription_total_price_INR',
					'order_pg_name',
					'order_pg_fees_percent',
					'order_pg_settlement_date',
					'order_status',
					'order_start_date',
					'order_end_date'
					)
					->where(DB::raw('MONTH(order_start_date)'), '=', $month)
					->where(DB::raw('YEAR(order_start_date)'), '=', $year)
					->where('order_status', 'Subscribed')
					->where('order_is_complimentary', 0)
					->orderBy('order_start_date','ASC')
					->get()->toArray();
			// dd($orderData);
			if(!empty($orderData)) {
				$filename = "/tmp/Users_".time().".csv";
				$handle = fopen($filename, 'w+');
				fputcsv($handle, array('Order ID', 'Username',
					'Email', 'Mobile No', 'Plan Name', 'Plan Duration', 
					'Base Price', 'Service Tax', 'Total Price', 
					'PG Name', 'Fees Percent', 'Settlement Date', 
					'Status', 'Start Date', 'End Date'));	
				foreach($orderData as $order) {
					$order['order_mobile_number'] = $order['order_mobile_country_code'].$order['order_mobile_number'];
					unset($order['order_mobile_country_code']);
					fputcsv($handle, $order);
					// dd($order);
				}
				// dd($orderData);
				fclose($handle);
				$headers = array(
					'Content-Type' => 'text/csv',
				);
				// dd($headers);
				return Response::download($filename, 'Orders_'.(date('Y-m-d').'_'.date('H:i:s')).'.csv', $headers);
			} else {
				return redirect()->back()->with('alert_danger', 'Orders not found to download.');
			}
		}
	}

	public function update($request)
	{
		$postedData = $request->all();
		if(Session::get('is_admin'))
		{
			if(!empty($postedData)) {
				if(!empty($postedData['order_id']) && !empty($postedData['cheque_no']) && !empty($postedData['cheque_amt'])) {
					$order_details = order_details::select('order_id','order_usr_id','order_username','order_subscription_plan_code','order_status','order_start_date','order_end_date','stock_query_limit_alloted','stock_query_limit_pending')
		        		->where('order_id',$postedData['order_id'])
		        		->where('order_subscription_total_price_INR',$postedData['cheque_amt'])
		        		->whereRaw("order_pg_transaction_id LIKE '%" . $postedData['cheque_no'] . "%'")
		        		->first();
		        		// dd($order_details);
	        		if(!empty($order_details) && !empty($order_details->order_status)) {
	        			if($order_details->order_status=='Processing') {
	        				$cal_order_data = $this->order_details_cntr->calculateOrderData($order_details->order_subscription_plan_code, $order_details->order_usr_id);
	        				// Activate Order
	        				$orderData  = array(
					                'order_start_date' => $cal_order_data['order_start_date'],
					                'order_end_date'=> (string)$cal_order_data['order_end_date'],
					                'order_status' => 'Subscribed',
					                'stock_query_limit_alloted' => $cal_order_data['stock_query_limit'],
					                'stock_query_limit_pending' => $cal_order_data['stock_query_limit']
					                );
	        				// dd($orderData);
					        $updateNowOrder = order_details::where('order_id', $postedData['order_id'])->update($orderData);

					        $userData  = array(
					            'usr_last_active_order_id' => $postedData['order_id'], 
					            'usr_status' => 'Subscribed');
					        $updateUserData = user_profiles::where('usr_id', $order_details->order_usr_id)->update($userData);

					        // Send Payment received email to user.
					        $this->order_details_cntr->send_user_email_for_order_activated($postedData['order_id']);

					        // Send admin alert
					        $Message = "Order Info Changed From Admin \n\nOrder Number : " . $postedData['order_id'] . " updated successfully. \n IP Address :".$this->aws->getClientIps();
							$Message .= "\n Time of Event :".Carbon::now();
							$Message .= "\nFields Changes :";
							$Message .= "\n \n Order Status : \n Old Value : ".$order_details->order_status." \n New Value : Subscribed";
							$Message .= "\n\n Start Date : \n Old Value : ".$order_details->order_start_date." \n New Value : ".$cal_order_data['order_start_date'];
							$Message .= "\n\n End Date : \n Old Value : ".$order_details->order_end_date." \n New Value : ".(string)$cal_order_data['order_end_date'];
							$Message .= "\n\n Stock Query Limit Alloted : \n Old Value : ".$order_details->stock_query_limit_alloted." \n New Value : ".$cal_order_data['stock_query_limit'];
							$Message .= "\n\n Stock Query Limit Pending : \n Old Value : ".$order_details->stock_query_limit_pending." \n New Value : ".$cal_order_data['stock_query_limit'];
							$this->aws->send_admin_alerts($this->Alert_Admin,$Message);
							// dd($Message);

					        return redirect()->back()->with('success_message', "Order ".$postedData['order_id']." for user ".$order_details->order_username." activated successfully.");
	        			} else if($order_details->order_status=='Subscribed') {
	        				return redirect()->back()->with('error_message', 'Order already activated.');
	        			} else {
	        				return redirect()->back()->with('error_message', 'Can not activate failed order.');
	        			}
	        		} else {
	        			return redirect()->back()->with('error_message', 'Information does not match. Kindly check entered info.');
	        		}
				}
			} 
			return redirect()->back()->with('error_message', 'Error! Something went wrong.');
		}
		else
		{
			return redirect('/');
		}
	}

	public function autoCompleteOrderId($request){
		
        $data = array();
        $order_details_exact = order_details::select('order_id')
        		->where('order_pg_payment_method','Cheque/DD')
        		// ->where('order_pg_name','Cheque/DD')
        		->whereRaw("order_id LIKE '%" . $request['query'] . "%'
    				order by case 
	    				when order_id like '" . $request['query'] . "'  then 1  
	                  	when order_id like '" . $request['query'] . "%' then 2  
	                  	when order_id like '%" . $request['query'] ."%' then 3 end")
        		->limit(100)->get();
        if(!empty($order_details_exact)) {
	        foreach ($order_details_exact as $order_details_exact){
	        	array_push($data, (string)$order_details_exact->order_id);
	        }
	    }

        $suggestions = array('suggestions' => $data );

        $data = json_encode($suggestions);
        return $data;
    }

    public function checkOrderId($request){
		
        $order_details = order_details::select('order_id')->where('order_id', $request['query'])->get();
		$data = count($order_details); 
        return $data;
    }

    public function getOrderInfo($request){
		
        $order_details = order_details::select('order_usr_id','order_username','order_subscription_plan_code','order_subscription_plan_name','order_status','order_start_date','order_end_date')->where('order_id', $request['query'])->first();
        if(!empty($order_details) && isset($order_details->order_status) && $order_details->order_status=='Processing') {
	        $cal_order_data = $this->order_details_cntr->calculateOrderData($order_details->order_subscription_plan_code, $order_details->order_usr_id);
	        $order_details->order_start_date = $cal_order_data['order_start_date'];
        	$order_details->order_end_date = (string)$cal_order_data['order_end_date'];
	    }	
		// $data = count($order_details); 
        return $order_details;
    }
}