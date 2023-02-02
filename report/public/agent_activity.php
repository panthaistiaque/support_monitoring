<!-- PHP code to establish connection with the localserver -->
<?php
require_once('config.php');
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
		<div class="row">
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
					<!-- PHP CODE TO FETCH DATA FROM ROWS -->
					<?php
						if(isset($_POST['viewReport'])) {
						$fdate = $_POST['fdate'];
						$tdate = $_POST['tdate'];
						$team = $_POST['team']; 
						$sql = " select agent, team, count(*) as total,sum(in_hand) in_hand, sum(resolved) resolved, sum(reply) reply from(".
								" select ut.id, ut.subject, utt.code as type,uts.code as status,ust.name as team,CONCAT(uu.first_name,' ' ,uu.last_name) as agent, case when uts.code in ('closed','resolved','answered' ) then 1 else 0 end as resolved , case when uts.code not in ('closed','resolved','answered' ) then 1 else 0 end as in_hand ".
								" ,COALESCE(th.reply,0) as reply, CONCAT(DATE_FORMAT(CONVERT_TZ(ut.created_at,'+00:00','+6:00') , '%d %b %Y'),' ',TIME_FORMAT(CONVERT_TZ(ut.created_at,'+00:00','+6:00'), '%h:%i %p'))  created_at  ".
								" ,DATE_FORMAT(CONVERT_TZ(ut.created_at,'+00:00','+6:00') , '%d %b %Y') date ".
								" from uv_ticket ut ".
								" left join uv_ticket_type utt on utt.id = ut.type_id ".
								" left join uv_ticket_status uts on uts.id = ut.status_id ".
								" left join uv_support_team ust on ust.id = ut.subGroup_id ".
								" left join uv_user uu on uu.id = ut.agent_id ".
								" left join (SELECT ticket_id, count(*) reply FROM uv_thread where thread_type = 'reply' group by ticket_id) th on th.ticket_id = ut.id  ".
								" where date(CONVERT_TZ(ut.created_at,'+00:00','+6:00')) BETWEEN '$fdate' and '$tdate' ";
								if ($team!='All') {
									$sql = $sql." and ust.id =  $team  ";
								}	
								
								$sql = $sql." ) as asd ".
								" group by agent, team ".
								" order by team ";
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
							<th>Agent Name</th>
							<th>Team</th>
							<th style="text-align: right;">Ticket Assigned </th>
							<th style="text-align: right;">Ticket In Hand</th>
							<th style="text-align: right;">Ticket Resolved</th>
							<th style="text-align: right;">No. of Reply</th>
						</tr>
						<?php
							while($rows=$result->fetch_assoc())
							{
								$totalTicket = $totalTicket+$rows['total'];
								$totalTicketInHand = $totalTicketInHand+ $rows['in_hand'];
								$totalResolved = $totalResolved+ $rows['resolved'];
								$totalReply = $totalReply+ $rows['reply'];
						?>
						<tr >
							<!-- FETCHING DATA FROM EACH
								ROW OF EVERY COLUMN -->
							<td><?php echo $rows['agent'];?></td>
							<td><?php echo $rows['team'];?></td>
							<td style="text-align: right;"><?php echo $rows['total'];?></td>
							<td style="text-align: right;"><?php echo $rows['in_hand'];?></td> 
							<td style="text-align: right;"><?php echo $rows['resolved'];?></td> 
							<td style="text-align: right;"><?php echo $rows['reply'];?></td> 
						</tr>
						<?php
							}
						?>
						<tr style="background-color:#80808082">
							<th colspan="2" style="text-align: right;">Total</th>
							<th style="text-align: right;"><?php echo $totalTicket;?></th>
							<th style="text-align: right;"><?php echo $totalTicketInHand;?></th> 
							<th style="text-align: right;"><?php echo $totalResolved;?></th> 
							<th style="text-align: right;"><?php echo $totalReply;?></th> 
						</tr>
					</table>
					<?php
						}
						mysqli_report(MYSQLI_REPORT_STRICT);
					?>				
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
