<?php 
    $ret = shell_exec(  "./matcher output.txt out.txt" );
    if( $ret == 0 )
        echo 'Correct';
    else if( $ret == -1 )
        echo 'Incorrect';
    else
        echo $ret;
?>