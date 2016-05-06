<?php
include_once 'createDB.php';
	$dao=new LoginDAO();
    $wrongpassword=false;
	$invalidinputs=false;
	$notregistered=false;
	$hasloggedin=false;
	date_default_timezone_set("America/Montreal"); // set timezone	
	session_start();
		
	if($_SERVER['REQUEST_METHOD']=='POST'){
		if(isset($_SESSION['username'])){
			$hasloggedin=true;
		}     
		// login verification
		elseif(isset($_POST['username']) && strlen($_POST['username'])>0 &&
		   isset($_POST['password']) && strlen($_POST['password'])>0){
			$result=$dao->findUserByUsername($_POST['username']);
			if(count($result)!=0){
				$gap=time()-strtotime($result[4]);
				$lockoutperiod=30; // 30s lockout
				if($gap>$lockoutperiod){
					// recover login page after specified lockout period				
					$dao->updateAttempts($_POST['username'], 0);					
				}
				if($result[3]<=3){
					// lock out user after 3 wrong tries
					if($result[3]==3){
						header('location:./blockpage.php');
					}
					$hash=$result[2];				
					if(password_verify($_POST['password'], $hash)){
						session_start();
						$_SESSION['username']=$_POST['username'];
						session_regenerate_id();
						header('location:./notesboard.php');
					}else{
						$wrongpassword=true;
						$ctr=$result[3]+1;						
						if($ctr==3){
							$dao->updateLastattempt($_POST['username']);
						}					
						$dao->updateAttempts($_POST['username'], $ctr);						
					}
				}				
			}else{
				$notregistered=true;
			}				
		}else{
			$invalidinputs=true;
		}		
	}
?>
	
	<head>
        <title>Sticky Application</title>
    </head>
	<body>
	
		<header><h1>Sticky Notes</h1></header>	
		
		<form action='' method='POST'>	
			<h3>Log in</h3>
			username<input id="loggeduser" type='text' name='username'/>
			</br></br>	
			password<input type='password' name='password'/></br>
			
			<?php 
				if($notregistered){
					echo "You have not registered yet.";
				}
				elseif($wrongpassword){
					echo "Invalid password! You have "
					     .(3-$ctr)." times to try.";
				}
				elseif($invalidinputs){
					echo "Invalid inputs for logging in.";
				} 
			?>				  
			</br></br>		
			<input type='submit' name='submit' value='Submit'/></br>
		</form>		
		<?php
			if($hasloggedin){
				echo "<p id='lg'>";
				echo "<a href='Logout.php' >Log out</a>";
				echo "</p>";
			}
		?>		
		<div> <a href="index.php">Registration</a> </div>		
	</body>
	
	
	