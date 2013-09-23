<?php
    include 'headers/connection.php';
    session_start();
?>
<?php
	$username = $_GET["n_username"];
	$password = $_GET["n_password"];
	
	function login( $username, $password ){
		$con;
         if( !( $con = start_connection() ) )
            return 'FALSE';
		$sql = "SELECT * FROM users WHERE username = '".$username."' AND password = '".$password."'";
		$result = mysql_query( $sql, $con );
		if( !$result ){
            end_connection( $con );
			return 'Internal Server Error. Could not connect to table.';
		}
		if( mysql_num_rows( $result ) == 0 ){
            end_connection( $con );
			return 'FALSE';
		} else {
			session_start();
			$row = mysql_fetch_array( $result );
			$_SESSION["username"] = $row["username"];
			$_SESSION["email"] = $row["email"];
			$_SESSION["name"] = $row["name"];
			$_SESSION["language"] = $row["language"];
			end_connection( $con );
            return 'TRUE';
        }
        end_connection( $con );
		return 'FALSE';
	}
	
	if( isset( $_SESSION["username"] ) )
		echo "You are already logged in. Refresh the page or logout.";
	else
		echo trim( login( $username, $password ) );
?>