<html>
	<head>
		<link rel="stylesheet" type="text/css" href="style.css">
		<script>
			function logout(){
				if( window.XMLHttpRequest ){
					xmlhttp = new XMLHttpRequest();
				} else {
					xmlhttp = new ActiveXObject( "Microsoft.XMLHTTP" );
				}
				xmlhttp.onreadystatechange = function(){
					if( xmlhttp.readyState == 4 && xmlhttp.status == 200 ){
						var response = xmlhttp.responseText.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
						if( response == "TRUE" ){
							document.getElementById( 'change' ).innerHTML = '<tr><td><table><tr><td>Username</td><td><input type="text" id="username" class ="text"></td></tr>' +
																			'<tr><td>Password</td><td><input type="password" id="password" class ="text"></td></tr></table></td></tr>' +
																			'<tr><td align = "center" ><input type="button" value="Log In" onclick="check_login()" class ="button"></td></tr>' +
																			'<tr><td id = "status" class = "small" ></td></tr>' +
																			'<tr><td align = "center" ><a href="signup.php" class="medium">Sign Up</a></td></tr>';
							window.location = "/index.php";
						} else if( response == "FALSE" ){
							document.getElementByid( 'status' ).innerHTML = 'Invalid Username/Password.';
						} else {
							alert( response );
						}
					}
				};
				xmlhttp.open("GET","serve_logout.php",true);
				xmlhttp.send();
			}
			function check_login(){
				var user = document.getElementById( "username" ).value;
				var pwd = document.getElementById( "password" ).value;
				if ( user.length == 0 || pwd.length == 0 ){
					alert( "The Username/Password fields can not be left blank." );
					return;
				}
				if( user.length > 20 || pwd.length > 20 ){
					alert( "The Username/Password can not be more than 20 characters in length." );
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
							document.getElementById( 'change' ).innerHTML = '<tr><td align="center"><h2>Hi, ' + user + '!</h2></td></tr>' +
																			'<tr><td align = "center" ><input type="button" value="Log Out" onclick="logout()" class ="button"></td></tr>' +
																			'<tr><td align = "center"><a href = "account.php" class="medium" > My Account </a></td></tr>';
							window.location = "/index.php";
						} else if( response == "FALSE" ){
							document.getElementById( 'status' ).innerHTML = 'Invalid Username/Password.';
						} else {
							alert( response );
						}
					}
				};
				xmlhttp.open("GET","serve_login.php?n_username="+user+"&n_password="+pwd,true);
				xmlhttp.send();
			}
		</script>
	</head>
	<body>
		<table width = "1000px" align = "center" cellspacing ="20px">
			<tr>
				<h1 align = "center">the123 of Coding</h1>
			</tr>
			<tr><td><table width = "1000px" align = "center" class = "large_round" bgcolor = "yellowgreen" ><tr>
				<td align = "center"> <a href="index.php" class="large"> Home </a> </td>
				<td align = "center"> <a href="compete.php" class="large"> Compete </a> </td>
				<td align = "center"> <a href="practise.php" class="large"> Practise </a> </td>
				<td align = "center"> <a href="http://www.topcoder.com/tc?d1=tutorials&d2=alg_index&module=Static" class="large"> Tutorials </a> </td>
			</tr></table></td></tr>
			<tr>
				<td><table cellspacing="20px" width="100%">
					<tr><td width = "240px" valign="top" ><table class = "small_round" bgcolor = "yellowgreen" cellspacing = "2px" >
						<tr><td><table id="change" width="240px" cellspacing = "2px" >
						<?php
							session_start();
							if( isset( $_SESSION["adminname"] ) ){
								echo( '<tr><td align= "center">To sign in as a user, please click <a href = "/admin/admin_index.php" >here</a> to log out from your admin account and login as user.</td></tr>' );

							} else if( isset( $_SESSION["username"] ) ){
								echo( '<tr><td align="center"><h2>Hi, '. $_SESSION["username"] .'!</h2></td></tr>' );
								echo( '<tr><td align = "center" ><input type="button" value="Log Out" onclick="logout()" class ="button"></td></tr>' );
								echo( '<tr><td align = "center"><a href = "account.php" class="medium" > My Account </a></td></tr>' );
							} else{
								echo( '<tr><td><table><tr><td>Username</td><td><input type="text" id="username" class ="text"></td></tr>' );
								echo( '<tr><td>Password</td><td><input type="password" id="password" class ="text"></td></tr></table></td></tr>' );
								echo( '<tr><td align = "center" ><input type="button" value="Log In" onclick="check_login()" class ="button"></td></tr>' );
								echo( '<tr><td id = "status" class = "small" align="center" ></td></tr>' );
								echo( '<tr><td align = "center" ><a href="signup.php" class="medium">Sign Up</a></td></tr>' );
							}
						?>
						</td></tr></table>
					<tr><td align = "center"><a href = "news.php" class="medium" > News </a></td></tr>
					<tr><td align = "center"><a href = "forum.php" class="medium"> Forum </a></td></tr>
					</table></td><td align="centre" width="760px" >
					