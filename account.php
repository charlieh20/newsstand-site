<?php
session_start();
require("utils.php");
?>

<!DOCTYPE html>

<html>
	
	<head>
		<title>Profile</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script>
			function signOut()
			{
				xmlhttp = new XMLHttpRequest();
	  			xmlhttp.onreadystatechange = function() 
	  			{
					if (this.readyState == 4 && this.status == 200) 
					{
						window.location.replace("index.php");
					}
				};
				xmlhttp.open("GET","clear.php",true);
				xmlhttp.send();
			}
		</script>
	</head>

	<body>
		<?php
		showHeader("account");

		print("<div id='content'>");

		if (isset($_SESSION["hipps_user"]))
		{
			try
			{
				$db = setupDB();

				print("<h1>Account</h1><hr>
					Username: " . $_SESSION["hipps_user"] . 
					"<h2>Preferred Sources:</h2>");

				$query = "SELECT name FROM (chipps_users u JOIN chipps_users_prefs up ON u.id = up.user_id JOIN chipps_preferences p ON up.pref_id = p.id) WHERE username = :username ORDER BY name ASC;";
				$input = $db->prepare($query);
				$input->execute(array('username' => $_SESSION["hipps_user"]));
				$userPrefs = $input->fetchAll();

				foreach($userPrefs as $pref)
				{
					print("<li>" . $pref["name"] . "</li>");
				}

				print("<br><br><a href='add.php'><button>Change Preferences</button></a><br><br>
					<hr>
					<br>
					<button onclick='signOut();'>Sign Out</button>");

			}
			catch (PDOException $ex)
			{
				print("Database error: " . $ex->getMessage());
			}
		}
		else
		{
			print("<div class='error'>Error occurred; please log back in.</div>");
		}
		print("</div>");
		?>
	</body>

</html>