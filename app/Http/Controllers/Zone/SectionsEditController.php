<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;
use App\Http\Controllers\ZoneBaseClass;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\member_zone_sections;
use App\free_zone_sections;
use App\stock_query_allowed_days;
use Session;
use Config;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;

class SectionsEditController extends Controller
{
    public function __construct()
	{
		$this->member_zone_sections = new member_zone_sections;
		$this->free_zone_sections = new free_zone_sections;
		$this->stock_query_allowed_days = new stock_query_allowed_days;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
	}

	public function display($zone_code){
    	if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$member_zone_sections = $this->member_zone_sections::select('*')->get();
			$free_zone_sections = $this->free_zone_sections::select('*')->get();
			$stock_query_allowed_days = $this->stock_query_allowed_days::select('sq_day', 'sq_allowed')->get()->toArray();
	   		return view('Admin.SectionsEdit.indexSectionsEdit',compact('member_zone_sections', 'free_zone_sections', 'stock_query_allowed_days'));
	   	}
	   	else
	   	{
	   		return redirect('/');
	   	}
    }

    public function edit($zone_code, $id)
    {
    	if(Session::get('is_admin'))
		{
			$member_zone_sections_obj = member_zone_sections::select('*')->where('sec_id', $id)->first();
			Session::flash('edit_member_sec', TRUE);
			Session::flash('sec_id', $id);
			Session::flash('sec_name', $member_zone_sections_obj->sec_name);
			Session::flash('sec_one_liner', $member_zone_sections_obj->sec_one_liner);
			// Session::flash('sec_description', $member_zone_sections_obj->sec_description);
			Session::flash('sec_controller', $member_zone_sections_obj->sec_controller);
			Session::flash('sec_future_table', $member_zone_sections_obj->sec_future_table);
			Session::flash('sec_active_table', $member_zone_sections_obj->sec_active_table);
			Session::flash('sec_past_table', $member_zone_sections_obj->sec_past_table);
			Session::flash('sec_is_active', $member_zone_sections_obj->sec_is_active);
			Session::flash('sec_ordering', $member_zone_sections_obj->sec_ordering);
			Session::flash('sec_url', $member_zone_sections_obj->sec_url);
			Session::flash('sec_seo_data', $member_zone_sections_obj->sec_seo_data);
			Session::flash('send_email', $member_zone_sections_obj->send_email);
			Session::flash('send_sms', $member_zone_sections_obj->send_sms);
			Session::flash('send_mobile_app_notifications', $member_zone_sections_obj->send_mobile_app_notifications);
			Session::flash('send_browser_notifications', $member_zone_sections_obj->send_browser_notifications);
			Session::flash('sec_guidelines', $member_zone_sections_obj->sec_guidelines);
			Session::flash('last_updated_at', $member_zone_sections_obj->last_updated_at);
			Session::flash('created_at', $member_zone_sections_obj->created_at);
			Session::flash('updated_at', $member_zone_sections_obj->updated_at);
			Session::flash('sec_frequency', $member_zone_sections_obj->sec_frequency);
			Session::flash('sec_stock_usage_category', $member_zone_sections_obj->sec_stock_usage_category);
			Session::flash('number_of_past_table_entries_to_show', $member_zone_sections_obj->number_of_past_table_entries_to_show);
			Session::flash('default_unload_days', $member_zone_sections_obj->default_unload_days);
			Session::flash('internal_rationale_min_size', $member_zone_sections_obj->internal_rationale_min_size);
			return view('Admin.SectionsEdit.editMemberSectionsEdit');
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
			if( !empty($request->input('sec_name'))  &&  !empty($request->input('sec_controller'))   &&   !empty($request->input('sec_url')) && is_numeric($request->input('internal_rationale_min_size'))   )
			{
					$data = array(
						'sec_name' => $request->input('sec_name'),
						'sec_one_liner' => $request->input('sec_one_liner'),
						//'sec_description' => $request->input('sec_description'),
						'sec_controller' => $request->input('sec_controller'),
						'sec_future_table' => $request->input('sec_future_table'),
						'sec_active_table' => $request->input('sec_active_table'),
						'sec_past_table' => $request->input('sec_past_table'),
						'sec_is_active' => $request->input('sec_is_active'),
						'sec_ordering' => $request->input('sec_ordering'),
						'sec_url' => $request->input('sec_url'),
						'sec_seo_data' => $request->input('sec_seo_data'),
						'send_email' => $request->input('send_email'),
						'send_sms' => $request->input('send_sms'),
						'send_mobile_app_notifications' => $request->input('send_mobile_app_notifications'),
						'send_browser_notifications' => $request->input('send_browser_notifications'),
						'sec_guidelines' => $request->input('sec_guidelines'),
						'default_unload_days' => $request->input('default_unload_days'),
						'internal_rationale_min_size' => $request->input('internal_rationale_min_size'),
						'number_of_past_table_entries_to_show' => $request->input('number_of_past_table_entries_to_show')
					);
					$member_zone_sections = member_zone_sections::first()->where(array('sec_id' => $request->input('sec_id')));
					$updateNow = $member_zone_sections->update($data);
					if($updateNow){
						Session::flash('error_message', 'Member Section Updated successfully!');
						$Message = "Memberzone Section \nSection ID:". $request->input('sec_id')." Section Name: " . $request->input('sec_name') . " updated successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
						$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
					}else{
						Session::flash('error_message_danger', 'Unable to edit Member Section!');
					}
			}else{
				Session::flash('error_message_danger', 'Unable to edit Member Section!');
			}
			$zone_code = Session::get('zone_name');
			Session::set('edit_member_sec',FALSE);
			return $this->display($zone_code);
		}
		else
		{
			return redirect('/');
		}
	}


	public function editFree($zone_code, $id)
    {
    	if(Session::get('is_admin'))
		{
			$free_zone_sections = free_zone_sections::select('*')->where('sec_id', $id)->first();
			Session::flash('edit_free_sec', TRUE);
			Session::flash('sec_id', $id);
			Session::flash('sec_name', $free_zone_sections->sec_name);
			Session::flash('sec_one_liner', $free_zone_sections->sec_one_liner);
			//Session::flash('sec_description', $free_zone_sections->sec_description);
			Session::flash('sec_controller', $free_zone_sections->sec_controller);
			Session::flash('sec_future_table', $free_zone_sections->sec_future_table);
			Session::flash('sec_active_table', $free_zone_sections->sec_active_table);
			Session::flash('sec_past_table', $free_zone_sections->sec_past_table);
			Session::flash('sec_is_active', $free_zone_sections->sec_is_active);
			Session::flash('sec_ordering', $free_zone_sections->sec_ordering);
			Session::flash('sec_url', $free_zone_sections->sec_url);
			Session::flash('send_email', $free_zone_sections->send_email);
			Session::flash('send_sms', $free_zone_sections->send_sms);
			Session::flash('send_mobile_app_notifications', $free_zone_sections->send_mobile_app_notifications);
			Session::flash('send_browser_notifications', $free_zone_sections->send_browser_notifications);
			Session::flash('last_updated_at', $free_zone_sections->last_updated_at);
			Session::flash('created_at', $free_zone_sections->created_at);
			Session::flash('updated_at', $free_zone_sections->updated_at);


			Session::flash('meta_title', $free_zone_sections->meta_title);
			Session::flash('meta_description', $free_zone_sections->meta_description);
			Session::flash('meta_canonical', $free_zone_sections->meta_canonical);
			Session::flash('meta_keywords', $free_zone_sections->meta_keywords);
			Session::flash('meta_robots', $free_zone_sections->meta_robots);


			Session::flash('ogp_og_title', $free_zone_sections->ogp_og_title);
			Session::flash('ogp_og_type', $free_zone_sections->ogp_og_type);
			Session::flash('ogp_og_image', $free_zone_sections->ogp_og_image);
			Session::flash('ogp_og_url', $free_zone_sections->ogp_og_url);
			Session::flash('ogp_og_description', $free_zone_sections->ogp_og_description);
			Session::flash('ogp_og_site_name', $free_zone_sections->ogp_og_site_name);
			Session::flash('ogp_og_image_url', $free_zone_sections->ogp_og_image_url);
			Session::flash('ogp_og_image_secure_url', $free_zone_sections->ogp_og_image_secure_url);
			Session::flash('ogp_article_published_time', $free_zone_sections->ogp_article_published_time);
			Session::flash('ogp_article_modified_time', $free_zone_sections->ogp_article_modified_time);
			Session::flash('ogp_article_expiration_time', $free_zone_sections->ogp_article_expiration_time);
			Session::flash('ogp_article_author', $free_zone_sections->ogp_article_author);
			Session::flash('ogp_article_section', $free_zone_sections->ogp_article_section);
			Session::flash('ogp_article_tag', $free_zone_sections->ogp_article_tag);


			Session::flash('twitter_card', $free_zone_sections->twitter_card);
			Session::flash('twitter_site', $free_zone_sections->twitter_site);
			Session::flash('twitter_title', $free_zone_sections->twitter_title);
			Session::flash('twitter_description', $free_zone_sections->twitter_description);
			Session::flash('twitter_image', $free_zone_sections->twitter_image);
			Session::flash('twitter_image_alt', $free_zone_sections->twitter_image_alt);
			Session::flash('twitter_creator_id', $free_zone_sections->twitter_creator_id);
			

			Session::flash('schema_url', $free_zone_sections->schema_url);
			Session::flash('schema_name', $free_zone_sections->schema_name);
			Session::flash('schema_image', $free_zone_sections->schema_image);
			Session::flash('schema_description', $free_zone_sections->schema_description);
			Session::flash('schema_thumbnail_url', $free_zone_sections->schema_thumbnail_url);
			Session::flash('schema_text', $free_zone_sections->schema_text);
			Session::flash('schema_source_organization', $free_zone_sections->schema_source_organization);
			Session::flash('schema_schema_version', $free_zone_sections->schema_schema_version);
			Session::flash('schema_publisher', $free_zone_sections->schema_publisher);
			Session::flash('schema_keywords', $free_zone_sections->schema_keywords);
			Session::flash('schema_is_accessible_free', $free_zone_sections->schema_is_accessible_free);
			Session::flash('schema_headline', $free_zone_sections->schema_headline);
			Session::flash('schema_genre', $free_zone_sections->schema_genre);
			Session::flash('schema_date_published', $free_zone_sections->schema_date_published);
			Session::flash('schema_date_modified', $free_zone_sections->schema_date_modified);
			Session::flash('schema_date_created', $free_zone_sections->schema_date_created);
			Session::flash('schema_creator', $free_zone_sections->schema_creator);
			Session::flash('schema_contributor', $free_zone_sections->schema_contributor);
			Session::flash('schema_content_rating', $free_zone_sections->schema_content_rating);
			Session::flash('schema_comment_count', $free_zone_sections->schema_comment_count);
			Session::flash('schema_comment', $free_zone_sections->schema_comment);
			Session::flash('schema_author', $free_zone_sections->schema_author);
			Session::flash('schema_about', $free_zone_sections->schema_about);
			Session::flash('schema_word_count', $free_zone_sections->schema_word_count);
			Session::flash('schema_article_section', $free_zone_sections->schema_article_section);
			Session::flash('schema_article_body', $free_zone_sections->schema_article_body);
			Session::flash('schema_type', $free_zone_sections->schema_type);
			Session::flash('schema_context', $free_zone_sections->schema_context);

			Session::flash('sec_seo_data', $free_zone_sections->sec_seo_data);
			Session::flash('sec_guidelines', $free_zone_sections->sec_guidelines);
			Session::flash('topic_ARN', $free_zone_sections->topic_ARN);
			Session::flash('home_page_title', $free_zone_sections->home_page_title);
			Session::flash('home_page_image', $free_zone_sections->home_page_image);
			Session::flash('home_page_preview', $free_zone_sections->home_page_preview);
			Session::flash('home_page_count', $free_zone_sections->home_page_count);
			Session::flash('number_of_days_for_which_to_show_entries', $free_zone_sections->number_of_days_for_which_to_show_entries);
			Session::flash('valid_for_search', $free_zone_sections->valid_for_search);


			return view('Admin.SectionsEdit.editFreeSectionsEdit');
		}
		else
		{
			return redirect('/');
		}
	}

	public function updateFree($request)
	{
		if($request->session()->get('is_admin'))
		{
			
		
			if( !empty($request->input('sec_name'))  &&  !empty($request->input('sec_controller'))   &&   !empty($request->input('sec_url'))   )
			{
					$data = array(
					'sec_name' => $request->input('sec_name'),
					'sec_one_liner' => $request->input('sec_one_liner'),
					//'sec_description' => $request->input('sec_description'),
					'sec_controller' => $request->input('sec_controller'),
					'sec_future_table' => $request->input('sec_future_table'),
					'sec_active_table' => $request->input('sec_active_table'),
					// 'sec_past_table' => $request->input('sec_past_table'),
					'sec_is_active' => $request->input('sec_is_active'),
					'sec_ordering' => $request->input('sec_ordering'),
					'sec_url' => $request->input('sec_url'),
					'send_email' => $request->input('send_email'),
					'send_sms' => $request->input('send_sms'),
					'send_mobile_app_notifications' => $request->input('send_mobile_app_notifications'),
					'send_browser_notifications' => $request->input('send_browser_notifications'),

					'meta_title' => $request->input('meta_title'),
					'meta_description' => $request->input('meta_description'),
					'meta_canonical' => $request->input('meta_canonical'),
					'meta_keywords' => $request->input('meta_keywords'),
					'meta_robots' => $request->input('meta_robots'),

					'ogp_og_title' => $request->input('ogp_og_title'),
					'ogp_og_type' => $request->input('ogp_og_type'),
					'ogp_og_image' => $request->input('ogp_og_image'),
					'ogp_og_url' => $request->input('ogp_og_url'),
					'ogp_og_description' => $request->input('ogp_og_description'),
					'ogp_og_site_name' => $request->input('ogp_og_site_name'),
					'ogp_og_image_url' => $request->input('ogp_og_image_url'),
					'ogp_og_image_secure_url' => $request->input('ogp_og_image_secure_url'),
					'ogp_article_published_time' => $request->input('ogp_article_published_time'),
					'ogp_article_modified_time' => $request->input('ogp_article_modified_time'),
					'ogp_article_expiration_time' => $request->input('ogp_article_expiration_time'),
					'ogp_article_author' => $request->input('ogp_article_author'),
					'ogp_article_section' => $request->input('ogp_article_section'),
					'ogp_article_tag' => $request->input('ogp_article_tag'),

					'twitter_card' => $request->input('twitter_card'),
					'twitter_site' => $request->input('twitter_site'),
					'twitter_title' => $request->input('ogp_article_tag'),
					'twitter_description' => $request->input('twitter_description'),
					'twitter_image' => $request->input('twitter_image'),
					'twitter_image_alt' => $request->input('twitter_image_alt'),
					'twitter_creator_id' => $request->input('twitter_creator_id'),

					'schema_url' => $request->input('schema_url'),
					'schema_name' => $request->input('schema_name'),
					'schema_image' => $request->input('schema_image'),
					'schema_description' => $request->input('schema_description'),
					'schema_thumbnail_url' => $request->input('schema_thumbnail_url'),
					'schema_text' => $request->input('schema_text'),
					'schema_source_organization' => $request->input('schema_source_organization'),
					'schema_schema_version' => $request->input('schema_schema_version'),
					'schema_publisher' => $request->input('schema_publisher'),
					'schema_keywords' => $request->input('schema_keywords'),
					'schema_is_accessible_free' => $request->input('schema_is_accessible_free'),
					'schema_headline' => $request->input('schema_headline'),
					'schema_genre' => $request->input('schema_genre'),
					'schema_date_published' => $request->input('schema_date_published'),
					'schema_date_modified' => $request->input('schema_date_modified'),
					'schema_date_created' => $request->input('schema_date_created'),
					'schema_creator' => $request->input('schema_creator'),
					'schema_contributor' => $request->input('schema_contributor'),
					'schema_content_rating' => $request->input('schema_content_rating'),
					'schema_comment_count' => $request->input('schema_comment_count'),
					'schema_comment' => $request->input('schema_comment'),
					'schema_author' => $request->input('schema_author'),
					'schema_about' => $request->input('schema_about'),
					'schema_word_count' => $request->input('schema_word_count'),
					'schema_article_section' => $request->input('schema_article_section'),
					'schema_article_body' => $request->input('schema_article_body'),
					'schema_type' => $request->input('schema_type'),
					'schema_context' => $request->input('schema_context'),

					'sec_seo_data' => $request->input('sec_seo_data'),
					'sec_guidelines' => $request->input('sec_guidelines'),
					'topic_ARN' => $request->input('topic_ARN'),
					'home_page_title' => $request->input('home_page_title'),
					'home_page_image' => $request->input('home_page_image'),
					'home_page_preview' => $request->input('home_page_preview'),
					'home_page_count' => $request->input('home_page_count'),
					'number_of_days_for_which_to_show_entries' => $request->input('number_of_days_for_which_to_show_entries'),
					'valid_for_search' => $request->input('valid_for_search')
				);
					$free_zone_sections = free_zone_sections::first()->where(array('sec_id' => $request->input('sec_id')));
					$updateNow = $free_zone_sections->update($data);
					if($updateNow){
						Session::flash('error_message', 'Free Section Updated successfully!');
						$Message = "Freezone Section \nSection ID:". $request->input('sec_id')." Section Name: " . $request->input('sec_name') . " updated successfully \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
						$this->aws->send_admin_alerts($this->Alert_SuperAdmin,$Message);
					}else{
						Session::flash('error_message_danger', 'Unable to edit Member Section!');
					}
			}else{
				Session::flash('error_message_danger', 'Unable to edit Member Section!');
			}
			$zone_code = Session::get('zone_name');
			Session::set('edit_member_sec',FALSE);
			return $this->display($zone_code);
				
		}
		else
		{
			return redirect('/');
		}
	}
}
