<?php 
    include 'headers/connection.php';
    session_start();
?>
<?php
	$username = $_POST["n_uname"];
	$name = $_POST["n_name"];
	$email = $_POST["n_email"];
	$institute = $_POST["n_institute"];
	$password = $_POST["n_pword"];
	$language = $_POST["language"];
	$cpassword = $_POST["n_cpword"];
	//validation start
	function validate( $username, $name, $email, $institute, $password, $language, $cpassword ){
		if( strlen( $username ) == 0 || strlen( $password ) == 0 || strlen( $cpassword ) == 0 || strlen( $name ) == 0 || 
			strlen( $email ) == 0 || strlen( $institute ) == 0 ){
			include 'headers/top_menu.php';
			echo( 'Please fill in all the details before moving ahead.' );
			include 'headers/bottom_menu.php';
			return FALSE;
		}
		if( strlen( $username ) > 20 || strlen( $password ) > 20 ){
			include 'headers/top_menu.php';
			echo( 'The Username/Password fields can not be more than 20 characters in length.' );
			include 'headers/bottom_menu.php';
			return FALSE;
		}
		if( strcmp( $password, $cpassword ) != 0 ){
			include 'headers/top_menu.php';
			echo( 'Passwords do not match.' );
			include 'headers/bottom_menu.php';
			return FALSE;
		}
		if( strlen( $institute ) > 30 ){
			include 'headers/top_menu.php';
			echo( 'Please enter an Institute/Organization name not more than 30 characters.' );
			include 'headers/bottom_menu.php';
			return FALSE;
		}
		if( strlen( $name ) > 20 ){
			include 'headers/top_menu.php';
			echo( "Please enter a name not more than 20 characters." );
			include 'headers/bottom_menu.php';
			return FALSE;
		}
		$name_arr = explode( " ", $name );
		for( $i=0; $i<count( $name_arr ); $i++ ){
			if( preg_match( "/^[a-zA-Z]+$/", $name_arr[$i] ) == 0 ){
				include 'headers/top_menu.php';
				echo( 'Please enter a valid name' );
				include 'headers/bottom_menu.php';
				return FALSE;
			}
		}
		if( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ){
			include 'headers/top_menu.php';
			echo( 'Please enter a valid email.' );
			include 'headers/bottom_menu.php';
			return FALSE;
		}
		$lang_arr = Array( "c", "cplusplus", "java" );
		$miss = 1;
		for( $i=0; $i<count( $lang_arr ); $i++ )
			if( strcmp( $lang_arr[$i], $language ) )
				$miss = 0;
		if( $miss == 1 ){
			include 'headers/top_menu.php';
			echo( "Invalid language. Internal server error." );
			include 'headers/bottom_menu.php';
			return FALSE;
		}
		return TRUE;
	}
	//validation end
	
	function check_insert( $username, $name, $email, $institute, $password, $language, $cpassword ){
		$con;
         if( !( $con = start_connection() ) )
            return FALSE;
		
        lock_tables_write( "users", $con );
		$result = mysql_query( "SELECT * FROM users WHERE username = '".$username."'", $con);
		if( !$result ){
			include 'headers/top_menu.php';
			echo( 'Internal Server Error. Could not connect to table.' );
			include 'headers/bottom_menu.php';
            unlock_tables( $con );
			end_connection( $con );
            return FALSE;
		}
		if( mysql_num_rows( $result ) > 0 ){
			include 'headers/top_menu.php';
			echo( 'User already exists. Please choose a different username.' );
			include 'headers/bottom_menu.php';
			end_connection( $con );
            unlock_tables( $con );
            return FALSE;
		}
		
		$result = mysql_query( "SELECT * FROM users WHERE email = '".$email."'", $con);
		if( !$result ){
			include 'headers/top_menu.php';
			echo( 'Internal Server Error. Could not connect to table.' );
			include 'headers/bottom_menu.php';
            unlock_tables( $con );
			end_connection( $con );
            return FALSE;
		}
		if( mysql_num_rows( $result ) > 0 ){
			include 'headers/top_menu.php';
			echo( 'Email already registered.' );
			include 'headers/bottom_menu.php';
			unlock_tables( $con );
            end_connection( $con );
            return FALSE;
		}
		
		$sql = "INSERT INTO users VALUES( '".$username."', '".$name."', '".$password."', '".$email."', '".$institute."', '".$language."', 'false' )";
		if( !mysql_query( $sql, $con ) ){
			include 'headers/top_menu.php';
			echo( 'Internal Server Error. Could not create database entry.' );
			include 'headers/bottom_menu.php';
            unlock_tables( $con );
			end_connection( $con );
            return FALSE;
		}

        unlock_tables( $con );
		end_connection( $con );
		return TRUE;
	}
	
	if( isset( $_SESSION["username"] ) ){
		include 'headers/top_menu.php';
		echo "You are already signed in via a different ID. Logout to continue.";
		include 'headers/bottom_menu.php';
	} else{
		if( validate( $username, $name, $email, $institute, $password, $language, $cpassword ) ){
			if( check_insert( $username, $name, $email, $institute, $password, $language, $cpassword ) ){
				$_SESSION["username"] = $username;
				$_SESSION["email"] = $email;
				$_SESSION["name"] = $name;
				$_SESSION["language"] = $language;
				header( "Location: /index.php" );
			}
		}
	}
?>
