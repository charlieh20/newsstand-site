<?php
function setupDB()
{
	$db = new PDO("'mysql:dbname=1920project;host=mysql.1920.lakeside-cs.org',
	'student1920', 'm545CS41920'");

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	return $db;
} 

function showHeader($currentPage)
{
	print("<div id='banner'>");
		if ($currentPage == "home")
		{
			print("<img id='logo' src='logo.png' alt='Logo'>
				<div class='selectedLink' id='homeTab'>Home<div id='underline'></div></div>");
		}
		else 
		{
			print("<a href='index.php'><img id='logo' src='logo.png' alt='Logo'></a>
				<a href='index.php'><div class='link' id='homeTab'>Home</div></a>");
		}
		if ($currentPage == "search")
		{
			print("<div class='selectedLink' id='searchTab'>Search<div id='underline'></div></div>");
		}
		else
		{
			print("<a href='search.php'><div class='link' id='searchTab'>Search</div></a>");
		}
		if (isset($_SESSION["hipps_user"]))
		{
			if ($currentPage == "account")
			{
				print("<div class='selectedLink' id='accountTab'>Account<div id='underline'></div></div>");
			}
			else
			{
				print("<a href='account.php'><div class='link' id='accountTab'>Account</div></a>");
			}
		}
		else
		{
			if ($currentPage == "login")
			{
				print("<div class='selectedLink' id='accountTab'>Log In<div id='underline'></div></div>");
			}
			else
			{
				print("<a href='login.php'><div class='link' id='accountTab'>Log In</div></a>");
			}
		}
	print("</div>");
}

/*
 * checks that the username and password passed in exist in the database, and prints an appropriate error message if the combo does not exist
 * param: database – link to the database
 * param: username – the username that needs to be checked
 * param: password – the password that needs to be checked
 * return: TRUE if the combo exists, FALSE if it doesn't
 */
function checkLogin($database, $username, $password)
{
	$query = "SELECT password FROM chipps_users WHERE username = :usernameinput;";
	$input = $database->prepare($query);
	$input->execute(array('usernameinput' => $username));

	$results = $input->fetchAll();

	$correctPassword = getWithUsername($database, $username, "password");

	if ($correctPassword != null)
	{
		if (password_verify($password, $correctPassword))
		{
			return true;
		}
		else
		{
			print("Incorrect password.");
			return false;
		}
	}
	else
	{
		print("That user does not exist.");
		return false;
	}
}

/*
 * gets the desired value from the database corresponding to the passed in username
 * param: database – the link to the database
 * param: username – the username corresponding the the desired value
 * param: value – the column of the desired value
 * return: the desired value if it exists and is unique, null if not
 */
function getWithUsername($database, $username, $value)
{
	$query = "SELECT $value FROM chipps_users WHERE username=:user;";
	$input = $database->prepare($query);
	$input->execute(array('user' => $username));
	$results = $input->fetchAll();
	if (isset($results[0][0]))
	{
		return $results[0][0];
	}
	else
	{
		return null;
	}
}

function showArticlePreview($article, $showSource, $id)
{
	if ($article->urlToImage != null && $article->urlToImage != "null")
	{		
		if (strrpos($article->title, " - ") > -1)
		{
			$title = substr($article->title, 0, strrpos($article->title, " - "));
			$source = substr($article->title, strrpos($article->title, " - ")+3);
		}
		else
		{
			$title = $article->title;
			if ($article->source->name != null and $article->source->name != "null")
			{
				$source = $article->source->name;
			}
			else
			{
				$showSource = false;
			}
		}
		if ($showSource && $article->source->id != null && $article->source->id != "null")
		{
			$linkToSrc = "'source.php?source=" . $article->source->id . "'";
		}
		else
		{
			$linkToSrc = "''";
		}
		print("<li>
			<div class='articlePreview");
			if (!$showSource)
			{
				print(" noSource");
			}
			$titleToSend = $text = preg_replace("/\"/", "\\\"", $title);
			$titleToSend = str_replace("'", "\\'", $titleToSend);

			$text = preg_replace("/\n/", "\\n", $article->content);
			$text = preg_replace("/\r/", "", $text);
			$text = preg_replace("/\"/", "\\\"", $text);
			$text = str_replace("'", "\\'", $text);
			if (strrpos($text, " [+") > 0)
			{
				$text = substr($text, 0, strrpos($text, " [+"));
			}
			print("' onclick=\"showFull('" . $article->urlToImage ."', '" . $titleToSend . "', '" . $source . "', " . $linkToSrc . ", '" . $text . "', '" . $article->url . "');\">");
			print("<img class='previewImage' src='" . $article->urlToImage . "' alt='Article image'>");
			print("<div class='title'>" . $title . "</div>");
			if ($showSource)
			{
				if ($linkToSrc != "''")
				{
					print("<div class=sourceWithLink onclick='preventFull(this);'><a href=" . $linkToSrc . "'>" . $source . "</a></div>");
				}
				else
				{
					print("<div class=source>" . $source . "</div>");
				}
			}
			print("</div>
		</li>");
		return true;
	}
	return false;
}

function initializeFullArticleView()
{
	print("<div id='fullArticle'>
				<div id='closeWindow' onclick='closeWindow();'><span class='cross'></span></div>
				<img id='fullImg' src='' alt='Article image'>
				<hr>
				<div id='titleSrcWrapper'>
					<strong><div id='fullTitle'></div></strong>
					<div id='fullSrc'></div>
				</div>
				<div id='fullText'></div>
				<hr>
				<div id='aricleLinkWrapper'>
					<a id='articleLink' href='' target='_blank'>Read full article</a>
				</div>
			</div>");
}
?>
