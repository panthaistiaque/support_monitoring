

<nav class="navbar navbar-expand-sm" style=" border: 1px solid #ced4da; border-radius: 5px;">
  <div class="container-fluid">
    <a class="navbar-brand" style="font-family: 'Kaushan Script', cursive;" href="#">Support Monitoring System</a>
	<div class="btn-group dropstart">
		  <div class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:13px">
			<?php echo $_SESSION['email']; ?>
		  </div>
		  <div class="dropdown-menu p-4 text-muted" >
			  <p class="mb-0" style="font-size:11px">
				<?php echo $_SESSION['email']; ?>
			  </p>
			  <p class="mb-0" style="font-size:11px">
				<?php echo 'Start at:'.$_SESSION['creation_time']; ?>
			  </p>
			  <p class="mb-0" style="font-size:11px">
				<?php echo 'IP: '.$_SERVER['REMOTE_ADDR']; ?>
			  </p>
		   </div>
	</div>
  </div>
</nav>
