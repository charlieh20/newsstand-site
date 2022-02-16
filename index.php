<?php
session_start();
require("utils.php");
?>

<!DOCTYPE html>

<html>

	<head>
		<title>News</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script>
			function showFull(imgSrc, title, src, srcLink, text, link)
			{
				document.getElementById("fullBackground").style.display = "block";
				document.getElementById("fullArticle").style.display = "block";
				document.getElementById("fullImg").src = imgSrc;
				document.getElementById("fullTitle").innerHTML = title;
				var source = document.getElementById("fullSrc")
				if (srcLink != "")
				{
					source.innerHTML = "<a href='" + srcLink + "'>" + src + "</a>";
					source.classList.add("withLink");
				}
				else
				{
					source.innerHTML = src;
				}
				var articleText = text.replace("newLine", "\n");
				articleText = articleText.replace("doubleQuote", "\"");
				articleText = articleText.replace("singleQuote", "'");
				document.getElementById("fullText").innerHTML = articleText;
				document.getElementById("articleLink").href = link;
			}

			function closeWindow()
			{
				document.getElementById("fullBackground").style.display = "none";
				document.getElementById("fullArticle").style.display = "none";
				document.getElementById("fullSrc").classList.remove("withLink");
			}

			function preventFull(thisDiv)
			{
				thisDiv.parentElement.onclick = null;
			}
		</script>
	</head>

	<body>
		<?php
		showHeader("home");

		print("<div id='fullBackground'></div>");

		print("<div id='content'>");

		initializeFullArticleView();

		if (!isset($_SESSION["hipps_user"]))
		{
			$response = file_get_contents("https://newsapi.org/v2/top-headlines?country=us&pageSize=100&apiKey=428aecd7fbda4629873438afd20a033c");
			$response = json_decode($response);
			if ($response->status == "ok")
			{
				print("<h1>Top Stories</h1>");
				print("<ul class='storyList'>");
				$articlesShown = 0;
				foreach ($response->articles as $article)
				{
					showArticlePreview($article, true, "t" . $articlesShown);
					$articlesShown++;
				}
				print("</ul>");
			}
		}
		else
		{
			try
			{
				$db = setupDB();

				$query = "SELECT name, api_id FROM (chipps_users u JOIN chipps_users_prefs up ON u.id = up.user_id JOIN chipps_preferences p ON up.pref_id = p.id) WHERE username = :username ORDER BY name ASC;";
				$input = $db->prepare($query);
				$input->execute(array('username' => $_SESSION["hipps_user"]));
				$userPrefs = $input->fetchAll();
				if (count($userPrefs) > 0)
				{
					$sourcesStr = "";
					foreach ($userPrefs as $pref)
					{
						$sourcesStr .= $pref["api_id"] . ",";
					}
					$sourcesStr = substr($sourcesStr, 0, strlen($sourcesStr)-1);
					$response = file_get_contents("https://newsapi.org/v2/top-headlines?sources=" . $sourcesStr . "&pageSize=50&apiKey=428aecd7fbda4629873438afd20a033c");
					$response = json_decode($response);

					if ($response->status == "ok")
					{
						print("<h1>Top Stories for You</h1>");
						print("<ul class='storyList'>");
						$articlesShown = 0;
						foreach ($response->articles as $article)
						{
							if (showArticlePreview($article, true,"t" . $articlesShown))
							{
								$articlesShown++;
							}
							if ($articlesShown == 3)
							{
								break;
							}
						}
						print("</ul>");
					}
					foreach ($userPrefs as $pref)
					{
						print("<h1>" . $pref["name"] . "</h1>");

						$response = file_get_contents("https://newsapi.org/v2/everything?sources=" . $pref["api_id"] . "&pageSize=50&apiKey=428aecd7fbda4629873438afd20a033c");
						$response = json_decode($response);
						if ($response->status == "ok")
						{
							print("<ul class='storyList'>");
							$articlesShown = 0;
							foreach ($response->articles as $article)
							{
								if (showArticlePreview($article, false, $article->source->id . "" . $articlesShown))
								{
									$articlesShown++;
								}
								if ($articlesShown == 3)
								{
									break;
								}
							}
							print("</ul>");
						}
					}
				}
				else
				{
					print("Add preferred sources!");
				}
			}
			catch (PDOException $ex)
			{
				print("Database error: " . $ex->getMessage());
			}
		}
		print("</div>");
		?>
	</body>

</html>