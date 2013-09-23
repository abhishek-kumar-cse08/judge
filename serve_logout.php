<?php
	session_start();
	if( !isset( $_SESSION["username"] ) )
		echo 'You have already been logged out!';
	else if( session_destroy() )
		echo 'TRUE';
	else 
		echo 'FALSE';
?>