<?php
	$activePage = basename($_SERVER['PHP_SELF'], ".php");
?>
<div class="list-group">
	<a href="index.php" class="list-group-item list-group-item-action <?php echo ($activePage == 'index') ? 'active':''; ?> ">Dashboard</a>
	<a href="agent_activity.php" class="list-group-item list-group-item-action <?php echo ($activePage  == 'agent_activity') ? 'active':''; ?>">Agent Activity </a>
	<a href="agent_profile.php" class="list-group-item list-group-item-action <?php echo ($activePage  == 'agent_profile') ? 'active':''; ?>">Agent Profile</a>
</div>