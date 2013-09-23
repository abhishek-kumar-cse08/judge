<?php
	session_start();
	if( !isset( $_SESSION["adminname"] ) )
		echo 'You have already been logged out!';
	else if( session_destroy() )
		echo 'TRUE';
	else 
		echo 'FALSE';
?>