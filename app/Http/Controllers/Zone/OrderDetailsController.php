<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\order_details;
use App\user_profiles;
use App\subscription_plans;
use Session;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;
define("SITE_ADDRESS1",config('config_path_vars.site_address'));

class OrderDetailsController extends ZoneBaseClass
{
    public function __construct()
	{
		$this->order_details = new order_details;
		$this->aws = new CustomAwsController;
		$this->Alert_Admin = Config::get('config_path_vars.Alert_Admin');
	}
	public function display($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$order_details = $this->order_details::select('*')->orderBy('order_id','DESC')->paginate(1000);
			return view('Admin.OrderDetails.indexOrderDetails', compact('order_details'));
		}
		else
		{
			return redirect('/');
		}
	}

	public function renderTable($zone_code){
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
						 
			// Array of database columns which should be read and sent back to DataTables.
			// The `db` parameter represents the column name in the database, while the `dt`
			// parameter represents the DataTables column identifier. In this case simple
			// indexes
                                                          
			$columns = array(
			    array( 'db' => 'order_id', 'dt' => 0 ),
			    array( 'db' => 'order_username',  'dt' => 1 ),
			    array( 'db' => 'order_email_id',   'dt' => 2 ),
			    array( 'db' => 'order_mobile_number',     'dt' => 3 ),
			    array( 'db' => 'order_subscription_plan_name',     'dt' => 4 ),
			    array( 'db' => 'order_subscription_total_price_INR',     'dt' => 5 ),
			    array( 'db' => 'order_pg_name',     'dt' => 6 ),
			    array( 'db' => 'order_status',     'dt' => 7 ),
			    array( 'db' => 'order_start_date',     'dt' => 8 ),
			    array( 'db' => 'order_end_date',     'dt' => 9 ),
			    array( 'db' => 'order_usr_id',     'dt' => 10 ),
			    array( 'db' => 'order_user_profile_json_dump',     'dt' => 11 ),
			    array( 'db' => 'order_subscription_plan_code',     'dt' => 12 ),
			    array( 'db' => 'order_subscription_duration',     'dt' => 13 ),
			    array( 'db' => 'order_subscription_base_price_INR',     'dt' => 14 ),
			    array( 'db' => 'order_subscription_service_tax_INR',     'dt' => 15 ),
			    array( 'db' => 'order_pg_code',     'dt' => 16 ),
			    array( 'db' => 'order_pg_currency',     'dt' => 17 ),
			    array( 'db' => 'order_currency_exchange_rate',     'dt' => 18 ),
			    array( 'db' => 'order_created_ip',     'dt' => 19 ),
			    array( 'db' => 'order_creation_time',     'dt' => 20 ),
			    array( 'db' => 'order_modified_time',     'dt' => 21 ),
			    array( 'db' => 'order_modified_by',     'dt' => 22 ),
			    array( 'db' => 'order_questionnaire_1',     'dt' => 23 ),
			    array( 'db' => 'order_questionnaire_2',     'dt' => 24 ),
			    array( 'db' => 'order_questionnaire_3',     'dt' => 25 ),
			    array( 'db' => 'order_questionnaire_4',     'dt' => 26 ),
			    array( 'db' => 'order_questionnaire_5',     'dt' => 27 ),
			    array( 'db' => 'order_questionnaire_6',     'dt' => 28 ),
			    array( 'db' => 'order_questionnaire_7',     'dt' => 29 ),
			    array( 'db' => 'order_questionnaire_8',     'dt' => 30 ),
			    array( 'db' => 'order_questionnaire_9',     'dt' => 31 ),
			    array( 'db' => 'order_our_estimate',     'dt' => 32 ),
			    array( 'db' => 'order_user_estimate',     'dt' => 33 ),
			    array( 'db' => 'order_pg_return_code',     'dt' => 34 ),
			    array( 'db' => 'order_pg_payment_status',     'dt' => 35 ),
			    array( 'db' => 'order_pg_gross_amount',     'dt' => 36 ),
			    array( 'db' => 'order_pg_fees',     'dt' => 37 ),
			    array( 'db' => 'order_pg_net_amount',     'dt' => 38 ),
			    array( 'db' => 'order_pg_transaction_id',     'dt' => 39 ),
			    array( 'db' => 'order_pg_payment_method',     'dt' => 40 ),
			    array( 'db' => 'order_pg_fees_percent',     'dt' => 41 ),
			    array( 'db' => 'order_pg_settlement_date',     'dt' => 42 ),
			    array( 'db' => 'order_pg_payment_gateway_dump',     'dt' => 43 ),
			    array( 'db' => 'stock_query_limit_alloted',     'dt' => 44 ),
			    array( 'db' => 'stock_query_limit_pending',     'dt' => 45 ),
			    array( 'db' => 'created_at',     'dt' => 46 ),
			    array( 'db' => 'updated_at',     'dt' => 47 )
			    
			);

			$orders = order_details::select('*');//->orderBy('order_id','DESC');

        	return \Datatables::of($orders)->editColumn('order_id', function ($orders) {
                return '<a href="/Admin/'.Session::get('zone_name').'/edit/'.$orders->order_id.'">'.$orders->order_id.'</a>';
            })->make(true);
			 
			

			//return view('Admin.UserProfiles.indexUserProfiles', compact('user_profiles'));
		}
		else
		{
			return "FALSE";
		}
	}

	public function addNew($zone_code){

	}

	public function save($request){

	}

	public function edit($zone_code, $id)
    {
    	if(Session::get('is_admin'))
		{
			$order_obj = order_details::select('*')->where('order_id', $id)->first();

			// Start -- Calculate order start, end date and query limit
			if(isset($order_obj->order_status) && $order_obj->order_status=='Processing')
			{
				$cal_order_data = $this->calculateOrderData($order_obj->order_subscription_plan_code, $order_obj->order_usr_id);
				$order_obj->order_start_date = $cal_order_data['order_start_date'];
				$order_obj->order_end_date = $cal_order_data['order_end_date'];
				$order_obj->stock_query_limit_alloted = $cal_order_data['stock_query_limit'];
				$order_obj->stock_query_limit_pending = $cal_order_data['stock_query_limit'];
			}
	        // End -- Calculate order start, end date and query limit

			$order_details = $order_obj;
			Session::flash('edit_order', TRUE);
			Session::flash('order_id', $id);

			Session::flash('order_pg_settlement_date', $order_obj->order_pg_settlement_date);
			Session::flash('order_status', $order_obj->order_status);
			Session::flash('order_start_date', $order_obj->order_start_date);
			Session::flash('order_end_date', $order_obj->order_end_date);
			Session::flash('order_is_complimentary', $order_obj->order_is_complimentary);
////////////////////////////////////////////////////////////////////////////////////////////
			Session::flash('order_usr_id', $order_obj->order_usr_id);
			Session::flash('order_username', $order_obj->order_username);
			Session::flash('order_email_id', $order_obj->order_email_id);
			Session::flash('order_mobile_country_code', $order_obj->order_mobile_country_code);
			Session::flash('order_mobile_number', $order_obj->order_mobile_number);

			Session::flash('order_user_profile_json_dump', $order_obj->order_user_profile_json_dump);
			Session::flash('order_subscription_plan_code', $order_obj->order_subscription_plan_code);
			Session::flash('order_subscription_plan_name', $order_obj->order_subscription_plan_name);
			Session::flash('order_subscription_duration', $order_obj->order_subscription_duration);
			Session::flash('order_subscription_base_price_INR', $order_obj->order_subscription_base_price_INR);

			Session::flash('order_subscription_service_tax_INR', $order_obj->order_subscription_service_tax_INR);
			Session::flash('order_subscription_total_price_INR', $order_obj->order_subscription_total_price_INR);
			Session::flash('order_pg_code', $order_obj->order_pg_code);
			Session::flash('order_pg_name', $order_obj->order_pg_name);
			Session::flash('order_pg_currency', $order_obj->order_pg_currency);

			Session::flash('order_currency_exchange_rate', $order_obj->order_currency_exchange_rate);
			Session::flash('order_created_ip', $order_obj->order_created_ip);
			Session::flash('order_creation_time', $order_obj->order_creation_time);
			Session::flash('order_modified_time', $order_obj->order_modified_time);
			Session::flash('order_modified_by', $order_obj->order_modified_by);

			Session::flash('order_questionnaire_1', $order_obj->order_questionnaire_1);
			Session::flash('order_questionnaire_2', $order_obj->order_questionnaire_2);
			Session::flash('order_questionnaire_3', $order_obj->order_questionnaire_3);
			Session::flash('order_questionnaire_4', $order_obj->order_questionnaire_4);
			Session::flash('order_questionnaire_5', $order_obj->order_questionnaire_5);

			Session::flash('order_questionnaire_6', $order_obj->order_questionnaire_6);
			Session::flash('order_questionnaire_7', $order_obj->order_questionnaire_7);
			Session::flash('order_questionnaire_8', $order_obj->order_questionnaire_8);
			Session::flash('order_questionnaire_9', $order_obj->order_questionnaire_9);
			Session::flash('order_questionnaire_10', $order_obj->order_questionnaire_10);

			Session::flash('order_our_estimate', $order_obj->order_our_estimate);
			Session::flash('order_user_estimate', $order_obj->order_user_estimate);
			Session::flash('order_pg_return_code', $order_obj->order_pg_return_code);
			Session::flash('order_pg_payment_status', $order_obj->order_pg_payment_status);
			Session::flash('order_pg_gross_amount', $order_obj->order_pg_gross_amount);

			Session::flash('order_pg_fees', $order_obj->order_pg_fees);
			Session::flash('order_pg_net_amount', $order_obj->order_pg_net_amount);
			Session::flash('order_pg_transaction_id', $order_obj->order_pg_transaction_id);
			Session::flash('order_pg_payment_method', $order_obj->order_pg_payment_method);
			Session::flash('order_pg_fees_percent', $order_obj->order_pg_fees_percent);
			
			Session::flash('order_pg_payment_gateway_dump', $order_obj->order_pg_payment_gateway_dump);

			Session::flash('stock_query_limit_alloted', $order_obj->stock_query_limit_alloted);
			Session::flash('stock_query_limit_pending', $order_obj->stock_query_limit_pending);
			
			Session::flash('created_at', $order_obj->created_at);
			Session::flash('updated_at', $order_obj->updated_at);
			
			return view('Admin.OrderDetails.editOrders', compact('order_details'));
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
			$orderData = array(
				'order_pg_code' => $request->input('order_pg_code'),
				'order_pg_name' => $request->input('order_pg_name'),
				'order_pg_currency' => $request->input('order_pg_currency'),
				'order_currency_exchange_rate' => $request->input('order_currency_exchange_rate'),
				'order_user_estimate' => $request->input('order_user_estimate'),
				'order_pg_settlement_date' => $request->input('order_pg_settlement_date'),
				'order_status' => $request->input('order_status'),
				'order_start_date' => $request->input('order_start_date'),
				'order_end_date' => $request->input('order_end_date'),
				'order_is_complimentary' => $request->input('order_is_complimentary'),
				'stock_query_limit_pending' => $request->input('stock_query_limit_pending')
			);
			$order_id = $request->input('order_id');
			$new_status = $request->input('order_status');

			$order_obj = order_details::select('order_usr_id','order_status','order_subscription_plan_code','order_pg_settlement_date','order_start_date','order_end_date','order_is_complimentary','stock_query_limit_pending')->where('order_id', $order_id)->first();

			// If order status changed by admin
			if($request->input('order_status') != $order_obj->order_status)
			{
				$userID = $order_obj->order_usr_id;
				// If order status changed to Subscribed
				if($new_status == "Subscribed"){
					// Calculate stock query limit
					$stock_query_limit = 0;
					$sub_plan_data = subscription_plans::select('stock_query_limit_alloted')->where('subscription_plan_code', $order_obj->order_subscription_plan_code)->first();
					$UserObj1 = user_profiles::select('usr_status','usr_last_active_order_id')->where('usr_id','=',$userID)->first();

					// If user status changed to Subscribed
					if($UserObj1->usr_status != 'Subscribed'){
					    if(isset($sub_plan_data->stock_query_limit_alloted))
					        $stock_query_limit = $sub_plan_data->stock_query_limit_alloted;
					}else{
					    $current_order_id = $UserObj1->usr_last_active_order_id;
					    $current_order_object = order_details::select('order_end_date','stock_query_limit_pending','order_subscription_duration')->where('order_id','=',$current_order_id)->first();
					                    
					    if(isset($sub_plan_data->stock_query_limit_alloted) && !empty($current_order_object)) {
					        $now = time(); 
					        $current_end_datetime = strtotime($current_order_object->order_end_date);
					        $datediff = $current_end_datetime - $now;
					        $remained_days = floor($datediff / (60 * 60 * 24));
					        $stock_query_limit = $sub_plan_data->stock_query_limit_alloted + ceil(($current_order_object->stock_query_limit_pending*$remained_days)/$current_order_object->order_subscription_duration);
					    }
					}
					$orderData['stock_query_limit_alloted']=$stock_query_limit;
					$orderData['stock_query_limit_pending']=$stock_query_limit;

					// update user table
				 	$userData  = array(
	                    'usr_last_active_order_id' => $order_id, 
	                    'usr_status' => 'Subscribed');
	                $updateUserData = user_profiles::where('usr_id', $userID)->update($userData);

	                // Send Email to user for Order Activation
	                $this->send_user_email_for_order_activated($order_id);
				
				} 
				else if($order_obj->order_status == "Subscribed") {
					$user_obj_new = user_profiles::first()->where(array('usr_id' => $userID));
					$updateData = array('usr_status'=> 'Expired');
					$updateUser = $user_obj_new->update($updateData);

	                // Send Email to user for Order Deactivation
	                $this->send_user_email_for_order_deactivated($order_id);
				}
			} 

			$updateNowOrder = order_details::where('order_id', $order_id)->update($orderData);

			if($updateNowOrder){
				Session::flash('error_message', 'Order Updated successfully!');
				$Message = "Order Info Changed From Admin \n\nOrder Number : " . $request->input('order_id') . " updated successfully. \n IP Address :".$this->aws->getClientIps();
				$Message .= "\n Time of Event :".Carbon::now();
				$Message .= "\nFields Changes : \n Gateway Settlement Date : \n Old Value : ".$order_obj->order_pg_settlement_date." \n New Value : ".$request->input('order_pg_settlement_date');
				$Message .= "\n \n Order Status : \n Old Value : ".$order_obj->order_status." \n New Value : ".$request->input('order_status');
				$Message .= "\n\n Start Date : \n Old Value : ".$order_obj->order_start_date." \n New Value : ".$request->input('order_start_date');
				$Message .= "\n\n End Date : \n Old Value : ".$order_obj->order_end_date." \n New Value : ".$request->input('order_end_date');
				$Message .= "\n\n Is order complimentary Status : \n Old Value : ".$order_obj->order_is_complimentary." \n New Value : ".$request->input('order_is_complimentary');
				$Message .= "\n\n Stock Query Limit Pending : \n Old Value : ".$order_obj->stock_query_limit_pending." \n New Value : ".$request->input('stock_query_limit_pending');
				$this->aws->send_admin_alerts($this->Alert_Admin,$Message);
			}else{
				Session::flash('error_message_danger', 'Unable to edit Order!');
			}
			
			$zoneCode = Session::get('zone_name');
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/";
			return redirect($zone_name);
				
		}
		else
		{
			return redirect('/');
		}
	}

	public function send_user_email_for_order_activated( $order_id = null )
    {
        if(empty($order_id)){
           return false;
        }
        $order = order_details::where('order_id', $order_id)->first();
        $user_full_name = user_profiles::where('usr_username', $order->order_username)->pluck('usr_name')->first();

        $emailSubject = 'SPTulsian.com - Order '.$order->order_id.' activated at SPTulsian.com';
        $emailBody = '<br/>Dear '.$user_full_name.', ';
        $emailBody = $emailBody.'<br/><br/>Your order '.$order->order_id.' has been successfully activated at SPTulsian.com.';
        $emailBody = $emailBody.'<br/><br/>The following credits related to the order have been added to your account with username '.$order->order_username.' - ';

        if($order->order_subscription_plan_code)
        {
            $subscription_name = subscription_plans::where('subscription_plan_code', $order->order_subscription_plan_code)->pluck('subscription_plan_name')->first();  
            $emailBody = $emailBody.'and subscription plan name '.$subscription_name;
        }
        
        $emailBody = $emailBody."<br/><br/>You can check subscription information in your account from the 'My Account' section of the website after logging in. Thank you. ";
        $notificationBody = "Your order ".$order->order_id." for username ".$order->order_username." has been successfully activated. Please check email for details. Thank you.";

        $url = SITE_ADDRESS1 . "/order-info";

        \MemberZoneAlert::sendSingleAlert($order->order_username, $emailSubject, $emailBody, $url, NULL, 2, 0, 0, 0, 0);
        \MemberZoneAlert::sendSingleAlert($order->order_username, $emailSubject, $notificationBody, $url, NULL, 0, 2, 2, 2, 2);

        /*$emailArray = array();
        $emailArray[0] = $order->order_email_id;
        
        $result = $this->aws->sendEmail_Centralized($emailArray, $emailSubject, $emailBody);*/
    }

	public function send_user_email_for_order_deactivated( $order_id = null )
    {
        if(empty($order_id)){
           return false;
        }
        $order = order_details::where('order_id', $order_id)->first();
        $user_full_name = user_profiles::where('usr_username', $order->order_username)->pluck('usr_name')->first();

        $emailSubject = 'SPTulsian.com : Subscription Expired';
        $emailBody = '<br/>Dear '.$user_full_name.', ';
        $emailBody = $emailBody.'<br/><br/>Your account on www.sptulsian.com has expired on '.$order->order_end_date;
        $emailBody = $emailBody.'<br/><br/>To renew your account, once you sign in with your username and password, you will see a message on top right corner on the home page that your subscription is ending and a request to extent it. Click on the Renew tab there which will directly lead you to choosing the subscription package. Please note that your existing username and password will be valid. ';
        $emailBody = $emailBody.'<br/><br/>Please ignore this message if already renewed.';
        $emailBody = $emailBody.'<br/>Have a profitable day!<br/><br/>';
        
        $notificationBody = "Your payment for order ".$order->order_id." has failed. Kindly compelte payment to activate your order. Thank you.";

        $url = SITE_ADDRESS1 . "/order-info";

        \MemberZoneAlert::sendSingleAlert($order->order_username, $emailSubject, $emailBody, $url, null, 2, 0, 0, 0, 0);
        \MemberZoneAlert::sendSingleAlert($order->order_username, $emailSubject, $notificationBody, $url, null, 0, 2, 2, 2, 2);

        /*$emailArray = array();
        $emailArray[0] = $order->order_email_id;
        
        $result = $this->aws->sendEmail_Centralized($emailArray, $emailSubject, $emailBody);*/
    }  


    public function calculateOrderData($packageCode, $UserID){   
        $today = Carbon::now();
        $order_start_date = date('Y-m-d H:i:s');
        $order_end_date = date('Y-m-d H:i:s');
        $stock_query_limit = 0;

        $sub_plan_duration = subscription_plans::select('subscription_plan_duration','stock_query_limit_alloted')->where('subscription_plan_code', $packageCode)->first();

        $UserObj1 = user_profiles::select('usr_status','usr_last_active_order_id')->where('usr_id','=',$UserID)->first();

        if(!empty($UserObj1) && !empty($sub_plan_duration)) {
            if($UserObj1->usr_status != 'Subscribed'){
                //If no last order available update data with latest info.
                $order_end_date = $today->addDays($sub_plan_duration->subscription_plan_duration);
                if(isset($sub_plan_duration->stock_query_limit_alloted))
                    $stock_query_limit = $sub_plan_duration->stock_query_limit_alloted;
            }else{
                $current_order_id = $UserObj1->usr_last_active_order_id;
                $current_order_object = order_details::select('order_end_date','stock_query_limit_pending','order_subscription_duration')->where('order_id','=',$current_order_id)->first();
                if(!empty($current_order_object)) {
                    $current_order_end_date = carbon::parse($current_order_object->order_end_date);
                    $order_end_date = $current_order_end_date->addDays($sub_plan_duration->subscription_plan_duration);

                    // calculate stock_query_limit
                    if(isset($sub_plan_duration->stock_query_limit_alloted)) {
                        $now = time(); // or your date as well
                        $current_end_datetime = strtotime($current_order_object->order_end_date);
                        $datediff = $current_end_datetime - $now;
                        $remained_days = floor($datediff / (60 * 60 * 24));
                        $stock_query_limit = $sub_plan_duration->stock_query_limit_alloted + ceil(($current_order_object->stock_query_limit_pending*$remained_days)/$current_order_object->order_subscription_duration);
                    }
                }
            }
        }

        return ['order_start_date'=>$order_start_date,
                'order_end_date'=>$order_end_date,
                'stock_query_limit'=>$stock_query_limit];
    }

	public function delete($zone_code, $id){

	}
}
