<?php
require_once('config/config.php');
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$test = null;
$email = null;
$phone = null;  
?>


<!DOCTYPE html>
<html lang="en">

<head>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

<style>
     body{
        padding-top:4.2rem;
		padding-bottom:4.2rem;
		background:rgba(0, 0, 0, 0.76);
        }
        a{
         text-decoration:none !important;
         }
         h1,h2,h3{
         font-family: 'Kaushan Script', cursive;
         }
          .myform{
		position: relative;
		display: -ms-flexbox;
		display: flex;
		padding: 1rem;
		-ms-flex-direction: column;
		flex-direction: column;
		width: 100%;
		pointer-events: auto;
		background-color: #fff;
		background-clip: padding-box;
		border: 1px solid rgba(0,0,0,.2);
		border-radius: 1.1rem;
		outline: 0;
		max-width: 500px;
		 }
         .tx-tfm{
         text-transform:uppercase;
         }
         .mybtn{
         border-radius:50px;
         }
        
         .login-or {
         position: relative;
         color: #aaa;
         margin-top: 10px;
         margin-bottom: 10px;
         padding-top: 10px;
         padding-bottom: 10px;
         }
         .span-or {
         display: block;
         position: absolute;
         left: 50%;
         top: -2px;
         margin-left: -25px;
         background-color: #fff;
         width: 50px;
         text-align: center;
         }
         .hr-or {
         height: 1px;
         margin-top: 0px !important;
         margin-bottom: 0px !important;
         }
         .google {
         color:#666;
         width:100%;
         height:40px;
         text-align:center;
         outline:none;
         border: 1px solid lightgrey;
         }
          form .error {
         color: #ff0000;
         }
         #second{display:none;}
</style>
</head> 
<body>

    <div class="container">
        <div class="row">
			<div class="col-md-5 mx-auto"> 
							<?php
								if (isset($_POST['submit'])) {
									// Get the values from the form
									$email = $_POST['email'];
									$phone = $_POST['phone']; 
									$query = "select * from uv_user uu
											inner join uv_user_instance uui on uui.user_id = uu.id
											where uui.supportRole_id not in (4) and uu.is_enabled = 1 and uui.is_active = 1
											and email ='$email'"; 
									$result = mysqli_query($mysqli, $query); 
									$user = mysqli_fetch_assoc($result);  
									if ($user['contact_number'] == $phone) { 
										session_start();
										$_SESSION['email'] = $email;
										$_SESSION['creation_time'] = date("Y-m-d H:i");
										header("Location: index.php");
										exit();
									} else {
										// Password is incorrect, show an error message
										$test = "Incorrect email or phone";
									}
								}
							?>
			<div id="first">
				<div class="myform form ">
					 <div class="logo mb-3">
						 <div class="col-md-12 text-center">
							<h2>Support Monitoring System</h2>
							<h4 style="color:red"><?php echo $test;  ?></h4>
						  </div> 
					</div>
                   <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="login">
                           <div class="form-group">
                              <label for="exampleInputEmail1">Email address</label>
                              <input type="email" name="email"  class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email">
                           </div>
                           <div class="form-group">
                              <label for="exampleInputEmail1">Contact Number</label>
                              <input type="text" name="phone" id="phone"  class="form-control" aria-describedby="emailHelp" placeholder="Enter Password">
                           </div>
                           <div class="col-md-12 text-center ">
                              <button type="submit" name="submit" class=" btn btn-block mybtn btn-primary tx-tfm">Login</button>
                           </div>
                           <div class="form-group">
                              <p class="text-center">Don't have account? <a href="#" id="signup">Sign up here</a></p>
                           </div>
                    </form> 
				</div>
			</div>
			  <div id="second">
			      <div class="myform form ">
                        <div class="logo mb-3">
                           <div class="col-md-12 text-center">
                              <h1 >Signup</h1>
                           </div>
                        </div>
                        <form action="#" name="registration">
                           <div class="form-group">
                              <label for="exampleInputEmail1">First Name</label>
                              <input type="text"  name="firstname" class="form-control" id="firstname" aria-describedby="emailHelp" placeholder="Enter Firstname">
                           </div>
                           <div class="form-group">
                              <label for="exampleInputEmail1">Last Name</label>
                              <input type="text"  name="lastname" class="form-control" id="lastname" aria-describedby="emailHelp" placeholder="Enter Lastname">
                           </div>
                           <div class="form-group">
                              <label for="exampleInputEmail1">Email address</label>
                              <input type="email" name="email"  class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email">
                           </div>
                           <div class="form-group">
                              <label for="exampleInputEmail1">Password</label>
                              <input type="password" name="password" id="password"  class="form-control" aria-describedby="emailHelp" placeholder="Enter Password">
                           </div>
                           <div class="col-md-12 text-center mb-3">
                              <button type="submit" class=" btn btn-block mybtn btn-primary tx-tfm">Get Started For Free</button>
                           </div>
                           <div class="col-md-12 ">
                              <div class="form-group">
                                 <p class="text-center"><a href="#" id="signin">Already have an account?</a></p>
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
	$("#signup").click(function() {
	$("#first").fadeOut("fast", function() {
	$("#second").fadeIn("fast");
	});
	});

	$("#signin").click(function() {
	$("#second").fadeOut("fast", function() {
	$("#first").fadeIn("fast");
	});
	});


	  
			 $(function() {
			   $("form[name='login']").validate({
				 rules: {
				   
				   email: {
					 required: true,
					 email: true
				   },
				   phone: {
					 required: true,
					 
				   }
				 },
				  messages: {
				   email: "Please enter a valid email address",
				  
				   phone: {
					 required: "Please enter phone",
					
				   }
				   
				 },
				 submitHandler: function(form) {
				   form.submit();
				 }
			   });
			 });
			 


	$(function() {
	  
	  $("form[name='registration']").validate({
		rules: {
		  firstname: "required",
		  lastname: "required",
		  email: {
			required: true,
			email: true
		  },
		  password: {
			required: true,
			minlength: 5
		  }
		},
		
		messages: {
		  firstname: "Please enter your firstname",
		  lastname: "Please enter your lastname",
		  password: {
			required: "Please provide a password",
			minlength: "Your password must be at least 5 characters long"
		  },
		  email: "Please enter a valid email address"
		},
	  
		submitHandler: function(form) {
		  form.submit();
		}
	  });
	});

	</script>
</html>