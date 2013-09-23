<?php 
    include 'headers/admin_top_menu.php'; 
    session_start();
?>
<script>
	function remove_files(){
		var code = document.getElementById( "code" ).innerHTML;
		window.location = "remove_testfiles.php?code="+code;
	}

	function add_files(){
		if( !new RegExp("^[0-9]+$").test( document.getElementById( "number" ).value ) ){
			alert( "Please enter a valid number" );
			return;
		}
		var N = Number( document.getElementById( "number" ).value );
		if( N > 500 || N < 1 ){
			alert( "The number of test files must be in the range 1 to 500" );
		} else{
			var code = document.getElementById( "code" ).innerHTML;
			window.location = "upload_testfiles.php?code="+code+"&N="+N;
		}
	}
	
	function upload(){
		document.getElementById( "testfile" ).innerHTML = 
			'<tr><td align="center" class="medium">Number of files to upload</td>'+
			'<td align="center"><input id="number" type="text" class="text"></td></tr>'+
			'<tr><td colspan="2" align="center"><input type="button" value="Go" class="button" onclick="add_files()"></td></tr>'+
			'<tr><td colspan="2"><table align="center" id="upload_form" cellspacing="15"></table></td></tr>';
	}
</script>

<?php 
	if( !isset( $_SESSION["adminname"] ) )
		echo 'Please login to continue';
	else if( !$_GET["code"] ){
		echo 'Please select a question to add Test Files to.';
	} else{
		$code = $_GET["code"];
		echo '<table align="center" cellspacing="20">';
		echo '<tr><td id="code" align="center" class="heading_large" >'.$code.'</td></tr>';
		echo '<table align="center" id="testfile" cellspacing="15">';
		echo '<tr><td align="center"><input type="button" value="Remove Testfiles" class="button" onclick="remove_files()"></td></tr>';
	  	echo '<tr><td align="center"><input type="button" value="Upload Testfiles" class="button" onclick="upload()"></td></tr>';
		echo '</table>';
	  	echo '</table>';
	}
?>
<?php include 'headers/admin_bottom_menu.php'; ?>