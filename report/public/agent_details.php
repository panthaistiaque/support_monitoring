<?php
require_once('config.php');
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

if(isset($_POST['id'])){
	$id = $_POST['id'];
	$sql = "SELECT uu.id, CONCAT(uu.first_name,' ', uu.last_name) name, uui.designation,  uui.profile_image_path, usr.description 
			FROM uv_user uu 
			LEFT JOIN uv_user_instance uui on uui.user_id = uu.id
			LEFT JOIN uv_support_role usr on usr.id = uui.supportRole_id
			WHERE usr.code NOT IN ('ROLE_CUSTOMER') AND uui.is_active = 1 and uu.id = $id 
			ORDER BY CONCAT(uu.first_name,' ', uu.last_name) ASC";
	$result = $mysqli->query($sql);
	$data = array();
	
	while ($row = $result->fetch_assoc()) {
		$data[] = $row;
	}
	
	echo json_encode($data);
}

?>