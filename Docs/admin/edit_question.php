<?php 
    include 'headers/admin_top_menu.php'; 
    include 'headers/connection.php';         
    session_start();
?>
<script>
	function formSubmit(){
		var code = document.getElementById( "qcode" ).innerHTML;
		var name = document.getElementById( "qname" ).value;
		var statement = document.getElementById( "qstatement" ).value;
		var time = document.getElementById( "qtime" ).value;
		var question_display = document.getElementById( "qquestion_display"  ).value;
		question_display = question_display.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
		var submission_display = document.getElementById( "qsubmission_display"  ).value;
		submission_display = submission_display.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
		
		var languages = "";
		for( var i=0; i<document.getElementsByName( "language" ).length; i++ ){
			if( document.getElementsByName( "language" )[i].checked )
				languages += document.getElementsByName( "language" )[i].value + " ";
		}
		var file = "50000";
		var judge = document.getElementById( "judge" ).value;
		
		if( code.length == 0 || name.length == 0 || statement.length == 0 || time.length == 0 
				|| file.length == 0 || judge.length == 0 ){
			alert( "None of the input fields can be left blank." );
			return;
		}
		if( languages.length == 0 ){
			alert( "You must choose atleast one language." );
			return;
		}
		
		if( code.length > 20 ){
			alert( "The code name should not be more than 20 characters in length" );
			return;
		}
		if( new RegExp( "[^A-Z0-9]" ).test( code ) ){
			alert( "Each character of the question code must be a capital letter or a number." );
			return;
		}
		var query = "n_code="+code;
		
		if( name.length > 50 ){
			alert( "The question name should not be more than 50 characters in length." );
			return;
		}
		var name_arr = name.split(" ");
		var pattern = new RegExp( "[^a-zA-Z0-9]" );
		for( var i=0; i<name_arr.length; i++ )
			if( pattern.test( name_arr[i] ) ){
				alert( "Pease enter a valid name consisting only alphanumeric characters." );
				return;
			}
		query += "&n_name="+name;			
		
		query += "&n_statement="+statement;
		
		var question_display_arr = question_display.split( /\s+/ );
		if( question_display_arr.length != 5 ){
			alert( "Please enter a valid Question Display time in the format specified" );
			return;
		}
		pattern = new RegExp( "[^0-9]" );
		for( var i=0; i<question_display_arr.length; i++ ){
			if( pattern.test( question_display_arr[i] ) ){
				alert( "Please enter a valid Question Display time in the format specified" );
				return;
			}
		}
		
		if( Number( question_display_arr[0] ) > 23 || Number( question_display_arr[1] ) > 59 || Number( question_display_arr[2] ) > 12 ||
		    Number( question_display_arr[3] ) > 31 || Number( question_display_arr[4] ) < 2000 ){
			    alert( "Please enter a valid Question Display time in the format specified" );
			    return;
		}
		
		query += "&n_question_display="+question_display;
		
		var submission_display_arr = submission_display.split( /\s+/ );
		if( submission_display_arr.length != 5 ){
			alert( "Please enter a valid Submission Display time in the format specified" );
			return;
		}
		pattern = new RegExp( "[^0-9]" );
		for( var i=0; i<submission_display_arr.length; i++ ){
			if( pattern.test( submission_display_arr[i] ) ){
				alert( "Please enter a valid Submission Display time in the format specified" );
				return;
			}
		}
		
		if( Number( submission_display_arr[0] ) > 23 || Number( submission_display_arr[1] ) > 59 || Number( submission_display_arr[2] ) > 12 ||
		    Number( submission_display_arr[3] ) > 31 || Number( submission_display_arr[4] ) > 3000 ){
			    alert( "Please enter a valid Question Display time in the format specified" );
			    return;
		    }
		
		query += "&n_submission_display="+submission_display;
		
		if( new RegExp( "[^0-9]" ).test( time ) ){
			alert( "Please enter a numerical value for the time limit" );
			return;
		}
		if( time.length > 2 ){
			alert( "The time limit can not be set greater than 99 seconds." );
			return;
		}
		query += "&n_time="+time;
		query += "&n_languages="+languages;
		query += "&n_file=50000";
		query += "&n_judge="+judge;

		if( window.XMLHttpRequest ){
			xmlhttp = new XMLHttpRequest();
		} else {
			xmlhttp = new ActiveXObject( "Microsoft.XMLHTTP" );
		}
		xmlhttp.onreadystatechange = function(){
			if( xmlhttp.readyState == 4 && xmlhttp.status == 200 ){
				var response = xmlhttp.responseText.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
				if( response == "TRUE" ){
					alert( "Question successfully updated!" );
					window.location = "admin_question.php?code="+code;
				} else if( response == "FALSE" ){
					alert( "Something didn't go right!" );
				} else {
					alert( response );
				}
			}
		};
		xmlhttp.open( "POST", "serve_edit_question.php", true );
		xmlhttp.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
		xmlhttp.send( query );
	}
</script>
<?php
	function get_string_date( $timestamp ){
		return date( "H i m d Y", $timestamp );
	}

	function query( $code ){
		$con = start_connection();
		if( !$con )
			return FALSE;
		
		$result = mysql_query( "SELECT * FROM questions WHERE code = '".$code."'", $con);
		if( !$result ){
			echo 'Internal Server Error. Could not connect to table.';
			end_connection( $con );
			return FALSE;
		}
		if( mysql_num_rows( $result ) == 0 ){
			echo 'Question code does not exist. Please choose a different question code.';
			end_connection( $con );
			return FALSE;
		}
		$row = mysql_fetch_array( $result );
		if( $row["admin"] != $_SESSION["adminname"] ){
			echo 'You do not have the permission to edit this question.';
			end_connection( $con );
			return FALSE;
		}
		end_connection( $con );
		return $row;
	}
	
	$code = $_GET["code"];
	if( !isset( $_SESSION[ "adminname" ] ) ){
		echo 'Please login to continue!';
	} else if( !$_GET["code"] ) {
		echo 'Please enter a question code to edit.';
	} else if( $row = query( $code ) ) {
		echo '<table align ="center" width="100%" cellspacing="15px">';
		echo '<tr><td>Question Code</td><h3><td id="qcode">'.$code.'</td></h3></tr>';
		echo '<tr><td>Question Name</td><td><input type="text" id="qname" value="'.$row["name"].'" class="text"></td></tr>';
		echo '<tr><td valign="top">Question Statement</td><td><textarea rows="20" cols="50" id="qstatement" class="text">'.$row["statement"].'</textarea></td></tr>';
		echo '<tr><td>Question Display Time<br>(hr min mm dd yy) (24 hr format)</td><td><input type="text" id="qquestion_display" 
			value="'.get_string_date( $row["question_time"] ).'"class="text"></td></tr>';
		echo '<tr><td>Submission Display Time<br>(hr min mm dd yy) (24 hr format)</td><td><input type="text" id="qsubmission_display" 
			value="'.get_string_date( $row["submission_time"] ).'"class="text"></td></tr>';
		echo '<tr><td>Time limit(seconds)</td><td><input type="text" id="qtime" value="'.$row["time"].'" class="text"></td></tr>';
		
		$languages = $row["languages"];
		$language_arr = explode( " ", trim( $languages ) );
		echo '<tr><td valign="top">Languages</td><td><table>';
		if( in_array( "c", $language_arr ) )
			echo '<td width = "60px"><input type="checkbox" name="language" value="c" checked="checked" class="radio"><h3>C</h3></td>';
		else 
			echo '<td width = "60px"><input type="checkbox" name="language" value="c" class="radio"><h3>C</h3></td>';
		if( in_array( "cplusplus", $language_arr ) )
			echo '<td width = "60px"><input type="checkbox" name="language" value="cplusplus" checked="checked" class="radio"><h3>C++</h3></td>';
		else
			echo '<td width = "60px"><input type="checkbox" name="language" value="cplusplus" class="radio"><h3>C++</h3></td>';	
		if( in_array( "java", $language_arr ) )
			echo '<td width = "60px"><input type="checkbox" name="language" value="java" checked="checked" class="radio"><h3>Java</h3></td>';	
		else
			echo '<td width = "60px"><input type="checkbox" name="language" value="java" class="radio"><h3>Java</h3></td>';				
		echo '</table></td></tr>';
		
		echo '<tr><td>File Size limit(Bytes)</td><td id="file">50000</td></tr>';
		echo '<tr><td>Judge Type</td><td><input type="text" id="judge" class="text" value="'.$row["judge"].'"></td></tr>';
		echo '<tr><td colspan="2" align="center"><input type="button" onclick="formSubmit()" class="button" value="Update Question"></td></tr>';
		echo '</table>';
	}
?>
<?php include 'headers/admin_bottom_menu.php'; ?>