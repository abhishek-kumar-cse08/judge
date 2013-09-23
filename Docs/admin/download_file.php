<?php
	$file = $_GET["filename"];
	if ( $file && file_exists($file)) {
	    header('Content-type: text/plain');
		//open/save dialog box
		header('Content-Disposition: attachment; filename="'.end( explode( "/",$file ) ).'"');
		//read from server and write to buffer
		readfile( $file );
	} else 
		echo "File does not exist.";
?>