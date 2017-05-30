<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\support_incoming;
use App\support_replies;
use App\support_reply_templates;
use App\order_details;
use App\user_profiles;
use Session;
use Config;
use URL;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;
define("SITE_ADDRESS1",config('config_path_vars.site_address'));

class CustomerSupportController extends ZoneBaseClass
{
	public function __construct()
	{
		$this->support_incoming = new support_incoming;
		$this->support_replies = new support_replies;
		$this->support_reply_templates = new support_reply_templates;
		$this->aws = new CustomAwsController;
		// $this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}
	public function display($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			return view('Admin.CustomerSupport.viewSupportSection');
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
			

			$open_cases = support_incoming::select('*')->where('case_closed',0);

        	return \Datatables::of($open_cases)->addColumn('Reply', function ($case) {
                return '<a href="/Admin/'.Session::get('zone_name').'/edit/'.$case->id.'">Reply</a>';
            })->editColumn('has_attachments', function ($case) {
                return $case->has_attachments ? 'Yes' : 'No';
            })->editColumn('case_replied', function ($case) {
                return $case->case_replied ? 'Yes' : 'No';
            })->editColumn('case_flagged', function ($case) {
                return $case->case_flagged ? 'Yes' : 'No';
            })->editColumn('case_closed', function ($case) {
                return $case->case_closed ? 'Yes' : 'No';
            })->editColumn('email_body', function ($case) {
                return base64_decode($case->email_body);
            })->make(true);
		}
		else
		{
			return "FALSE";
		}
	}

	public function renderTable2($zone_code){
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			

			$closed_cases = support_incoming::select('*')->where('case_closed',1);

        	return \Datatables::of($closed_cases)->editColumn('has_attachments', function ($case) {
                return $case->has_attachments ? 'Yes' : 'No';
            })->editColumn('case_replied', function ($case) {
                return $case->case_replied ? 'Yes' : 'No';
            })->editColumn('case_flagged', function ($case) {
                return $case->case_flagged ? 'Yes' : 'No';
            })->editColumn('case_closed', function ($case) {
                return $case->case_closed ? 'Yes' : 'No';
            })->editColumn('email_body', function ($case) {
                return base64_decode($case->email_body);
            })->make(true);
		}
		else
		{
			return "FALSE";
		}
	}

	public function addNew($zone_code)
	{
		if(Session::get('is_admin'))
		{
			
			$case_category_arr = $this->support_incoming::select('case_category')->groupBy('case_category')->pluck('case_category')->toArray();
			return view('Admin.CustomerSupport.addCase', compact('case_category_arr'));
		}
		else
		{
			return redirect('/');
		}
	}
	public function save($request)
	{
		
		$username = $request->input('username');
		$customer_name = $request->input('customer_name');
		$user_mobile = $request->input('user_mobile');
		$email_from = $request->input('email_from');
		$case_category = $request->input('case_category');
		$email_body = $request->input('email_body');
		if(!empty($case_category) and !empty($email_body))
		{
			$data = array(
				'username' => $username,
				'customer_name' => $customer_name,
				'user_mobile' => $user_mobile,
				'email_from' => $email_from,
				'case_category' => $case_category,
				'subject' => 'Query on '.$case_category,
				'email_body' => $email_body
			);
			$saved = $this->saveCase($data);
			if($saved) {
				return redirect('/Admin/'.Session::get('zone_name'))->with('support_message', 'Case added successfully.');
			} else {
				return redirect()->back()->with('support_danger', 'Case was not added.');
			}
		} else {
			return redirect()->back()->with('support_danger', 'Error! Something went wrong.');
		}
	}
	public function saveCase($data)
	{
		$this->support_incoming->username = $data['username'];
		$this->support_incoming->customer_name = $data['customer_name'];
		$this->support_incoming->user_mobile = $data['user_mobile'];
		$this->support_incoming->email_from = $data['email_from'];
		$this->support_incoming->case_category = $data['case_category'];
		$this->support_incoming->subject = $data['subject'];
		$this->support_incoming->email_body = base64_encode( $data['email_body'] );
		if($this->support_incoming->save()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function edit($zone_code, $id)
	{
		if(Session::get('is_admin'))
		{
			$support_incoming = $this->support_incoming::select('id','customer_name','username','user_mobile','email_from','email_body','case_flagged')->where('id', $id)->first()->toArray();
			if(!empty($support_incoming)) {
				if(isset($support_incoming['username']) && !empty($support_incoming['username'])) {
					$order_details = order_details::select('order_id')->where('order_username', $support_incoming['username'])->orderBy('order_id','desc')->first();
					if(!empty($order_details)) {
						$support_incoming['order_id'] = $order_details->order_id;
						
					}
				}

				// Update Name, Email, Mobile if already empty
				if(!empty($support_incoming['username'])) {
					if(empty($support_incoming['customer_name']) || empty($support_incoming['user_mobile']) || empty($support_incoming['email_from'])) {
        				$update_arr = [];
        				$user_profiles = user_profiles::select('usr_name','usr_email_id','usr_mobile_number')->where('usr_username', $support_incoming['username'])->first()->toArray();
        				if(!empty($user_profiles)) {
        					if(empty($support_incoming['customer_name']) && !empty($user_profiles['usr_name'])) {
        						$support_incoming['customer_name'] = $update_arr['customer_name'] = $user_profiles['usr_name'];
        					}
        					if(empty($support_incoming['email_from']) && !empty($user_profiles['usr_email_id'])) {
        						$support_incoming['email_from'] = $update_arr['email_from'] = $user_profiles['usr_email_id'];
        					}
        					if(empty($support_incoming['user_mobile']) && !empty($user_profiles['usr_mobile_number'])) {
        						$support_incoming['user_mobile'] = $update_arr['user_mobile'] = $user_profiles['usr_mobile_number'];
        					}
        				}
        				$res = $this->support_incoming::where('id', $support_incoming['id'])->update($update_arr);
    					// dd($support_incoming);
					}
				}

				$category_arr = $this->support_reply_templates::select('category')->groupBy('category')->pluck('category')->toArray();
				$support_reply_templates = [];
				foreach($category_arr as $category) {
					
					$support_reply_templates[$category] = $this->support_reply_templates::select('*')->where('category',$category)->get()->toArray();
				}
				// dd($support_reply_templates);
				// $support_incoming['email_body'] = base64_encode('Enter \r\n for newline');
				return view('Admin.CustomerSupport.editCase', compact('support_incoming','support_reply_templates'));
			}
		}
		return redirect('/');
	}
	public function update($request)
	{
		$postedData = $request->all();
		if(Session::get('is_admin'))
		{
			if(!empty($postedData)) {
				$admin_reply = $postedData['email_body'];
				$case_id = $postedData['case_id'];

				

				if(!isset($postedData['close'])) { // Reply / ReplyClose
					

					$token = $this->randomTokenGenerator().'_'.microtime(true);
					$data = array(
						'case_replied' => isset($postedData['reply']) || isset($postedData['reply_close']) ? 1 : 0,
						'case_closed' => isset($postedData['close']) || isset($postedData['reply_close']) ? 1 : 0,
						'case_rating_auth_code' => $token
					);
					if(isset($postedData['reply_close'])) {
						$data['closed_by'] = Session::get('user_name');
					}
					$support_incoming_update = $this->support_incoming::first()->where(array('id' => $case_id));
					$updateNow = $support_incoming_update->update($data);
					if(!empty($admin_reply)) {
						if($updateNow) {
							// Check if email is present.
							$support_incoming = $this->support_incoming::select('email_from')->where('id', $case_id)->first()->toArray();
							if(!isset($support_incoming['email_from']) || empty($support_incoming['email_from'])) {
								return redirect()->back()->with('support_danger', 'Error! Could not sent reply for empty email id.');
							} else {
								$saved = $this->saveReply($case_id,$admin_reply,$token);
								return redirect('/Admin/'.Session::get('zone_name'))->with('support_message', 'Reply sent successfully.');
							}
						} else {
							return redirect()->back()->with('support_danger', 'Error! Something went wrong.');
						}
						
					} else {
						return redirect()->back()->with('support_danger', 'Error! Reply field should not be empty.');
					}
				} else { // Close Case
					$data = array( 'case_closed' => 1, 'closed_by'=>Session::get('user_name') );
					$support_incoming_update = $this->support_incoming::first()->where(array('id' => $case_id));
					$updateNow = $support_incoming_update->update($data);
					return redirect('/Admin/'.Session::get('zone_name'))->with('support_message', 'Case closed successfully.');
				}
				
			} else {
				return redirect()->back()->with('support_danger', 'Error! Something went wrong.');
			}
		}
		else
		{
			return redirect('/');
		}
	}
	public function saveReply($case_id,$admin_reply,$token)
	{
		$support_incoming = $this->support_incoming::select('id','username','email_from','subject','email_body')->where('id', $case_id)->first()->toArray();
		$user_url = Config::get('config_path_vars.site_address');

		$url1= $user_url.'/user/1/'.$token.'/user-ratings';
		$url2= $user_url.'/user/2/'.$token.'/user-ratings';
		$url3= $user_url.'/user/3/'.$token.'/user-ratings';
		$url4= $user_url.'/user/4/'.$token.'/user-ratings';
		$url5= $user_url.'/user/5/'.$token.'/user-ratings';
		$email_to = $support_incoming['email_from'];
		$email_subject = $support_incoming['subject'];

		
		$email_body = 'Your support case has been replied by our team. Details are as below -';
		$email_body .= '<br/><br/>';
		$email_body .= '<b>Your Query</b> : '.base64_decode($support_incoming['email_body']);
		$email_body .= '<br/><br/>';
		$email_body .= '<b>Reply</b> : '.trim($admin_reply);
		$email_body .= '<br/><br/>';
		$email_body .= '<html>
                        <head>
                        <style type="text/css">
                            @-ms-viewport {
                                width: device-width;
                            }
                            body {
                                margin: 0;
                                padding: 0;
                                min-width: 100%;
                            }
                            table {
                                border-collapse: collapse;
                                border-spacing: 0;
                            }
                            td {
                                vertical-align: top;
                            }
                            img {
                                border: 0;
                                -ms-interpolation-mode: bicubic;
                                max-width: 50% !important;
                                height: auto;
                            }
                            a {
                                text-decoration: none;
                                color: #119da2;
                            }
                            a:hover {
                                text-decoration: underline;
                            }

                            *[class=main-wrapper],
                            *[class=main-content]{
                                min-width: 0 !important;
                                width: 300px !important;
                                margin: 0 !important;
                            }
                            *[class=rating] {
                              unicode-bidi: bidi-override;
                              direction: rtl;
                            }
                            *[class=rating] > *[class=star] {
                              display: inline-block;
                              position: relative;
                              text-decoration: none;
                            }

                            @media (max-width: 621px) {
                                body {
                                    margin: 0;
                                    padding: 0;
                                    width: 50%;
                                }
                                * {
                                    box-sizing: border-box;
                                    -moz-box-sizing: border-box;
                                    -webkit-box-sizing: border-box;
                                    -o-box-sizing: border-box;
                                }
                                table {
                                    min-width: 0 !important;
                                    width: 100% !important;
                                }
                                *[class=body-copy] {
                                    padding: 0 10px !important;
                                }
                                *[class=main-wrapper],
                                *[class=main-content]{
                                    min-width: 0 !important;
                                    width: 320px !important;
                                    margin: 0 auto !important;
                                }
                                *[class=ms-sixhundred-table] {
                                    width: 100% !important;
                                    display: block !important;
                                    float: left !important;
                                    clear: both !important;
                                }
                                *[class=content-padding] {
                                    padding-left: 10px !important;
                                    padding-right: 10px !important;
                                }
                                *[class=bottom-padding]{
                                    margin-bottom: 15px !important;
                                    font-size: 0 !important;
                                    line-height: 0 !important;
                                }
                                *[class=top-padding] {
                                    display: none !important;
                                }
                                *[class=hide-mobile] {
                                    display: none !important;
                                }
                                

                                * [lang~="x-star-wrapper"]:hover *[lang~="x-star-number"]{
                                    color: #AEAEAE !important;
                                    border-color: #FFFFFF !important;
                                }
                                * [lang~="x-star-wrapper"]{
                                    pointer-events: none !important;
                                }
                                * [lang~="x-star-divbox"]{
                                    pointer-events: auto !important;
                                }
                                *[class=rating] *[class="star-wrapper"] a div:nth-child(2),
                                *[class=rating] *[class="star-wrapper"]:hover a div:nth-child(2),
                                *[class=rating] *[class="star-wrapper"] ~ *[class="star-wrapper"] a div:nth-child(2){
                                  display : none !important;
                                  width : 0 !important;
                                  height: 0 !important;
                                  overflow : hidden !important;
                                  float : left !important;
                                }
                                *[class=rating] *[class="star-wrapper"] a div:nth-child(1),
                                *[class=rating] *[class="star-wrapper"]:hover a div:nth-child(1),
                                *[class=rating] *[class="star-wrapper"] ~ *[class="star-wrapper"] a div:nth-child(1){
                                  display : block !important;
                                  width : auto !important;
                                  overflow : visible !important;
                                  float : none !important;
                                }
                            }
                        </style>
                        </head>
                        <body style="margin-top: 0;margin-bottom: 0;margin-left: 0;margin-right: 0;padding-top: 0;padding-bottom: 0;padding-left: 0;padding-right: 0;width: 100%;background-color: #f5f5f5">
                        <table class="main-wrapper" style="border-collapse: collapse;border-spacing: 0;display: table;table-layout: fixed; margin: 0 auto; -webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;text-rendering: optimizeLegibility;background-color: #f5f5f5; width: 100%;">
                                <tbody>
                                    <tr>
                                        <td style="padding: 0;vertical-align: top; width: 100%;" class="">
                                            <center>

                                                <table class="main-content" style="width: 100%; max-width: 600px; border-collapse: separate;border-spacing: 0;margin-left: auto;margin-right: auto; border: 1px solid #EAEAEA; border-radius: 4px; -webkit-border-radius: 4px; -moz-border-radius: 4px; background-color: #ffffff; overflow: hidden;" width="600">
                                                    <tbody>
                                                        <tr>
                                                            <td style="padding: 0;vertical-align: top;">
                                                                <table class="main-content" style="border-collapse: collapse;border-spacing: 0;margin-left: auto;margin-right: auto;width: 100%; max-width: 600px;">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td style="padding: 0;vertical-align: top;text-align: left">
                                                                                <table class="contents" style="border-collapse: collapse;border-spacing: 0;width: 100%;">
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td class="content-padding" style="padding: 0;vertical-align: top">
                                                                                                <div class="body-copy" style="margin: 0;">

                                                                                                    <div style="margin: 0;color: #60666d;font-size: 50px;font-family: sans-serif;line-height: 20px; text-align: left;">
                                                                                                        <div class="bottom-padding" style="margin-bottom: 0px; line-height: 15px; font-size: 15px;">&nbsp;</div>
                                                                                                        <div style="text-align: center; margin: 0; font-size: 12px;  text-transform: uppercase; letter-spacing: .5px;">Please rate the reply:</div>
                                                                                                        <div style="width: 100%; text-align: center; float: left;">
                                                                                                            <div class="rating" style="text-align: center; margin: 0; font-size: 50px; width: 275px; margin: 0 auto; margin-top: 10px;">

                                                                                                                <table style="border-collapse: collapse;border-spacing: 0;width: 275px; margin: 0 auto; font-size: 45px; direction: rtl;" dir="rtl">
                                                                                                                    <tbody><tr>
                                                                                                                        <td style="padding: 0;vertical-align: top;" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                                                                            <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                                                                                <a href="'.$url5.'" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="1">
                                                                                                                                    <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                                                                    <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                                                                                </a>
                                                                                                                                <a href="'.$url5.'" class="star-number" target="_blank" lang="x-star-number" style="font-family: sans-serif;color: #AEAEAE; font-size: 14px; text-decoration: none; display: block;height: 35px;width: 55px;overflow: hidden;line-height: 25px;border-bottom: 3px solid #FFFFFF; text-align: center;">5</a>
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                        <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                                                                            <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                                                                                <a href="'.$url4.'" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="2">
                                                                                                                                    <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                                                                    <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                                                                                </a>
                                                                                                                                <a href="'.$url4.'" class="star-number" target="_blank" lang="x-star-number" style="font-family: sans-serif;color: #AEAEAE; font-size: 14px; text-decoration: none; display: block;height: 35px;width: 55px;overflow: hidden;line-height: 25px;border-bottom: 3px solid #FFFFFF; text-align: center;">4</a>
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                        <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                                                                            <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                                                                                <a href="'.$url3.'" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="3">
                                                                                                                                    <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                                                                    <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                                                                                </a>
                                                                                                                                <a href="'.$url3.'" class="star-number" target="_blank" lang="x-star-number" style="font-family: sans-serif;color: #AEAEAE; font-size: 14px; text-decoration: none; display: block;height: 35px;width: 55px;overflow: hidden;line-height: 25px;border-bottom: 3px solid #FFFFFF; text-align: center;">3</a>
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                        <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                                                                            <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                                                                                <a href="'.$url2.'" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="4">
                                                                                                                                    <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                                                                    <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                                                                                </a>
                                                                                                                                <a href="'.$url2.'" class="star-number" target="_blank" lang="x-star-number" style="font-family: sans-serif;color: #AEAEAE; font-size: 14px; text-decoration: none; display: block;height: 35px;width: 55px;overflow: hidden;line-height: 25px;border-bottom: 3px solid #FFFFFF; text-align: center;">2</a>
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                        <td style="padding: 0;vertical-align: top" width="55" class="star-wrapper" lang="x-star-wrapper">
                                                                                                                            <div style="display: block; text-align: center; float: left;width: 55px;overflow: hidden;line-height: 60px;">
                                                                                                                                <a href="'.$url1.'" class="star" target="_blank" lang="x-star-divbox" style="color: #FFCC00; text-decoration: none; display: inline-block;height: 50px;width: 55px;overflow: hidden;line-height: 60px;" tabindex="5">
                                                                                                                                    <div lang="x-empty-star" style="margin: 0;display: inline-block;">☆</div>
                                                                                                                                    <div lang="x-full-star" style="margin: 0;display: inline-block; width:0; overflow:hidden;float:left; display:none; height: 0; max-height: 0;">★</div>
                                                                                                                                </a>
                                                                                                                                <a href="'.$url1.'" class="star-number" target="_blank" lang="x-star-number" style="font-family: sans-serif;color: #AEAEAE; font-size: 14px; text-decoration: none; display: block;height: 35px;width: 55px;overflow: hidden;line-height: 25px;border-bottom: 3px solid #FFFFFF; text-align: center;">1</a>
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                </tbody></table>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>

                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </center>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </body>
                        </html>';
		// dd($email_body);
		$res = $this->aws->sendEmail_Centralized($email_to,$email_subject,$email_body);
		
		// Send notifications if username present
		if(!empty($support_incoming['username'])) {
			$notificationBody = "Support has replied to your case on www.sptulsian.com. Kindly check your email for detailed information. Thank you.";
	        $url = SITE_ADDRESS1;

			\MemberZoneAlert::sendSingleAlert($support_incoming['username'], $email_subject, $notificationBody, $url, null, 0, 2, 2, 2, 2);
		}

		$this->support_replies->support_incoming_id = $case_id;
		$this->support_replies->reply_time = date('Y-m-d H:i:s');
		$this->support_replies->reply_by = Session::get('user_name');
		$this->support_replies->email_to = $email_to;
		$this->support_replies->subject = $email_subject;
		$this->support_replies->email_body = trim($admin_reply);
		if($this->support_replies->save()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	public function autoCompleteUserName($request){
		
        $data = array();
        $user_profiles_exact = user_profiles::select('usr_username')
        		->whereRaw("usr_username LIKE '%" . $request['query'] . "%'
    				order by case 
	    				when usr_username like '" . $request['query'] . "'  then 1  
	                  	when usr_username like '" . $request['query'] . "%' then 2  
	                  	when usr_username like '%" . $request['query'] ."%' then 3 end")
        		->limit(1000)->get();
        if(!empty($user_profiles_exact)) {
	        foreach ($user_profiles_exact as $user_profiles_exact){
	        	array_push($data, $user_profiles_exact->usr_username);
	        }
	    }

        $suggestions = array('suggestions' => $data );

        $data = json_encode($suggestions);
        return $data;
    }
    public function checkUserName($request){
		
        $user_profiles = user_profiles::select('usr_username')->where('usr_username', $request['query'])->get();
		$data = count($user_profiles); 
        return $data;
    }
    public function saveUsernameForCase($request){
		
		$inputData = $request->all();
		$case_update = $this->support_incoming::first()->where(array('id' => $request['id']));
		$updateNow = $case_update->update(['username'=>$inputData['username']]);
        /*if($updateNow) {
			$order_details = order_details::select('order_id')->where('order_username', $inputData['username'])->orderBy('order_id','desc')->first();
			if(!empty($order_details)) {
				return $order_details->order_id;
			} 
        }*/
    	return 1;
    }
    public function getUserInfo($request){
		
        $user_profiles = user_profiles::select('usr_name','usr_email_id','usr_mobile_number')->where('usr_username', $request['query'])->first();
		// $data = count($user_profiles); 
        return $user_profiles;
    }
    public function setFlagToCase($request){
		
		$case_update = $this->support_incoming::first()->where(array('id' => $request['id']));
		$updateNow = $case_update->update(['case_flagged'=>$request['case_flagged'], 'flagged_by'=>Session::get('user_name')]);
        return $updateNow;
    }
    public function saveTemplate($request){
		
		$inputData = $request->all();
		$this->support_reply_templates->category = $inputData['category'];
		$this->support_reply_templates->title = trim($inputData['title']);
		$this->support_reply_templates->template_data = trim($inputData['template_data']);
		if($this->support_reply_templates->save()) {
			return 1;
		} else {
			return 0;
		}
    }
    public function getUserDetails($request){
		
		if($request->has('username')) {
			$user_profiles = user_profiles::select('*')->where('usr_username', $request->get('username'))->first();
			// $data = count($user_profiles); 
			if(!empty($user_profiles)) {
				$user_profiles=$user_profiles->toArray();
				unset($user_profiles['usr_password']);
				unset($user_profiles['usr_verify_email_code']);
				unset($user_profiles['fcm_token_android']);
				unset($user_profiles['fcm_token_ios']);
				unset($user_profiles['gcm_browser_token']);
				unset($user_profiles['safari_browser_token']);
				$new_user_profile=[];
				foreach($user_profiles as $key => $val) {
					$newKey = str_replace('usr_', '', $key);
					$newKey = ucwords(str_replace('_', ' ', $newKey));
					$new_user_profile[$newKey] = $val;
				}
		        return json_encode($new_user_profile);
		    } else {
		    	return 'fail';
		    }
		}
    }
    public function getOrderDetails($request){
		if($request->has('order_id')) {
			$user_profiles = order_details::select('*')->where('order_id', $request->get('order_id'))->first();
			if(!empty($user_profiles)) {
				$user_profiles=$user_profiles->toArray();
				unset($user_profiles['order_pg_payment_gateway_dump']);
				$new_user_profile=[];
				foreach($user_profiles as $key => $val) {
					$newKey = str_replace('order_', '', $key);
					$newKey = ucwords(str_replace('_', ' ', $newKey));
					$new_user_profile[$newKey] = $val;
				}
		        return json_encode($new_user_profile);
		    } else {
		    	return 'fail';
		    }
		}
    }
    public function unsubscribeUser($request){
		if($request->has('email')) {
			$res = user_profiles::where('usr_email_id', $request->get('email'))
				->orWhere('usr_email_id_temp', $request->get('email'))
				->update(['usr_email_id'=>null,'usr_email_id_temp'=>null]);
	    	return 'success';
		}
    }
    public function randomTokenGenerator()
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
		$charactersLength = strlen($characters); 
		$randomString = ''; 
		$length = 120;
		for ($i = 0; $i < $length; $i++) 
		{ 
			$randomString .= $characters[rand(0, $charactersLength - 1)]; 
		} 
		return $randomString;
	}
	
}