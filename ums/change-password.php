<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/function.php'); ?>
<?php 
	
	//checking user login
	if (!isset($_SESSION['user_id'])) {
		header('Location: index.php');
	}

	$errors = array();
	$user_id = '';
	$first_name = '';
	$last_name = '';  
	$email = '';
	   

	if(isset($_GET['user_id'])){
		$user_id = mysqli_real_escape_string($connection, $_GET['user_id']);
		$query = "SELECT * FROM user WHERE id = {$user_id} LIMIT 1";

		$result_set = mysqli_query($connection, $query);

		if($result_set){
			if(mysqli_num_rows($result_set) == 1){
				//user found
				$result = mysqli_fetch_assoc($result_set);
				$first_name = $result['first_name'];
				$last_name = $result['last_name'];
				$email = $result['email'];
			}else{
				//user not found
				header("Location: users.php?err=user not found");
			}
		}else{
			//query unsuccessful
			header('Location: user.php?err=query_failed');
		}
	}


	if(isset($_POST['submit'])){
		$user_id = $_POST['user_id'];
		$password = $_POST['password'];
	

		//checking fields
		$req_fields = array('user_id','password' );
		$errors = array_merge($errors,check_req_fields($req_fields));

		//checking max length
		$max_lan_fields = array('password'=> 40);
		$errors = array_merge($errors,check_max_len($max_lan_fields));		
 

		if(empty($errors)){ 
			//no errors found... adding new record
			$password = mysqli_real_escape_string($connection, $_POST['password']);
			$hashed_password = sha1($password);

				$query = "UPDATE user SET ";
				$query = "password = '{$hashed_password}'";
				$query = "WHERE id = '{$user_id}'LIMIT 1";
		
			$result = mysqli_query($connection, $query);

			if($result){
				//query success...
				header('Location: users.php?user_modified=true');
			}else{
				$errors[] = '-Feild to update to password ';
			}  
		}

	}



 ?>
 <!DOCTYPE html>
 <html>
 <head>
 	<title>Change Password</title>
 	 <link rel="stylesheet" type="text/css" href="css/main.css">
 </head>
 <body>
 	<header>
 		<div class="appname">User Management System</div>
 		<div class="loggedin">Welcome <?php echo $_SESSION['first_name']; ?>!<br><a href="logout.php">Log Out</a> </div>
 	</header>

 	<main>
 		<h1>Change Password<span><a href="users.php"> Back to User List</a></span></h1>
 		<?php 
 			if (!empty($errors)) {
 				display_errors($errors);
 			}

 		 ?>

 		<form action="change-password.php" method="post" class="userform">
 			<input type="hidden" name="user_id" value="<<?php echo $user_id ?>">
 			<p>
 				<label for="">First Name:</label>
 				<input type="text" name="first_name" <?php echo 'value="' .$first_name .'"';?>disabled>
 			</p>

 			<p>
 				<label for="">Last Name:</label>
 				<input type="text" name="last_name" <?php echo 'value="' .$last_name .'"';?>disabled>
 			</p>

 			<p>
 				<label for="">Email Address:</label>
 				<input type="text" name="email" <?php echo 'value="' .$email .'"';?>disabled>
 			</p>

 			<p>
 				<label for="">New Password:</label>
 				 <input type="Password" name="Password" id="">
 			</p>

 			<p>
 				<label for="">&nbsp;</label><br> 
 				<button type="submit" name="submit">Update Password</button>
 			</p>
 		</form>
 	</main>

 	
 </body>
 </html>  