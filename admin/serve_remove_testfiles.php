<?php 
    include 'headers/connection.php';
    session_start();
?>

<?php	
	$string = $_POST["files"];
	if( !isset( $_SESSION["adminname"] ) ){
		echo 'Please login to continue';
	} else if( !$string || strlen( $string ) == 0 ){
		echo 'Please provide a testcase to delete';
	} else if( $con = start_connection() ){
		$return_string = "TRUE";
		$string = trim( $string );
		$testcases = explode( " ", $string );
		for( $i=0; $i<count($testcases); $i++ ){
			begin_transaction( $con );
			$result = mysql_query( 'SELECT * FROM files WHERE testfiles="'.$testcases[$i].'" AND admin = "'.$_SESSION["adminname"].'" FOR UPDATE', $con );
			if( !$result || mysql_num_rows( $result ) == 0 ){
				echo "The given testcases do not exist on server.";
				$return_string = "";
				rollback_transaction( $con );
			} else {
				while( $row = mysql_fetch_array( $result ) ){
					unlink( $row["testfiles"] );
					unlink( $row["outputfiles"] );
				}
				mysql_query( 'DELETE FROM files WHERE testfiles="'.$testcases[$i].'"', $con );
				commit_transaction( $con );
			}
		}
		echo $return_string;
        end_connection( $con );
	}
?>