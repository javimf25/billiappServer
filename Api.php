<?php 
        //getting the database connection
 require_once 'DbConnect.php';
 
 //an array to display response
 $response = array();
 
 //if it is an api call 
 //that means a get parameter named api call is set in the URL 
 //and with this parameter we are concluding that it is an api call 
 if(isset($_GET['apicall'])){
 
 switch($_GET['apicall']){
 
 case 'signup':
 
if(isTheseParametersAvailable(array('username','email','password'))){
 
 //getting the values 
 $username = $_POST['username']; 
 $email = $_POST['email']; 
 $password = $_POST['password'];
 $club = $_POST['club'];

 
 //checking if the user is already exist with this username or email
 //as the email and username should be unique for every user 
 $stmt = $conn->prepare("SELECT id FROM users WHERE UserName = ? OR email = ?");
 $stmt->bind_param("ss", $username, $email);
 $stmt->execute();
 $stmt->store_result();
 
 //if the user already exist in the database 
 if($stmt->num_rows > 0){
 $response['error'] = true;
 $response['message'] = 'User already registered';
 $stmt->close();
 }else{
 
 //if user is new creating an insert query 
 if($club=="0"){
$stmt = $conn->prepare("INSERT INTO users (UserName,Password,email,idClub) VALUES (?, ?, ?, ?)");
 $stmt->bind_param("ssss", $username, $password, $email, "999");
 }
 else{
 $stmt = $conn->prepare("INSERT INTO users (UserName,Password,email,idClub) VALUES (?, ?, ?, ?)");
 $stmt->bind_param("ssss", $username, $password, $email, $club);
 }
 
 //if the user is successfully added to the database 
 if($stmt->execute()){
 
 //fetching the user back 
 $stmt = $conn->prepare("SELECT  UserName, Password, email FROM users WHERE UserName = ?"); 
 $stmt->bind_param("s",$username);
 $stmt->execute();
 $stmt->bind_result($username, $password, $email);
 $stmt->fetch();
 
 $user = array(
 'id'=>NULL, 
 'username'=>$username, 
 'email'=>$email,
 'password'=>$password
 );
 
 $stmt->close();
 
 //adding the user data in response 
 $response['error'] = false; 
 $response['message'] = 'User registered successfully'; 
 $response['user'] = $user; 
 }
 }
 
 }else{
 $response['error'] = true; 
 $response['message'] = 'required parameters are not available'; 
 }
 
 break; 
 
 case 'login':
 
 //for login we need the username and password 
 if(isTheseParametersAvailable(array('username', 'password'))){
 //getting values 
 $username = $_POST['username'];
 $password = $_POST['password']; 
$id="";
 //creating the query 
 $stmt = $conn->prepare("SELECT UserName,id FROM users WHERE UserName = ? AND Password = ?");
 $stmt->bind_param("ss",$username,$password);
 
 $stmt->execute();
 
 $stmt->store_result();
 
 //if the user exist with given credentials 
 if($stmt->num_rows > 0){
 
 $stmt->bind_result($username, $id);
 $stmt->fetch();
 
 $user = array(
 'id'=>$id, 
 'username'=>$username, 
 );
 
 $response['error'] = false; 
 $response['message'] = 'Login successfull'; 
 $response['user'] = $user; 
 }else{
 //if the user not found 
 $response['error'] = false; 
 $response['message'] = 'Invalid username or password';
 }
 }
 
 break;
 
  case 'getclubs':
 
 //for login we need the username and password 

 //creating the query 
 $stmt = $conn->prepare("SELECT Nom,id FROM club");
 $stmt->execute();
 $result=$stmt->get_result();
 //if the user exist with given credentials 
 $responses=array();
     while($row = $result->fetch_assoc()) {
        $responses[] = array(
			 'id'=>$row['id'], 
			 'nom'=>$row['Nom'], 
        );  
	 }

 $response['error'] = false; 
 $response['message'] = 'Login successfull'; 
 $response['clubs'] = $responses; 


 
 
 break;
 
   case 'getclubsrank':
 
 //for login we need the username and password 

 //creating the query 
 $stmt = $conn->prepare("select c.Nom, SUM(u.punts) as sum_points from club c inner join users u on c.id = u.idClub group by c.id order by sum_points desc");
 $stmt->execute();
 $result=$stmt->get_result();
 //if the user exist with given credentials 
 $responses=array();
     while($row = $result->fetch_assoc()) {
        $responses[] = array(
			 'nom'=>$row['Nom'], 
			 'sumapunts'=>$row['sum_points']
        );  
	 }

 $response['error'] = false; 
 $response['message'] = 'Login successfull'; 
 $response['clubs'] = $responses; 


 
 
 break;
 
case 'getcampionat':
 
 //for login we need the username and password 
$id=$_POST['id'];
 //creating the query 
 $stmt = $conn->prepare("SELECT * FROM competicio WHERE id=?");
 $stmt->bind_param("s",$id);
 $stmt->execute();
 $stmt->store_result();
 $stmt->bind_result($id, $nom, $data,$modalitat,$arbit,$jornada);
 $stmt->fetch();
 
 $campionat = array(
 'id'=>$id, 
 'nom'=>$nom, 
 'data'=>$data,
 'modalitat'=>$modalitat,
 'arbit'=>$arbit,
 'jornada'=>$jornada
 
 );
 
 //adding the user data in response 
 $response['error'] = false; 
 $response['message'] = 'User registered successfully'; 
 $response['user'] = $campionat; 
 
 $stmt->close();

 break;

 case 'getmatchesuser':

$id=$_POST['id']; ;
 //creating the query 
 $stmt = $conn->prepare("SELECT PuntsJugadorFinal1,PuntsJugadorFinal2,NomJugador1,NomJugador2,PuntuacioFinalJ1,PuntuacioFinalJ2,PromigJ1,PromigJ2,parcialsJ1,parcialsJ2,DataPartida,CompeticioId FROM partida WHERE idJugador1=? OR idJugador2=?");
 $stmt->bind_param("ss",$id,$id);
 $stmt->execute();
  $result=$stmt->get_result();
 //if the user exist with given credentials 
 if($result->num_rows > 0){
 
 //$stmt->bind_result($PuntsJugadorFinal1, $PuntsJugadorFinal2,$NomJugador1,$NomJugador2,$PuntuacioFinalJ1,$PuntuacioFinalJ2,$PromigJ1,$PromigJ2,$parcialsJ1,$parcialsJ2);
 //$stmt->fetch();
 $responses=array();
     while($row = $result->fetch_assoc()) {
        $responses[] = array(
			 'nomjugador1'=>$row['NomJugador1'], 
			 'nomjugador2'=>$row['NomJugador2'], 
			 'PuntsJugadorFinal1'=>$row['PuntsJugadorFinal1'],
			 'PuntsJugadorFinal2'=>$row['PuntsJugadorFinal2'],
			 'PuntuacioFinalJ1'=>$row['PuntuacioFinalJ1'],
			 'PuntuacioFinalJ2'=>$row['PuntuacioFinalJ2'],
			 'PromigJ1'=>$row['PromigJ1'],
			 'PromigJ2'=>$row['PromigJ2'],
			 'parcialsJ1'=>$row['parcialsJ1'],
			 'parcialsJ2'=>$row['parcialsJ2'],
			'Data'=>$row['DataPartida'],
			'Competicio'=>$row['CompeticioId']
        );  
	 }
 
 $response['error'] = false; 
 $response['message'] = 'Login successfull'; 
 $response['user'] = $responses; 
 }else{
 //if the user not found 
 $response['error'] = false; 
 $response['message'] = 'Invalid username or password';
 }
 
 
 break;  
 
 case 'savematch':
 

 $vectorfinalJ1 = $_POST['vectorfinalJ1'];
 $vectorfinalJ2 = $_POST['vectorfinalJ2'];
 $vectorparcialJ1=$_POST['vectorparcialJ1'];
 $vectorparcialJ2=$_POST['vectorparcialJ2'];
 $resultatFinalJ1=$_POST['resultatFinalJ1'];
 $resultatFinalJ2=$_POST['resultatFinalJ2'];
 $idJ1=$_POST['idJ1'];
 $idJ2=$_POST['idJ2'];
 $promigJ1=$_POST['promigJ1'];
 $promigJ2=$_POST['promigJ2'];
 $nomJ1=$_POST['NomJ1'];
 $nomJ2=$_POST['NomJ2'];
 $tipus=$_POST['tipus'];
 $stmt = $conn->prepare("INSERT INTO partida(idJugador1,idJugador2,PuntsJugadorFinal1,PuntsJugadorFinal2,NomJugador1,NomJugador2,PuntuacioFinalJ1,PuntuacioFinalJ2,PromigJ1,PromigJ2,parcialsJ1,parcialsJ2)
 VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
 $stmt->bind_param("ssssssssssss",$idJ1,$idJ2,$vectorfinalJ1,$vectorfinalJ2,$nomJ1,$nomJ2,$resultatFinalJ1,$resultatFinalJ2,$promigJ1,$promigJ2,$vectorparcialJ1,$vectorparcialJ2);
 
 $stmt->execute();
 
 $stmt->store_result();
 $stmt->close();
 if($resultatFinalJ1>=50){
 
 $stmt=$conn->prepare("UPDATE users SET Punts = Punts + 10  WHERE id=?");
 $stmt->bind_param("s",$idJ1);
 $stmt->execute();
 $stmt->store_result();
 $stmt->close();
 $response['error'] = false; 
 $response['message'] = 'partida insertada'; 
 }else if($resultatFinalJ2>=50){

 $stmt=$conn->prepare("UPDATE users SET Punts = Punts + 10  WHERE id=?");
 $stmt->bind_param("s",$idJ2);
 $stmt->execute();
 $stmt->store_result();
 $stmt->close();
 $response['error'] = false; 
 $response['message'] = 'Partida no insertada';
 }
 
 
 break; 
 
case 'savematchchamp':
 

 $vectorfinalJ1 = $_POST['vectorfinalJ1'];
 $vectorfinalJ2 = $_POST['vectorfinalJ2'];
 $vectorparcialJ1=$_POST['vectorparcialJ1'];
 $vectorparcialJ2=$_POST['vectorparcialJ2'];
 $resultatFinalJ1=$_POST['resultatFinalJ1'];
 $resultatFinalJ2=$_POST['resultatFinalJ2'];
 $idJ1=$_POST['idJ1'];
 $idJ2=$_POST['idJ2'];
 $promigJ1=$_POST['promigJ1'];
 $promigJ2=$_POST['promigJ2'];
 $nomJ1=$_POST['NomJ1'];
 $nomJ2=$_POST['NomJ2'];
 $tipus=$_POST['tipus'];	
 $data=$_POST['data'];
 $idCompeticio=$_POST['idCompeticio'];
 $stmt = $conn->prepare("INSERT INTO partida(idJugador1,idJugador2,PuntsJugadorFinal1,PuntsJugadorFinal2,NomJugador1,NomJugador2,PuntuacioFinalJ1,PuntuacioFinalJ2,PromigJ1,PromigJ2,parcialsJ1,parcialsJ2,DataPartida,CompeticioId)
 VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
 $stmt->bind_param("ssssssssssssss",$idJ1,$idJ2,$vectorfinalJ1,$vectorfinalJ2,$nomJ1,$nomJ2,$resultatFinalJ1,$resultatFinalJ2,$promigJ1,$promigJ2,$vectorparcialJ1,$vectorparcialJ2,$data,$idCompeticio);
 $stmt->execute();
 $stmt->store_result();
 $stmt->close();
 if($resultatFinalJ1>=50){
 
 $stmt=$conn->prepare("UPDATE users SET Punts = Punts + 10  WHERE id=?");
 $stmt->bind_param("s",$idJ1);
 $stmt->execute();
 $stmt->store_result();
 $stmt->close();
 $response['error'] = false; 
 $response['message'] = 'partida insertada'; 
 }
 else if($resultatFinalJ2>=50){

 $stmt=$conn->prepare("UPDATE users SET Punts = Punts + 10  WHERE id=?");
 $stmt->bind_param("s",$idJ2);
 $stmt->execute();
 $stmt->store_result();
 $stmt->close();
 $response['error'] = false; 
 $response['message'] = 'Partida no insertada';
 }

 
 
 break; 
  case 'createchampionship':
 

 
 $data=$_POST['data'];
 $arbit=$_POST['arbit'];
 $jornada=$_POST['jornada'];
 $modalitat=$_POST['modalitat'];
 $competicio=$_POST['competicio'];
 
 //creating the query 
 $stmt = $conn->prepare("INSERT INTO competicio(NomCompeticio,DataCompeticio,Modalitat,Arbit,Jornada)
 VALUES(?,?,?,?,?)");
 $stmt->bind_param("sssss",$competicio,$data,$modalitat,$arbit,$jornada);
 
 $stmt->execute();
 
 $stmt->store_result();
 
 if($stmt){
 $last_id=$conn->insert_id;
 $response['error'] = false; 
 $response['message'] = 'Competicio creada'; 
 $response['id']=$last_id;
 }else{

 $response['error'] = false; 
 $response['message'] = 'Competicio no creada';
 }
 
 
 break; 
 
  case 'getusers':

 $stmt = $conn->prepare("SELECT id,Username,idClub,punts FROM users ORDER BY punts DESC");
 $stmt->execute();
 $result=$stmt->get_result();
  if($result->num_rows > 0){
	   $responses=array();
     while($row = $result->fetch_assoc()) {
        $responses[] = array(
			 'id'=>$row['id'],
			 'Username'=>$row['Username'],
			 'punts'=>$row['punts'],
			 'idclub'=>$row['idClub']
        );  
	 }
 $response['usuaris'] = $responses; 
  }
  else{
	$response ['user']="$result";  
  }
 
 break;  
case 'changepass':
$passwordA = $_POST['passwordA'];
$passwordN = $_POST['passwordN']; 
	
$stmt = $conn->prepare("SELECT Password FROM users WHERE Password = ?");
$stmt->bind_param("s",$passwordA);
$stmt->execute();

$stmt->store_result();
if($stmt->num_rows > 0){
		
	$stmt = $conn->prepare("UPDATE users SET Password=? WHERE Password=?");
	$stmt->bind_param("ss",$passwordN,$passwordA);
	$stmt->execute();
	$response['error'] = false; 
	$response['message'] = 'Password changed successfully!'; 	
	}
else{
	$response['error'] = false; 
	$response['message'] = 'Password anterior incorrecte';	
	}
	
  
 break;
 
case 'changeuser':
  
$usernameA = $_POST['usernameA'];
$usernameN = $_POST['usernameN']; 
	
$stmt = $conn->prepare("SELECT UserName FROM users WHERE UserName = ?");
$stmt->bind_param("s",$usernameA);
$stmt->execute();

$stmt->store_result();
if($stmt->num_rows > 0){
		
	$stmt = $conn->prepare("UPDATE users SET UserName=? WHERE UserName=?");
	$stmt->bind_param("ss",$usernameN,$usernameA);
	$stmt->execute();
	$response['error'] = false; 
	$response['message'] = 'Username changed successfully!'; 	
	}
else{
	$response['error'] = false; 
	$response['message'] = 'Username anterior incorrecte';	
	}

 break;
 
case 'checkuser':

$username1=$_POST['username1'];
$username2=$_POST['username2'];

 //creating the query 
 $stmt = $conn->prepare("SELECT id FROM users WHERE UserName = ? OR UserName = ?");
 $stmt->bind_param("ss",$username1,$username2);
 
 $stmt->execute();
 
 $stmt->store_result();
 
 //if the user exist with given credentials 
 if($stmt->num_rows > 0){
 
 $stmt->bind_result($id);
  $cart = array();
while($stmt->fetch()) {
array_push($cart,$id);
}


 
 $response['error'] = false; 
 $response['message'] = 'Login successfull'; 
 $response['user'] = $cart[0];
 $response['user2'] = $cart[1];
 }else{
 //if the user not found 
 $response['error'] = false; 
 $response['message'] = 'Invalid username or password';
 }
 

break;

case 'detallshistorial':

$id=$_POST['id'];
$idclub=$_POST['idclub'];


 //creating the query 
 $stmt = $conn->prepare("SELECT COUNT(id) as total1 FROM partida WHERE idJugador1=? OR idJugador2=?");
 $stmt->bind_param("ss",$id,$id); 
 $stmt->execute();
 $resultatprimera=$stmt->get_result();
 $stmt->close();
 
 $stma = $conn->prepare("SELECT COUNT(id) as total2 FROM partida WHERE (idJugador1=? AND PuntuacioFinalJ1=50) OR (idJugador2=? AND PuntuacioFinalJ2=50)");
 $stma->bind_param("ss",$id,$id); 
 $stma->execute();
 $resultatsegona=$stma->get_result();
 $stma->close();
 
 $stmb = $conn->prepare("SELECT AVG(PromigJ1) as promig1 FROM partida WHERE idJugador1=?");
 $stmb->bind_param("s",$id); 
 $stmb->execute();
$resultattercera=$stmb->get_result();
$stmb->close();
 
 $stmc = $conn->prepare("SELECT AVG(PromigJ2) as promig2 FROM partida WHERE idJugador2=?");
 $stmc->bind_param("s",$id); 
 $stmc->execute();
 $resultatquatre=$stmc->get_result();
 $stmc->close();
 
 $stmd = $conn->prepare("SELECT Nom FROM club WHERE id=?");
 $stmd->bind_param("s",$idclub); 
 $stmd->execute();
 $resultatcinc=$stmd->get_result();
 $stmd->close();
 
 //if the user exist with given credentials 
	$response['error'] = false; 
	$response['uno']=$resultatprimera->fetch_assoc();
	$response['dos']=$resultatsegona->fetch_assoc();
	$response['tres']=$resultattercera->fetch_assoc();
	$response['cuatro']=$resultatquatre->fetch_assoc();
	$response['cinco']=$resultatcinc->fetch_assoc();
 
break;

case 'checkuser1':

$username1=$_POST['username'];

 //creating the query 
 $stmt = $conn->prepare("SELECT UserName,id FROM users WHERE UserName = ? ");
 $stmt->bind_param("s",$username1);
 
 $stmt->execute();
 
 $stmt->store_result();
 
 //if the user exist with given credentials 
 if($stmt->num_rows > 0){
 $stmt->bind_result($username2, $id);
 $stmt->fetch();
 
 $user = array(
 'id'=>$id, 
 'username'=>$username2, 
 );
 $response['error'] = false; 
 $response['message'] = 'Usuari correcte'; 
 $response['user'] = $user;

 }else{
 //if the user not found 
 $response['error'] = false; 
 $response['message'] = 'Invalid username';
 }
 
break;
default: 
 $response['error'] = true; 
 $response['message'] = 'Invalid Operation Called';
 }
 
 }else{
 //if it is not api call 
 //pushing appropriate values to response array 
 $response['error'] = true; 
 $response['message'] = 'Invalid API Call';
 }
 
 //displaying the response in json structure 
 echo json_encode($response);
 
 //function validating all the paramters are available
 //we will pass the required parameters to this function 
 function isTheseParametersAvailable($params){
 
 //traversing through all the parameters 
 foreach($params as $param){
 //if the paramter is not available
 if(!isset($_POST[$param])){
 //return false 
 return false; 
 }
 }
 //return true if every param is available 
 return true; 
 }
 ?>