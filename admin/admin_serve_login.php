<?php
    include 'headers/connection.php';
    session_start();
?>
<?php 
	$adminname = $_GET["n_adminname"];
	$password = $_GET["n_password"];
	
	function login( $adminname, $password ){
		$con;
        if( !( $con = start_connection() )  ){
                return 'FALSE';
        }
		$sql = "SELECT * FROM users WHERE username = '".$adminname."' AND password = '".$password."' AND admin='true'";
		$result = mysql_query( $sql, $con );
		if( !$result ){
            end_connection();
			return 'Internal Server Error. Could not connect to table.';
		}
		if( mysql_num_rows( $result ) == 0 ){
            end_connection();
			return 'FALSE';
		} else {
			session_start();
			$row = mysql_fetch_array( $result );
			$_SESSION["adminname"] = $row["username"];
			$_SESSION["email"] = $row["email"];
			$_SESSION["name"] = $row["name"];
			$_SESSION["language"] = $row["language"];
			end_connection();
            return 'TRUE';
        }
        end_connection();
		return 'FALSE';
	}
	
	if( isset( $_SESSION["adminname"] ) )
		echo "You are already logged in. Refresh the page or logout.";
	else
		echo login( $adminname, $password );	
?>