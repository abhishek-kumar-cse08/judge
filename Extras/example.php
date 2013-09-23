<script>
function query(){

	if( window.XMLHttpRequest ){
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject( "Microsoft.XMLHTTP" );
	}
	xmlhttp.onreadystatechange = function(){
		if( xmlhttp.readyState == 4 && xmlhttp.status == 200 ){
			alert( xmlhttp.responseText );
			var response = eval("(" + xmlhttp.responseText + ")");
			alert(  response[0]['i'] + " & " + response[1]['we'] );
			alert( "Here -" + response[2] + "-" );
			if( response[2] )
				alert( "Not Null" );
			else
				alert( "Is Null" );
		}
	};

	xmlhttp.open( "POST", "serve.php", true );
	xmlhttp.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
	xmlhttp.send( "" );
}
</script>

<html>
    <body>
          <input type="textfield"></input><br>
        <input type ="button" onclick="query()" value="Click Me"></input>
    </body>
</html>
