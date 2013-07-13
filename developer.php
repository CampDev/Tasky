<html>
	<head>
		<title>Developer Ressources - Tasky</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<?php
			require_once('markdown.php');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://gist.github.com/Cacauu/24ef6e73bb5b12a66007/raw/9d9271b741475c478e5404de73f61258ee9cf372/posts_types.md');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$markdown = curl_exec($ch);
			curl_close($ch);
			$html = Markdown($markdown);
			echo $html;
		?>
	</body>
</html>