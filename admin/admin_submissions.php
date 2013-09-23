<?php
	include 'headers/admin_top_menu.php'; 
	include 'headers/connection.php';         
	session_start();
?>

<script>
	var offset = 0;
	var ranges = 20;
	var stop = 0;
    
	function download( filename, question ){
		window.open( "admin_download_submissions.php?filename="+filename+"&question="+question );
	}
	
	function get_records( code, type, direction ){
        
		if( direction == "-" ){
		    if( offset - 2*ranges < 0 ){
			alert( "Can not move further back" );
			return;
		    } else
			offset -= 2*ranges;
			stop = 0;
		}
		if( stop ){
			alert( "No more submissions to display" );
			return;
		}
		
		if( window.XMLHttpRequest ){
			xmlhttp = new XMLHttpRequest();
		} else {
			xmlhttp = new ActiveXObject( "Microsoft.XMLHTTP" );
		}
		xmlhttp.onreadystatechange = function(){
		if( xmlhttp.readyState == 4 && xmlhttp.status == 200 ){
		var response = xmlhttp.responseText.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
		//alert( response );
		if( response.indexOf( "-1" ) != -1 && response.indexOf( "-1" ) <= 2 ){
			alert( response.substring( 3 ) );
			return;
		}
		response = eval("(" + response + ")");
		var inHtml = '<table align="center" width="100%" cellspacing="5px" class="table">' + 
				    '<tr><th align="center" class="small">ID</th><th align="center" class="small">Question</th><th align="center" class="small">User</th>' +
				    '<th align="center" class="small">Verdict</th><th align="center" class="small">Language</th></tr>';
		var i;
		for( i=0; response[i]; i++ ){
			inHtml += "<tr><td align='center'><input type='button' value='" + response[i]['id'] + "' onclick=\"download('" + response[i]['file'] + "', '" + response[i]['question'] + "')\" class='button_small'>" +
			"</input></td><td align='center'><a href='admin_question.php?code=" + response[i]['question'] + "'>" + response[i]['question'] + 
			"</a></td><td align='center'>" + response[i]['user']+"</td><td align='center'>" + 
			response[i]['verdict'] +"</td><td align='center'>" + response[i]['language'] + "</td></tr>";
		}
		if( i != ranges )
		    stop = 1;
		if( i == 0 ){
		    alert( "No more submissions to display" );
		    offset -= ranges;
		    return;
		}
	    
		inHtml +=  '</table?\>';
		document.getElementById( "inner" ).innerHTML = inHtml;
		}
	};
        
	xmlhttp.open( "GET", "serve_admin_submissions.php?code="+code+"&type="+type+"&offset="+offset+"&ranges="+ranges, true );
	xmlhttp.send(  );
    
        offset += ranges;
    }
</script>

<?php
	$code = $_GET["code"];
	$type = $_GET["type"];
	if( !isset( $_SESSION["adminname"] ) )
		echo ' Please login to continue';
	else if( !$code || !$type )
		echo 'Invalid request';
	else{
        echo '<span id="inner"></span>';
        echo '<br>';
        echo '<table align="center" width="100%" cellspacing="5px">';
        echo '<tr><td align="center"><input type="button" onclick="get_records( \''.$code.'\', \''.$type.'\', \'+\' )" class="button" value="Next"></input></td>';
        echo '<td align="center"><input type="button" onclick="get_records( \''.$code.'\', \''.$type.'\', \'-\' )" class="button" value="Previous"></input></td></tr>';
        echo '</table>';
        echo '<script>get_records( "'.$code.'", "'.$type.'", "+" );</script>';
	}
?>

