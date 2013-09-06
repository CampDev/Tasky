<!DOCTYPE html>
<html>
	<head>
		<title>Developer Ressources - Tasky</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
	</head>
	<body>
   		<div class="container">
		<?php
			require_once('markdown.php');
			$markdown = '#Tasky Post Type Documentation

Tasky uses two custom post types to store tasks and lists in your Tent account. These two post types are, of course, available for every other app too and documented below:

(Required parameters are bold, optional parameters are regular)

##Task 
####URL: http://cacauu.de/tasky/task/v0.1

* **Title (required) - Title describing the task**

* **Status (required) - Status of the post *(Possible: Done, To Do; see fragments.md for more detailed used of the status in tasks)***

* Priority (optional) - Priority of the task *(Possible in Tasky: 0 (low), 1 (average), 2 (high), 3 (urgent); Default in Tasky: 1 (average))*

* List (optional) - List for the task *(Default in Tasky: To Do)*

* Assigned User - (optional) - User who is assigned to *do* the task *(In the JSON: Assignee)*

* Notes (optional) - Additional notes about the task. Notes can use [Tent-flavored Markdown](https://tent.io/docs/post-types#markdown) to add links and a certain style to the content.

* Due Date (optional) - Unix epoch of date (and time) the task is due *(In the JSON: duedate)*

* Public (optional) - Public or private task *(Possible: True or False, managed through Tent permissions)*

* Group (optional) - Users who are allowed to view the task *(Only for private tasks, managed through Tent mentions)*

##Task List 
####URL: http://cacauu.de/tasky/list/v0.1

* **Name (required) - Title of the list, brief description**

* Description (optional) - Description of the list';
			$html = Markdown($markdown);
			echo $html;

			$fragments = '#Usage of Fragments

##New post

The status field in the task post type is used additionally to the fragment of the post type which allows apps to filter tasks directly in the request 
to the Tent server. When requesting done tasks only, the requested post type should be ```http://cacauu.de/tasky/task/v0.1#done```, when requesting tasks which are *to do*, the requested post type should be ```http://cacauu.de/tasky/task/v0.1#todo``` and 
when requesting all tasks it should be ```http://cacauu.de/tasky/task/v0.1```.

The usage of fragments also requires developers to adjust the post type they are using when creating a new task. In the common 
case, all the new tasks will be *to do* so the used post type should be ```http://cacauu.de/tasky/task/v0.1#todo``` and not ```http://cacauu.de/tasky/task/v0.1```. If an app creates a new post that is already done, it should use ```http://cacauu.de/tasky/task/v0.1#done```. 

##New version

Also when creating a new version of a task that completes the task or marks a completed task as to do, the app should update the fragment of the post to allow other apps to filter the tasks correctly. You can update the fragment of a post when ```PUTTING```a new version to the server and changing the used type from ```http://cacauu.de/tasky/task/v0.1#todo``` to ```http://cacauu.de/tasky/task/v0.1#done``` for example.

This is important to consider when using the Tasky task post type because only the correct usage of fragments allows other apps to filter the posts based on *to do* and *done*. ';
	echo Markdown($fragments);
	include_once('footer.php');
		?>
		</div>
	</body>
</html>
