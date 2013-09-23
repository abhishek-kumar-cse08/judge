<?php 
    include 'headers/connection.php';
    session_start();
?>

<?php
	function check( $file, $admin ){
		
		$con = start_connection();
		$res = mysql_query( "SELECT * FROM files WHERE admin = '".$admin."' AND ( testfiles = '".$file."' OR outputfiles = '".$file."' )", $con );
		if( !$res ){
			echo 'Error retrieving results';
			end_connection( $con );
			return FALSE;
		}
		if( mysql_num_rows( $res ) == 0 ){
			echo 'You do not have the permission to download these files';
			end_connection( $con );
			return FALSE;
		}
		
		end_connection( $con );
		return TRUE;
	}
	
	$file = $_GET["filename"];
	if( !isset( $_SESSION[ "adminname" ] ) )
		echo 'Please login as an admin to continue';
	else if ( check( $file, $_SESSION[ "adminname" ] ) && $file && file_exists($file) ) {
	    header('Content-type: text/plain');
		//open/save dialog box
		header('Content-Disposition: attachment; filename="'.end( explode( "/",$file ) ).'"');
		//read from server and write to buffer
		readfile( $file );
	}
?>