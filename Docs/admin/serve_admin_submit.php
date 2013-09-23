<?php 
	include 'headers/admin_top_menu.php'; 
	include 'headers/connection.php';
	session_start();
?>
<?php
    function get_question( $code, $admin, $language ){
        $con = start_connection();
		if( !$con )
            return FALSE;
            
		$result = mysql_query( "SELECT * FROM questions WHERE code = '".$code."' AND admin = '".$admin."'", $con);
		if( !$result ){
			echo 'Internal Server Error. Could not connect to table.';
            end_connection( $con );
            return FALSE;
        }
		if( mysql_num_rows( $result ) == 0 ){
			echo 'You do not have the permission to submit the solution to this question.';
			end_connection( $con );
            return FALSE;
        }
    
        $row = mysql_fetch_array( $result );
		$language_arr = explode( " ", trim( $row["languages"] ) );
        if( !in_array( $language, $language_arr ) ){
            echo 'The solution to this problem can not be submitted in this language';
            end_connection( $con );
            return FALSE;
        }
        end_connection( $con );
        return $row;
    }
    
    if( !isset( $_SESSION["adminname"] ) )
        echo 'Please login to continue.';
    else{
        $admin = $_SESSION["adminname"];
        $code = $_POST["n_code"];
        $language = strtolower( $_POST["n_language"] );
        echo '<table align="center" width="600px" class="table">';
        echo '<tr><td align="center">';
        if(  $_FILES["n_file"]["error"] > 0 )
            echo 'Error uploading files.';
        else if( strlen( $_FILES["n_files"]["name"] ) > 50 ){
            echo 'Please choose a valid file name less than 50 characters';
        }
        else if( $_FILES["n_file"]["size"] > 50000 ){
            echo 'File size exceeded 50000 Byte.';
        }
        else if( ( $row = get_question( $code, $admin, $language ) ) && ( $mysqli = mysqli_start_connection() ) ){
            $mysqli->query( 'SELECT COUNT(1) FROM queue' );
            $result_row = mysqli_fetch_row(  mysqli_use_result( $mysqli ) );
            $num_rows = $result_row[0];
            if( $num_rows <= 200 ){
                    $mysqli->query( "START TRANSACTION" );
                    $query = 'INSERT INTO submissions VALUES ( NULL, "Queued", "'.$code.'", "'.$language.'", "'.$admin.'", NULL )';
                    if( $mysqli->query( $query ) ){
                             if( !is_dir( "submissions" ) )
                                mkdir( "submissions" );
                            $id = mysqli_insert_id( $mysqli );
                            $filename = "submissions/".$id."".$_FILES["n_file"]["name"];
                            if(  $mysqli->query( 'UPDATE submissions SET file = "'.$filename.'" WHERE id = '.$id )  ){
                                    if( $mysqli->query( 'INSERT INTO queue VALUES ( '.$id.', "Queued", "'.$code.'", "'.$language.'", "'.$admin.'", "'.$filename.'" )' ) ){						 if( !is_dir( "submissions" ) )
                									mkdir( "submissions" );	
                                        if( move_uploaded_file( $_FILES["n_file"]["tmp_name"],  $filename ) ){
                                            $mysqli->query( "COMMIT" );
                                            echo 'Solution successfully submitted!<br>
                                                    Go to mysubmissions to check how you did.<br>';
                                        } else {
                                                echo 'Unable to submit file';
                                                $mysqli->query( "ROLLBACK" );
                                        }
                                    } else {
                                             echo 'Error inserting the submission in submission queue';
                                             $mysqli->query( "ROLLBACK" );
                                    }
                            } else{
                                    echo 'Error submitting the solution';
                                    $mysqli->query( "ROLLBACK" );
                            }
                    } else {
                            echo 'Error submitting the solution';
                            $mysqli->query( "ROLLBACK" );
                    }
            } else {
                    echo 'Server is too busy. Please try submitting your solution a little later';
            }
            mysqli_end_connection( $mysqli );
        }
        echo '</td></tr>';
        echo '</table>';
    }
?>
<?php include 'headers/admin_bottom_menu.php'; ?>
