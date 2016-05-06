<?php
include_once 'createDB.php';
	// create db & tables here for once only
	//$cbt=new CreatDbAndtable();
	//$cbt->create();
	$existingname=false;
	$dao=new LoginDAO();
	$invalidinputs=false;
	
	if($_SERVER['REQUEST_METHOD']=='POST'){ 		
		// registration part
		if(isset($_POST['regiusername']) && strlen($_POST['regiusername'])>0 
		   && isset($_POST['regipassword']) && 
		   strlen($_POST['regipassword'])>0){		
			$result=$dao->findUserByUsername($_POST['regiusername']);		  
			if(count($result)==0){
				$dao->create($_POST['regiusername'],$_POST['regipassword']);
				
				session_start();
				$_SESSION['username']=$_POST['regiusername'];
				session_regenerate_id();
				
				header('location:./notesboard.php');
			}else{
				$existingname=true;
			}				
		}else{
			$invalidinputs=true;
		}
	}	
?>
	<head>
        <title>Sticky Application</title>
    </head>
	
	<header><h1>Sticky Notes</h1></header>
	
	<form action='' method='POST'>
		<h3>Registration</h3>
		username<input id='ru' type='text' name='regiusername'/>
		<?php if($existingname)
		    {echo "   Existing username! You have to pick another one.";} ?>
		</br></br>	
		password<input id='rp' type='password' name='regipassword'/>
		</br></br>
		<?php if($invalidinputs){echo "Invalid inputs for registration.";}?>
		</br></br>
		<input id='rs' type='submit' name='regisubmit' value='Submit'/></br>
	</form>
	<div> <a href="login.php">Login</a> </div>
