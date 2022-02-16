<?php
require("utils.php");

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	$input = str_replace(" ", "%20", $_POST["input"]);
	$response = file_get_contents("https://newsapi.org/v2/everything?q=" . $input . "&sortBy=relevancy&pageSize=25&apiKey=428aecd7fbda4629873438afd20a033c");
	$response = json_decode($response);
	if ($response->status == "ok")
	{
		if (count($response->articles) > 0)
		{
			print("<h1>Search Results</h1>");
			print("<ul class='storyList'>");
			$articlesShown = 0;
			foreach($response->articles as $article)
			{
				showArticlePreview($article, true, "s" . $articlesShown);
				$articlesShown++;
			}
			print("</ul>");
		}
	} 
} ?>