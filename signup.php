<?php include 'headers/top_menu.php'; 
            session_start();
?>

<script>
	function formSubmit(){
		var username = document.getElementById( "uname" ).value;
		var password = document.getElementById( "pword" ).value;
		var cpassword = document.getElementById( "cpword" ).value;
		var name = document.getElementById( "name" ).value;
		var email = document.getElementById( "email" ).value;
		var inst = document.getElementById( "institute" ).value;
		
		if( username.length == 0 || password.length == 0 || cpassword.length == 0 || name.length == 0 || email.length == 0 || inst.length == 0 ){
			alert( "Please fill in all the details before moving ahead." );
			return;
		}
		if( username.length > 20 || password.length > 20 ){
			alert( "The Username/Password can not be more than 20 characters in length" );
			return;
		}
		if( password != cpassword ){
			alert( "Passwords do not match." );
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
		var atpos=email.indexOf("@");
		var dotpos=email.lastIndexOf(".");
		if (atpos<1 || dotpos<atpos+2 || dotpos + 2 >= email.length ){
			alert("Please enter a valid e-mail address");
			return;
		}
		var lang;
		for( var i=0; i < 3; i++ )
			if( document.getElementsByName( "language" )[i].checked )
				lang = document.getElementsByName( "language" )[i].value;
		document.getElementById( "submit_form" ).submit();
	}
</script>
<?php
if( isset( $_SESSION["username"] ) ){
		echo "You are already signed in via a different ID. Logout to continue.";
} else {
    echo( '<form id="submit_form" action="serve_signup.php" method="post">' );
    echo( '<table align ="center" width="600px" cellspacing="15px">' );
    echo( '<tr><td><h3> Username* </h3></td> <td><input type="text" id="uname" name="n_uname" class ="text"></td></tr>' );
    echo( '<tr><td><h3> Name* </h3></td> <td><input type="text" id="name" name="n_name" class ="text"></td></tr>' );
    echo( '<tr><td><h3> E-mail* </h3></td> <td><input type="text" id="email" name="n_email" class ="text"></td></tr>' );
    echo( '<tr><td><h3> Instutitute/Organization* </h3></td> <td><input type="text" id="institute" name="n_institute" class ="text"></td></tr>' );
    echo( '<tr>
            <td valign="top"><h3> Preferred Language* </h3></td> <td><table><tr>
                                                   <td width = "60px"><input type="radio" name="language" value="c" checked="checked" class="radio"><h3>C</h3></td>
                                                   <td width = "60px"><input type="radio" name="language" value="cplusplus" class="radio"><h3>C++</h3></td>
                                                   <td width = "60px"><input type="radio" name="language" value="java" class="radio"><h3>Java</h3></td>
                                                   </tr></table></td>
        </tr>' );
        echo( '<tr><td><h3> Password* </h3></td> <td><input type="password" id="pword" name="n_pword" class ="text"></td></tr>' );
        echo( '<tr><td><h3> Confirm Password* </h3></td> <td><input type="password" id="cpword" name="n_cpword" class ="text"></td></tr>' );
        echo( '<br/><br/>' );
        echo( '<tr><td colspan ="2" align="center"><input type="button" value="Submit" onclick="formSubmit()" class="button"></td></tr>' );
        echo( '</table>' );
        echo( '</form>' );
}
?>
<?php include 'headers/bottom_menu.php' ?>