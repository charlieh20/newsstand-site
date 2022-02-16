<?php
session_start();
require("utils.php");
?>

<!DOCTYPE html>

<html>

	<head>
		<title>Source info</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script>
			function showFull(imgSrc, title, src, srcLink, text, link)
			{
				document.getElementById("fullBackground").style.display = "block";
				document.getElementById("fullArticle").style.display = "block";
				document.getElementById("fullImg").src = imgSrc;
				var articleTitle = title.replace("newLine", "\n");
				articleTitle = articleTitle.replace("doubleQuote", "\"");
				articleTitle = articleTitle.replace("singleQuote", "'");
				document.getElementById("fullTitle").innerHTML = articleTitle;
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

		</script>
	</head>

	<body>
		<?php
		showHeader("");

		print("<div id='fullBackground'></div>");

		if ($_SERVER["REQUEST_METHOD"] == "GET")
		{
			if (isset($_GET["source"]))
			{
				$targetID = $_GET["source"];
				$response = file_get_contents("https://newsapi.org/v2/sources?apiKey=428aecd7fbda4629873438afd20a033c");
				$response = json_decode($response);
				print("<div id='content'>");

				initializeFullArticleView();

				if ($response->status == "ok")
				{
					foreach ($response->sources as $source)
					{
						if ($source->id == $targetID)
						{
							print("<h1>" . $source->name . "</h1>" . 
								$source->description . "<br><br>
								<div id='linkToSrcWebpage'><a href='" . $source->url . "' target='_blank'>Link to webpage</a></div><hr>");
							$response = file_get_contents("https://newsapi.org/v2/everything?sources=" . $targetID . "&pageSize=25&apiKey=428aecd7fbda4629873438afd20a033c");
							$response = json_decode($response);
							if ($response->status == "ok")
							{
								if (count($response->articles) > 0)
								{
									print("<h2>Recent stories:</h2>");
									print("<ul class='storyList'>");
									$articlesShown = 0;
									foreach($response->articles as $article)
									{
										showArticlePreview($article, false, "s" . $articlesShown);
										$articlesShown++;
									}
									print("</ul>");
								}
								else
								{
									print("No stories from this source are currently avaiable.");
								}
							}
							break;
						}
					}
				}
				print("</div>");
			}
		}


		?>
	</body>

</html>