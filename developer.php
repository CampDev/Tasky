<html>
	<head>
		<title>Developer Ressources - Tasky</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
		<?php
			require_once('markdown.php');
			$markdown = '#Tasky Post Type Documentation

Tasky uses two custom post types to store tasks and lists in your Tent account. These two post types are, of course, available for every other app to and documented below:

(Required parameters are bold, optional parameters are regular)

##Task 
####URL: http://cacauu.de/tasky/task/v0.1

* **Title (required) - Title describing the task**

* **Status (required) - Status of the post *(Possible: Done, Due)***

* Priority (optional) - Priority of the task *(Possible in Tasky: 0 (low), 1 (average), 2 (high), 3 (urgent); Default in Tasky: 1 (average))*

* List (optional) - List for the task *(Default in Tasky: To Do)*

* Tags (optional) - Tags for the task

* Assigned User - (optional) - User who is assigned to *do* the task *(In the JSON: Assignee)*

* Notes (optional) - Additional notes about the task

* Due Date (optional) - Unix epoch of date (and time) the task is due *(In the JSON: duedate)*

* Public (optional) - Public or private task *(Possible: True or False, managed through Tent permissions)*

* Group (optional) - Users who are allowed to view the task *(Only for private tasks, managed through Tent mentions)*

##Task List 
####URL: http://cacauu.de/tasky/list/v0.1

* **Name (required) - Title of the list, brief description**

* Description (optional) - Description of the list';
			$html = Markdown($markdown);
			echo $html;
		?>
	</body>
</html>