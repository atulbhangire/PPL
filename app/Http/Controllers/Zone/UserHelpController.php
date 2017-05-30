<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\user_help;
use Session;
use Config;
use Validator;
use Carbon\Carbon;
use Date;

class UserHelpController extends Controller
{

	function timeInReadableFormat($date_time)
    {
        if(!empty($date_time))
        {
        	return $date_time->format('jS M \\a\\t Y h:iA');
        }
    }

	public function display($zone_code)
	{
		if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);
			$user_help_msgs = user_help::get();
			if(count($user_help_msgs) > 0)
			{
				foreach ($user_help_msgs as $user_help_msg)
				{
					if(!empty($user_help_msg->id))
					{
						$user_help_msg['updated_at_date_time'] = $this->timeInReadableFormat($user_help_msg->updated_at);
					}
				}
			}
			// dd($user_help_msgs);
			return view('Admin.UserHelp.indexUserHelp', compact('user_help_msgs'));
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
			if(!empty($id))
			{
				$user_help_msg = user_help::select('id', 'html_id', 'title', 'data')->where('id', $id)->first();
				if(!empty($user_help_msg->id) && !empty($user_help_msg->html_id))
				{
					Session::flash('edit_user_help_msg', TRUE);
					Session::flash('edit_id', $user_help_msg->id);
					Session::flash('edit_html_id', $user_help_msg->html_id);
					Session::flash('edit_data', $user_help_msg->data);
					Session::flash('edit_title', $user_help_msg->title);
					return view('Admin.UserHelp.editUserHelp');
				}
				else
				{
					Session::flash('error_message', 'Error! Something went wrong.');
					$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
					return redirect($zone_name);
				}
			}
			else
			{
				Session::flash('error_message', 'Error! Something went wrong.');
				$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
				return redirect($zone_name);
			}
		}
		else
		{
			return redirect('/');
		}
	}

	public function update(Request $request)
	{
		if(Session::get('is_admin'))
		{
			$postData = $request->all();
			$rules = [
				'id' => 'bail|required|bail|exists:user_help,id',
			];
			$messages = [
				'id.required' => 'Error! Something went wrong.',
				'id.exists' => 'Error! Something went wrong.',
			];
			$validator = Validator::make($postData, $rules, $messages);
			if ($validator->fails())
			{
				return redirect()->back()->withErrors($validator);
			}
			else
			{
				$id = $request->id;
				$data = array(
					'data' => $request->edit_data,
					'title' => $request->edit_title
				);
				// dd($data);
				$update = user_help::where('id', $id)->update($data);
				if($update)
				{
					Session::flash('success_message', 'UserHelp updated successfully.');
				}
				else
				{
					Session::flash('error_message', 'UserHelp was not updated.');
				}
				$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
				return redirect($zone_name);
			}
		}
		else
		{
			return redirect('/');
		}
	}
}