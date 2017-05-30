<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\announcements;
use Session;
use Config;
use Validator;
use Carbon\Carbon;
use Date;

class AnnouncementController extends Controller
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
			$announcements = announcements::select('id', 'identifier_name', 'content', 'updated_at')->get();
			if(count($announcements) > 0)
			{
				foreach ($announcements as $announcement)
				{
					if(!empty($announcement->id))
					{
						$announcement['updated_at_date_time'] = $this->timeInReadableFormat($announcement->updated_at);
					}
				}
			}
			return view('Admin.Announcement.indexAnnouncement', compact('announcements'));
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
				$announcements = announcements::select('id', 'identifier_name', 'content')->where('id', $id)->first();
				if(!empty($announcements->id) && !empty($announcements->identifier_name) && isset($announcements->content))
				{
					Session::flash('edit_announcement', TRUE);
					Session::flash('edit_annoucement_id', $announcements->id);
					Session::flash('edit_identifier_name', $announcements->identifier_name);
					Session::flash('edit_content', $announcements->content);
					return view('Admin.Announcement.editAnnouncement');
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
				'annoucement_id' => 'bail|required|bail|exists:announcements,id',
			];
			$messages = [
				'annoucement_id.required' => 'Error! Something went wrong.',
				'annoucement_id.exists' => 'Error! Something went wrong.',
			];
			$validator = Validator::make($postData, $rules, $messages);
			if ($validator->fails())
			{
				return redirect()->back()->withErrors($validator);
			}
			else
			{
				$id = $request->annoucement_id;
				$data = array(
					'content' => $request->annoucement_content
				);
				$update = announcements::where('id', $id)->update($data);
				if($update)
				{
					Session::flash('success_message', 'Announcement updated successfully.');

					// Upload file to S3
					$identifier_name = announcements::where('id', $id)->pluck('identifier_name')->first();
					if(!empty($identifier_name)) {
						$identifier_name = str_replace(' ', '-', $identifier_name);
						$file = $identifier_name.'.txt';
						$content = str_replace('&nbsp;', ' ', htmlspecialchars_decode(strip_tags($request->annoucement_content)));
						file_put_contents('/tmp/'.$file, $content); 

						$folder = base64_encode("announcements-backups");
						$bucket = base64_encode("sptulsian-important-files");
						$filename = base64_encode($file);
						$cmd = "php /var/www/CRONS/upload_file_to_s3_admin.php " . $folder . " " . $bucket . " " . $filename. " > /dev/null 2> /dev/null &";
						exec($cmd);
					}
				}
				else
				{
					Session::flash('error_message', 'Announcement was not updated.');
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