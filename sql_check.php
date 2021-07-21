<?php
include_once('connection.php'); //Connect Databases


$cid_staft 	= $_POST['cid_staft'];
$token 		= $_POST['token'];
$cid_person = $_POST['cid_person'];
$return_arr = array();
$person 	= array();
$return_arr['count'] = 0;

foreach (explode("\n", $cid_person) as $line) {

	$idcard = intval($line);

	if (is_numeric($idcard) AND strlen($idcard) == 13) {

		$data = soap_sorporsorchor($cid_staft,$token,intval($line));

		$status 	= isset($data['return']['status']) ? $data['return']['status'] : ''; // เก็บค่าตัวแปรสถานะว่าคนตายหรือเปล่า
		$ex_token 	= isset($data['return']['ws_status_desc']) ? $data['return']['ws_status_desc'] : ''; // เก็บค่าตัวแปรสถานะ token ว่าหมดอายุหรือเปล่า

		// ตรวจสอบกรณี Token หมดอายุ
		if ($ex_token != 'ok') {
			$return_arr['status'] = false;
			$return_arr['st_desc'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
			  <strong>ไม่สามารถตรวจสอบได้!</strong> กรุณาตรวจสอบเลขประจำตัวประชาชนเจ้าหน้าที่หรือรหัส Token.
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			    <span aria-hidden="true">&times;</span>
			  </button>
			</div>';
		}

		if ($status != '') {

			if ($data['return']['status'] == '003') { // 003 หมายถึงสถานะคนตาย

				$fname 	= $data['return']['fname'];
				$lname 	= $data['return']['lname'];
				$cid 	= $data['return']['person_id'];

				$person[] = array(
					'cid'	=> $cid,
					'fname' => $fname,
					'lname' => $lname,
					'date_check' => DATE('Y-m-d H:i:s')
				);

				$data_sql = [$cid,$fname,$lname,$cid_staft];
				$sql = "INSERT INTO api.checkdeath (idc,fname,lname,idc_admin,date_updated) VALUES(?,?,?,?,CURRENT_TIMESTAMP)";
				$stmt 	= $dbcon->prepare($sql);
				$stmt->execute($data_sql);

				$return_arr['count']++;

			}
			else{
				continue;
			}
		}
		else{
			continue;
		}

	}
	else{
		continue;
	} // End if check number 

} // End Loop

$return_arr['load_file'] = '<a href="export.php?person='.urlencode(serialize($person)).'" class="btn btn-block btn-warning btn-lg"><i class="fa fa-download" aria-hidden="true"></i> ดาวน์โหลดรายชื่อคนตายจำนวน '.$return_arr['count'].' คน</a>';

echo json_encode($return_arr);


function soap_sorporsorchor($cid_staft,$token,$cid_person){
	// global $dbcon;
try{

	$soapclient = new SoapClient('http://ucws.nhso.go.th:80/ucwstokenp1/UCWSTokenP1?wsdl');

	$param = array(
		'user_person_id' => $cid_staft,
		'smctoken' => $token,
		'person_id' => $cid_person
	);

	$response =$soapclient->searchCurrentByPID($param);

	$array = json_decode(json_encode($response),true);


	///////////////////////////////

	return $array;

	unset($array);


}catch(Exception $e){
	echo $e->getMessage();
}
} // End Function soap_sorporsorchor
?>