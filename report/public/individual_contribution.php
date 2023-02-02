<?php
require_once('config/config.php');
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$totalTicket = 0;
$totalTicketInHand = 0;
$totalResolved = 0;
$totalReply = 0;
$fdate = date("Y-m-d");
$tdate = date("Y-m-d");
$team = null;
$agent = null;
$defultPic  = "/bundles/uvdeskcoreframework/images/uv-avatar-batman.png";
$defultSystyemUrl  ="https://helpdesk.apps.friendship.ngo/infosyshd/public";
?>
<!-- HTML code to display data in tabular format -->
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Help Desk Report</title>
	<link rel="icon" type="image/x-icon" sizes="16x16 32x32 48x48" href="favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1">
  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
  
	<script src="//cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
	<script src="https://unpkg.com/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
	
	<script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
	<!-- Bootstrap Date-Picker Plugin -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
	<!-- CSS FOR STYLING THE PAGE -->
	<style>
		table {
			margin: 0 auto; 
			border: 1px solid black;
		} 
		th{
			font-weight: bold; 
			padding: 10px;
		} 
		.dash-card{
			width:32.5%;
			margin-left: 5px;
		}
		 .fa{
			text-shadow: 0px 0px 10px black;
			color: white;
		 }
	</style>
</head>

<body>
<div class="container-fluid mt-3">
	<div class="container-fluid">
		<div class="row">
		  <div class="col-md-2">
			<?php	include 'menu.php';?>
		  </div>
		  <div class="col-md-10"> 
		  <form   action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			  <div  class="col-md-12 row" > 
				  <div class="col-auto col-md-2">
				    <div class="input-group">
						<input class="form-control input-group date" type="text" readonly  id="fdate"  name="fdate">
					</div>
				  </div>
				  <div class="col-auto col-md-2">
					<div class="input-group">
						<input type="text" class="form-control input-group date" readonly id="tdate" name="tdate">
					</div>
				  </div>
				  <!--<div class="col-auto col-md-3">
					<div class="input-group">
						<span class="input-group-text">Team</span>
						<select class="form-control" id="team" name="team">
							<option value='All'>All</option>
						<?php
							$sqlForTeam = 'select id, name from uv_support_team ust where is_active=1 order by name asc';
							$resultForTeam = $mysqli->query($sqlForTeam); 
							
							while($rowsForTeam=$resultForTeam->fetch_assoc())
							{
						?>		
						 <option value="<?php echo $rowsForTeam['id'];?>"><?php echo $rowsForTeam['name'];?></option>
						<?php
							}
						?>	
						</select>
					</div>
				  </div>-->
				  <div class="col-auto col-md-4">
					<div class="input-group">
						<span class="input-group-text">Agent</span>
						<select class="form-control" id="agent" name="agent"> 
						<?php
							$sqlForAgentList = " select uu.id, CONCAT(uu.first_name,' ', uu.last_name) name from uv_user uu ".
										  " left join uv_user_instance uui on uui.user_id = uu.id ".
										  " left join uv_support_role usr on usr.id = uui.supportRole_id ".
										  " where usr.code not in ('ROLE_CUSTOMER') and uui.is_active = 1 ".
										  " order by CONCAT(uu.first_name,' ', uu.last_name) ASC ";
							$resultForAgentList = $mysqli->query($sqlForAgentList); 
							
							while($rowsForAgent=$resultForAgentList->fetch_assoc())
							{
						?>		
						 <option value="<?php echo $rowsForAgent['id'];?>"><?php echo $rowsForAgent['name'];?></option>
						<?php
							}
						?>	
						</select>
					</div>
				  </div>
				  <div class="col-auto col-md-1">
					  <button type="submit" name="viewReport" class="btn btn-primary mb-3">View</button> 
				  </div>
			  </div>
			  <?php
				if(isset($_POST['agent'])){
					$agent = $_POST['agent'];
					$sql = "SELECT uu.id agent_id, ust.id team_id, CONCAT(uu.first_name,' ', uu.last_name) name, uui.designation,  uui.profile_image_path, usr.description role , ust.name team  
							,date(uui.created_at) agent_sience, date(uui.updated_at) profile_update, uui.contact_number
							FROM uv_user uu 
							LEFT JOIN uv_user_instance uui on uui.user_id = uu.id
							LEFT JOIN uv_support_role usr on usr.id = uui.supportRole_id
							left join uv_user_support_teams uust on uust.userInstanceId = uui.id
							left join uv_support_team ust on ust.id = uust.supportTeamId
							WHERE usr.code NOT IN ('ROLE_CUSTOMER') AND uui.is_active = 1 and uu.id = $agent 
							ORDER BY CONCAT(uu.first_name,' ', uu.last_name) ASC";
					$result = $mysqli->query($sql);
					$row = $result->fetch_assoc();
					if($row['profile_image_path']!=''){
					$defultPic = $row['profile_image_path'];
					}
					$fdate = $row['agent_sience'];
					$team = $row['team_id'];
					$agent = $row['agent_id'];
					
					if($team != null && $agent != null){
						
						$sql2 = " select count(*) team_total, 
								sum(case when ut.agent_id = $agent then 1 else 0 end) agent_total, 
								sum(case when (ut.agent_id = $agent and ut.status_id in (3,4,5)) then 1 else 0 end) agent_complete_total, 
								sum(case when (ut.agent_id = $agent and ut.status_id in (1,2)) then 1 else 0 end) agent_incomplete_total  
								from uv_ticket ut
								where ut.subGroup_id = $team
								and date(CONVERT_TZ(created_at,'+00:00','+6:00'))  >= '$fdate' ";
						$result2 = $mysqli->query($sql2);
						$row2 = $result2->fetch_assoc();
;					}
				}
			  ?> 
			  <div class="col-md-12 row" > 
				<div class="col-md-3" > 
					<div class="card text-center" >
					  <img class="card-img-top img-fluid" style="width:220px; height:220px; padding: 5px; border-radius: 10px" src="<?php echo $defultSystyemUrl.$defultPic ?>" alt="Card image">
					  <div class="card-body">
						<h5 class="card-title"><?php echo $row['name']; ?></h5>
						<p class="card-text"><?php echo $row['team']; ?></p>
					  </div>
					</div>
				</div>	
				<div class="col-md-9" >
					<div class="row">
						<div class="card dash-card ">
							<div class="card-body">
								<div class="card-title row">
									<div class="col-sm-4" ><i class="fa fa-users fa-3x" ></i></div>
									<div class="col-sm-8"><center style="color:#767676" class="h1"><?php echo $row2['team_total']; ?></center></div>
								</div>
								<center class="card-text ">Total Team Request</center> 
							</div>
						</div>
						<div class="card dash-card">
							<div class="card-body">
								<div class="card-title row">
									<div class="col-sm-4" ><i class="fa fa-tasks  fa-3x"></i></div>
									<div class="col-sm-8"><center style="color:#767676" class="h1"><?php echo $row2['agent_total']; ?></center></div>
								</div>
								<center class="card-text ">Individual Total Requests</center> 
							</div>
						</div>
						<div class="card dash-card">
							<div class="card-body">
								<div class="card-title row">
									<div class="col-sm-4" ><i class="fa fa-percent fa-3x" ></i></div>
									<div class="col-sm-8"><center style="color:#767676" class="h1"><?php echo $row2['team_total']==0?0:round((($row2['agent_total']/$row2['team_total']) * 100),2); ?></center></div>
								</div>
								<center class="card-text ">Percent of Request</center> 
							</div>
						</div>
					</div>
					<div class="row mt-3">
						<div class="card dash-card ">
							<div class="card-body">
								<div class="card-title row">
									<div class="col-sm-4" ><i class="fa fa-check fa-3x" aria-hidden="true"></i></div>
									<div class="col-sm-8"><center style="color:#767676" class="h1"><?php echo $row2['agent_complete_total']; ?></center></div>
								</div>
								<center class="card-text ">Completed Request</center> 
							</div>
						</div>
						<div class="card dash-card">
							<div class="card-body">
								<div class="card-title row">
									<div class="col-sm-4" ><i class="fa fa-hourglass-half fa-3x" aria-hidden="true"></i></div>
									<div class="col-sm-8"><center style="color:#767676" class="h1"><?php echo $row2['agent_incomplete_total']; ?></center></div>
								</div>
								<center class="card-text ">uncompleted Request</center> 
							</div>
						</div>
						<div class="card dash-card">
							<div class="card-body">
								<div class="card-title row">
									<div class="col-sm-4" ><i class="fa fa-percent fa-3x" ></i></div>
									<div class="col-sm-8"><center style="color:#767676" class="h1"><?php echo $row2['agent_total']==0?0:round((($row2['agent_complete_total']/$row2['agent_total']) * 100),2); ?></center></div>
								</div>
								<center class="card-text ">Percent of Completed</center> 
							</div>
						</div>
					</div>
					<div class="row mt-3">
						<div class="card" style="height:100%">
							<div class="card-body">
								<div class="row">
									<div class="col-sm-3">Role: <?php echo $row['role']; ?></div>
									<div class="col-sm-3">Profile Creation: <?php echo $row['agent_sience']; ?></div>
									<div class="col-sm-3">Profile Update: <?php echo $row['profile_update']; ?></div>
									<div class="col-sm-3">Phone Number: <?php echo $row['contact_number']; ?></div>
								</div> 
							</div>
						</div>
					</div>
				</div>					
			  </div>
			</form>
		  </div>
		</div>  
	</div>
</div>
</body>
<script>
 
 $(document).ready(function () {
	 $("#fdate").val('<?php echo $fdate ?>');
	 $("#tdate").val('<?php echo $tdate ?>');
	 $("#team").val('<?php echo $team ?>');
	 
	 $('.input-group.date').datepicker({
	  format: 'yyyy-mm-dd', 
      todayHighlight: true,
      autoclose: true,
	});
	
	$("#agentActivityDownload").click(function(){
		$("#agentActivitySummary").table2excel({
		 filename: "agentActivitySummary.xls"
		});
	});
	
	$("#agent").click(function(){
		var selectedValue = $(this).val();
		$.ajax({
			type: "POST",
			url: "agent_details.php",
			data: { value: selectedValue }
		  })
		  .done(function( response ) {
			$("#result").html(response);
		  });
			});
		
 });
  
</script>

</html>
