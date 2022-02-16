<?php
session_start();
require("utils.php");
?>

<!DOCTYPE html>

<html>
	
	<head>
		<title>Search</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
		<script>
			function updateSearchResults(input)
			{
				if (input != "")
				{
					xmlhttp = new XMLHttpRequest();
		  			xmlhttp.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							document.getElementById("search").blur();
							if (this.responseText != "")
							{
								document.getElementById("input").value = "";
			                	document.getElementById("results").innerHTML = this.responseText;
			                }
			                else
			                {
			                	document.getElementById("results").innerHTML = "No results.";
			                	var searchField = document.getElementById("input");
		                		searchField.select();
		                		searchField.style.border = "1px solid red";
		                		searchField.style.backgroundColor = "#FEE";
		                		searchField.focus();
			                }
						}
					};
					xmlhttp.open("POST","updateSearch.php",true);
					xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					xmlhttp.send("input="+input);
				}
			}

			function showFull(imgSrc, title, src, hasSrcLink, text, link)
			{
				document.getElementById("fullBackground").style.display = "block";
				document.getElementById("fullArticle").style.display = "block";
				document.getElementById("fullImg").src = imgSrc;
				document.getElementById("fullTitle").innerHTML = title;
				var source = document.getElementById("fullSrc")
				source.innerHTML = src;
				if (hasSrcLink)
				{
					source.classList.add("withLink");
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
		showHeader("search"); ?>
		<div id="fullBackground"></div>

		<div id="content">
			<h1>Search</h1>
			Search for recent news stories by keyword.<hr>
			<input type="text" class="textInput" id="input" placeholder="Search..." autofocus="true" /><br><br>
			<button id="search" onclick="updateSearchResults(input.value)">Search</button><hr>
			<script>
				document.getElementById("input").addEventListener("keypress", function(event) {
					if (event.keyCode === 13) {
						event.preventDefault();
						document.getElementById("input").blur();
						document.getElementById("search").focus();
						document.getElementById("search").click();
					}
					else if (document.getElementById("results").innerHTML == "No results.")
					{
						var searchField = document.getElementById("input");
		                searchField.style.border = "1px solid gray";
		                searchField.style.backgroundColor = "white";
					}
				});
			</script>

		<?php
		initializeFullArticleView(); ?>

			<span id="results"></span>
		</div>
	</body>

</html>
			