<?php 
    include 'headers/connection.php';         
    session_start();
?>
<?php
	$code = $_POST["n_code"];
	$name = $_POST["n_name"];
	$statement = $_POST["n_statement"];
	$time = $_POST["n_time"];
	$languages = $_POST["n_languages"];
	$file = $_POST["n_file"];
	$judge = $_POST["n_judge"];
	$question_display = $_POST["n_question_display"];
	$submission_display = $_POST["n_submission_display"];
		
    function check( $s ){
            $tok = strtok( $s,  " \t" );
            $counts = 0;
            while( $tok != false ){
                    if( preg_match( "/[^0-9]/", $tok ) )
                        return false;
                    switch( $counts ){
                            case 0 :    if( intval( $tok ) > 23 )
                                                return false;
                                            break;
                            case 1 :    if( intval( $tok ) > 59 )
                                                return false;
                                            break;
                            case 2 :    if( intval( $tok ) > 12 )
                                                return false;
                                            break;
                            case 3 :    if( intval( $tok ) > 31 )
                                                return false;
                                            break;
                    }
                    $counts++;
                    $tok = strtok( " \t" );
            }
            if( $counts != 5 )
                return false;
            return true;
    }
    
    function find_time( $s ){
            $hr; $min; $sec; $MM; $DD; $YY;
            $tok = strtok( $s,  " \t" );
            $counts = 0;
            while( $tok != false ){
                    switch( $counts ){
                            case 0 :    $hr = intval( $tok );
                                            break;
                            case 1 :    $min = intval( $tok );
                                            break;
                            case 2 :    $MM = intval( $tok );
                                            break;
                            case 3 :    $DD = intval( $tok );
                                            break;
                            case 4 :    $YY = intval( $tok );
                                            break;
                    }
                    $counts++;
                    $tok = strtok( " \t" );
            }
            return mktime( $hr, $min, 0, $MM, $DD, $YY );
    }

    function validate( $admin, $code, $name, $statement, $time, $languages, $file, $judge, $question_display, $submission_display ){
		if( strlen( $admin ) == 0 || strlen( $code ) == 0 || strlen( $name ) == 0 || strlen( $statement ) == 0
				|| strlen( $time ) == 0 || strlen( $languages ) == 0 || strlen( $file == 0 ) || strlen( $judge ) == 0 ){
			echo "None of the input fields can be left blank.";
			return false;
		}
		
		if( strlen( $code ) > 20 ){
			echo 'The code name should not be more than 20 characters in length.';
			return false;
		}
		if( preg_match( "/[^A-Z0-9]/",  $code ) ){
			echo 'Each character of the question code must be a capital letter or a number.';
			return;
		}
		
		if( strlen( $name ) > 50 ){
			echo 'The question name should not be more than 50 characters in length.';
			return false;
		}	
		
		$name_arr = explode( " ", $name );
		for( $i=0; $i<count( $name_arr ); $i++ )
			if( preg_match( "/[^a-zA-Z0-9]/", $name_arr[$i] ) ){
				echo 'Please enter a valid name consisting only alphanumeric characters';
				return false;
			}
		
		if( !check( $question_display ) ){
		    echo 'Please enter a valid question display time in the format specified';
		    return false;
		}
		
		if( !check( $submission_display ) ){
		    echo 'Please enter a valid question display time in the format specified';
		    return false;
		}
        
		if( preg_match( "/[^0-9]/", $time ) ){
			echo 'Please enter a numerical value for the time limit.';
			return;
		}
		if( strlen( $time ) > 2 ){
			echo 'The time limit can not be greater than 99 seconds.';
			return false;
		}
		return true;
	}
	
	function check_insert( $admin, $code, $name, $statement, $time, $languages, $file, $judge, $question_display, $submission_display ){
		$con = start_connection();
		if( !$con )
			return FALSE;
		lock_tables_write( "questions", $con );
		$result = mysql_query( "SELECT * FROM questions WHERE code = '".$code."'", $con);
		if( !$result ){
			echo 'Internal Server Error. Could not connect to table.';
		unlock_tables( $con );
		end_connection( $con );
			return FALSE;
		}
		if( mysql_num_rows( $result ) > 0 ){
			echo 'Question code already exists. Please choose a different question code.';
			unlock_tables( $con );
			end_connection( $con );
			return FALSE;
		}
		
		$question_time = find_time( $question_display );
		$submission_time = find_time( $submission_display );
		
		if( !$question_time || !$submission_time ){
			echo 'Invalid Question or Submissin Display date';
			return false;
		}
        
		$sql = "INSERT INTO questions VALUES( '".$code."', '".$name."', '".$statement."', '".$time."', '".$languages."', '".
			$file."', '".$judge."', '".$admin."', ".$question_time.", ".$submission_time.", 0, 0 )";
		if( !mysql_query( $sql, $con ) ){
			echo 'Internal Server Error. Could not create database entry.';
			unlock_tables( $con );
			end_connection( $con );
			return FALSE;
		}
		
		unlock_tables( $con );
		end_connection( $con );
		return TRUE;
	}
	
	if( !isset( $_SESSION["adminname"] ) ){
		echo 'Please log in to continue!';
	} else {
		$admin = $_SESSION["adminname"];
		if( validate( $admin, $code, $name, $statement, $time, $languages, $file, $judge, $question_display, $submission_display ) )
			if( check_insert( $admin, $code, $name, $statement, $time, $languages, $file, $judge, $question_display, $submission_display ) ){
				echo "TRUE";
			}
	}
?>
