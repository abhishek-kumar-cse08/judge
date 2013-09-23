<?php 
	include 'headers/connection.php';         
	session_start();
?>
<?php	
	function check( $question, $file, $admin ){
		$con = start_connection();
		if( !$con )
			return FALSE;
		
		$res = mysql_query( "SELECT * FROM questions WHERE code ='".$question."' AND admin = '".$admin."'", $con );
		if( !$res ){
			echo 'Error retrieving the results';
			end_connection( $con );
			return FALSE;
		}
		if( mysql_num_rows( $res ) == 0 ){
			echo 'You can not view the submissions to this question';
			end_connection( $con );
			return FALSE;
		}
		
		$res = mysql_query( "SELECT * FROM submissions WHERE question = '".$question."' AND file ='".$file."'", $con );
		if( !$res ){
			echo 'Error retrieving the results';
			end_connection( $con );
			return FALSE;
		}
		if( mysql_num_rows( $res ) == 0 ){
			echo 'You can not view the submissions to this question.';
			end_connection( $con );
			return FALSE;
		}
		
		end_connection( $con );
		return TRUE;
	}

	$file = $_GET["filename"];
	$question = $_GET["question"];
	if( !isset( $_SESSION["adminname"] ) )
		echo 'Please login to continue';
	else if( !$file || !$question )
		echo 'Invalid url';
	else if( check( $question, $file, $_SESSION["adminname"] ) ){
		if ( $file && file_exists($file)) {
			header('Content-type: text/plain');
			//open/save dialog box
			header('Content-Disposition: attachment; filename="'.end( explode( "/",$file ) ).'"');
			//read from server and write to buffer
			readfile( $file );
		} else 
			echo "File does not exist.";
	}
?>
