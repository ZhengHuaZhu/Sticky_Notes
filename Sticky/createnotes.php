<?php
	include_once 'createDB.php';

	$dao=new StickyDAO();
	session_start();
	session_regenerate_id();
	
	if(isset($_SESSION['username'])){
		$username=$_SESSION['username'];	
		if($_SERVER['REQUEST_METHOD']=='POST'){
			if(isset($_POST['content']) && strlen($_POST['content'])>0){	
				$memo=$_POST['content'];
				$results=$dao->findAll($username);
				$z=count($results)+1;
				$dao->create(600, 150, $z, $memo, $username);
				$results=$dao->findAll($username);

				// in a presentation layer class:
				header('Content-Type: application/json');
				//echo the json 	
				echo json_encode($results);	
			}
		}			
	}else{
		$results=array('results'=>'error');
		echo json_encode($results);	
	}
