<html>
 <head>
 <Title>Registration Form</Title>
 <style type="text/css">
 	body { background-color: #fff; border-top: solid 10px #000;
 	    color: #333; font-size: .85em; margin: 20; padding: 20;
 	    font-family: "Segoe UI", Verdana, Helvetica, Sans-Serif;
 	}
 	h1, h2, h3,{ color: #000; margin-bottom: 0; padding-bottom: 0; }
 	h1 { font-size: 2em; }
 	h2 { font-size: 1.75em; }
 	h3 { font-size: 1.2em; }
 	table { margin-top: 0.75em; }
 	th { font-size: 1.2em; text-align: left; border: none; padding-left: 0; }
 	td { padding: 0.25em 2em 0.25em 0em; border: 0 none; }
 </style>
 </head>
 <body>
 <h1>Supervision</h1>
 <?php
	 // DB connection info
	 $host = "tcp:bddintern.database.windows.net,1433";
	 $user = "superuser";
	 $pwd = "Pa55w.rd123";
	 $db = "bddintern";
	 // Connect to database.
	 try {
	 	$conn = new PDO( "sqlsrv:Server= $host ; Database = $db ", $user, $pwd);
	 	$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	 }
	 catch(Exception $e){
	 	die(var_dump($e));
	 }

	 if(!empty($_POST)) {
	 try {
	 	$name = $_POST['name'];
	 	$email = $_POST['email'];
	 	$date = date("Y-m-d");
	 	// Insert data
	 	$sql_insert = "INSERT INTO registration_tbl (name, email, date) 
	 				   VALUES (?,?,?)";
	 	$stmt = $conn->prepare($sql_insert);
	 	$stmt->bindValue(1, $name);
	 	$stmt->bindValue(2, $email);
	 	$stmt->bindValue(3, $date);
	 	$stmt->execute();
	 }
	 catch(Exception $e) {
	 	die(var_dump($e));
	 }
	 echo "<h3>Your're registered!</h3>";
	 }


	 $sql_select = "SELECT count(*) FROM dossier where traite=0";
	 $stmt = $conn->query($sql_select);
	 $registrants = $stmt->fetchAll(); 
	 echo "<h3> DOSSIER NON TRAITE".$registrant"</h3>";

	
	 $sql_select = "SELECT count(*) FROM dossier where traite=1";
	 $stmt = $conn->query($sql_select);
	 $registrants = $stmt->fetchAll(); 
	 echo "<h3> DOSSIER TRAITE".$registrant"</h3>";
 ?>
 </body>
 </html>	


 
