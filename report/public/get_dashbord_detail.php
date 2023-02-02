<?php
require_once('config.php');
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

if(isset($_POST['type'])){
	$type = $_POST['type'];
	$fdate = $_POST['fdate'];
	$tdate = $_POST['tdate'];
	$team = $_POST['team']; 
	$sql = " select ut.id, ut.subject, utt.code as type,uts.code as status,ust.name as team,CONCAT(uu2.first_name,' ' ,uu2.last_name) as customer,CONCAT(uu.first_name,' ' ,uu.last_name) as agent, case when uts.code in ('closed','resolved','answered' ) then 1 else 0 end as resolved , case when uts.code not in ('closed','resolved','answered' ) then 1 else 0 end as in_hand ".
			" ,COALESCE(th.reply,0) as reply, CONCAT(DATE_FORMAT(CONVERT_TZ(ut.created_at,'+00:00','+6:00') , '%d %b %Y'),' ',TIME_FORMAT(CONVERT_TZ(ut.created_at,'+00:00','+6:00'), '%h:%i %p'))  created_at  ".
			" ,DATE_FORMAT(CONVERT_TZ(ut.created_at,'+00:00','+6:00') , '%d %b %Y') date ".
			" from uv_ticket ut ".
			" left join uv_ticket_type utt on utt.id = ut.type_id ".
			" left join uv_ticket_status uts on uts.id = ut.status_id ".
			" left join uv_support_team ust on ust.id = ut.subGroup_id ".
			" left join uv_user uu on uu.id = ut.agent_id ".
			" left join uv_user uu2 on uu2.id = ut.customer_id ".
			" left join (SELECT ticket_id, count(*) reply FROM uv_thread where thread_type = 'reply' group by ticket_id) th on th.ticket_id = ut.id  ";
			if($type=='long_pending'){
				$sql = $sql." where date(CONVERT_TZ(ut.created_at,'+00:00','+6:00')) BETWEEN '2022-11-01' and date(now()) - INTERVAL 30 DAY "
				." and uts.code in ('open','pending')  ";
			}else{ 
				$sql = $sql." where date(CONVERT_TZ(ut.created_at,'+00:00','+6:00')) BETWEEN '$fdate' and '$tdate' ";
			}
			if ($team!='All') {
				$sql = $sql." and ust.id =  $team  ";
			}
			if ($type=='completed') {
				$sql = $sql." and uts.code in ('closed','resolved','answered')  ";
			}else if($type=='open'){
				$sql = $sql." and uts.code in ('open')  ";
			}else if($type=='spam'){
				$sql = $sql." and uts.code in ('spam')  ";
			}else if($type=='progress'){
				$sql = $sql." and uts.code in ('pending')  ";
			} 
			
			$sql = $sql." order by created_at";
			
	$result = $mysqli->query($sql);
	$data = array();
	
	while ($row = $result->fetch_assoc()) {
		$data[] = $row;
	}
	
	echo json_encode($data);
}


?>
