<?php
	class CreatDbAndtable{
		
		function create(){
			//declare variables to create db & table
			$servername = 'localhost';
			$username = 'root';
			$password = '';
			$dbName='stickynotes';
			$tablename1='login';
			$tablename2='sticky';
		
			// create database 
			try {
				// use only servername, username and password to create a 
				// database first
				$conn = new PDO("mysql:host=$servername", 
				                $username, $password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, 
				                    PDO::ERRMODE_EXCEPTION);
				$sql = "CREATE DATABASE $dbName";
				// use exec() because no results are returned
				$conn->exec($sql);
				//echo "Database $dbName has created successfully"."\n";
			}catch(PDOException $e){
				echo $sql . "\n" . $e->getMessage()."\n";
			}finally{
				$conn = null;
			}

			// create tables
			try {
				$conn = new PDO("mysql:host=$servername;dbname=$dbName", 
				                $username, $password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, 
				                    PDO::ERRMODE_EXCEPTION);
				// sql to create table
				$sql1 = "CREATE TABLE $tablename1 (
				id INT(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 	
				username VARCHAR(100) NOT NULL, 			
				password VARCHAR(255) NOT NULL,
				attempts INT(1) DEFAULT 0,
				lastattempt timestamp DEFAULT CURRENT_TIMESTAMP
				)";
				$sql2 = "CREATE TABLE $tablename2 (
				id INT(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 	
				lft INT(10) NOT NULL, 			
				top INT(10) NOT NULL,
				z_index INT(5) NOT NULL,
				memo VARCHAR(255) NOT NULL,
				username VARCHAR(100) NOT NULL
				)";

				// use exec() because no results are returned
				$conn->exec($sql1);
				$conn->exec($sql2);
			}catch(PDOException $e){
				echo $sql."\n".$e->getMessage()."\n";
			}finally{
				$conn = null;
			}
		}
	}

	class LoginDAO{
		//declare variables to connect to DB
		private $servername;
		private $username;
		private $password;
		private $dbName;
		private $tablename;
		
		function __construct(){
			$this->servername='localhost';
			$this->username = 'root';
		    $this->password = '';
		    $this->dbName='stickynotes';
		    $this->tablename='login';
		}
	
		function findUserByUsername($name){
			try {
				// connect to DB
				$conn = 
				new PDO("mysql:host=$this->servername;dbname=$this->dbName", 
				$this->username, $this->password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, 
				                    PDO::ERRMODE_EXCEPTION);
				// prepared statement 
				$sql="select id, username, password, attempts, 
				lastattempt from $this->tablename where username = ?";				
				$stmt=$conn->prepare($sql);
				
				$li= new Login(-1, $name, "");
				$name=$li->getUsername();
								
				$stmt->bindParam(1, $name);							
				$stmt->execute();
				
				$response=null; //to make sure an array is empty		
				$stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 
				                    'Login');
				
				while ($row = $stmt->fetch()){	
				    $response[0]=$row->getId();
					$response[1]=$row->getUsername();
					$response[2]=$row->getPassword();
					$response[3]=$row->getAttempts();
					$response[4]=$row->getLastattempt();
				}		
				return $response; // user found returned
			}catch(PDOException $e){
				echo $sql."\n".$e->getMessage()."\n";
			}finally{
				$conn = null;
			}
		}
		
		function updateLastattempt($name){
			try {
				// connect to DB
				$conn = 
				new PDO("mysql:host=$this->servername;dbname=$this->dbName", 
				$this->username, $this->password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, 
				                    PDO::ERRMODE_EXCEPTION);
				// prepared statement 
				$sql="update $this->tablename set lastattempt = ? 
				where username = ?";				
				$stmt=$conn->prepare($sql);
				// set timezone
				date_default_timezone_set("America/Montreal"); 
                //echo date_default_timezone_get();
				// get a unixtimestamp and convert it to mysql datetime format
				$now=date("Y-m-d H:i:s", time()); 
				
				$li= new Login(-1, $name, "");
				$li->setLastattempt($now);
				$updatedlastattempt=$li->getLastattempt();
				$name=$li->getUsername();
				
				$stmt->bindParam(1, $updatedlastattempt);
				$stmt->bindParam(2, $name);          				
				$stmt->execute();
			}catch(PDOException $e){
				echo $sql."\n".$e->getMessage()."\n";
			}finally{
				$conn = null;
			}
		}
		
		function updateAttempts($name, $ctr){
			try {
				// connect to DB
				$conn = 
				new PDO("mysql:host=$this->servername;dbname=$this->dbName", 
				        $this->username, $this->password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, 
				                    PDO::ERRMODE_EXCEPTION);
				// prepared statement 
				$sql="update $this->tablename set attempts = ? 
				where username = ?";				
				$stmt=$conn->prepare($sql);
				
				$li= new Login(-1, $name, "");
				$li->setAttempts($ctr);
				$updatedattempts=$li->getAttempts();
				$name=$li->getUsername();
				
				$stmt->bindParam(1, $updatedattempts);
				$stmt->bindParam(2, $name);              				
				$stmt->execute();
			}catch(PDOException $e){
				echo $sql."\n".$e->getMessage()."\n";
			}finally{
				$conn = null;
			}
		}

		function create($name, $password){
			try {
				// connect to DB
				$conn = 
				new PDO("mysql:host=$this->servername;dbname=$this->dbName", 
				         $this->username, $this->password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, 
				                    PDO::ERRMODE_EXCEPTION);
				// prepared statement 
				$sql="insert into $this->tablename (username, password) 
				values(?, ?)";				
				$stmt=$conn->prepare($sql);
			
				$li= new Login(-1, $name, $password);
				$name=$li->getUsername();
				$password=$li->getPassword();
				
				$stmt->bindParam(1, $name);	
				$stmt->bindParam(2, $password);				
				$result=$stmt->execute();
				return $result;
			}catch(PDOException $e){
				echo $sql."\n".$e->getMessage()."\n";
			}finally{
				$conn = null;
			}
		}					
	}
	
	class StickyDAO{
		//declare variables to connect to DB
		private $servername;
		private $username;
		private $password;
		private $dbName;
		private $tablename;
		
		function __construct(){
			$this->servername='localhost';
			$this->username = 'root';
		    $this->password = '';
		    $this->dbName='stickynotes';
		    $this->tablename='sticky';
		}

		function findAll($username){
			try {
				// connect to DB
				$conn = 
				new PDO("mysql:host=$this->servername;dbname=$this->dbName", 
				        $this->username, $this->password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, 
				                    PDO::ERRMODE_EXCEPTION);
				// prepared statement 
				$sql="select id,lft,top,z_index,memo from $this->tablename 
				where username=?";				
				$stmt=$conn->prepare($sql);
				
				$sn=new Sticky();
				$sn->setUsername($username);
			    $username=$sn->getUsername();
                $stmt->bindParam(1, $username);			
				$result=$stmt->execute();
				
				$results=null; //to make sure an array is empty	
			    $response=null;
				$stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 
				                    'Sticky');
				
				while ($row = $stmt->fetch()){	
				    $response['id']=$row->getId();
					$response['lft']=$row->getLft();
					$response['top']=$row->getTop();
					$response['z_index']=$row->getZ_index();
					$response['memo']=$row->getMemo();
					$results[]=$response;
				}		
				return $results; // user found returned
			}catch(PDOException $e){
				echo $sql."\n".$e->getMessage()."\n";
			}finally{
				$conn = null;
			}
		}
		
		function create($lft, $top, $z_index, $memo, $username){
			try {
				// connect to DB
				$conn = 
				new PDO("mysql:host=$this->servername;dbname=$this->dbName", 
				$this->username, $this->password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, 
				                    PDO::ERRMODE_EXCEPTION);
				// prepared statement 
				$sql="insert into $this->tablename 
				(lft,top,z_index,memo,username) values(?,?,?,?,?)";				
				$stmt=$conn->prepare($sql);
				
				$sn=new Sticky(-1, $lft, $top, $z_index, $memo, $username);			
				$lft=$sn->getLft();
				$top=$sn->getTop();
				$z_index=$sn->getZ_index();
				$memo=$sn->getMemo();
				$username=$sn->getUsername();
											
				$stmt->bindParam(1, $lft);	
				$stmt->bindParam(2, $top);	
				$stmt->bindParam(3, $z_index);	
				$stmt->bindParam(4, $memo);
                $stmt->bindParam(5, $username);			
				$result=$stmt->execute();
				return $result;
			}catch(PDOException $e){
				echo $sql."\n".$e->getMessage()."\n";
			}finally{
				$conn = null;
			}
		}

		function update($lft, $top, $id, $z_index){
			try {
				// connect to DB
				$conn = 
				new PDO("mysql:host=$this->servername;dbname=$this->dbName", 
				        $this->username, $this->password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, 
				                    PDO::ERRMODE_EXCEPTION);
				// prepared statement 
				$sql="update $this->tablename set lft=?,top=?,z_index=? 
				where id=?";				
				$stmt=$conn->prepare($sql);
				$sn=new Sticky($id, $lft, $top, $z_index);
				$id=$sn->getId();
				$lft=$sn->getLft();
				$top=$sn->getTop();
				$z_index=$sn->getZ_index();
															
				$stmt->bindParam(1, $lft);	
				$stmt->bindParam(2, $top);	
				$stmt->bindParam(3, $z_index);	
				$stmt->bindParam(4, $id);			
				$result=$stmt->execute();
				return $result;
			}catch(PDOException $e){
				echo $sql."\n".$e->getMessage()."\n";
			}finally{
				$conn = null;
			}
		}	

		function delete($id){
			try {
				// connect to DB
				$conn = 
				new PDO("mysql:host=$this->servername;dbname=$this->dbName", 
				$this->username, $this->password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, 
				                    PDO::ERRMODE_EXCEPTION);
				// prepared statement 
				$sql="delete from $this->tablename where id=?";				
				$stmt=$conn->prepare($sql);
				
				$sn=new Sticky($id);
				$id=$sn->getId();
											
				$stmt->bindParam(1, $id);			
				$result=$stmt->execute();
				return $result;
			}catch(PDOException $e){
				echo $sql."\n".$e->getMessage()."\n";
			}finally{
				$conn = null;
			}
		}
	}
	
	class Login{
		private $id;
		private $username;
		private $password;
		private $attempts;
		private $lastattempt;
				
		function __construct($id=-1, $username="", $password=""){			
			$this->setId($id);	
			$this->setUsername($username);
			$this->setPassword($password);
			$this->attempts=0;
			// current time in seconds
			$this->lastattempt=date("Y-m-d H:i:s"); 
		}	
		function getId() {
			return $this->id;
		}
		function getUsername() {
			return $this->username;
		}	
		function getPassword(){
			return $this->password;
		}
		function getAttempts(){
			return $this->attempts;
		}
		function getLastattempt(){
			return $this->lastattempt;
		}
		function setAttempts($num){
			if(!empty($num) && is_numeric($num)){
				$this->attempts=$num;
			}
		}
		function setLastattempt($time){// timestamp 
		//how to check if $time is a timestamp or date?
			if(!empty($time)){
				$this->lastattempt=$time;
			}
		}
		function setId($id){
			if(!empty($id) && is_numeric($id)){
				$this->id=$id;
			}
		}
		function setUsername($username){
			if(!empty($username) && strlen($username)>0){
				$this->username=$username;
			}
		}
		function setPassword($password){
			if(!empty($password) && strlen($password)>0){
				$hash=password_hash($password, PASSWORD_DEFAULT);
				$this->password=$hash;
			}
		}
	}
	
	class Sticky{
		private $id;
		private $lft;
		private $top;
		private $z_index;
		private $memo;
		private $username;
				
		function __construct($id=-1, $lft=600, $top=150, $z_index=-1, 
		                     $memo="", $username=""){			
			$this->setId($id);	
			$this->setLft($lft);
			$this->setTop($top);
			$this->setZ_index($z_index);
			$this->setMemo($memo);
			$this->setUsername($username);
		}
		
		function getId() {
			return $this->id;
		}
		function getLft() {
			return $this->lft;
		}	
		function getTop(){
			return $this->top;
		}
		function getZ_index(){
			return $this->z_index;
		}
		function getMemo(){
			return $this->memo;
		}	
		function getUsername(){
			return $this->username;
		}
		function setUsername($username){
			if(isset($username) && strlen($username)>0){
				$this->username=$username;
			}
		}
		function setId($id){
			if(isset($id) && is_numeric($id)){
				$this->id=$id;
			}
		}
		function setLft($lft){
			if(isset($lft) && is_numeric($lft)){
				$this->lft=$lft;
			}
		}
		function setTop($top){
			if(isset($top) && is_numeric($top)){
				$this->top=$top;
			}
		}
		function setZ_index($z_index){
			if(isset($z_index) && is_numeric($z_index)){
				$this->z_index=$z_index;
			}
		}
		function setMemo($memo){
			if(isset($memo) && strlen($memo)>0){
				$this->memo=$memo;
			}
		}
	}
	
		