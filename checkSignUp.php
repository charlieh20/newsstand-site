<?php
session_start();
require("utils.php");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	# checks the login when the login/sign up form is submitted
	try 
	{	
		$username = $_POST["username"];
		$password = $_POST["password"];
		$password2 = $_POST["password2"];

		$db = setupDB();

		if (strlen($username) != 0 && strlen($password) != 0 && strlen($password) != 0)
		{
			# checks to see if the username already exists in the database
			$query = "SELECT username FROM chipps_users WHERE username = :usernameinput;";
			$input = $db->prepare($query);
			$input->execute(array('usernameinput' => $username));

			$results = $input->fetchAll();

			if (count($results) == 0)
			{
				# tries to add the new user's information to the database if the type is sign up and the username and password are both not empty
				if ($password == $password2)
				{
					$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
					# adds the username and password of the new user to the database
					$query = "INSERT INTO chipps_users (username, password) VALUES (:usernameinput, :passwordinput);";
					$input = $db->prepare($query);
					$input->execute(array('usernameinput' => $username, 'passwordinput' => $hashedPassword)); 

					$_SESSION["hipps_user"] = $username;
				}
				else
				{
					print("The passwords do not match.");
				}
			}
			else
			{
				# shows an error message if the username already exists in the database
				print("There is already a user with this username!");
			}
		}
		else
		{
			print("Please enter something in all the fields.");
		}
	}
	catch (PDOException $ex)
	{
		print("Database error: " . $ex->getMessage());
	}
}
?>