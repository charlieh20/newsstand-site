<?php
session_start();
require("utils.php");
?>

<!DOCTYPE html>

<html>
	
	<head>
		<title>Preferences</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script type="text/javascript">
			function filterSources()
			{
				var input = document.getElementById("sourceSearch").value.toUpperCase();
				var sources = document.getElementsByClassName("sourceButton");
				for (var i = 0; i < sources.length; i++) 
				{
					if (sources[i].innerHTML.toUpperCase().indexOf(input) == -1)
					{
						sources[i].style.display = "none";
					}
					else
					{
						sources[i].style.display = "";
					}
				}
			}

			function showConfirmButton()
			{
				document.getElementById("sourceButtonsWrapper").classList.add("shrink");
				var searchBlock = document.getElementById("sourceSearchWrapper");
				searchBlock.classList.add("shrink");
				searchBlock.style.marginBottom = "10px";
				var selectedList = document.getElementById("selectedListWrapper");
				selectedList.classList.add("shrink");
				selectedList.style.marginBottom = "10px";

				document.getElementById("confirmPrefs").classList.add("appear");
			}

			function addToList(source)
			{
				source.style.display = "none";
				document.getElementById("selectedList").innerHTML += "<li id='" + source.id + "-block'><div class='preferenceWrapper'><div class='preference'>" + source.innerHTML + "</div><a class='dropPreference' onclick='dropSource(\"" + source.id + "\")'>Unselect</a></div></li>";

				if (document.getElementById("confirmPrefs").style.display == "" || document.getElementById("confirmPrefs").style.display == "none")
				{
					showConfirmButton();
				}
			}

			function dropSource(id)
			{
				var toRemove = document.getElementById(id + "-block");
				toRemove.parentNode.removeChild(toRemove);
				var sources = document.getElementsByClassName("sourceButton");
				for (var i = 0; i < sources.length; i++) 
				{
					if (sources[i].id == id)
					{
						sources[i].style.display = "";
					}
				}

				if (document.getElementById("selectedList").innerHTML.trim() == "")
				{
					document.getElementById("sourceButtonsWrapper").classList.remove("shrink");
					var searchBlock = document.getElementById("sourceSearchWrapper");
					searchBlock.classList.remove("shrink");
					searchBlock.style.marginBottom = "0";
					var selectedList = document.getElementById("selectedListWrapper");
					selectedList.classList.remove("shrink");
					selectedList.style.marginBottom = "0";
					document.getElementById("confirmPrefs").classList.remove("appear");
				}
				else if (document.getElementById("confirmPrefs").style.display == "")
				{
					showConfirmButton();
				}
			}

			function sendPreferences()
			{
				var prefs = document.getElementById("selectedList").children;
				for (var i = 0; i < prefs.length; i++) 
				{
					id = prefs[i].id.replace("-block", "");
					document.getElementById("data").innerHTML += "<input type='hidden' name='preferences[]' value='" + id + "' />";
				}
			}
		</script>
	</head>

	<body>
		<?php

		if (!isset($_SESSION["hipps_user"]))
		{
			showHeader("");

			print("<div id='content'><div class='error'>Error occurred; please log back in.</div></div>");
		}
		else if ($_SERVER["REQUEST_METHOD"] == "GET")
		{
			$response = file_get_contents("https://newsapi.org/v2/sources?language=en&country=us&apiKey=428aecd7fbda4629873438afd20a033c");
			$response = json_decode($response);
			if ($response->status == "ok")
			{ 
				try
				{
					$db = setupDB();

					$query = "SELECT name, api_id FROM (chipps_users u JOIN chipps_users_prefs up ON u.id = up.user_id JOIN chipps_preferences p ON up.pref_id = p.id) WHERE username = :user;";
					$input = $db->prepare($query);
					$input->execute(array('user' => $_SESSION["hipps_user"]));
					$userPrefs = $input->fetchAll();

					if (count($userPrefs) == 0)
					{
						print("<div id='accountSetUp'>
							<div id='welcomeHeaderWrapper'>
								<h1>Welcome!</h1>
								NEWS wants to show you news that is most relevant to your interests. To complete setting up your account, select sources from below that you would prefer to see stories from.
							</div>");
					}
					else
					{
						showHeader("");

						print("<div id='content'>
							<div id='addHeaderWrapper'>
								<h1>Select Preferences</h1>
								Choose the sources that you would prefer to see stories from.
							</div>");
					}
					?>
					<div id="sourceSearchWrapper">
						<input type="text" class="textInput" id="sourceSearch" placeholder="Search..." onkeyup="filterSources();" />
						<div id="sourceButtonsWrapper">
							<?php
							$alreadyAddedSources = array();
							foreach ($response->sources as $source)
							{
								$alreadyAdded = false;
								foreach ($userPrefs as $p)
								{
									if ($p["api_id"] == $source->id)
									{
										$alreadyAdded = true;
										break;
									}
								}
								if ($alreadyAdded)
								{
									array_push($alreadyAddedSources, $source);
									print("<button class='sourceButton' id='" . $source->id . "' onclick='addToList(this);' style='display:none;'>" . $source->name . "</button>");
								}
								else
								{
									print("<button class='sourceButton' id='" . $source->id . "' onclick='addToList(this);'>" . $source->name . "</button>");
								}
							} ?>
						</div>
					</div>
					<div id="selectedListWrapper">
						<ul id="selectedList">
							<?php
							foreach ($alreadyAddedSources as $source)
							{
								print("<li id='" . $source->id . "-block'><div class='preferenceWrapper'><div class='preference'>" . $source->name . "</div><a class='dropPreference' onclick='dropSource(\"" . $source->id . "\")'>Unselect</a></div></li>");
							}
							?>
						</ul>
					</div>
					<form action="add.php" method="POST">
						<span id="data"></span>
						<input type="submit" id="confirmPrefs" value="Confirm preferences" onclick="sendPreferences();" />
					</form>
					<?php
				}
				catch (PDOException $ex)
				{
					print("Database error: " . $ex->getMessage());
				}
			}
		}
		else if ($_SERVER["REQUEST_METHOD"] == "POST")
		{
			showHeader("");

			print("<div id='content'>");

			if (isset($_POST["preferences"]) && count($_POST["preferences"]) != 0)
			{
				try
				{
					$db = setupDB();

					$query = "SELECT id FROM chipps_users WHERE username = :username;";
					$input = $db->prepare($query);
					$input->execute(array('username' => $_SESSION["hipps_user"]));
					$user_id = $input->fetchAll()[0][0];

					$query = "SELECT pref_id FROM (chipps_users u JOIN chipps_users_prefs up ON u.id = up.user_id) WHERE username = :user;";
					$input = $db->prepare($query);
					$input->execute(array('user' => $_SESSION["hipps_user"]));
					$currentPrefs = $input->fetchAll();

					$oldPrefs = array();
					foreach ($currentPrefs as $p)
					{
						array_push($oldPrefs, $p["pref_id"]);
					}

					$prefs = $_POST["preferences"];
					$newPrefs = array();
					foreach ($prefs as $p)
					{
						$query = "SELECT id FROM chipps_preferences WHERE api_id = :id;";
						$input = $db->prepare($query);
						$input->execute(array('id' => $p));
						$results = $input->fetchAll();
						if (count($results) == 0)
						{
							$response = file_get_contents("https://newsapi.org/v2/sources?language=en&country=us&apiKey=428aecd7fbda4629873438afd20a033c");
							$response = json_decode($response);
							if ($response->status == "ok")
							{
								foreach ($response->sources as $source)
								{
									if ($source->id == $p)
									{
										# adds the preference to the database table 
										$query = "INSERT INTO chipps_preferences (name, api_id) VALUES (:name, :api_id);";
										$input = $db->prepare($query);
										$input->execute(array('name' => $source->name, 'api_id' => $p));
									}
								}

								$query = "SELECT id FROM chipps_preferences WHERE api_id = :id;";
								$input = $db->prepare($query);
								$input->execute(array('id' => $p));
								array_push($newPrefs, $input->fetchAll()[0][0]);
							}
						}
						else
						{
							array_push($newPrefs, $results[0][0]);
						}
					}

					$toAdd = array();
					foreach ($newPrefs as $new)
					{
						$isNewPref = !in_array($new, $oldPrefs);
						if ($isNewPref)
						{
							array_push($toAdd, $new);
						}
					}

					$toDelete = array();
					foreach ($oldPrefs as $old)
					{
						$stillAPref = in_array($old, $newPrefs);
						if (!$stillAPref)
						{
							array_push($toDelete, $old);
						}
					}

					$prefsAdded = 0;
					foreach ($toAdd as $pref)
					{

						$query = "INSERT INTO chipps_users_prefs (user_id, pref_id) VALUES (:user, :pref);";
						$input = $db->prepare($query);
						$input->execute(array('user' => $user_id, 'pref' => $pref));

						$prefsAdded++;
					}
					
					$prefsDeleted = 0;
					foreach ($toDelete as $pref)
					{
						$query = "DELETE FROM chipps_users_prefs WHERE (user_id=:user AND pref_id=:pref);";
						$input = $db->prepare($query);
						$input->execute(array('user' => $user_id, 'pref' => $pref));

						$prefsDeleted++;
					}

					print("<h1>Success!</h1>");
					if ($prefsAdded > 0)
					{
						print("You successfully added " . $prefsAdded . " preference(s).<br>");
					}
					if ($prefsDeleted > 0)
					{
						print("You successfully deleted " . $prefsDeleted . " preference(s).<br>");
					}
					print("You can change preferences at any time from your account page.<hr>
						<a href='index.php'>Click here to go to the home page.</a>");
				}
				catch (PDOException $ex)
				{
					print("Database error: " . $ex->getMessage());
				}

			}
		}
		print("</div>");
		?>
	</body>

</html>