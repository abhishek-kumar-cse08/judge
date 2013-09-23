<?php
	passthru( "gcc example.c -o example", $ret );
	echo "Return Value : " . $ret . "<br>";
	$descriptorspec = array(
   	0 => array("file", "1" , "r"), 
  	 	1 => array("file", "out_1", "w" ), 
   	2 => array("file", "error-output.txt", "a")
	);
	$ret = proc_open( "./example", $descriptorspec, $pipes, NULL, NULL );
    sleep( 2 );
    $status = proc_get_status( $ret );
    if( $status["running"] )
        echo 'Process is still running';
    else 
        echo 'Process stpped';
	passthru( "./example <1 >garbage", $fin_ret ); 
	echo $ret ." & ". $fin_ret;
?>
