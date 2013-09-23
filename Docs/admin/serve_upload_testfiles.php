<?php
    include 'headers/admin_top_menu.php'; 
    include 'headers/connection.php';
    session_start();
?>
<?php
	function query( $code, $admin, $N, $con ){
		$result = mysql_query( "SELECT * FROM questions WHERE admin = '".$admin."' AND code = '".$code."'", $con);

		if( mysql_num_rows( $result ) != 1 ){
            return FALSE;
        }
		$result = mysql_query( "SELECT * FROM files WHERE code = '".$code."'", $con);
		if( mysql_num_rows( $result ) + $N > 500 ){
            return FALSE;
        }
        
		return TRUE;
	}
	
	function valid( $N ){
		for( $i=1; $i<=$N; $i++ ){
			if( $_FILES["test".$i]["error"] > 0 ){
				echo 'Error uploading files : '.$_FILES["test".$i]["error"];
				return FALSE;
			}
			if( $_FILES["test".$i]["type"] != "text/plain" ){
				echo 'Testfiles must be text files.';
				return FALSE;
			}
			if( $_FILES["test".$i]["size"] == 0 || $_FILES["test".$i]["size"] > 10*( 2<<20 ) ){
				echo 'Please upload a valid file less than 10 MB.';
				return FALSE;
			}
			if( $_FILES["output".$i]["error"] > 0 ){
				echo 'Error uploading files : '.$_FILES["output".$i]["error"];
				return FALSE;
			}
			if( $_FILES["output".$i]["type"] != "text/plain" ){
				echo 'Output files must be text files.';
				return FALSE;
			}
			if( $_FILES["output".$i]["size"] == 0 || $_FILES["output".$i]["size"] > 10*( 2<<20 ) ){
				echo 'Please upload a valid file less than 10 MB.';
				return FALSE;
			}
		}
		return TRUE;
	}
	
	function insert( $con, $code, $test, $output, $admin ){
        $result = mysql_query( 'INSERT INTO files VALUES("'.$code.'", "'.$test.'", "'.$output.'", "'.$admin.'")' );
		return $result; 
	}
	
	$N = intval( $_POST["N"] );
	$code = $_POST["code"];
	if( !isset( $_SESSION["adminname"] ) ){
		echo 'Please login to continue.';
	} else if( preg_match( "/^[0-9]+$/", $N ) == 0 || $N < 1 || $N > 500 ){
		echo 'The number of testfiles should be between 1 and 500';
	} else if( !valid( $N ) ){
		echo 'Error while uploading files.';
	} else if( ( $con = start_connection() ) ){
        lock_two_tables_write( "files", "questions", $con );
        if( !query( $code, $_SESSION["adminname"], $N, $con ) ) {
            echo 'Either there is an error with Problem code or Admin name, or uploading these testfiles will increase the number of testfiles beyond 500.';
        } else{
            if( !is_dir( "upload/".$code ) )
                mkdir( "upload/".$code );
            for( $i=1; $i<=$N; $i++){
                $checked = true;
                if( file_exists( "upload/".$code."/". $_FILES["test".$i]["name"] ) ){
                    echo $_FILES["test".$i]["name"] . " already exists.<br>";
                    $checked = false;
                }
                if( $checked && !move_uploaded_file( $_FILES["test".$i]["tmp_name"], "upload/".$code."/".$_FILES["test".$i]["name"] ) ){
                    $checked = false;
                }
                if( $checked && file_exists( "upload/".$code."/".$_FILES["output".$i]["name"] ) ){
                    echo $_FILES["output".$i]["name"] . " already exists.<br>";
                    $checked = false;
                    if( file_exists( "upload/".$code."/".$_FILES["test".$i]["name"] ) )
                        unlink( "upload/".$code."/".$_FILES["test".$i]["name"] );
                }
                if( $checked && !move_uploaded_file( $_FILES["output".$i]["tmp_name"], "upload/".$code."/".$_FILES["output".$i]["name"] ) ){
                    $checked = false;
                    if( file_exists( "upload/".$code."/".$_FILES["test".$i]["name"] ) )
                        unlink( "upload/".$code."/".$_FILES["test".$i]["name"] );
                }
                if( $checked && !insert( $con, $code, "upload/".$code."/".$_FILES["test".$i]["name"], 
                    "upload/".$code."/".$_FILES["output".$i]["name"], $_SESSION["adminname"] ) ) {
                    if( file_exists( "upload/".$code."/".$_FILES["test".$i]["name"] ) )
                        unlink( "upload/".$code."/".$_FILES["test".$i]["name"] );
                    if( file_exists( "upload/".$code."/".$_FILES["output".$i]["name"] ) )
                        unlink( "upload/".$code."/".$_FILES["output".$i]["name"] );
                    echo 'Error uploading Test and Output files for Serial Number '.$i.'.<br>';
                }
                if( $checked )
                    echo "Test and Output Files for Serial Number ".$i." successfully uploaded.<br>";
            }
            echo 'Click <a href="testfiles.php?code='.$code.'">here</a> to further upload or remove files.<br>';
        }
        unlock_tables( $con );
        end_connection( $con );
    } else {
            echo 'Something went wrong.';
    }
?>
<?php include 'headers/admin_bottom_menu.php'; ?>
