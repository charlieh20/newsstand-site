<?php
session_start();
require("utils.php");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	if (isset($_POST["username"]) && isset($_POST["password"]))
	{
		$user = $_POST["username"];
		$pass = $_POST["password"];
		try
		{
			$db = setupDB();

			if (checkLogin($db, $user, $pass))
			{
				$_SESSION["hipps_user"] = $user;
			}
		}
		catch (PDOExceptionx $ex)
		{
			print("Database error: " . $ex->getMessage());
		}
	}
	else
	{
		print("Username and password required.");
	}
}
?>