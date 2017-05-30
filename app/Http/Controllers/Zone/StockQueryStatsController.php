<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\stock_query_stats;
use App\subscription_plans;
use App\payment_gateways;
use App\order_details;
use App\member_zone_sections;
use App\free_zone_sections;
use App\user_profiles;
use App\admin_elb_instances;
use App\user_elb_instances;
use Session;
use Config;
use Validator;
use Carbon\Carbon;
use Date;
use DB;
define("show_stats_of_last_days", 15);

class StockQueryStatsController extends Controller
{
    function dateInReadableFormat($date)
    {
        if(!empty($date))
        {
            $date = date_create($date);
        	return date_format($date, "d") . "/" . date_format($date, "m");
        }
    }

	public function display($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			// ordering parameter to be added
			$subscription_plans = subscription_plans::select('subscription_plan_name')->orderBy('subscription_plan_duration')->get()->toArray();
			$payment_gateways = payment_gateways::select('pgw_pg_name')->orderBy('pgw_pg_code')->get()->toArray();
			$member_zone_sections = member_zone_sections::select('sec_id', 'sec_name')->orderBy('sec_ordering')->get()->toArray();

			ini_set('memory_limit', "100M");

			/*TABLE 1 STARTS*/
			$member_stats['Subscribed']['Total'] = user_profiles::where('usr_status', 'Subscribed')->count();
			$member_stats_keys = ['Total'];
			$flag = 0;
			$add_colummn_subscribed = 0;
			$payment_total = 0;
			$payment_row_add_other = 0;
			$add_row_subscription = [];
			foreach ($payment_gateways as $payment_gateway)
			{
				$add_column_payment_subscribed = 0;
				$member_stats[$payment_gateway['pgw_pg_name']]['Total'] = user_profiles::where('usr_status', 'Subscribed')->leftJoin('order_details', 'user_profiles.usr_last_active_order_id', '=', 'order_details.order_id')->where('order_details.order_pg_name', $payment_gateway['pgw_pg_name'])->count();
				$payment_total = $payment_total + $member_stats[$payment_gateway['pgw_pg_name']]['Total'];
				foreach ($subscription_plans as $subscription_plan)
				{
					if(empty($flag))
					{
						$member_stats['Subscribed'][$subscription_plan['subscription_plan_name']] = user_profiles::where('usr_status', 'Subscribed')->leftJoin('order_details', 'user_profiles.usr_last_active_order_id', '=', 'order_details.order_id')->where('order_details.order_subscription_plan_name', $subscription_plan['subscription_plan_name'])->count();
						$member_stats_keys[] = $subscription_plan['subscription_plan_name'];
						$add_colummn_subscribed = $add_colummn_subscribed + $member_stats['Subscribed'][$subscription_plan['subscription_plan_name']];
					}
					$member_stats[$payment_gateway['pgw_pg_name']][$subscription_plan['subscription_plan_name']] = user_profiles::where('usr_status', 'Subscribed')->leftJoin('order_details', 'user_profiles.usr_last_active_order_id', '=', 'order_details.order_id')->where('order_details.order_pg_name', $payment_gateway['pgw_pg_name'])->where('order_details.order_subscription_plan_name', $subscription_plan['subscription_plan_name'])->count();
					$add_row_subscription[$subscription_plan['subscription_plan_name']][] = $member_stats[$payment_gateway['pgw_pg_name']][$subscription_plan['subscription_plan_name']];
					$add_column_payment_subscribed = $add_column_payment_subscribed + $member_stats[$payment_gateway['pgw_pg_name']][$subscription_plan['subscription_plan_name']];
				}
				$member_stats[$payment_gateway['pgw_pg_name']]['Other'] =  $member_stats[$payment_gateway['pgw_pg_name']]['Total'] - $add_column_payment_subscribed;
				$payment_row_add_other = $payment_row_add_other + $member_stats[$payment_gateway['pgw_pg_name']]['Other'];
				$flag = 1;
			}
			$member_stats['Subscribed']['Other'] = $member_stats['Subscribed']['Total'] - $add_colummn_subscribed;
			$member_stats['Other']['Total'] = $member_stats['Subscribed']['Total'] - $payment_total;
			$member_stats['Complimentary']['Total'] = user_profiles::where('usr_status', 'Subscribed')->leftJoin('order_details', 'user_profiles.usr_last_active_order_id', '=', 'order_details.order_id')->where('order_details.order_is_complimentary', 1)->count();
			$complimentary_col_add = 0;
			foreach ($subscription_plans as $subscription_plan)
			{
				$col_add = array_sum($add_row_subscription[$subscription_plan['subscription_plan_name']]);
				$member_stats['Other'][$subscription_plan['subscription_plan_name']] = $member_stats['Subscribed'][$subscription_plan['subscription_plan_name']] - $col_add;
				$member_stats['Complimentary'][$subscription_plan['subscription_plan_name']] = user_profiles::where('usr_status', 'Subscribed')->leftJoin('order_details', 'user_profiles.usr_last_active_order_id', '=', 'order_details.order_id')->where('order_details.order_subscription_plan_name', $subscription_plan['subscription_plan_name'])->where('order_details.order_is_complimentary', 1)->count();
				$complimentary_col_add = $complimentary_col_add + $member_stats['Complimentary'][$subscription_plan['subscription_plan_name']];
			}
			$member_stats['Other']['Other'] = $member_stats['Subscribed']['Other'] - $payment_row_add_other;
			$member_stats['Complimentary']['Other'] = $member_stats['Complimentary']['Total'] - $complimentary_col_add;
			$member_stats_keys[] = "Other";
			//dd($member_stats);
			/*TABLE 1 ENDS*/

			/*TABLE 2 STARTS*/
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

			/*TABLE 3 STARTS*/
			$stock_query_stats = stock_query_stats::select('*')->whereNotNull('id')->whereRaw('date BETWEEN CURRENT_DATE() - INTERVAL ' . show_stats_of_last_days . ' DAY AND CURRENT_DATE()')->orderBy('date', 'DESC')->limit(30)->get();
			if(count($stock_query_stats) > 0)
			{
				foreach ($stock_query_stats as $stock_query_stat)
				{
					$stock_query_stat['date'] = $this->dateInReadableFormat($stock_query_stat->date);
				}
			}
			$stock_query_sum = stock_query_stats::select(DB::raw('sum(users_went_to_posting_page) as users_went_to_posting_page, sum(repeat_queries_ignored) as repeat_queries_ignored, sum(query_answered_by_suggestions) as query_answered_by_suggestions, sum(posted_queries) as posted_queries, sum(deleted_queries) as deleted_queries, sum(replied_queries) as replied_queries, sum(post_after_limit_exhausted) as post_after_limit_exhausted, sum(query_posted_on_disallowed_days) as query_posted_on_disallowed_days, sum(posted_by_kyc_non_verfied) as posted_by_kyc_non_verfied'))->first();
			/*TABLE 3 ENDS*/

			/*TABLE 4 STARTS*/
			$user_profiles_chunk = array_chunk(user_profiles::select(
				DB::raw("length(usr_name) as usr_name"),
				DB::raw("length(usr_address) as usr_address"),
				DB::raw("length(usr_city) as usr_city"),
				DB::raw("length(usr_state) as usr_state"),
				DB::raw("length(usr_country) as usr_country"),
				DB::raw("length(usr_postalcode) as usr_postalcode"),
				DB::raw("length(usr_email_id) as usr_email_id"),
				DB::raw("length(usr_mobile_number) as usr_mobile_number"),
				'usr_status',
				DB::raw("length(fcm_token_android) as fcm_token_android"),
				DB::raw("length(fcm_token_ios) as fcm_token_ios"),
				DB::raw("length(gcm_browser_token) as gcm_browser_token"),
				DB::raw("length(safari_browser_token) as safari_browser_token"),
				DB::raw("length(trusted_mobile_device) as trusted_mobile_device"),
				'send_email',
				'send_sms',
				'send_mobile_app_notifications',
				'send_browser_notifications',
				'm0_alerts',
				'm1_alerts',
				'm2_alerts',
				'm3_alerts',
				'm4_alerts',
				'm5_alerts',
				'm6_alerts',
				'm7_alerts',
				'm8_alerts',
				'm9_alerts',
				'f1_alerts',
				'f2_alerts',
				'f3_alerts',
				'f4_alerts',
				'f5_alerts',
				'f6_alerts',
				'f8_alerts',
				'f9_alerts',
				DB::raw("length(usr_dob) as usr_dob"),
				DB::raw("length(usr_ckyc_number) as usr_ckyc_number"),
				DB::raw("length(usr_pan_number) as usr_pan_number"),
				DB::raw("length(usr_aadhar_number) as usr_aadhar_number"),
				'kyc_verified',
				'kyc_ckyc_verified',
				'kyc_kra_verified',
				'kyc_aadhar_verified',
				'usr_is_require_otp',
				'usr_name_verified',
				'usr_address_verified',
				'usr_city_verified',
				'usr_state_verified',
				'usr_country_verified',
				'usr_postalcode_verified',
				'usr_mobile_number_verified',
				'usr_email_id_verified',
				'usr_pan_number_verified',
				'usr_aadhar_number_verified',
				'usr_dob_verified',
				'usr_uploaded_pan_verified',
				'usr_uploaded_pan',
				'cams_kra_pan_info_free_verified',
				'cams_kra_aadhar_info_free_verified',
				'usr_uploaded_aadhar_verified',
				'usr_uploaded_aadhar'
			)->where('usr_status', 'Subscribed')->get()->toArray(), 20000, true);

			/*INITIALISE TABLE 4 TOTAL START*/
			$subscribed_alert_stats['Subscribed']['Total'] = 0;

			$subscribed_alert_stats['Subscribed']['Send SMS']['Valid'] = 0;
			$subscribed_alert_stats['Subscribed']['Send SMS']['Total'] = 0;

			$subscribed_alert_stats['Subscribed']['Send Email']['Valid'] = 0;
			$subscribed_alert_stats['Subscribed']['Send Email']['Total'] = 0;

			$subscribed_alert_stats['Subscribed']['Send App']['Valid'] = 0;
			$subscribed_alert_stats['Subscribed']['Send App']['Total'] = 0;

			$subscribed_alert_stats['Subscribed']['Send Browser']['Valid'] = 0;
			$subscribed_alert_stats['Subscribed']['Send Browser']['Total'] = 0;

			// kyc starts
			$kyc_subscribed_verified_details['kyc_verified'] = 0;
			$kyc_subscribed_verified_details['kyc_ckyc_verified'] = 0;
			$kyc_subscribed_verified_details['kyc_kra_verified'] = 0;
			$kyc_subscribed_verified_details['kyc_aadhar_verified'] = 0;
			$kyc_subscribed_verified_details['usr_pan_number'] = 0;
			$kyc_subscribed_verified_details['usr_aadhar_number'] = 0;
			$kyc_subscribed_verified_details['usr_dob'] = 0;
			$kyc_subscribed_verified_details['usr_ckyc_number'] = 0;
			$kyc_subscribed_verified_details['usr_kra_number'] = 0;
			$kyc_subscribed_verified_details['usr_is_require_otp'] = 0;
			// kyc ends

			// manual kyc starts
			$kyc_subscribed_verified_details['usr_name'] = 0;
			$kyc_subscribed_verified_details['usr_name_verified'] = 0;
			$kyc_subscribed_verified_details['usr_address'] = 0;
			$kyc_subscribed_verified_details['usr_address_verified'] = 0;
			$kyc_subscribed_verified_details['usr_city'] = 0;
			$kyc_subscribed_verified_details['usr_city_verified'] = 0;
			$kyc_subscribed_verified_details['usr_state'] = 0;
			$kyc_subscribed_verified_details['usr_state_verified'] = 0;
			$kyc_subscribed_verified_details['usr_country'] = 0;
			$kyc_subscribed_verified_details['usr_country_verified'] = 0;
			$kyc_subscribed_verified_details['usr_postalcode'] = 0;
			$kyc_subscribed_verified_details['usr_postalcode_verified'] = 0;
			$kyc_subscribed_verified_details['usr_mobile_number'] = 0;
			$kyc_subscribed_verified_details['usr_mobile_number_verified'] = 0;
			$kyc_subscribed_verified_details['usr_email_id'] = 0;
			$kyc_subscribed_verified_details['usr_email_id_verified'] = 0;
			$kyc_subscribed_verified_details['usr_pan_number'] = 0;
			$kyc_subscribed_verified_details['usr_pan_number_verified'] = 0;
			$kyc_subscribed_verified_details['usr_uploaded_pan'] = 0;
			$kyc_subscribed_verified_details['usr_uploaded_pan_verified'] = 0;
			$kyc_subscribed_verified_details['cams_kra_pan_info_free_verified'] = 0;
			$kyc_subscribed_verified_details['usr_aadhar_number_verified'] = 0;
			$kyc_subscribed_verified_details['usr_uploaded_aadhar'] = 0;
			$kyc_subscribed_verified_details['usr_uploaded_aadhar_verified'] = 0;
			$kyc_subscribed_verified_details['cams_kra_aadhar_info_free_verified'] = 0;
			$kyc_subscribed_verified_details['usr_dob'] = 0;
			$kyc_subscribed_verified_details['usr_dob_verified'] = 0;
			// manual kyc ends
			/*INITIALISE TABLE 4 TOTAL END*/

			/*INITIALISE TABLE 4 START*/
			foreach ($member_zone_sections as $member_zone_section)
			{
				$subscribed_alert_stats[$member_zone_section['sec_name']]['Total'] = 0;

				$subscribed_alert_stats[$member_zone_section['sec_name']]['Send SMS']['Valid'] = 0;
				$subscribed_alert_stats[$member_zone_section['sec_name']]['Send SMS']['Total'] = 0;

				$subscribed_alert_stats[$member_zone_section['sec_name']]['Send Email']['Valid'] = 0;
				$subscribed_alert_stats[$member_zone_section['sec_name']]['Send Email']['Total'] = 0;

				$subscribed_alert_stats[$member_zone_section['sec_name']]['Send App']['Valid'] = 0;
				$subscribed_alert_stats[$member_zone_section['sec_name']]['Send App']['Total'] = 0;

				$subscribed_alert_stats[$member_zone_section['sec_name']]['Send Browser']['Valid'] = 0;
				$subscribed_alert_stats[$member_zone_section['sec_name']]['Send Browser']['Total'] = 0;
			}
			/*INITIALISE TABLE 4 END*/

			/*PROCESS TABLE 4 START*/
			foreach ($user_profiles_chunk as $user_profiles)
			{
				foreach ($user_profiles as $user_profile)
				{
					if($user_profile['usr_status'] == "Subscribed")
					{
						if(($user_profile['send_sms'] == 1) && (!empty($user_profile['usr_mobile_number'])))
						{
							$subscribed_alert_stats['Subscribed']['Send SMS']['Valid']++;
						}
						if($user_profile['send_sms'] == 1)
						{
							$subscribed_alert_stats['Subscribed']['Send SMS']['Total']++;
						}

						if(($user_profile['send_email'] == 1) && (!empty($user_profile['usr_email_id'])))
						{
							$subscribed_alert_stats['Subscribed']['Send Email']['Valid']++;
						}
						if($user_profile['send_email'] == 1)
						{
							$subscribed_alert_stats['Subscribed']['Send Email']['Total']++;
						}

						if(($user_profile['send_mobile_app_notifications'] == 1) && ((!empty($user_profile['trusted_mobile_device']))))
						{
							$subscribed_alert_stats['Subscribed']['Send App']['Valid']++;
						}
						if($user_profile['send_mobile_app_notifications'] == 1)
						{
							$subscribed_alert_stats['Subscribed']['Send App']['Total']++;
						}

						if(($user_profile['send_browser_notifications'] == 1) && ((!empty($user_profile['gcm_browser_token'])) || (!empty($user_profile['safari_browser_token']))))
						{
							$subscribed_alert_stats['Subscribed']['Send Browser']['Valid']++;
						}
						if($user_profile['send_browser_notifications'] == 1)
						{
							$subscribed_alert_stats['Subscribed']['Send Browser']['Total']++;
						}
						// kyc manual start
						if(!empty($user_profile['usr_name']))
						{
							$kyc_subscribed_verified_details['usr_name']++;
						}
						if($user_profile['usr_name_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_name_verified']++;
						}
						if(!empty($user_profile['usr_address']))
						{
							$kyc_subscribed_verified_details['usr_address']++;
						}
						if($user_profile['usr_address_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_address_verified']++;
						}
						if(!empty($user_profile['usr_city']))
						{
							$kyc_subscribed_verified_details['usr_city']++;
						}
						if($user_profile['usr_city_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_city_verified']++;
						}
						if(!empty($user_profile['usr_state']))
						{
							$kyc_subscribed_verified_details['usr_state']++;
						}
						if($user_profile['usr_state_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_state_verified']++;
						}
						if(!empty($user_profile['usr_country']))
						{
							$kyc_subscribed_verified_details['usr_country']++;
						}
						if($user_profile['usr_country_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_country_verified']++;
						}
						if(!empty($user_profile['usr_postalcode']))
						{
							$kyc_subscribed_verified_details['usr_postalcode']++;
						}
						if($user_profile['usr_postalcode_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_postalcode_verified']++;
						}
						if(!empty($user_profile['usr_mobile_number']))
						{
							$kyc_subscribed_verified_details['usr_mobile_number']++;
						}
						if($user_profile['usr_mobile_number_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_mobile_number_verified']++;
						}
						if(!empty($user_profile['usr_email_id']))
						{
							$kyc_subscribed_verified_details['usr_email_id']++;
						}
						if($user_profile['usr_email_id_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_email_id_verified']++;
						}
						if(!empty($user_profile['usr_pan_number']))
						{
							$kyc_subscribed_verified_details['usr_pan_number']++;
						}
						if($user_profile['usr_pan_number_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_pan_number_verified']++;
						}
						if($user_profile['cams_kra_pan_info_free_verified'] == 1)
						{
							$kyc_subscribed_verified_details['cams_kra_pan_info_free_verified']++;
						}
						if($user_profile['usr_uploaded_pan'] == 1)
						{
							$kyc_subscribed_verified_details['usr_uploaded_pan']++;
						}
						if($user_profile['usr_uploaded_pan_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_uploaded_pan_verified']++;
						}
						if(!empty($user_profile['usr_aadhar_number']))
						{
							$kyc_subscribed_verified_details['usr_aadhar_number']++;
						}
						if($user_profile['usr_aadhar_number_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_aadhar_number_verified']++;
						}
						if($user_profile['cams_kra_aadhar_info_free_verified'] == 1)
						{
							$kyc_subscribed_verified_details['cams_kra_aadhar_info_free_verified']++;
						}
						if($user_profile['usr_uploaded_aadhar'] == 1)
						{
							$kyc_subscribed_verified_details['usr_uploaded_aadhar']++;
						}
						if($user_profile['usr_uploaded_aadhar_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_uploaded_aadhar_verified']++;
						}
						if(!empty($user_profile['usr_dob']))
						{
							$kyc_subscribed_verified_details['usr_dob']++;
						}
						if($user_profile['usr_dob_verified'] == 1)
						{
							$kyc_subscribed_verified_details['usr_dob_verified']++;
						}
						if(!empty($user_profile['usr_ckyc_number']))
						{
							$kyc_subscribed_verified_details['usr_ckyc_number']++;
						}
						if($user_profile['kyc_ckyc_verified'] == 1)
						{
							$kyc_subscribed_verified_details['kyc_ckyc_verified']++;
						}
						// kyc manual end
						// kyc start
						if($user_profile['kyc_verified'] == 1)
						{
							$kyc_subscribed_verified_details['kyc_verified']++;
						}
						if($user_profile['usr_is_require_otp'] == 1)
						{
							$kyc_subscribed_verified_details['usr_is_require_otp']++;
						}
						// kyc end
						foreach ($member_zone_sections as $member_zone_section)
						{
							$member_zone_section_sec_id = "m" . $member_zone_section['sec_id'] . "_alerts";
							if($user_profile[$member_zone_section_sec_id] == 1)
							{
								$subscribed_alert_stats[$member_zone_section['sec_name']]['Total']++;
							}
							if(($user_profile[$member_zone_section_sec_id] == 1) && ($user_profile['send_sms'] == 1) && (!empty($user_profile['usr_mobile_number'])))
							{
								$subscribed_alert_stats[$member_zone_section['sec_name']]['Send SMS']['Valid']++;
							}
							if(($user_profile[$member_zone_section_sec_id] == 1) && $user_profile['send_sms'] == 1)
							{
								$subscribed_alert_stats[$member_zone_section['sec_name']]['Send SMS']['Total']++;
							}

							if(($user_profile[$member_zone_section_sec_id] == 1) && ($user_profile['send_email'] == 1) && (!empty($user_profile['usr_email_id'])))
							{
								$subscribed_alert_stats[$member_zone_section['sec_name']]['Send Email']['Valid']++;
							}
							if(($user_profile[$member_zone_section_sec_id] == 1) && $user_profile['send_email'] == 1)
							{
								$subscribed_alert_stats[$member_zone_section['sec_name']]['Send Email']['Total']++;
							}

							if(($user_profile[$member_zone_section_sec_id] == 1) && ($user_profile['send_mobile_app_notifications'] == 1) && ((!empty($user_profile['trusted_mobile_device']))))
							{
								$subscribed_alert_stats[$member_zone_section['sec_name']]['Send App']['Valid']++;
							}
							if(($user_profile[$member_zone_section_sec_id] == 1) && $user_profile['send_mobile_app_notifications'] == 1)
							{
								$subscribed_alert_stats[$member_zone_section['sec_name']]['Send App']['Total']++;
							}

							if(($user_profile[$member_zone_section_sec_id] == 1) && ($user_profile['send_browser_notifications'] == 1) && ((!empty($user_profile['gcm_browser_token'])) || (!empty($user_profile['safari_browser_token']))))
							{
								$subscribed_alert_stats[$member_zone_section['sec_name']]['Send Browser']['Valid']++;
							}
							if(($user_profile[$member_zone_section_sec_id] == 1) && $user_profile['send_browser_notifications'] == 1)
							{
								$subscribed_alert_stats[$member_zone_section['sec_name']]['Send Browser']['Total']++;
							}
						}
						$subscribed_alert_stats['Subscribed']['Total']++;
					}
				}
			}
			unset($user_profiles_chunk);
			/*PROCESS TABLE 4 END*/

			/*TABLE 7 STARTS*/
			$user_active_instances_count = 0;
			$user_active_instances_private_ips = "";
			$user_active_instances_ips = user_elb_instances::select('private_ip')->where('is_active', 1)->get()->toArray();
			if(!empty($user_active_instances_ips))
			{
				foreach ($user_active_instances_ips as $user_active_instances_ip)
				{
					$user_active_instances_private_ips .= $user_active_instances_ip['private_ip'] . ", ";
					$user_active_instances_count++;
				}
			}
			$user_elb_stats = array(
				'user_active_instances_count' => $user_active_instances_count,
				'user_active_instances_private_ips' => rtrim($user_active_instances_private_ips, ", ")
			);
			$admin_active_instances_count = 0;
			$admin_active_instances_private_ips = "";
			$admin_active_instances_ips = admin_elb_instances::select('private_ip')->where('is_active', 1)->get()->toArray();
			if(!empty($admin_active_instances_ips))
			{
				foreach ($admin_active_instances_ips as $admin_active_instances_ip)
				{
					$admin_active_instances_private_ips .= $admin_active_instances_ip['private_ip'] . ", ";
					$admin_active_instances_count++;
				}
			}
			$admin_elb_stats = array(
				'admin_active_instances_count' => $admin_active_instances_count,
				'admin_active_instances_private_ips' => rtrim($admin_active_instances_private_ips, ", ")
			);
			unset($user_active_instances_ips);
			unset($admin_active_instances_ips);
			/*TABLE 7 ENDS*/

			return view('Admin.Stats.indexStats', compact('member_stats', 'member_stats_keys', 'subscription_stats', 'stock_query_stats', 'stock_query_sum', 'subscribed_alert_stats', 'expired_alert_stats', 'free_zone_alert_stats', 'kyc_subscribed_verified_details', 'user_elb_stats', 'admin_elb_stats'));
		}
		else
		{
			return redirect('/');
		}
	}

	public function loadExpiredData(Request $request)
	{
		if(Session::get('is_admin'))
		{
			$member_zone_sections = member_zone_sections::select('sec_id', 'sec_name')->orderBy('sec_ordering')->get()->toArray();
			ini_set('memory_limit', "100M");
			$user_profiles_chunk = array_chunk(user_profiles::select(
				DB::raw("length(usr_email_id) as usr_email_id"),
				DB::raw("length(usr_mobile_number) as usr_mobile_number"),
				DB::raw("length(fcm_token_android) as fcm_token_android"),
				DB::raw("length(fcm_token_ios) as fcm_token_ios"),
				DB::raw("length(gcm_browser_token) as gcm_browser_token"),
				DB::raw("length(safari_browser_token) as safari_browser_token"),
				DB::raw("length(trusted_mobile_device) as trusted_mobile_device"),
				'send_email',
				'send_sms',
				'send_mobile_app_notifications',
				'send_browser_notifications',
				'm0_alerts',
				'm1_alerts',
				'm2_alerts',
				'm3_alerts',
				'm4_alerts',
				'm5_alerts',
				'm6_alerts',
				'm7_alerts',
				'm8_alerts',
				'm9_alerts'
			)->where('usr_status', 'Expired')->get()->toArray(), 20000, true);
			/*INITIALISE TABLE 5 TOTAL START*/
			$expired_alert_stats['Expired']['Total'] = 0;

			$expired_alert_stats['Expired']['Send SMS']['Valid'] = 0;
			$expired_alert_stats['Expired']['Send SMS']['Total'] = 0;

			$expired_alert_stats['Expired']['Send Email']['Valid'] = 0;
			$expired_alert_stats['Expired']['Send Email']['Total'] = 0;

			$expired_alert_stats['Expired']['Send App']['Valid'] = 0;
			$expired_alert_stats['Expired']['Send App']['Total'] = 0;

			$expired_alert_stats['Expired']['Send Browser']['Valid'] = 0;
			$expired_alert_stats['Expired']['Send Browser']['Total'] = 0;
			/*INITIALISE TABLE 5 TOTAL END*/

			/*INITIALISE TABLE 5 START*/
			foreach ($member_zone_sections as $member_zone_section)
			{
				$expired_alert_stats[$member_zone_section['sec_name']]['Total'] = 0;

				$expired_alert_stats[$member_zone_section['sec_name']]['Send SMS']['Valid'] = 0;
				$expired_alert_stats[$member_zone_section['sec_name']]['Send SMS']['Total'] = 0;

				$expired_alert_stats[$member_zone_section['sec_name']]['Send Email']['Valid'] = 0;
				$expired_alert_stats[$member_zone_section['sec_name']]['Send Email']['Total'] = 0;

				$expired_alert_stats[$member_zone_section['sec_name']]['Send App']['Valid'] = 0;
				$expired_alert_stats[$member_zone_section['sec_name']]['Send App']['Total'] = 0;

				$expired_alert_stats[$member_zone_section['sec_name']]['Send Browser']['Valid'] = 0;
				$expired_alert_stats[$member_zone_section['sec_name']]['Send Browser']['Total'] = 0;
			}
			/*INITIALISE TABLE 5 END*/
			/*PROCESS TABLE 5 START*/
			foreach ($user_profiles_chunk as $user_profiles)
			{
				foreach ($user_profiles as $user_profile)
				{
					if(($user_profile['send_sms'] == 1) && (!empty($user_profile['usr_mobile_number'])))
					{
						$expired_alert_stats['Expired']['Send SMS']['Valid']++;
					}
					if($user_profile['send_sms'] == 1)
					{
						$expired_alert_stats['Expired']['Send SMS']['Total']++;
					}

					if(($user_profile['send_email'] == 1) && (!empty($user_profile['usr_email_id'])))
					{
						$expired_alert_stats['Expired']['Send Email']['Valid']++;
					}
					if($user_profile['send_email'] == 1)
					{
						$expired_alert_stats['Expired']['Send Email']['Total']++;
					}

					if(($user_profile['send_mobile_app_notifications'] == 1) && ((!empty($user_profile['trusted_mobile_device']))))
					{
						$expired_alert_stats['Expired']['Send App']['Valid']++;
					}
					if($user_profile['send_mobile_app_notifications'] == 1)
					{
						$expired_alert_stats['Expired']['Send App']['Total']++;
					}

					if(($user_profile['send_browser_notifications'] == 1) && ((!empty($user_profile['gcm_browser_token'])) || (!empty($user_profile['safari_browser_token']))))
					{
						$expired_alert_stats['Expired']['Send Browser']['Valid']++;
					}
					if($user_profile['send_browser_notifications'] == 1)
					{
						$expired_alert_stats['Expired']['Send Browser']['Total']++;
					}
					foreach ($member_zone_sections as $member_zone_section)
					{
						$member_zone_section_sec_id = "m" . $member_zone_section['sec_id'] . "_alerts";
						if($user_profile[$member_zone_section_sec_id] == 1)
						{
							$expired_alert_stats[$member_zone_section['sec_name']]['Total']++;
						}
						if(($user_profile[$member_zone_section_sec_id] == 1) && ($user_profile['send_sms'] == 1) && (!empty($user_profile['usr_mobile_number'])))
						{
							$expired_alert_stats[$member_zone_section['sec_name']]['Send SMS']['Valid']++;
						}
						if(($user_profile[$member_zone_section_sec_id] == 1) && $user_profile['send_sms'] == 1)
						{
							$expired_alert_stats[$member_zone_section['sec_name']]['Send SMS']['Total']++;
						}

						if(($user_profile[$member_zone_section_sec_id] == 1) && ($user_profile['send_email'] == 1) && (!empty($user_profile['usr_email_id'])))
						{
							$expired_alert_stats[$member_zone_section['sec_name']]['Send Email']['Valid']++;
						}
						if(($user_profile[$member_zone_section_sec_id] == 1) && $user_profile['send_email'] == 1)
						{
							$expired_alert_stats[$member_zone_section['sec_name']]['Send Email']['Total']++;
						}

						if(($user_profile[$member_zone_section_sec_id] == 1) && ($user_profile['send_mobile_app_notifications'] == 1) && ((!empty($user_profile['trusted_mobile_device']))))
						{
							$expired_alert_stats[$member_zone_section['sec_name']]['Send App']['Valid']++;
						}
						if(($user_profile[$member_zone_section_sec_id] == 1) && $user_profile['send_mobile_app_notifications'] == 1)
						{
							$expired_alert_stats[$member_zone_section['sec_name']]['Send App']['Total']++;
						}

						if(($user_profile[$member_zone_section_sec_id] == 1) && ($user_profile['send_browser_notifications'] == 1) && ((!empty($user_profile['gcm_browser_token'])) || (!empty($user_profile['safari_browser_token']))))
						{
							$expired_alert_stats[$member_zone_section['sec_name']]['Send Browser']['Valid']++;
						}
						if(($user_profile[$member_zone_section_sec_id] == 1) && $user_profile['send_browser_notifications'] == 1)
						{
							$expired_alert_stats[$member_zone_section['sec_name']]['Send Browser']['Total']++;
						}
					}
					$expired_alert_stats['Expired']['Total']++;
				}
			}
			unset($user_profiles_chunk);
			$table = "<table class='table table-striped table-hover table-bordered'>";
			$table .= "<thead>";
			$table .= "<th></th>";
			$table .= "<th>Total</th>";
			$table .= "<th>Send SMS</th>";
			$table .= "<th>Send Email</th>";
			$table .= "<th>Send App</th>";
			$table .= "<th>Send Browser</th>";
			$table .= "</thead>";
			$table .= "<tbody>";
			foreach($expired_alert_stats as $key => $expired_alert_stat)
			{
				$table .= "<tr>";
				$table .= "<td>$key</td>";
				foreach($expired_alert_stat as $expired_alert_stat_key => $expired_alert_stat1)
				{
					if($expired_alert_stat_key == 'Total')
					{
						$table .= "<th>$expired_alert_stat[$expired_alert_stat_key]</th>";
					}
					else
					{
						$table .= "<td>" . $expired_alert_stat[$expired_alert_stat_key]['Valid'] . "/" . $expired_alert_stat[$expired_alert_stat_key]['Total']. "</td>";
					}
				}
				$table .= "</tr>";
			}
			$table .= "</tbody>";
			$table .= "</table>";
			$data = array(
				'data' => $table
			);
			return json_encode($data);
			/*PROCESS TABLE 5 END*/
		}
	}

	public function loadFreeData(Request $request)
	{
		if(Session::get('is_admin'))
		{
			$free_zone_sections = free_zone_sections::select('sec_id', 'sec_name')->orderBy('sec_ordering')->get()->toArray();
			ini_set('memory_limit', "500M");
			$user_profiles_chunk = array_chunk(user_profiles::select(
				DB::raw("length(usr_email_id) as usr_email_id"),
				DB::raw("length(usr_mobile_number) as usr_mobile_number"),
				DB::raw("length(fcm_token_android) as fcm_token_android"),
				DB::raw("length(fcm_token_ios) as fcm_token_ios"),
				DB::raw("length(gcm_browser_token) as gcm_browser_token"),
				DB::raw("length(safari_browser_token) as safari_browser_token"),
				DB::raw("length(trusted_mobile_device) as trusted_mobile_device"),
				'send_email',
				'send_sms',
				'send_mobile_app_notifications',
				'send_browser_notifications',
				'f1_alerts',
				'f2_alerts',
				'f3_alerts',
				'f4_alerts',
				'f5_alerts',
				'f6_alerts',
				'f8_alerts',
				'f9_alerts'
			)->get()->toArray(), 20000, true);

			/*INITIALISE TABLE 6 TOTAL START*/
			$free_zone_alert_stats['Users']['Total'] = 0;

			$free_zone_alert_stats['Users']['Send SMS']['Valid'] = 0;
			$free_zone_alert_stats['Users']['Send SMS']['Total'] = 0;

			$free_zone_alert_stats['Users']['Send Email']["Valid"] = 0;
			$free_zone_alert_stats['Users']['Send Email']["Total"] = 0;

			$free_zone_alert_stats['Users']['Send App']['Valid'] = 0;
			$free_zone_alert_stats['Users']['Send App']['Total'] = 0;

			$free_zone_alert_stats['Users']['Send Browser']['Valid'] = 0;
			$free_zone_alert_stats['Users']['Send Browser']['Total'] = 0;
			/*INITIALISE TABLE 6 TOTAL END*/

			/*INITIALISE TABLE 6 START*/
			foreach ($free_zone_sections as $free_zone_section)
			{
				$free_zone_alert_stats[$free_zone_section['sec_name']]['Total'] = 0;

				$free_zone_alert_stats[$free_zone_section['sec_name']]['Send SMS']['Valid'] = 0;
				$free_zone_alert_stats[$free_zone_section['sec_name']]['Send SMS']['Total'] = 0;

				$free_zone_alert_stats[$free_zone_section['sec_name']]['Send Email']['Valid'] = 0;
				$free_zone_alert_stats[$free_zone_section['sec_name']]['Send Email']['Total'] = 0;

				$free_zone_alert_stats[$free_zone_section['sec_name']]['Send App']['Valid'] = 0;
				$free_zone_alert_stats[$free_zone_section['sec_name']]['Send App']['Total'] = 0;

				$free_zone_alert_stats[$free_zone_section['sec_name']]['Send Browser']['Valid'] = 0;
				$free_zone_alert_stats[$free_zone_section['sec_name']]['Send Browser']['Total'] = 0;
			}
			/*INITIALISE TABLE 6 END*/

			/*PROCESS TABLE 5 START*/
			foreach ($user_profiles_chunk as $user_profiles)
			{
				foreach ($user_profiles as $user_profile)
				{
					if(($user_profile['send_sms'] == 1) && (!empty($user_profile['usr_mobile_number'])))
					{
						$free_zone_alert_stats['Users']['Send SMS']['Valid']++;
					}
					if($user_profile['send_sms'] == 1)
					{
						$free_zone_alert_stats['Users']['Send SMS']['Total']++;
					}

					if(($user_profile['send_email'] == 1) && (!empty($user_profile['usr_email_id'])))
					{
						$free_zone_alert_stats['Users']['Send Email']['Valid']++;
					}
					if($user_profile['send_email'] == 1)
					{
						$free_zone_alert_stats['Users']['Send Email']['Total']++;
					}

					if(($user_profile['send_mobile_app_notifications'] == 1) && ((!empty($user_profile['trusted_mobile_device']))))
					{
						$free_zone_alert_stats['Users']['Send App']['Valid']++;
					}
					if($user_profile['send_mobile_app_notifications'] == 1)
					{
						$free_zone_alert_stats['Users']['Send App']['Total']++;
					}

					if(($user_profile['send_browser_notifications'] == 1) && ((!empty($user_profile['gcm_browser_token'])) || (!empty($user_profile['safari_browser_token']))))
					{
						$free_zone_alert_stats['Users']['Send Browser']['Valid']++;
					}
					if($user_profile['send_browser_notifications'] == 1)
					{
						$free_zone_alert_stats['Users']['Send Browser']['Total']++;
					}
					foreach ($free_zone_sections as $free_zone_section)
					{
						$free_zone_section_sec_id = "f" . $free_zone_section['sec_id'] . "_alerts";
						if($user_profile[$free_zone_section_sec_id] == 1)
						{
							$free_zone_alert_stats[$free_zone_section['sec_name']]['Total']++;
						}
						if(($user_profile[$free_zone_section_sec_id] == 1) && ($user_profile['send_sms'] == 1) && (!empty($user_profile['usr_mobile_number'])))
						{
							$free_zone_alert_stats[$free_zone_section['sec_name']]['Send SMS']['Valid']++;
						}
						if(($user_profile[$free_zone_section_sec_id] == 1) && $user_profile['send_sms'] == 1)
						{
							$free_zone_alert_stats[$free_zone_section['sec_name']]['Send SMS']['Total']++;
						}

						if(($user_profile[$free_zone_section_sec_id] == 1) && ($user_profile['send_email'] == 1) && (!empty($user_profile['usr_email_id'])))
						{
							$free_zone_alert_stats[$free_zone_section['sec_name']]['Send Email']['Valid']++;
						}
						if(($user_profile[$free_zone_section_sec_id] == 1) && $user_profile['send_email'] == 1)
						{
							$free_zone_alert_stats[$free_zone_section['sec_name']]['Send Email']['Total']++;
						}

						if(($user_profile[$free_zone_section_sec_id] == 1) && ($user_profile['send_mobile_app_notifications'] == 1) && ((!empty($user_profile['trusted_mobile_device']))))
						{
							$free_zone_alert_stats[$free_zone_section['sec_name']]['Send App']['Valid']++;
						}
						if(($user_profile[$free_zone_section_sec_id] == 1) && $user_profile['send_mobile_app_notifications'] == 1)
						{
							$free_zone_alert_stats[$free_zone_section['sec_name']]['Send App']['Total']++;
						}

						if(($user_profile[$free_zone_section_sec_id] == 1) && ($user_profile['send_browser_notifications'] == 1) && ((!empty($user_profile['gcm_browser_token'])) || (!empty($user_profile['safari_browser_token']))))
						{
							$free_zone_alert_stats[$free_zone_section['sec_name']]['Send Browser']['Valid']++;
						}
						if(($user_profile[$free_zone_section_sec_id] == 1) && $user_profile['send_browser_notifications'] == 1)
						{
							$free_zone_alert_stats[$free_zone_section['sec_name']]['Send Browser']['Total']++;
						}
					}
					$free_zone_alert_stats['Users']['Total']++;
				}
			}
			unset($user_profiles_chunk);
			$table = "<table class='table table-striped table-hover table-bordered' id='member_stats_table'>";
			$table .= "<thead>";
			$table .= "<th></th>";
			$table .= "<th>Total</th>";
			$table .= "<th>Send SMS</th>";
			$table .= "<th>Send Email</th>";
			$table .= "<th>Send App</th>";
			$table .= "<th>Send Browser</th>";
			$table .= "</thead>";
			$table .= "<tbody>";
			foreach($free_zone_alert_stats as $key => $free_zone_alert_stat)
			{
				$table .= "<tr>";
				$table .= "<td>$key</td>";
				foreach($free_zone_alert_stat as $free_zone_alert_stat_key => $f_alert_stat)
				{
					if($free_zone_alert_stat_key == 'Total')
					{
						$table .= "<th>$free_zone_alert_stat[$free_zone_alert_stat_key]</th>";
					}
					else
					{
						$table .= "<td>" . $free_zone_alert_stat[$free_zone_alert_stat_key]['Valid'] . "/" . $free_zone_alert_stat[$free_zone_alert_stat_key]['Total']. "</td>";
					}
				}
				$table .= "</tr>";
			}
			$table .= "</tbody>";
			$table .= "</table>";
			$data = array(
				'data' => $table
			);
			return json_encode($data);
			/*PROCESS TABLE 5 END*/
		}
	}
}