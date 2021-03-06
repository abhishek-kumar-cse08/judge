<?php 
    include 'headers/admin_top_menu.php';
    include 'headers/connection.php';
    session_start();
?>
<script>
	function download( filename ){
		window.open( "download_testfile.php?filename="+filename );
	}	
	function remove_files(){
		var checkboxes = document.getElementsByName( "cb_remove" );
		var string = "";
		for( var i=0; i<checkboxes.length; i++ ){
			if( checkboxes[i].checked == true )
				string += checkboxes[i].getAttribute( "id" ) + " ";
		}
		if( string == "" ){
			alert( "Please select a test case to remove." );
			return;
		}

		if( window.XMLHttpRequest ){
			xmlhttp = new XMLHttpRequest();
		} else {
			xmlhttp = new ActiveXObject( "Microsoft.XMLHTTP" );
		}

		xmlhttp.onreadystatechange = function(){
			if( xmlhttp.readyState == 4 && xmlhttp.status == 200 ){
				var response = xmlhttp.responseText.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
				if( response == "TRUE" ){
					alert( "Test cases successfully removed." );
					var code = document.getElementById( "code" ).innerHTML;
					window.location = "remove_testfiles.php?code="+code; 
				} else if( response == "FALSE" ){
					alert( "Internal Error" );
				} else {
					alert( xmlhttp.responseText );
				}
			}
		};
		xmlhttp.open("POST","serve_remove_testfiles.php",true);
		xmlhttp.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
		xmlhttp.send( "files="+string );
	}
</script>
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
	
	function get_files( $code ){
		$con = start_connection();
		if( !$con )
			return FALSE;
	
        $result = mysql_query( "SELECT * FROM files WHERE code = '".$code."'", $con);
        end_connection( $con );
        return $result; 
	}
	
	$code = $_GET["code"];
	if( !$code ){
		echo 'Please enter a question code to remove tesfiles from.';
	} else if( !isset( $_SESSION["adminname"] ) ){
		echo 'Please login to continue';
	} else if ( !query( $_SESSION["adminname"], $code ) ){
		echo 'Error in Question code or Admin name.';	
	} else {
		echo '<table align="center" cellspacing="20">';
		echo '<tr><td id="code" align="center" class="heading_large" >'.$code.'</td></tr>';
		echo '<tr><td><table align="centre" class="table" cellspacing="10">';
		echo '<tr><th align="center"><h3>S.No</h3></th><th align="center"><h3>Test file</h3></th><th align="center"><h3>Output File</h3></th><th align="center"><h3>Remove</h3></th></tr>';
		$result = get_files( $code );
		if( !$result )
			echo 'Error retrieving the test files for this problem.<br>';
		else{
			for( $i=1; $row = mysql_fetch_array( $result ); $i++ ){
				echo '<tr><td align="center">'.$i.'</td>'.
					 '<td align="center"><input type="button" id="'.$row["testfiles"].'" value="'.end( explode( "/", $row["testfiles"] ) ).'" onclick="download(\''.$row["testfiles"].'\')" class="button_small"></td>'.
					 '<td align="center"><input type="button" id="'.$row["outputfiles"].'" value="'.end( explode( "/", $row["outputfiles"] ) ).'" onclick="download(\''.$row["outputfiles"].'\')" class="button_small"></td>'.
					 '<td align="center"><input type="checkbox" id="'.$row["testfiles"].'" name="cb_remove" class="radio"></td>';					
			}
		}
		echo '</table></td></tr>';
		echo '<tr><td id="code" align="center"><input type="button" class="button" value="Remove Selected Test Cases" onclick="remove_files()"></td></tr>';
		echo '</table>';
	}
?>
<?php include 'headers/admin_bottom_menu.php'; ?>