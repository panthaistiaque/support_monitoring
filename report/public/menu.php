<?php
	$activePage = basename($_SERVER['PHP_SELF'], ".php");
?>
<div class="list-group">
	<a href="index.php" class="list-group-item list-group-item-action <?php echo ($activePage == 'index') ? 'active':''; ?> ">Dashboard</a>
	<a href="agent_activity.php" class="list-group-item list-group-item-action <?php echo ($activePage  == 'agent_activity') ? 'active':''; ?>">Agent Activity </a>
	<a href="individual_contribution.php" class="list-group-item list-group-item-action <?php echo ($activePage  == 'individual_contribution') ? 'active':''; ?>">Individual Contribution</a>
	<a href="employee_statistics.php" class="list-group-item list-group-item-action <?php echo ($activePage  == 'employee_statistics') ? 'active':''; ?>">Employee Statistics</a>
	<a href="<?php echo BASE_URL.'/en/member/login'?>" target="_blank" class="list-group-item list-group-item-action ">Agent Login</a>
	<a href="logout.php" class="list-group-item list-group-item-action ">Logout</a> 
</div>