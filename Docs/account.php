<?php 
    include 'headers/top_menu.php';
    include 'headers/connection.php';
    session_start();
?>
<script>
	function changePassword(){
		document.getElementById( "change_password" ).innerHTML = '<tr><td><h3> Current Password* </h3></td> <td><input type="password" id="cur_pword" class ="text"></td></tr>'
																+'<tr><td><h3> New Password* </h3></td> <td><input type="password" id="new_pword" class ="text"></td></tr>'
																+'<tr><td><h3> Confirm Password* </h3></td> <td><input type="password" id="conf_pword" class ="text"></td></tr>';
                                            
	}	
	function formSubmit(){
		var name = document.getElementById( "name" ).value;
		var inst = document.getElementById( "institute" ).value;
		var query_string = "n_name="+name+"&n_institute="+inst;
		if( document.getElementById( "btn_pword" ) == null ){
			var current_password = document.getElementById( "cur_pword" ).value;
			var password = document.getElementById( "new_pword" ).value;
			var cpassword = document.getElementById( "conf_pword" ).value;
			query_string += "&n_current_password="+current_password+"&n_password="+password+"&n_cpassword="+cpassword;
			if( password.length == 0 || cpassword.length == 0 || current_password.length == 0 ){
				alert( "None of the Password fields can be left blank." );
				return;
			}
			if( password.length > 20 ){
				alert( "The Password can not be more than 20 characters in length" );
				return;
			}
			if( password != cpassword ){
				alert( "Passwords do not match." );
				return;
			}
		} else{
			query_string += "&n_pwd_change=false";
		}
		if( name.length == 0 || inst.length == 0 ){
			alert( "Please fill in all the details before moving ahead." );
			return;
		}
		if( inst.length > 30 ){
			alert( "Please enter an Intitute/Organization not more than 30 characters." );
			return;
		}
		if( name.length > 20 ){
			alert( "Please enter a name not more than 20 characters." );
			return;
		}
		var name_arr = name.split(" ");
		var pattern = new RegExp( "[^a-zA-Z]" );
		for( var i=0; i<name_arr.length; i++ )
			if( pattern.test( name_arr[i] ) ){
				alert( "Pease enter a valid name." );
				return;
			}
		
		var lang;
		for( var i=0; i < 3; i++ )
			if( document.getElementsByName( "language" )[i].checked )
				lang = document.getElementsByName( "language" )[i].value;
		query_string += "&n_language="+lang;
		if( window.XMLHttpRequest ){
			xmlhttp = new XMLHttpRequest();
		} else {
			xmlhttp = new ActiveXObject( "Microsoft.XMLHTTP" );
		}
		xmlhttp.onreadystatechange = function(){
			if( xmlhttp.readyState == 4 && xmlhttp.status == 200 ){
				var response = xmlhttp.responseText.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
				if( response == "TRUE" ){
					alert( "Account successfully updated!" );
					window.location = "/account.php";
				} else if( response == "FALSE" ){
					alert( "Something didn't go right!" );
				} else {
					alert( response );
				}
			}
		};
		xmlhttp.open( "GET", "serve_account.php?"+query_string, true );
		xmlhttp.send();
	}
</script>
<?php 
	function query(){		
        $con;
        if( !( $con = start_connection() ) )
            return FALSE;
		$result = mysql_query( "SELECT * FROM users WHERE username = '".$_SESSION["username"]."'", $con);
		if( !$result ){
			echo( 'Internal Server Error. Could not connect to table.' );
			end_connection( $con );
            return FALSE;
		} else { 
			$row = mysql_fetch_array( $result );
            end_connection( $con );
			return $row;
        }
        end_connection( $con );
		return FALSE;
    }

	$row;
	if( !isset( $_SESSION["username"] ) ){
		echo 'Please login to continue!';
	} else if( ( $row = query() ) == FALSE ){
		//do nothing
	} else{
		echo '<table align ="center" width="600px" cellspacing="15px">';
		echo '<tr><td><h3> Username </h3></td> <td><h2>'.$row["username"].'</h2></td></tr>';
		echo '<tr><td><h3> Name </h3></td> <td><input type="text" id="name" name="n_name" value="'.$row["name"].'" class ="text"></td></tr>';
		echo '<tr><td><h3> E-mail </h3></td> <td>'.$row["email"].'</td></tr>';
		echo '<tr><td><h3> Instutitute/Organization* </h3></td> <td><input type="text" id="institute" name="n_institute" value="'.$row["institute"].'" class ="text"></td></tr>';
		echo '<tr><td valign="top"><h3> Preferred Language* </h3></td> <td><table><tr>';
		if( $row["language"] == "c" )
		    echo '<td width = "60px"><input type="radio" name="language" value="c" checked="checked" class="radio"><h3>C</h3></td>';
		else
			echo '<td width = "60px"><input type="radio" name="language" value="c" class="radio"><h3>C</h3></td>';
		if( $row["language"] == "cplusplus" )
			echo '<td width = "60px"><input type="radio" name="language" value="cplusplus" checked="checked" class="radio"><h3>C++</h3></td>';
		else
			echo '<td width = "60px"><input type="radio" name="language" value="cplusplus" class="radio"><h3>C++</h3></td>';
		if( $row["language"] == "java" )
			echo '<td width = "60px"><input type="radio" name="language" value="java" checked = "checked" class="radio"><h3>Java</h3></td>';
		else
			echo '<td width = "60px"><input type="radio" name="language" value="java" class="radio"><h3>Java</h3></td>';
		echo '</tr></table>';
		echo '<tr><td colspan="2"><table id="change_password" width="100%" align="center" cellspacing="15px">';
		echo '<tr><td align="center"><input id="btn_pword" type="button" value="Change Password" onclick="changePassword()" class="button"></td></tr>';
		echo '</table></td></tr>';
		echo '<br/><br/>';
		echo '<tr><td colspan="2" align="center"><input type="button" value="Save Changes" onclick="formSubmit()" class="button"></td></tr>';
		echo '</table>';
	}
?>
<?php include 'headers/bottom_menu.php'; ?>