<?php 
    include 'headers/admin_top_menu.php'; 
    session_start();            
?>
<script>
	function formSubmit(){
		var code = document.getElementById( "qcode" ).value;
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
		var name_arr = name.split( ' ' );
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
					alert( "Question successfully Added!" );
					window.location = "admin_question.php?code="+code;
				} else if( response == "FALSE" ){
					alert( "Something didn't go right!" );
				} else {
					alert( response );
				}
			}
		};
        
		xmlhttp.open( "POST", "serve_add_question.php", true );
		xmlhttp.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
		xmlhttp.send( query );
	}
</script>
<?php
	if( !isset( $_SESSION[ "adminname" ] ) ){
		echo 'Please login to continue!';
	} else {
		echo '<table align ="center" width="100%" cellspacing="15px">';
		echo '<tr><td>Question Code</td><td><input type="text" id="qcode" class="text"></td></tr>';
		echo '<tr><td>Question Name</td><td><input type="text" id="qname" class="text"></td></tr>';
		echo '<tr><td valign="top">Question Statement</td><td><textarea rows="20" cols="50" id="qstatement" class="text"></textarea></td></tr>';
		echo '<tr><td>Question Display Time<br>(hr min mm dd yy) (24 hr format)</td><td><input type="text" id="qquestion_display" class="text"></td></tr>';
		echo '<tr><td>Submission Display Time<br>(hr min mm dd yy) (24 hr format)</td><td><input type="text" id="qsubmission_display" class="text"></td></tr>';
		echo '<tr><td>Time limit(seconds)</td><td><input type="text" id="qtime" class="text"></td></tr>';
		echo '<tr><td valign="top">Languages</td><td><table>';
		echo '<td width = "60px"><input type="checkbox" name="language" value="c" class="radio"><h3>C</h3></td>';
		echo '<td width = "60px"><input type="checkbox" name="language" value="cplusplus" class="radio"><h3>C++</h3></td>';
		echo '<td width = "60px"><input type="checkbox" name="language" value="java" class="radio"><h3>Java</h3></td>';	
		echo '</table></td></tr>';
		echo '<tr><td>File Size limit(Bytes)</td><td id="file">50000</td></tr>';
		echo '<tr><td>Judge Type</td><td><input type="text" id="judge" class="text" value="Matcher"></td></tr>';
		echo '<tr><td colspan="2" align="center"><input type="button" onclick="formSubmit()" class="button" value="Create Question"></td></tr>';
		echo '</table>';
	}
?>
<?php include 'headers/admin_bottom_menu.php'; ?>
