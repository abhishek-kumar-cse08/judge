<?php
 	$my_array = array_fill( 0, 3, null );
 	$my_array[0] = array( 'i'=>'u' );
 	$my_array[1] = array( 'we'=>'all' );
 	echo json_encode( $my_array  );
?>