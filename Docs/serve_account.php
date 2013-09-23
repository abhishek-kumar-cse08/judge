<?php 
    include 'headers/connection.php';
    session_start();
?>
<?php
	$name = $_GET["n_name"];
	$institute = $_GET["n_institute"];
	$language = $_GET["n_language"];
	$current_password = $_GET["n_current_password"];
	$password = $_GET["n_password"];
	$cpassword = $_GET["n_cpassword"];
	$change = $_GET["n_pwd_change"];
	
	//validation start
	function validate( $name, $institute, $password, $language, $cpassword, $current_password ){
		if( strlen( $password ) == 0 || strlen( $cpassword ) == 0 || strlen( $current_password ) == 0 ||
			strlen( $name ) == 0 || strlen( $institute ) == 0 ){
			echo( 'Please fill in all the details before moving ahead.' );
			return FALSE;
		}
		if( strlen( $password ) > 20 || strlen( $current_password ) > 20 ){
			echo( 'The Password fields can not be more than 20 characters in length.' );
			return FALSE;
		}
		if( strcmp( $password, $cpassword ) != 0 ){
			echo( 'Passwords do not match.' );
			return FALSE;
		}
		if( strlen( $institute ) > 30 ){
			echo( 'Please enter an Institute/Organization name not more than 30 characters.' );
			return FALSE;
		}
		if( strlen( $name ) > 20 ){
			echo( "Please enter a name not more than 20 characters." );
			return FALSE;
		}
		$name_arr = explode( " ", $name );
		for( $i=0; $i<count( $name_arr ); $i++ ){
			if( preg_match( "/^[a-zA-Z]+$/", $name_arr[$i] ) == 0 ){
				echo( 'Please enter a valid name' );
				return FALSE;
			}
		}
		
		$lang_arr = Array( "c", "cplusplus", "java" );
		$miss = 1;
		for( $i=0; $i<count( $lang_arr ); $i++ )
			if( strcmp( $lang_arr[$i], $language ) )
				$miss = 0;
		if( $miss == 1 ){
			echo( "Invalid language. Internal server error." );
			return FALSE;
		}
		return TRUE;
	}
	//validation end
	
	function check_update( $name, $institute, $password, $language, $cpassword, $current_password, $change ){
		 $con;
         if( !( $con = start_connection() ) )
            return FALSE;
		
        begin_transaction( $con );
		if( strcmp( $change, "false" ) != 0 ){
			$result = mysql_query( "SELECT * FROM users WHERE username = '".$_SESSION["username"]."' FOR UPDATE", $con);
			if( !$result ){
				echo( 'Internal Server Error. Could not connect to table.' );
                rollback_transaction( $con );
				end_connection( $con );
                return FALSE;
			}
			$row = mysql_fetch_array( $result );
			if( strcmp( $current_password, $row["password"] ) != 0 ){
				echo( 'Incorrect current password.' );
                rollback_transaction( $con );
                end_connection( $con );
				return FALSE;
			}
		}
		
		if( strcmp( $change, "false" ) != 0 )
			$sql = "UPDATE users SET name='".$name."', institute='".$institute."', language='".$language."', password='".$password."' WHERE username='".$_SESSION["username"]."'";
		else
			$sql = "UPDATE users SET name='".$name."', institute='".$institute."', language='".$language."' WHERE username='".$_SESSION["username"]."'";
		if( !mysql_query( $sql, $con ) ){
			echo( 'Internal Server Error. Could not create database entry.' );
            rollback_transaction( $con );
            end_connection( $con );
			return FALSE;
		}
        
        commit_transaction( $con );
		end_connection( $con );
		return TRUE;
	}
	
	if( strcmp( $change, "false" ) == 0 ){
		$current_password = "abc";
		$password = "abc";
		$cpassword = "abc";
	}
	
	if( isset( $_SESSION["username"] ) ){ 
		if( validate( $name, $institute, $password, $language, $cpassword, $current_password ) ){
			if( check_update( $name, $institute, $password, $language, $cpassword, $current_password, $change ) ){
				$_SESSION["name"] = $name;
				$_SESSION["language"] = $language;
				echo "TRUE";
			}
		}
	} else {
		echo( 'You need to login to conitnue.' );
	}
?>
