<?php
	include_once 'createDB.php';

	$dao=new StickyDAO();
	session_start();
	session_regenerate_id();
	
	if(isset($_SESSION['username'])){
		$username=$_SESSION['username'];
		if($_SERVER['REQUEST_METHOD']=='POST'){
			if(isset($_POST['left']) && isset($_POST['top']) &&
			   isset($_POST['id']) && isset($_POST['z_index'])){
				$dao->update($_POST['left'], $_POST['top'], $_POST['id'], 
							 $_POST['z_index']);	   
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