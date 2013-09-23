<?php 
    include 'headers/admin_top_menu.php'; 
    include 'headers/connection.php';
    session_start();
?>
<?php
	function query( $admin, $code ){
		$con = start_connection();
		if( !$con )
			return FALSE;
		
		$result = mysql_query( "SELECT * FROM questions WHERE admin = '".$admin."' AND code = '".$code."'", $con);
		if( mysql_num_rows( $result ) == 1 ){
            end_connection( $con );
            return TRUE;
        }
        end_connection( $con );
		return FALSE;
	}	

	$N = intval( $_GET["N"] );
	$code = $_GET["code"];
	if( !isset( $_SESSION["adminname"] ) ){
		echo 'Please login to continue.';	
	} else if( preg_match( "/^[0-9]+$/", $N ) == 0 ){
		echo 'Please enter a vaid number.';
	} else if( $N < 1 || $N > 500 ){
		echo 'Please enter the number of testfiles between 1 and 500';
	} else if ( !query( $_SESSION["adminname"], $code ) ){
		echo 'Error in Question code or Admin name.';	
	} else{
		echo '<table align="center" cellspacing="10">';
		echo '<tr><td align="center" class="heading_large" >'.$code.'</td></tr>';
		echo '<tr><td align="center"><h3>Upload '.$N.' Test Files and corresponding Output Files</h3></td></tr>';
		echo '<form action="/admin/serve_upload_testfiles.php" method="post" enctype="multipart/form-data">';
		echo '<tr><td align="center"><input type="hidden" name="code" value="'.$code.'"></td></tr>';
		echo '<tr><td align="center"><input type="hidden" name="N" value="'.$N.'"></td></tr>';
		echo '<tr><td><table align="center" cellspacing="10" class="table">';
		echo '<tr><th align="center">S.No.</th><th align="center">Test Files</th><th align ="center">Output Files</th></tr>';
		for( $i=1; $i<=$N; $i++ ){
			echo '<tr><td align="center">'.$i.'</td>
				<td align="center"><input type="file" name="test'.$i.'" class="upload"></td>
				<td align ="center"><input type="file" name="output'.$i.'" class="upload"></td></tr>';
		}
		echo '<tr><td colspan="3" align="center"><input type="submit" name="submit" value="Submit" class="button"></td></tr>';
		echo '</table></td></tr>';
		echo '</form>';
		echo '</table>';
	}
?>
<?php include 'headers/admin_bottom_menu.php'; ?>