<?php 
    include 'headers/admin_top_menu.php';
    include 'headers/connection.php';
    session_start();
?>
<script>
    function formSubmit(){
    	if( document.getElementById( "file" ).value == "" )
    		alert( "Please choose the source file" );
    	else
    		document.getElementById( "form" ).submit();	    		
    }
</script>
<?php 
    function query( $code, $admin ){
		$con = start_connection();
		if( !$con ){
			return FALSE;
		}
		
		$result = mysql_query( "SELECT * FROM questions WHERE code = '".$code."' AND admin = '".$admin."'", $con);
		if( !$result ){
			echo 'Internal Server Error. Could not connect to table.';
			return FALSE;
		}
		if( mysql_num_rows( $result ) == 0 ){
			echo 'Question code does not exist or you do not have the permission to view this question.';
			return FALSE;
		}
		return mysql_fetch_array( $result );
	} 
    
    if( !isset( $_SESSION["adminname"] ) ){
		echo 'Please login to continue';
	} else if( !$_GET["code"] ) {
		echo 'Please provide a question to view.';
	} else if( $row = query( $_GET["code"],  $_SESSION["adminname"] ) ) {
		$code = $_GET["code"];
        $languages = explode( " ", trim( $row["languages"] ) );
        echo '<form action="serve_admin_submit.php" id="form" method="post" enctype="multipart/form-data">';
        echo '<table align="center" width = "600px" cellspacing="15px" >';
        echo '<input type="hidden" id="qcode" name="n_code" value="'.$code.'">';
        echo '<tr><td colspan="2" align="center" class="large">'.$code.'</td></tr>';
        echo '<tr><td align ="center" valign="top" class="medium">Source File</td><td align="center"><input type="file" name="n_file" id="file" class="upload" ><br></td></tr>';
        echo '<tr><td align ="center" class="medium">Language</td><td align="center"><select id="language" name="n_language">';
        echo '<option color="#FFFFFF">'.strtoupper( $_SESSION["language"] ).'</option>';
        for( $i=0; $i<count( $languages ); $i++ )
            if( $languages[$i] != $_SESSION["language"] )
                echo '<option color="#FFFFFF">'.strtoupper( $languages[$i] ).'</option>';
        echo '</select></td></tr>';
        echo '<tr><td colspan="2" align="center"><input type="button" class="button" onclick="formSubmit()" value="Submit"></td></tr>';
        echo '</table>';
        echo '</form>';
	}
?>
<?php include 'headers/admin_bottom_menu.php'; ?>
