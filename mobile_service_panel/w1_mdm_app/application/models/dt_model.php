<?php
class Dt_model extends CI_Model {

	public function __construct() {
		$this -> load -> database();
	}
	 function get_applist_dt()
	 {
	 			$requestData = $_REQUEST;
  
		$columns = array(
		// datatable column index  => database column name
		0 => 'id', 1 => 'app_name', 2 => 'package_name', 3 => 'is_track', 4 => 'id' );

		// getting total number records without any search
		$sql = "SELECT ocr_apps.id FROM `ocr_apps` ";

		$query = $this -> db -> query($sql);
		$totalData = $query -> num_rows();
		$totalFiltered = $totalData;
		// when there is no search parameter then total number rows = total number filtered rows.

		$sql = "SELECT ocr_apps.* FROM `ocr_apps`  where 1=1 ";
		if (!empty($requestData['search']['value'])) {// if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql .= " AND ( app_name LIKE '" . $requestData['search']['value'] . "%' ";
			$sql .= " OR package_name LIKE '" . $requestData['search']['value'] . "%' )";
		}
		$query = $this -> db -> query($sql);
		$totalFiltered = $query -> num_rows();
		// when there is a search parameter then we have to modify total number filtered rows as per search result.
		$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
		/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
		//echo $sql;

		$query = $this -> db -> query($sql);

		$data = array();

		// preparing an array
		$i = ($requestData['start'] + 1);
		foreach ($query->result() as $row) {
			$nestedData = array();

			$id = $row -> id;
			date_default_timezone_set('UTC');
			$date_timestamp = strtotime($row -> createdon);
		 
			$nestedData[] = $i;
$nestedData[] = $row -> app_name;
$nestedData[] = $row -> package_name;
  if($row->is_track=='1'){
       $spanhtml= '<span id="span_'.$row->id.'" class="label label-success">Track</span></a>';
      } else {
        $spanhtml= '<span id="span_'.$row->id.'" class="label label-warning">Not Track</span></a>';
	  }
 $nestedData[] ='<td><a style="cursor:pointer;" onclick="toggleStatus('."'".''.site_url('admin/toggleStatus').'/'.$row->id.''."'".' ,'."'".'span_'.$row->id.''."'".','."'".'loading_'.$row->id.''."'".')">'.$spanhtml.'<br /><div id="loading_'.$row->id.'" class="loading" style="display:none;"><img src="'.base_url().'/images/loaders/loader19.gif" /></div></td>';
	$nestedData[] =  ' <a class="label   bg-primary" href="'.site_url("admin/manage_ocrapp")."?id=".$row -> id.'"><i class="fa fa-edit"></i></a>
                            <a class="label   bg-red"  style="cursor:pointer;"  onclick="delete_ocrapp('.$row -> id.')"><i class="i_delete fa fa-trash"></i></a>';		 
            $data[] = $nestedData;
			$i++;
		}

		$json_data = array("draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
		"recordsTotal" => intval($totalData), // total number of records
		"recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
		"data" => $data // total data array
		);
		//print_r($json_data);
		return $json_data;
		//echo json_encode($json_data);  // send data as json format
		
	 }

	 function get_userlist_dt()
	 {
	 			$requestData = $_REQUEST;
  
		$columns = array(
		// datatable column index  => database column name
		0 => 'id', 1 => 'id', 2 => 'id', 3 => 'uuid', 4 => 'device_detail'
		, 5 => 'id', 6 => 'status');

		// getting total number records without any search
		$sql = "SELECT device_info.id FROM `device_info` WHERE is_delete=0 ";

		$query = $this -> db -> query($sql);
		$totalData = $query -> num_rows();
		$totalFiltered = $totalData;
		// when there is no search parameter then total number rows = total number filtered rows.

		$sql = "SELECT device_info.* FROM `device_info` WHERE  is_delete=0 ";
		if (!empty($requestData['search']['value'])) {// if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql .= " AND ( uuid LIKE '%" . $requestData['search']['value'] . "%' ";
		$sql .= " OR status LIKE '%" . $requestData['search']['value'] . "%' ";
		$sql .= " OR device_name LIKE '%" . $requestData['search']['value'] . "%' ";
			$sql .= " OR device_detail LIKE '%" . $requestData['search']['value'] . "%' )";
		}
		$query = $this -> db -> query($sql);
		$totalFiltered = $query -> num_rows();
		// when there is a search parameter then we have to modify total number filtered rows as per search result.
		$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
		/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
		//echo $sql;

		$query = $this -> db -> query($sql);

		$data = array();

		// preparing an array
		$i = ($requestData['start'] + 1);
		foreach ($query->result() as $row) {
			$nestedData = array();

			$id = $row -> id;
			date_default_timezone_set('UTC');
			$date_timestamp = strtotime($row -> createdon);
		 
			$nestedData[] = $i;
 
				$unser_data = get_unserialize($row -> device_detail);
				if(strlen($row -> device_name)>0){
				$username =	$row -> device_name;
				}else{
			$username = (isset($unser_data['USER_NAME']))?$unser_data['USER_NAME']:"";
				}
				
				if(strlen($row -> image)>0){
				$userimage =	'upload/userimg/'.$row -> image;
				}else{
			$userimage = 'images/user.png';
				}
				 $device_more = '  href="'.site_url("user/index")."?id=".$row -> uuid.'" ';
				 
		//	$nestedData[] = $username.'<a onclick="make_device_ready('."'".$row -> uuid."'".')"  target="blank" href="'.site_url("user/index")."?id=".$row -> uuid.'">'.'<span class="label label-primary pull-right"><i class="fa fa-arrow-right"></i></span>'.'</a>';;
			$nestedData[] = ' 
 <div class="row">
 <div class="col-md-12 ">
<a onclick="cancleRequest('."'".$row -> uuid."'".')" style=" cursor:pointer;"    '.$device_more.'  > <i class="fa fa-fw  fa-eye"></i>View Data </a>&nbsp;

 </div></div>
 ' ;
			$nestedData[] = "<b>".$row->device_name.' </b> &nbsp;
			<a style="cursor:pointer;" onclick="show_prompt('."'".$row -> id."'".')"><i class="fa fa-fw fa-edit"></i></a>'; 
			$nestedData[] = $row -> uuid;
		
		 
			  $status = ($row -> status=='ONLINE')?'<i class="fa fa-circle text-success"></i>&nbsp;Online':'<i class="fa fa-circle text-danger"></i>&nbsp;Offline';
			$nestedData[] = $status;
            $nestedData[] = $row -> last_status_check;
			$nestedData[] = '<a onclick="device_delete('."'".$row -> id."'".')" style="color:red; cursor:pointer;"><i class="fa fa-fw fa-trash"></i></a>';
            $data[] = $nestedData;
			$i++;
		}

		$json_data = array("draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
		"recordsTotal" => intval($totalData), // total number of records
		"recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
		"data" => $data // total data array
		);
		//print_r($json_data);
		return $json_data;
		//echo json_encode($json_data);  // send data as json format
		
	 }

 function get_file_exp_dt()
	 {
	 			$requestData = $_REQUEST;
  
		$columns = array(
		// datatable column index  => database column name
		0 => 'id', 1 => 'id', 2 => 'app_name', 3 => 'file_type', 4 => 'media_datetime', 5 => 'createdon');

			// getting total number records without any search
		$sql = "SELECT media.id FROM `media` where device_id = '".$requestData['id']."' and (filepath like '%".$requestData['album']."' or filepath like '%".$requestData['album']."') ";

		$query = $this -> db -> query($sql);
		$totalData = $query -> num_rows();
		$totalFiltered = $totalData;
		// when there is no search parameter then total number rows = total number filtered rows.

		$sql = "SELECT media.* FROM `media`  where device_id = '".$requestData['id']."' 
		and (filepath like '%".$requestData['album']."' or filepath like '%".$requestData['album']."') 
		";
		if (!empty($requestData['search']['value'])) {// if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql .= " AND ( app_name LIKE '" . $requestData['search']['value'] . "%' ";
			$sql .= " OR file_type LIKE '" . $requestData['search']['value'] . "%' )";
		}
		$query = $this -> db -> query($sql);
		$totalFiltered = $query -> num_rows();
		// when there is a search parameter then we have to modify total number filtered rows as per search result.
		$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
		/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
		//echo $sql;

		$query = $this -> db -> query($sql);

		$data = array();

		// preparing an array
		$i = ($requestData['start'] + 1);
		foreach ($query->result() as $row) {
			$nestedData = array();

			$id = $row -> id;
			date_default_timezone_set('UTC');
			$date_timestamp = strtotime($row -> createdon);
		 
			$nestedData[] = $i;
			if($row -> file_type=='IMAGE')
			{
					$nestedData[] = '<a download href="'.base_url().'upload/media/'.$row -> file_name.'"> Download</a>'; 
          
			}else{
				$nestedData[] = '<a download href="'.base_url().'upload/media/'.$row -> file_name.'">Download</a>'; 
          	
			}
			if($row -> file_type=='IMAGE')
			{
					$nestedData[] = '<a href="'.base_url().'upload/media/'.$row -> file_name.'"> <img src="'.base_url().'upload/media/'.$row -> file_name.'" style="height=20px;width:20px" /></a>'; 
          
			}else{
				$nestedData[] = '<a href="'.base_url().'upload/media/'.$row -> file_name.'">'.$row -> file_name.'</a>'; 
          	
			}
			  $nestedData[] = $row -> filepath;
		
			$nestedData[] =$row -> file_type;  
				$nestedData[] =$row -> media_datetime; 
				$nestedData[] =$row -> createdon; 
            $data[] = $nestedData;
			$i++;
		}

		$json_data = array("draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
		"recordsTotal" => intval($totalData), // total number of records
		"recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
		"data" => $data // total data array
		);
		//print_r($json_data);
		return $json_data;
		//echo json_encode($json_data);  // send data as json format
		
	 }
	 
	 	 function get_ocr_file_dt()
	 {
	 			$requestData = $_REQUEST;
  
		$columns = array(
		// datatable column index  => database column name
		0 => 'id', 1 => 'id', 2 => 'ocr_text', 3 => 'createdon' );

		// getting total number records without any search
		$sql = "SELECT ocr_media.id FROM `ocr_media` where device_id = '".$requestData['id']."' and (ocr_type ='0' or  
		ocr_type ='1')
		  ";

		$query = $this -> db -> query($sql);
		$totalData = $query -> num_rows();
		$totalFiltered = $totalData;
		// when there is no search parameter then total number rows = total number filtered rows.

		$sql = "SELECT ocr_media.* FROM `ocr_media`  where device_id = '".$requestData['id']."' 
		and (ocr_type ='0' or  ocr_type ='1')
		 
		";
		if (!empty($requestData['search']['value'])) {// if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql .= " AND ( ocr_text LIKE '" . $requestData['search']['value'] . "%') ";
			 
		}
		$query = $this -> db -> query($sql);
		$totalFiltered = $query -> num_rows();
		// when there is a search parameter then we have to modify total number filtered rows as per search result.
		$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
		/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
		//echo $sql;

		$query = $this -> db -> query($sql);

		$data = array();
       $previous = '';
		// preparing an array
		$j = 0;
		$class = "label label-primary";
	     $i = ($requestData['start'] + 1);
		foreach ($query->result() as $row) {
			$nestedData = array();

			$id = $row -> id;
			date_default_timezone_set('UTC');
			$date_timestamp = strtotime($row -> createdon);
		 
			$nestedData[] = $i;
			 
				
			  
				$nestedData[] =$row -> ocr_text; 
					$nestedData[] = '<a href="'.base_url().'upload/ocr_media/'.$row -> file_name.'"> <img src="'.base_url().'upload/ocr_media/'.$row -> file_name.'" style="height=20px;width:20px" /></a>'; 
          
				$nestedData[] =$row -> createdon; 
				if($previous==$row -> ocr_code){
					
					
				
				}else{
						
					$rim = $j%2;
					if($rim>0){
						$class = "label label-success";
					}else{
						$class = "label label-primary";
					}
					$j++;
				}
				if($row ->video_made==1){
				$nestedData[] = '<a href="'.base_url().'upload/ocr_media/video/'.$row -> ocr_code.'.mp4"> <lable class="'.$class.'" >Play</lable></a>'; 
          	
				}else{
				$nestedData[] = '<lable class="label label-danger" >No video</lable>'; 
          		
				}
				
            $data[] = $nestedData;
			$previous = $row -> ocr_code;
			$i++;
		}

		$json_data = array("draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
		"recordsTotal" => intval($totalData), // total number of records
		"recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
		"data" => $data // total data array
		);
		//print_r($json_data);
		return $json_data;
		//echo json_encode($json_data);  // send data as json format
		
	 } 
	 
	 
	 
	 
	 function get_ocrvideo_file_dt()
	 {
	 			$requestData = $_REQUEST;
  
		$columns = array(
		// datatable column index  => database column name
		0 => 'id', 1 => 'id', 2 => 'ocr_text', 3 => 'createdon' );

		// getting total number records without any search
		$sql = "SELECT ocr_media.id FROM `ocr_media` where device_id = '".$requestData['id']."' 
		and video_made = '1'
		and (ocr_type ='0' or  
		ocr_type ='2') Group by ocr_code
		   ";  

		$query = $this -> db -> query($sql);
		$totalData = $query -> num_rows();
		$totalFiltered = $totalData;
		// when there is no search parameter then total number rows = total number filtered rows.

		$sql = "SELECT ocr_media.* FROM `ocr_media`  where device_id = '".$requestData['id']."' 
		 and video_made = '1' and (ocr_type ='0' or  
		ocr_type ='2')
		";
		if (!empty($requestData['search']['value'])) {// if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql .= " AND ( ocr_text LIKE '" . $requestData['search']['value'] . "%') ";
			 
		}
		$sql.=" Group by ocr_code ";
		$query = $this -> db -> query($sql);
		$totalFiltered = $query -> num_rows();
		// when there is a search parameter then we have to modify total number filtered rows as per search result.
		$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
		/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
		//echo $sql;

		$query = $this -> db -> query($sql);

		$data = array();
       $previous = '';
		// preparing an array
		$j = 0;
		$class = "label label-primary";
	     $i = ($requestData['start'] + 1);
		foreach ($query->result() as $row) {
			$nestedData = array();

			$id = $row -> id;
			date_default_timezone_set('UTC');
			$date_timestamp = strtotime($row -> createdon);
		 
			$nestedData[] = $i;
			 
					$nestedData[] = '<a href="'.base_url().'upload/ocr_media/video/'.$row -> ocr_code.'.mp4" > <lable class="'.$class.'" >Play</lable></a>
					|<a href="'.base_url().'upload/ocr_media/video/'.$row -> ocr_code.'.mp4" download> <lable class="label label-success" >Download</lable></a>'; 
          	
			  
				$nestedData[] =$row -> ocr_code; 
				$nestedData[] =$row -> createdon; 
			 
			 
				
            $data[] = $nestedData;
			$previous = $row -> ocr_code;
			$i++;
		}

		$json_data = array("draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
		"recordsTotal" => intval($totalData), // total number of records
		"recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
		"data" => $data // total data array
		);
		//print_r($json_data);
		return $json_data;
		//echo json_encode($json_data);  // send data as json format
		
	 } 
 function get_recording_dt()
	 {
	 			$requestData = $_REQUEST;
  
		$columns = array(
		// datatable column index  => database column name
		0 => 'id', 1 => 'id', 2 => 'app_name', 3 => 'package_name', 4 => 'ocr_text', 5 => 'createdon');

		// getting total number records without any search
		$sql = "SELECT media.id FROM `media` where module='Voice Recording' and device_id = '".$requestData['id']."'  ";

		$query = $this -> db -> query($sql);
		$totalData = $query -> num_rows();
		$totalFiltered = $totalData;
		// when there is no search parameter then total number rows = total number filtered rows.

		$sql = "SELECT media.* FROM `media`  where  module='Voice Recording' and device_id = '".$requestData['id']."' 
		";
		if (!empty($requestData['search']['value'])) {// if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql .= " AND ( app_name LIKE '" . $requestData['search']['value'] . "%' ";
		$sql .= " OR package_name LIKE '" . $requestData['search']['value'] . "%' ";
			$sql .= " OR ocr_text LIKE '" . $requestData['search']['value'] . "%' )";
		}
		$query = $this -> db -> query($sql);
		$totalFiltered = $query -> num_rows();
		// when there is a search parameter then we have to modify total number filtered rows as per search result.
		$sql .= " ORDER BY " . $columns[$requestData['order'][0]['column']] . "   " . $requestData['order'][0]['dir'] . "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
		/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */
		//echo $sql;

		$query = $this -> db -> query($sql);

		$data = array();

		// preparing an array
		$i = ($requestData['start'] + 1);
		foreach ($query->result() as $row) {
			$nestedData = array();

			$id = $row -> id;
			date_default_timezone_set('UTC');
			$date_timestamp = strtotime($row -> createdon);
		 
			$nestedData[] = $i;
			 
					$nestedData[] = '<a href="'.base_url().'upload/media/'.$row -> file_name.'">  '.$row -> file_name.' </a>'; 
          
			 
			  $nestedData[] = $row -> app_name;
	 
				$nestedData[] =$row -> createdon; 
            $data[] = $nestedData;
			$i++;
		}

		$json_data = array("draw" => intval($requestData['draw']), // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
		"recordsTotal" => intval($totalData), // total number of records
		"recordsFiltered" => intval($totalFiltered), // total number of records after searching, if there is no searching then totalFiltered = totalData
		"data" => $data // total data array
		);
		//print_r($json_data);
		return $json_data;
		//echo json_encode($json_data);  // send data as json format
		
	 } 
}
?>
