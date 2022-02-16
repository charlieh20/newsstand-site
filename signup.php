<?php
session_start();
require("utils.php");
?>

<!DOCTYPE html>

<html>
	
	<head>
		<title>Sign Up</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script>
			function checkSignUp(username, password, password2)
			{
				xmlhttp = new XMLHttpRequest();
	  			xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						if (this.responseText == "")
						{
							window.location.replace("add.php");
						}
						else
						{
		                	document.getElementById("loginError").innerHTML = "<br>"+this.responseText;
		                	if (this.responseText == "There is already a user with this username!")
		                	{
		                		document.getElementById("password").value = "";
								document.getElementById("confirmPassword").value = "";
								var userField = document.getElementById("username");
		                		userField.select();
		                		userField.style.border = "1px solid red";
		                		userField.style.backgroundColor = "#FEE";
		                		userField.focus();
		                	}
		                	else if (this.responseText == "The passwords do not match.")
		                	{
		                		var pass1Field = document.getElementById("password");
		                		pass1Field.style.border = "1px solid red";
		                		pass1Field.style.backgroundColor = "#FEE";
		                		var pass2Field = document.getElementById("confirmPassword");
		                		pass2Field.style.border = "1px solid red";
		                		pass2Field.style.backgroundColor = "#FEE";
		                		pass2Field.select();
		                		pass2Field.focus();
		                	}
		                	else if (this.responseText == "Please enter something in all the fields.")
		                	{
		                		var userField = document.getElementById("username");
		                		var pass1Field = document.getElementById("password");
		                		var pass2Field = document.getElementById("confirmPassword");
		                		if (pass2Field.value == "")
		                		{
		                			pass2Field.style.border = "1px solid red";
		                			pass2Field.style.backgroundColor = "#FEE";
		                			pass2Field.focus();
		                		}
		                		if (pass1Field.value == "")
		                		{
		                			pass1Field.style.border = "1px solid red";
		                			pass1Field.style.backgroundColor = "#FEE";
		                			pass1Field.focus();
		                		}
		                		if (userField.value == "")
		                		{
		                			userField.style.border = "1px solid red";
		                			userField.style.backgroundColor = "#FEE";
		                			userField.focus();
		                		}
		                	}
		            	}
					}
				};
				xmlhttp.open("POST","checkSignUp.php",true);
				xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				xmlhttp.send("username="+username + "&password="+password + "&password2="+password2);
			}

			function removeError()
			{
				document.getElementById("username").style.border = "1px solid gray";
				document.getElementById("username").style.backgroundColor = "white";

				document.getElementById("password").style.border = "1px solid gray";
				document.getElementById("password").style.backgroundColor = "white";
				
				document.getElementById("confirmPassword").style.border = "1px solid gray";
				document.getElementById("confirmPassword").style.backgroundColor = "white";

				document.getElementById("loginError").innerHTML = "";
			}
		</script>
	</head>

	<body>
		<?php
		showHeader(""); ?>
		<div id="content">
			<h1>Sign Up</h1>
			<input type="text" class="textInput" id="username" placeholder="Username" autofocus="true" /><br>
			<br>
			<input type="password" class="textInput" id="password" placeholder="Password" /><br>
			<br>
			<input type="password" class="textInput" id="confirmPassword" placeholder="Confirm password" /><br>
			<span class="error" id="loginError"></span><br>
			<br>
			<button id ="submit" onclick="checkSignUp(username.value, password.value, confirmPassword.value);">Sign Up</button>
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
						removeError();
					}
				});

				document.getElementById("password").addEventListener("keypress", function(event) {
					if (event.keyCode === 13) {
						event.preventDefault();
						document.getElementById("password").blur();
						document.getElementById("submit").focus();
						document.getElementById("submit").click();
					}
					else if (document.getElementById("loginError").innerHTML != "")
					{
						removeError();
					}
				});

				document.getElementById("confirmPassword").addEventListener("keypress", function(event) {
					if (event.keyCode === 13) {
						event.preventDefault();
						document.getElementById("confirmPassword").blur();
						document.getElementById("submit").focus();
						document.getElementById("submit").click();
					}
					else if (document.getElementById("loginError").innerHTML != "")
					{
						removeError();
					}
				});
			</script>
		<hr>
		Already signed up? <a href="login.php"><u>Log in</u></a> instead.</div>
	</body>

</html>