<?php 
    include 'headers/admin_top_menu.php';
    include 'headers/connection.php';
    session_start();
?>
<?php

	function questions( $admin ){
		$con = start_connection();
		if( !$con )
			return FALSE;
	
		$result = mysql_query( "SELECT * FROM questions WHERE admin = '".$admin."'", $con);
        end_connection( $con );
        return $result;
	}
	
	if( !isset( $_SESSION[ "adminname" ] ) ){
		echo 'Please login to continue!';
	} else {
		echo '<table align ="center" width="600px" cellspacing="25px">';
		echo '<tr><td align="center"><a href="add_question.php" class="medium">Add a new question</a></td></tr>';
		
		$result = questions( $_SESSION[ "adminname" ] );
		if( !$result )
			echo '<tr><td align="center">Unable to fetch your questions.</td><tr>.';
		else if( mysql_num_rows( $result ) == 0)
			echo '<tr><td align="center">You have not added any questions yet.</td><tr>.';
		else{
			echo '<tr><td><table width="100%" cellspacing="15px" class="table">';
			for( $i=1; $row = mysql_fetch_array( $result ); $i++ ){
				echo '<tr><td align="center">'.$i.'.</td>'. 
				'<td align="center"><a href="admin_question.php?code='.$row["code"].'">'.$row["code"].'</a></td><td align="center">'.$row["name"].'</td>'.
				'<td align="center"><a href="edit_question.php?code='.$row["code"].'">Edit</a></td>'.
				'<td align="center"><a href="testfiles.php?code='.$row["code"].'">Test Files</a></td></tr>';
			}
			echo '</table></td></tr>';
			echo '</div>';
		}
		echo '</table>';
	}
?>
<?php include 'headers/admin_bottom_menu.php'; ?>