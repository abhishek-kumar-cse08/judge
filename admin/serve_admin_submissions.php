<?php
	include 'headers/connection.php';         
	session_start();
?>

<?php
	$code = $_GET["code"];
	$type = $_GET["type"];
	$offset = $_GET["offset"];
	$ranges = $_GET["ranges"];
    
    function check_query( $code, $admin, $type, $offset, $ranges ){
            $con = start_connection();
            $res = mysql_query( 'SELECT * FROM questions WHERE code = "'.$code.'" AND admin = "'.$admin.'"', $con );
            
            if( !$con || !$res )
                return '-1 Internal Error.';
            
            if( mysql_num_rows( $res ) == 0 ){
                end_connection( $con );
                return '-1 You do not have the permission to view the submissions for this question';
            }
            
            if( $type == "all" )
                $res = mysql_query( 'SELECT * FROM submissions WHERE question = "'.$code.'" LIMIT '.$offset.',  '.$ranges, $con ); 
            else if( $type == "my" )
                $res = mysql_query( 'SELECT * FROM submissions WHERE question = "'.$code.'" AND user = "'.$admin.'" LIMIT '.$offset.',  '.$ranges, $con );
            else
                $res = mysql_query( 'SELECT * FROM submissions WHERE question = "'.$code.'" AND verdict = "Correct" LIMIT '.$offset.',  '.$ranges, $con );
            
            $N = mysql_num_rows( $res );
            $arr = array_fill( 0, $ranges, null );
            for( $i=0; $i<$N; $i++ )
                $arr[$i] = mysql_fetch_array( $res );
            
            end_connection( $con );
            return $arr;
    }
    
	if( !$code || !$type || !$ranges )
		echo '-1 Invalid url';
	else if( $type != "all" && $type != "my" && $type != "correct" )
		echo '-1 Invalid url';
	else if( intval( $offset ) < 0 || intval($ranges) > 100 )
		echo '-1 Invalid url';
	else if( !isset( $_SESSION["adminname"] ) )
		echo '-1 Please login to continue';
	else{
        $ret = check_query( $code, $_SESSION["adminname"], $type, $offset, $ranges ) ;
        if( strstr( $ret, "-1") )
            echo $ret;
        else
            echo json_encode( $ret );
    }
?>
