<?php
session_start();
require("utils.php");
?>

<!DOCTYPE html>

<html>
	
	<head>
		<title>Log In</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script>
			function checkLogin(username, password)
			{
				xmlhttp = new XMLHttpRequest();
	  			xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						if (this.responseText == "")
						{
		                	window.location.replace("index.php");
		                }
		                else
		                {
		                	if (this.responseText == "Incorrect password.")
		                	{
		                		var passwordField = document.getElementById("password");
		                		passwordField.select();
		                		passwordField.style.border = "1px solid red";
		                		passwordField.style.backgroundColor = "#FEE";
		                		passwordField.focus();
		                	}
		                	else if (this.responseText == "That user does not exist.")
		                	{
		                		var userField = document.getElementById("username");
		                		userField.select();
		                		userField.style.border = "1px solid red";
		                		userField.style.backgroundColor = "#FEE";
		                		userField.focus();
		                		document.getElementById("password").value = "";
		                	}
		                	else
			                {
			                	document.getElementById("username").value = "";
								document.getElementById("password").value = "";
							}
							document.getElementById("submit").blur();
		                	document.getElementById("loginError").innerHTML = "<br>"+this.responseText;
		                }
					}
				};
				xmlhttp.open("POST","checkLogin.php",true);
				xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				xmlhttp.send("username="+username + "&password="+password);
			}
		</script>
	</head>

	<body>
		<?php
		showHeader("login"); ?>
		<div id="content">
			<h1>Log In</h1>
			<input type="text" class="textInput" id="username" placeholder="Username" autofocus="true" /><br><br>
			<input type="password" class="textInput" id="password" placeholder="Password" /><br>
			<span class="error" id="loginError"></span><br>
			<br>
			<button id="submit" onclick="checkLogin(username.value, password.value);">Login</button>
			<script>
				document.getElementById("username").addEventListener("keypress", function(event) {
					if (event.keyCode === 13) {
						event.preventDefault();
						document.getElementById("username").blur();
						document.getElementById("submit").focus();
						document.getElementById("submit").click();
					}
					else if (document.getElementById("loginError").innerHTML != "")
					{
						document.getElementById("username").style.border = "1px solid gray";
						document.getElementById("username").style.backgroundColor = "white";
						document.getElementById("password").style.border = "1px solid gray";
						document.getElementById("password").style.backgroundColor = "white";
						document.getElementById("loginError").innerHTML = "";
					}
				});

				document.getElementById("password").addEventListener("keypress", function(event) {
					if (event.keyCode === 13) 
					{
						event.preventDefault();
						document.getElementById("password").blur();
						document.getElementById("submit").focus();
						document.getElementById("submit").click();
					}
					else if (document.getElementById("loginError").innerHTML != "")
					{
						document.getElementById("password").style.border = "1px solid gray";
						document.getElementById("password").style.backgroundColor = "white";
						document.getElementById("loginError").innerHTML = "";
					}
				});
			</script>
			<hr>
			No account yet? <a href="signup.php"><u>Sign up</u></a> instead.
		</div>
	</body>

</html>