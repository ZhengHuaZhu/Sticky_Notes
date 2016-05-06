<?php
session_start();
session_regenerate_id();
// go back to index page if sessionid has lost
if(!$_SESSION['username']){
	header('location:./index.php');
}
?>

<!DOCTYPE HTML>
<html>
	<head>
        <title>Notes Board</title>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/
		smoothness/jquery-ui.css">
		<script src="//code.jquery.com/jquery-1.10.2.js"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script type="text/javascript" src="create.js"></script>
		<link rel="stylesheet" type="text/css" href="notesboard.css">
    </head>
	<body>
		<div id='div0'>
			<div class='div1'>
			
				<header><h1>Notes Board</h1></header>
			
				<div id="title_notecreator"><h3>Write A Note</h3></div>
				
				<form action='' method='POST'>			
					<textarea id='textarea' rows='8' cols='30'></textarea>
					<input id='createbtn' type='submit' name='submit' 
					       value='Create'/>			
				</form>
			</div>
			<p id='username'><?php echo 'Hi '.$_SESSION['username']; ?></p>
			<p id='logout'><a href="Logout.php" >Log out</a></p>
		</div>
	</body>
</html>