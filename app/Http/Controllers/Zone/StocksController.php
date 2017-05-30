<?php

namespace App\Http\Controllers\Zone;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ZoneBaseClass;
use App\stock_index_table;
use Session;
use Config;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\AWS\CustomAwsController;

class StocksController extends ZoneBaseClass
{
	public function __construct()
	{
		$this->stock_index_table = new stock_index_table;
		$this->aws = new CustomAwsController;
		$this->Alert_SuperAdmin = Config::get('config_path_vars.Alert_SuperAdmin');
		$this->Bucket = Config::get('config_path_vars.Image_Bucket');
	}
	public function display($zone_code)
	{
    	if(Session::get('is_admin'))
		{
			Session::set('zone_name', $zone_code);

			/*$stocks = $this->stock_index_table::select('*')->get();*/
	   		return view('Admin.Stocks.indexStocks'/*, compact('stocks')*/);
	   	}
	   	else
	   	{
	   		return redirect('/');
	   	}
    }

    public function addNew($zone_code)
    {
   		return view('Admin.Stocks.addStocks');
    }

    public function save($request)
    {
    	$scrip_name = $request->input('scrip_name');
		$stock_spellings = $request->input('stock_spellings');
		$unique_identifier = $request->input('unique_identifier');
		$stock_status = $request->input('stock_status');
		if(!empty($scrip_name) and !empty($stock_spellings) and !empty($unique_identifier) and isset($stock_status))
		{
			if ($this->stock_index_table::where('unique_identifier', '=', $unique_identifier)->exists()) 
			{
				return redirect()->back()->with('stock_message_danger', 'Error! ' . $unique_identifier . ' unique identifier already exists.');
			}
			else if((substr($stock_spellings, 0) != ",") && (substr($stock_spellings, -1) != ","))
			{
				return redirect()->back()->with('stock_message_danger', 'Error! ' . 'Stock spellings should have comma (,) at the start and end.');
			}
			else
			{
				$data = array(
					'bse_scrip_code' => $request->input('bse_scrip_code'),
					'bse_scrip_id' => $request->input('bse_scrip_id'),
					'google_bse_code' => $request->input('google_bse_code'),
					'nse_symbol' => $request->input('nse_symbol'),
					'google_nse_code' => $request->input('google_nse_code'),
					'scrip_name' => $scrip_name,
					'scrip_group' => $request->input('scrip_group'),
					//'image_url_in_s3' => $request->input('image_url_in_s3'),
					'stock_spellings' => $stock_spellings,
					'unique_identifier' => $unique_identifier,
					'google_code_for_live_prices' => $request->input('google_code_for_live_prices'),
					'is_active' => $stock_status
				);
				$result = $this->saveStock($data);
				if($result)
				{
					$updateNow;
					if(!empty($request['image_url_in_s3'])){
						$lasId = $result;

						$name = $this->generateRandomNumbers(3).$lasId.$this->generateRandomNumbers(3);

						$destinationPath = '/tmp/'; // upload path
					    $extension = $request['image_url_in_s3']->getClientOriginalExtension(); // getting image extension
					    $fileName = $name.'.'.$extension; // renameing image
					    $request['image_url_in_s3']->move($destinationPath, $fileName);
					    $filepath = $destinationPath.$fileName;
					    $s3_URL = $this->aws->pushPublicFileS3($filepath,"stocks/".$name,$extension);

						$stock_index_table = $this->stock_index_table::first()->where(array('id' => $lasId));
						$updateNow = $stock_index_table->update(array('image_url_in_s3' => $s3_URL ));

										
						if($updateNow){
							$Message = "Stock Added From Admin \n\nStock : " . $scrip_name . " added successfully.";
							$Message .= " \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
							$Message .= "\nFields Added : \n BSE Scrip Code : \n Value : ".$request->input('bse_scrip_code');
							$Message .= "\n \n BSE Scrip ID : \n Value : ".$request->input('bse_scrip_id');
							$Message .= "\n\n Google BSE Code : \n Value : ".$request->input('google_bse_code');
							$Message .= "\n\n NSE Symbol : \n Value : ".$request->input('nse_symbol');
							$Message .= "\n\n Google NSE Code : \n Value : ".$request->input('google_nse_code');
							$Message .= "\n\n Scrip Name : \n Value : ".$request->input('scrip_name');
							$Message .= "\n\n Image URL in S3 : \n Value : ".$request->input('image_url_in_s3');
							$Message .= "\n\n Stock Spellings : \n Value : ".$stock_spellings;
							$Message .= "\n\n Unique Identifier : \n Value : ".$unique_identifier;
							$Message .= "\n\n Google Code for Live Prices : \n Value : ".$request->input('google_code_for_live_prices');
							$Message .= "\n\n Stock Status : \n Value : ".$stock_status;
							$this->aws->send_admin_alerts($this->Alert_SuperAdmin, $Message);
							Session::flash('success_message', $scrip_name . ' stock added successfully.');
							$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
							return redirect($zone_name);
						}else{

							$stock = $this->stock_index_table::select('*')->where(array('id' => $lasId))->first();
							$this->stock_index_table::first()->where(array('id' => $id))->delete();
							Session::flash('error_message', 'Error! Something went wrong.');
							$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
							return redirect($zone_name);
						}
					}else{
						Session::flash('success_message', $scrip_name . ' stock added successfully.');
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
		}
		else
		{
			Session::flash('error_message', 'Error! Something went wrong.');
			$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
			return redirect($zone_name);
		}
    }

    public function generateRandomNumbers($digits){
		return rand(pow(10, $digits-1), pow(10, $digits)-1);
    }

    public function saveStock($data)
    {
    	$this->stock_index_table->bse_scrip_code = $data['bse_scrip_code'];
		$this->stock_index_table->bse_scrip_id = $data['bse_scrip_id'];
		$this->stock_index_table->google_bse_code = $data['google_bse_code'];
		$this->stock_index_table->nse_symbol = $data['nse_symbol'];
		$this->stock_index_table->google_nse_code = $data['google_nse_code'];
		$this->stock_index_table->scrip_name = $data['scrip_name'];
		$this->stock_index_table->scrip_group = $data['scrip_group'];
		//$this->stock_index_table->image_url_in_s3 = $data['image_url_in_s3'];
		$this->stock_index_table->stock_spellings = $data['stock_spellings'];
		$this->stock_index_table->unique_identifier = $data['unique_identifier'];
		$this->stock_index_table->google_code_for_live_prices = $data['google_code_for_live_prices'];
		$this->stock_index_table->is_active = $data['is_active'];
		if($this->stock_index_table->save())
		{
			return $this->stock_index_table->id;
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
			$stock_index_table = $this->stock_index_table::select('*')->where('id', $id)->first();
			Session::flash('edit_stock', TRUE);
			Session::flash('stock_id', $id);
			Session::flash('bse_scrip_code', $stock_index_table->bse_scrip_code);
			Session::flash('bse_scrip_id', $stock_index_table->bse_scrip_id);
			Session::flash('google_bse_code', $stock_index_table->google_bse_code);
			Session::flash('nse_symbol', $stock_index_table->nse_symbol);
			Session::flash('google_nse_code', $stock_index_table->google_nse_code);
			Session::flash('scrip_name', $stock_index_table->scrip_name);
			Session::flash('scrip_group', $stock_index_table->scrip_group);
			Session::flash('image_url_in_s3', $stock_index_table->image_url_in_s3);
			Session::flash('stock_spellings', $stock_index_table->stock_spellings);
			Session::flash('unique_identifier', $stock_index_table->unique_identifier);
			Session::flash('google_code_for_live_prices', $stock_index_table->google_code_for_live_prices);
			Session::flash('stock_status', intval($stock_index_table->is_active));
			return view('Admin.Stocks.addStocks');
		}
		else
		{
			return redirect('/');
		}
	}

	public function editStkName($zone_code, $id)
	{
		if(Session::get('is_admin'))
		{
			$stock_index_table = $this->stock_index_table::select('*')->where('id', $id)->first();
			Session::flash('edit_stock', TRUE);
			Session::flash('stock_id', $id);
			Session::flash('unique_identifier', $stock_index_table->unique_identifier);
			return view('Admin.Stocks.editStockName');
		}
		else
		{
			return redirect('/');
		}
	}

	public function updateStkName($request)
	{
		if($request->session()->get('is_admin'))
		{
			$stock_id = $request->input('stock_id');
			$unique_identifier_new = $request->input('unique_identifier');
			$unique_identifier_old = $request->input('unique_identifier_hidden');
			if(!empty($unique_identifier_new) and !empty($unique_identifier_old))
			{
				if($unique_identifier_new != $unique_identifier_old)
				{
					if ($this->stock_index_table::where('unique_identifier', '=', $unique_identifier_new)->exists())
					{
						Session::flash('error_message', 'Error! ' . $unique_identifier_new . ' unique identifier already exists.');
						$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
						return redirect($zone_name);
					}else{
						$data = array(
							'unique_identifier' => $unique_identifier_new
						);
						$stock_index_table = $this->stock_index_table::first()->where(array('id' => $stock_id));
						$updateNow = $stock_index_table->update($data);
						if($updateNow)
						{
							////////// All Memberzone Section
							for($i = 1; $i <= 9; $i++){
								DB::table('m'.$i.'_active')
						        ->where('m'.$i.'_stock_code', $unique_identifier_old)
						        ->update(array('m'.$i.'_stock_code' => $unique_identifier_new));

							    DB::table('m'.$i.'_future')
							        ->where('m'.$i.'_f_stock_code', $unique_identifier_old)
							        ->update(array('m'.$i.'_f_stock_code' => $unique_identifier_new));

							    DB::table('m'.$i.'_past')
							        ->where('m'.$i.'_p_stock_code', $unique_identifier_old)
							        ->update(array('m'.$i.'_p_stock_code' => $unique_identifier_new));
							}
							
							///////////////// Danger Stocks
						    DB::table('f1_active')
						        ->where('f1_stock_code', $unique_identifier_old)
						        ->update(array('f1_stock_code' => $unique_identifier_new));

						    DB::table('f1_future')
						        ->where('f1_f_stock_code', $unique_identifier_old)
						        ->update(array('f1_f_stock_code' => $unique_identifier_new));

						    ////////////////// IPO Analysis
						    DB::table('f4_future')
						        ->where('f4_f_stock_code', $unique_identifier_old)
						        ->update(array('f4_f_stock_code' => $unique_identifier_new));

						    DB::table('f4_active')
						        ->where('f4_stock_code', $unique_identifier_old)
						        ->update(array('f4_stock_code' => $unique_identifier_new));

						    //////////////// Grey Market Premium
						    DB::table('f5_future')
						        ->where('f5_f_stock_code', $unique_identifier_old)
						        ->update(array('f5_f_stock_code' => $unique_identifier_new));

						    DB::table('f5_active')
						        ->where('f5_stock_code', $unique_identifier_old)
						        ->update(array('f5_stock_code' => $unique_identifier_new));

						    /////////////// Result Analysis
						    DB::table('f9_future')
						        ->where('f9_f_stock_code', $unique_identifier_old)
						        ->update(array('f9_f_stock_code' => $unique_identifier_new));

						    DB::table('f9_active')
						        ->where('f9_stock_code', $unique_identifier_old)
						        ->update(array('f9_stock_code' => $unique_identifier_new));

						    ///////////////// Stock Query Tags
						    $data = array('stock_tags' => DB::raw("replace(stock_tags,'".$unique_identifier_old."','".$unique_identifier_new."')"  ));				    
						  	DB::table('stock_query')
						  		->update($data);

						    Session::flash('success_message', ' Stock Name updated successfully.');
							$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
							return redirect($zone_name);
						}
					}
				}else{
					Session::flash('error_message', 'Error! Old and new stock names are same.');
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
		}else{
			return redirect('/');
		}
	}

	public function update($request)
	{
		if($request->session()->get('is_admin'))
		{
			$stock_id = $request->input('stock_id');
			$scrip_name = $request->input('scrip_name');
			$stock_spellings = $request->input('stock_spellings');
			// $unique_identifier = $request->input('unique_identifier');
			// $unique_identifier_hidden = $request->input('unique_identifier_hidden');
			$stock_status = $request->input('stock_status');
			if((substr($stock_spellings, 0) != ",") && (substr($stock_spellings, -1) != ","))
			{
				return redirect()->back()->with('error_message', 'Error! ' . 'Stock spellings should have comma (,) at the start and end.');
			}
			if(!empty($scrip_name) and !empty($stock_spellings) and isset($stock_status))
			{
				$old_stock_data = $this->stock_index_table::select('*')->where(array('id' => $stock_id))->first();
				$old_data = array(
					'bse_scrip_code' => $old_stock_data->bse_scrip_code,
					'bse_scrip_id' => $old_stock_data->bse_scrip_id,
					'google_bse_code' => $old_stock_data->google_bse_code,
					'nse_symbol' => $old_stock_data->nse_symbol,
					'google_nse_code' => $old_stock_data->google_nse_code,
					'scrip_name' => $old_stock_data->scrip_name,
					'scrip_group' => $old_stock_data->scrip_group,
					'image_url_in_s3' => $old_stock_data->image_url_in_s3,
					'stock_spellings' => $old_stock_data->stock_spellings,
					//'unique_identifier' => $old_stock_data->unique_identifier,
					'google_code_for_live_prices' => $old_stock_data->google_code_for_live_prices,
					'is_active' => $old_stock_data->is_active
				);

				if(!empty($request->image_url_in_s3_edit))
				{

					if(!empty($old_stock_data->image_url_in_s3)){
						$name=explode("/",$old_stock_data->image_url_in_s3); 
			    		$name = $name[count($name) - 1];
			    		$flat_name = explode('.', $name);
				    	$flat_name = $flat_name[0];

					}else{
						$name = $this->generateRandomNumbers(3).$stock_id.$this->generateRandomNumbers(3);						
						$flat_name = $name;
					}

			    	$destinationPath = '/tmp/'; // upload path
				    $extension = $request->image_url_in_s3_edit->getClientOriginalExtension(); // getting image extension
				    $fileName = $name.'.'.$extension; // renameing image
				    $request->image_url_in_s3_edit->move($destinationPath, $fileName);
				    $filepath = $destinationPath.$fileName;

				    $s3_URL = $this->aws->pushPublicFileS3($filepath,"stocks/".$flat_name,$extension);

			    	$data = array(
						'bse_scrip_code' => $request->input('bse_scrip_code'),
						'bse_scrip_id' => $request->input('bse_scrip_id'),
						'google_bse_code' => $request->input('google_bse_code'),
						'nse_symbol' => $request->input('nse_symbol'),
						'google_nse_code' => $request->input('google_nse_code'),
						'scrip_name' => $scrip_name,
						'scrip_group' => $request->input('scrip_group'),
						'image_url_in_s3' => $s3_URL,
						'stock_spellings' => $stock_spellings,
						// 'unique_identifier' => $unique_identifier,
						'google_code_for_live_prices' => $request->input('google_code_for_live_prices'),
						'is_active' => $stock_status
					);
		    	}
		    	elseif(!empty($request->image_url_in_s3))
		    	{
		    		if(!empty($old_stock_data->image_url_in_s3)){
						$name=explode("/",$old_stock_data->image_url_in_s3); 
			    		$name = $name[count($name) - 1];
			    		$flat_name = explode('.', $name);
				    	$flat_name = $flat_name[0];

					}else{
						$name = $this->generateRandomNumbers(3).$stock_id.$this->generateRandomNumbers(3);						
						$flat_name = $name;
					}

			    	$destinationPath = '/tmp/'; // upload path
				    $extension = $request->image_url_in_s3->getClientOriginalExtension(); // getting image extension
				    $fileName = $name.'.'.$extension; // renameing image
				    $request->image_url_in_s3->move($destinationPath, $fileName);
				    $filepath = $destinationPath.$fileName;

				    $s3_URL = $this->aws->pushPublicFileS3($filepath,"stocks/".$flat_name,$extension);

			    	$data = array(
						'bse_scrip_code' => $request->input('bse_scrip_code'),
						'bse_scrip_id' => $request->input('bse_scrip_id'),
						'google_bse_code' => $request->input('google_bse_code'),
						'nse_symbol' => $request->input('nse_symbol'),
						'google_nse_code' => $request->input('google_nse_code'),
						'scrip_name' => $scrip_name,
						'scrip_group' => $request->input('scrip_group'),
						'image_url_in_s3' => $s3_URL,
						'stock_spellings' => $stock_spellings,
						// 'unique_identifier' => $unique_identifier,
						'google_code_for_live_prices' => $request->input('google_code_for_live_prices'),
						'is_active' => $stock_status
					);
		    	}
		    	else
		    	{

			    	$data = array(
						'bse_scrip_code' => $request->input('bse_scrip_code'),
						'bse_scrip_id' => $request->input('bse_scrip_id'),
						'google_bse_code' => $request->input('google_bse_code'),
						'nse_symbol' => $request->input('nse_symbol'),
						'google_nse_code' => $request->input('google_nse_code'),
						'scrip_name' => $scrip_name,
						'scrip_group' => $request->input('scrip_group'),
						'image_url_in_s3' => $request->input('image_url_in_s3'),
						'stock_spellings' => $stock_spellings,
						// 'unique_identifier' => $unique_identifier,
						'google_code_for_live_prices' => $request->input('google_code_for_live_prices'),
						'is_active' => $stock_status
					);
		    	}


				
				$stock_index_table = $this->stock_index_table::first()->where(array('id' => $stock_id));
				$updateNow = $stock_index_table->update($data);
				if($updateNow)
				{
					$Message = "Stock Updated From Admin \n\nStock : " . $scrip_name . " updated successfully.";
					$Message .= " \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
					$Message .= "\nFields Updated : \n BSE Scrip Code : \n Old Value : ".$old_data['bse_scrip_code']." \n New Value : ".$request->input('bse_scrip_code');
					$Message .= "\n \n BSE Scrip ID : \n Old Value : ".$old_data['bse_scrip_id']." \n New Value : ".$request->input('bse_scrip_id');
					$Message .= "\n\n Google BSE Code : \n Old Value : ".$old_data['google_bse_code']." \n New Value : ".$request->input('google_bse_code');
					$Message .= "\n\n NSE Symbol : \n Old Value : ".$old_data['nse_symbol']." \n New Value : ".$request->input('nse_symbol');
					$Message .= "\n\n Google NSE Code : \n Old Value : ".$old_data['google_nse_code']." \n New Value : ".$request->input('google_nse_code');
					$Message .= "\n\n Scrip Name : \n Old Value : ".$old_data['scrip_name']." \n New Value : ".$request->input('scrip_name');
					$Message .= "\n\n Image URL in S3 : \n Old Value : ".$old_data['image_url_in_s3']." \n New Value : ".$request->input('image_url_in_s3');
					$Message .= "\n\n Stock Spellings : \n Old Value : ".$old_data['stock_spellings']." \n New Value : ".$stock_spellings;
					//$Message .= "\n\n Unique Identifier : \n Old Value : ".$old_data['unique_identifier']." \n New Value : ".$unique_identifier;
					$Message .= "\n\n Google Code for Live Prices : \n Old Value : ".$old_data['google_code_for_live_prices']." \n New Value : ".$request->input('google_code_for_live_prices');
					$Message .= "\n\n Stock Status : \n Old Value : ".$old_data['is_active']." \n New Value : ".$stock_status;
					$this->aws->send_admin_alerts($this->Alert_SuperAdmin, $Message);
					Session::flash('success_message', $scrip_name . ' stock updated successfully.');
					$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
					return redirect($zone_name);
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

	public function delete($zone_code, $id)
	{
		if(Session::get('is_admin'))
		{
			$stock = $this->stock_index_table::select('*')->where(array('id' => $id))->first();
			if($this->stock_index_table::first()->where(array('id' => $id))->delete())
			{
				$Message = "Stock Deleted From Admin \n\nStock : " . $stock->scrip_name . " deleted successfully.";
				$Message .= " \n IP Address :".$this->aws->getClientIps()."\n Time of Event :".Carbon::now();
				$Message .= "\nFields Deleted : \n BSE Scrip Code : \n Value : ".$stock->bse_scrip_code;
				$Message .= "\n \n BSE Scrip ID : \n Value : ".$stock->bse_scrip_id;
				$Message .= "\n\n Google BSE Code : \n Value : ".$stock->google_bse_code;
				$Message .= "\n\n NSE Symbol : \n Value : ".$stock->nse_symbol;
				$Message .= "\n\n Google NSE Code : \n Value : ".$stock->google_nse_code;
				$Message .= "\n\n Scrip Name : \n Value : ".$stock->scrip_name;
				$Message .= "\n\n Image URL in S3 : \n Value : ".$stock->image_url_in_s3;
				$Message .= "\n\n Stock Spellings : \n Value : ".$stock->stock_spellings;
				$Message .= "\n\n Unique Identifier : \n Value : ".$stock->unique_identifier;
				$Message .= "\n\n Google Code for Live Prices : \n Value : ".$stock->google_code_for_live_prices;
				$Message .= "\n\n Stock Status : \n Value : ".$stock->is_active;
				$this->aws->send_admin_alerts($this->Alert_SuperAdmin, $Message);
				Session::flash('success_message', $stock->scrip_name . ' stock deleted successfully.');
				$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
				return redirect($zone_name);
			}
			else
			{
				Session::flash('error_message', $stock->scrip_name . ' stock was not deleted.');
				$zone_name = Session::has('zone_name') ? "/Admin/" . Session::get('zone_name') : "/Home";
				return redirect($zone_name);
			}
		}
		else
		{
			return redirect('/');
		}
	}

	public function getStockData()
	{
		if(Session::get('is_admin'))
		{
			$stock_index_table = $this->stock_index_table::select('*')->get();
			$zone_name = Session::has('zone_name') ? Session::get('zone_name') : NULL;
			foreach($stock_index_table as $stock)
			{
				$stock->edit = "<a class='edit' href='/Admin/" . $zone_name . "/edit/" . $stock['id'] . "'> Edit </a>";
				$stock->editname = "<a class='edit' href='/Admin/" . $zone_name . "/editStkName/" . $stock['id'] . "'> Edit Name </a>";
				$delete = "<a data-toggle='modal' href='#deleteZone" . $stock['id'] . "'>Delete</a>";
				$delete .= "<div class='modal fade' id='deleteZone" . $stock['id'] . "' tabindex='-1' role='basic' aria-hidden='true' style='display: none;'>";
				$delete .= "<div class='modal-dialog'>";
				$delete .= "<div class='modal-content'>";
				$delete .= "<div class='modal-header'>";
				$delete .= "<button type='button' class='close' data-dismiss='modal' aria-hidden='true'></button>";
				$delete .= "<h4 class='modal-title'>Delete Stock</h4>";
				$delete .= "</div>";
				$delete .= "<div class='modal-body'> Do you want delete " . $stock['scrip_name'] . "? </div>";
				$delete .= "<div class='modal-footer'>";
				$delete .= " <button type='button' class='btn dark btn-outline' data-dismiss='modal'>Close</button>";
				$delete .= "<button type='button' class='btn green' onclick=\"window.location.href='" . $zone_name . "/delete/" . $stock['id'] . "'\">Yes</button>";
				$delete .= "</div>";
				$delete .= "</div>";
				$delete .= "</div>";
				$delete .= "</div>";
				$stock->delete = $delete;
				$stock['is_active'] = ($stock['is_active'] == 1)? 'Active' : 'inactive';
			}
			$stockData = '{ "data":'.json_encode($stock_index_table). '}';
			return $stockData;
		}
		else
		{
			return redirect('/');
		}
	}
}