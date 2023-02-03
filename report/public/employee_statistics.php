<!-- PHP code to establish connection with the localserver -->
<?php
require_once('sessions.php');
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
$team = 'All';
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
		  <form   action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			  <div  class="col-md-12 row" > 
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
					  <a class="btn btn-primary mb-3" id="agentActivityDownload">Excel</a>
				  </div>
			  </div>
			  <div class="col-md-12" > 
					<?php
						if(isset($_POST['viewReport'])) {
						$fdate = $_POST['fdate'];
						$tdate = $_POST['tdate']; 
						$sql = " select uu.id, CONCAT(uu.first_name,' ', uu.last_name) name, uu.email, uui.designation,  uui.profile_image_path, count(ut.id) total, sum(case when ( ut.status_id in (3,4,5)) then 1 else 0 end) completed, sum(case when ( ut.status_id in (1,2)) then 1 else 0 end) uncompleted
								from uv_user uu
								inner join uv_user_instance uui on uui.user_id = uu.id
								inner join uv_ticket ut on ut.customer_id = uu.id
								where uui.supportRole_id in (4)
								and date(CONVERT_TZ(ut.created_at,'+00:00','+6:00')) BETWEEN '$fdate' and date('$tdate')
								group by uu.id, uu.first_name, uu.last_name, uui.designation,  uui.profile_image_path
								order by CONCAT(uu.first_name,' ', uu.last_name) asc ";
						$result = $mysqli->query($sql);
					?>
					<table class="table table-bordered table-sm" id="agentActivitySummary">
						<tr>
							<td colspan="6" style="text-align:center">Agent Activity Report</td>
						</tr>
						<tr>
							<td colspan="6" style="text-align:center"><?php echo '<b>'.$fdate.'</b> to <b>'.$tdate.'</b>';?></td>
						</tr>
						<tr style="background-color:#80808082">
							<th>Employee Name</th>
							<th>Sys ID</th>
							<th >Email</th>
							<th style="text-align: right;">Total Ticket</th>
							<th style="text-align: right;">Resolved Ticket</th>
							<th style="text-align: right;">Unresolved Ticket</th>
						</tr>
						<?php
							while($rows=$result->fetch_assoc())
							{
								$totalTicket = $totalTicket+$rows['total'];
								$totalTicketInHand = $totalTicketInHand+ $rows['completed'];
								$totalResolved = $totalResolved+ $rows['uncompleted']; 
						?>
						<tr >
							<!-- FETCHING DATA FROM EACH
								ROW OF EVERY COLUMN -->
							<td><?php echo $rows['name'];?></td>
							<td><?php echo $rows['id'];?></td>
							<td><?php echo $rows['email'];?></td>
							<td style="text-align: right;"><?php echo $rows['total'];?></td> 
							<td style="text-align: right;"><?php echo $rows['completed'];?></td> 
							<td style="text-align: right;"><?php echo $rows['uncompleted'];?></td> 
						</tr>
						<?php
							}
						?>
						<tr style="background-color:#80808082">
							<th colspan="3" style="text-align: right;">Total</th>
							<th style="text-align: right;"><?php echo $totalTicket;?></th>
							<th style="text-align: right;"><?php echo $totalTicketInHand;?></th> 
							<th style="text-align: right;"><?php echo $totalResolved;?></th> 
						</tr>
					</table>
					<?php
						}
						mysqli_report(MYSQLI_REPORT_STRICT);
					?>				
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
	
		
 });
  
</script>

</html>
