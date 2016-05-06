<?php
	include_once 'createDB.php';
	$dao=new StickyDAO();
	session_start();
	session_regenerate_id();
	
	if(isset($_SESSION['username'])){
		$username=$_SESSION['username'];
		if($_SERVER['REQUEST_METHOD']=='POST'){  
			if(isset($_POST['load'])){
				$results=$dao->findAll($username);
				header('Content-Type: application/json');	
				echo json_encode($results);	
		    }
		}
	}else{
		$results=array('results'=>'error');
		echo json_encode($results);	
	}
	
	

