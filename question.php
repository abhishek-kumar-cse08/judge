<?php 
	include 'headers/top_menu.php';
	include 'headers/connection.php';         
	session_start();
?>
<script>
	function submission( type, code ){
		window.location = "submissions.php?type="+type+"&code="+code;
	}
	function submit( code ){
		window.location = "submit.php?code="+code;
	}
</script>
<?php
	function query( $code ){
		$con = start_connection();
		if( !$con ){
			return FALSE;
		}
		if( !mysql_select_db( "coding", $con ) ){
			echo 'Internal Server Error. Could not connect to database.';
			end_connection( $con );
			return FALSE;
		}
		
		$result = mysql_query( "SELECT * FROM questions WHERE code = '".$code."' AND question_time <= ".time(), $con);
		if( !$result ){
			echo 'Internal Server Error. Could not connect to table.';
			end_connection( $con );
			return FALSE;
		}
		if( mysql_num_rows( $result ) == 0 ){
			echo 'Question does not exist or can not be viewed now.';
			end_connection( $con );
			return FALSE;
		}
		end_connection( $con );
		return mysql_fetch_array( $result );
	} 
	
	if( !isset( $_SESSION["username"] ) ){
		echo 'Please login to continue';
	} else if( !$_GET["code"] ) {
		echo 'Please provide a question to view.';
	} else if( $row = query( $_GET["code"] ) ) {
		$code = $_GET["code"];
		echo '<table width="100%" class="table">';
		echo '<tr>';
		echo '<td align="center"><input type="button" value="All Submissions" onclick="submission( \'all\', \''.$code.'\' )" class="button_small"></td>';
		echo '<td align="center"><input type="button" value="My Submissions" onclick="submission( \'my\', \''.$code.'\' )" class="button_small"></td>';
		echo '<td align="center"><input type="button" value="Correct Submissions" onclick="submission( \'correct\', \''.$code.'\' )" class="button_small"></td>';
		echo '<td align="center"><input type="button" value="Submit" onclick="submit( \''.$code.'\' )" class="button_small"></td>';
		echo '</tr>';
		echo '<tr><td><br></td></tr>';
		echo '<tr><td align="center" colspan="4"><h2>'.$row["code"].'</h2></td></tr>';
		echo '<tr><td align="center" colspan="4"><h3>'.$row["name"].'</h3></td></tr>';
		echo '<tr><td colspan="4" width="450px">'.$row["statement"].'</td></tr>';
		
		echo '<tr><td><br></td></tr>';
		echo '<tr><td colspan="4">Time Limit : '.$row["time"].'</td></tr>';
		echo '<tr><td colspan="4" width="400px">Languages : '.$row["languages"].'</td></tr>';
		echo '<tr><td colspan="4">Question By : '.$row["admin"].'</td></tr>';
		echo '<tr><td colspan="4">File limit : '.$row["file"].' Bytes</td></tr>';
		
		echo '</table>';
	}
?>
<?php include 'headers/bottom_menu.php'; ?>