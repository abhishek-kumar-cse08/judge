 <?php
    function start_connection(){
        $con = mysql_connect( "localhost", "root", "sureshchandra" );
        if( !$con ){
            echo('Could not connect: ' . mysql_error());
            return FALSE;
        }
        if( !mysql_select_db( "coding", $con ) ){
            echo( 'Internal Server Error. Could not connect to database.' );
            return FALSE;
        }
        return $con;
    }
    
    function end_connection( $con ){
            mysql_close( $con );
    }

    function begin_transaction( $con ){
            mysql_query( "SET TRANSACTION ISOLATION LEVEL REPEATABLE READ", $con );
            mysql_query( "START TRANSACTION", $con );
    }
    
    function commit_transaction( $con ){
            mysql_query( "COMMIT", $con );
    }

    function rollback_transaction( $con ){
            mysql_query( "ROLLBACK", $con );
    }

    function lock_tables_write( $tables, $con ){
            mysql_query( "LOCK TABLES ".$tables." WRITE", $con );
    }

    function lock_two_tables_write( $tables_1, $tables_2, $con ){
            mysql_query( "LOCK TABLES ".$tables_1." WRITE, ".$tables_2." WRITE", $con );
    }

    function unlock_tables( $con ){
            mysql_query( "UNLOCK TABLES", $con );
    }
    
    function mysqli_start_connection( ){
            $mysqli = new mysqli( "localhost", "root", "sureshchandra", "coding" );
            if ( mysqli_connect_errno() ) {
                echo "Object Connect failed: ". mysqli_connect_error();
                return FALSE;
            }
            return $mysqli;
    }

    function mysqli_end_connection( $mysqli ){
            $mysqli->close();
    }
?>
