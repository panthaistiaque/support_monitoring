<!-- PHP code to establish connection with the localserver -->
<?php
require_once('sessions.php');
require_once('config/config.php');
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$total=0;
$totalOpen = 0;
$totalCompleted = 0;
$totalProgress = 0;
$totalSpam = 0;
$fdate = date("Y-m-d");
$tdate = date("Y-m-d");
$team = 'All';

?>
<!-- HTML code to display data in tabular format -->
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Dashbord</title>
	<link rel="icon" type="image/x-icon" sizes="16x16 32x32 48x48" href="image/favicon.png">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="//cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
	<script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
	
	<!-- Bootstrap Date-Picker Plugin -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
	<!-- CSS FOR STYLING THE PAGE -->
	<style>
		th, td{
			text-align: center;
		}
		.card{
			margin-left: 10px;
			
		}
		.dash-card{
			width:19%;
		}
	</style>
</head>

<body>  
<div class="container-fluid mt-3">
	<div class="container-fluid">
	<?php	include 'topbar.php';?>
		<div class="row mt-3">
			<div class="col-md-2">
			<?php	include 'menu.php';?>
			</div>
			<div class="col-md-10">
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">	
					<div class="col-md-12 row" > 
						<div class="col-auto col-md-3">
							<div class="input-group">
								<span class="input-group-text">From Date</span>
								<input class="form-control input-group date" type="text" readonly  id="fdate"  name="fdate">
							</div>
						</div>
						<div class="col-auto col-md-3">
							<div class="input-group">
								<span class="input-group-text">To Date</span>
								<input type="text" class="form-control input-group date" readonly id="tdate" name="tdate">
							</div>
						</div>
						<div class="col-auto col-md-3">
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
						</div>
						<div class="col-auto col-md-3">
							<button type="submit" name="viewReport" class="btn btn-primary mb-3">View</button> 
							<small class="text-wrap text-sx float-end" >Long Pending issue : <a data-type="long_pending" class="run-php">07</a></small>
						</div> 
					</div>
				</form>
				<?php
					if(isset($_POST['viewReport'])) {
						$fdate = $_POST['fdate'];
						$tdate = $_POST['tdate']; 
						$team = $_POST['team'];
							$sql = " select count(case when ut.id>1 then 1 else 0 end) total,  ".
									" COALESCE(sum(case when uts.code in ('closed','resolved','answered') then 1 else 0 end),0) completed,  ".
									" COALESCE(sum(case when uts.code in ('pending') then 1 else 0 end),0) progress,  ".
									" COALESCE(sum(case when uts.code in ('open') then 1 else 0 end),0) open,  ".
									" COALESCE(sum(case when uts.code in ('spam') then 1 else 0 end),0) spam   ".
									" from uv_ticket ut ".
								   "  left join uv_ticket_status uts on uts.id = ut.status_id ".
								   "  where date(CONVERT_TZ(ut.created_at,'+00:00','+6:00')) BETWEEN '$fdate' and date('$tdate') ";
								   if ($team!='All') {
										$sql = $sql." and ut.subGroup_id =  $team  ";
									}
								    

	 
						$result = $mysqli->query($sql);
						$row = $result->fetch_assoc();
						$total=$row['total'];
						$totalOpen = $row['open'];
						$totalCompleted =$row['completed'];
						$totalProgress = $row['progress'];
						$totalSpam = $row['spam'];
					}
				?>
				<div class="col-md-12 row" style="width: 100%;">
					<div class="card dash-card">
						<div class="card-body">
							<div class="card-title row">
								<div class="col-sm-4" ><image style="width:140%; height:auto;"  src="image/total.png"/></div>
								<div class="col-sm-8"><center style="color:#767676" class="h1"><?php echo $total;?></center></div>
							</div>
							<center class="card-text ">Total Request</center> 
						</div>
					</div>
					<div class="card dash-card">
						<div class="card-body">
							<div class="card-title row">
								<div class="col-sm-4"><image style="width:140%; height:auto;" src="image/completed.png"/></div>
								<div class="col-sm-8"><center style="color:#2CD651" class="h1"><a data-type="completed" class="run-php"><?php echo $totalCompleted;?></a></center></div>
							</div>
							<center class="card-text ">Completed</center> 
						</div>
					</div>
					<div class="card dash-card">
						<div class="card-body">
							<div class="card-title row">
								<div class="col-sm-4"><image style="width:100%; height:auto;" src="image/progress.png"/></div>
								<div class="col-sm-8"><center style="color:#FF6A6B" class="h1"><a data-type="progress" class="run-php"><?php echo $totalProgress;?></a></center></div>
							</div>
							<center class="card-text ">In progress</center> 
						</div>
					</div>
					<div class="card dash-card">
						<div class="card-body">
							<div class="card-title row">
								<div class="col-sm-4"><image style="width:140%;; height:auto;" src="image/open.png"/></div>
								<div class="col-sm-8"><center style="color:#7E91F0" class="h1"><a data-type="open" class="run-php"><?php echo $totalOpen;?></a></center></div>
							</div>
							<center class="card-text ">Open</center> 
						</div>
					</div>
					<div class="card dash-card" >
						<div class="card-body">
							<div class="card-title row">
								<div class="col-sm-4"><image style="width:140%; height:auto;" src="image/spam.png"/></div>
								<div class="col-sm-8"><center style="color:#00A1F2" class="h1"><a data-type="spam" class="run-php"><?php echo $totalSpam;?></a></center></div>
							</div>
							<center class="card-text ">Spam </center> 
						</div>
					</div>
				</div>
				<div class="col-md-12 row mt-3" id="details-table" >
					<div class="card">
						<div class="card-body">
							<div class="card-text ">
								<table class="table table-hover table-sm" id="agentActivitySummary">
									<tr >
										<th>Date</th>
										<th>Ticket ID</th>
										<th >Subject</th>
										<th>Name of requester</th>
										<th>To Replay</th>
										<th>Agent Name <br/> Team</th>
										<!--<th>Last Status</th>-->
									</tr>
									 <tbody id="result"></tbody>
								</table>
							</div> 
						</div>
					</div>
				</div> 
			</div>
		</div>  
	</div>
</div>
</body>
<script>
 
 $(document).ready(function () {  
	 $("#details-table").hide();
	 $("#fdate").val('<?php echo $fdate ?>');
	 $("#tdate").val('<?php echo $tdate ?>');
	 $("#team").val('<?php echo $team ?>');
	 
	 $('.input-group.date').datepicker({
	  format: 'yyyy-mm-dd', 
      todayHighlight: true,
      autoclose: true,
	});
	
	$(".run-php").click(function(){
		$("#details-table").hide();
		let type = $(this).data("type");
		$.ajax({
		  type: "POST",
		  url: "get_dashbord_detail.php",
		  data: {type: type, fdate: '<?php echo $fdate ?>', tdate: '<?php echo $tdate ?>', team: '<?php echo $team ?>'}
		}).done(function( msg ) {
		  //$("#result").html( msg );
		  var jsonData = JSON.parse(msg);
		  var tbody = $("#result");
		  tbody.html("");
		  jsonData.forEach(function(rowData) {
			  $("#details-table").show();
			var row = $("<tr>");
			row.append("<td>" + rowData.created_at + "</td>");
			row.append("<td>" + rowData.id + "</td>");
			row.append("<td>" + rowData.subject + "</td>");
			row.append("<td>" + rowData.customer + "</td>");
			row.append("<td>" + rowData.reply + "</td>");
			row.append("<td>" + rowData.agent +"<br/>" + rowData.team+ "</td>");
			//row.append("<td>" + rowData.subject + "</td>");
			tbody.append(row);
			})
		});
	});
 });
 
</script>

</html>
