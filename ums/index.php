<?php session_start(); ?>
<?php require_once('inc/connection.php'); ?>
<?php require_once('inc/function.php'); ?>

<?php
//check for submit button
if (isset($_POST['submit'])) {
    $errors = array();

    //check username password
    if (!isset($_POST['email']) || strlen(trim($_POST['email'])) < 1) {
        $errors[] = 'Username is Missing / Invalid';
    }

    if (!isset($_POST['password']) || strlen(trim($_POST['password'])) < 1) {
        $errors[] = 'Password is Missing / Invalid';
    }
    //check any errors

    if (empty($errors)) {
        //save username and password
        $email = mysqli_real_escape_string($connection, $_POST['email']);
        $password = mysqli_real_escape_string($connection, $_POST['password']);
        $hashed_password = sha1($password);
        //prepare database query
        $quary = "SELECT * FROM user 
			WHERE  email = '{$email}'
			AND password = '{$hashed_password}'
			LIMIT 1";

        $result_set = mysqli_query($connection, $quary);
        varify_query($result_set);
        //query succesfull
        if (mysqli_num_rows($result_set) == 1) {
            //valid user found
            $user = mysqli_fetch_assoc($result_set);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            //print_r($_SESSION);
            //updating last login
            $quary = "UPDATE user SET last_login = NOW() ";
            $quary .= "WHERE id = {$_SESSION['user_id']} LIMIT 1";

            $result_set = mysqli_query($connection, $quary);

             if(!$result_set){
                die("database Query failed.");
             }

            //dierct users.php
            header('location: users.php');
        } else {
            $errors[] = 'Invalid Username / Password';
        }
        
    }
}
?>


    <!DOCTYPE html>
    <html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/main.css">
        <title>Log In - User Managment System</title>
    </head>
    <body>
    <div class="login">
        <form action="index.php" method="post">
            <fieldset>
                <legend><h1>Log In</h1></legend>

                <?php
                if (isset($errors) && !empty($errors)) {
                    echo '<p class = "error">Invalid Username /Password</p>';
                }
                ?>

                <?php 
                	if(isset($_GET['logout'])){
                		echo '<p class = "info">You have succesfully logout from the system</p>';
                	}
                 ?>

                <p>
                    <label for="">Username: </label>
                    <input type="text" name="email" id="" placeholder="Email Address" required>
                </p>

                <p>
                    <label for="">Password: </label>
                    <input type="password" name="password" id="" placeholder="Password">
                </p>

                <p>
                    <button type="submit" name="submit">Log In</button>
                </p>

            </fieldset>
        </form>
    </div><!-- login -->
    </body>
    </html>

<?php mysqli_close($connection); ?>