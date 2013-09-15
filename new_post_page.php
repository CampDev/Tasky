<?php
if(session_id() == '') {
	session_start();
}
require_once('functions.php');
$entity = $_SESSION['entity'];
$entity_sub = $_SESSION['entity_sub'];
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Tasky</title>
        <link rel="icon" type="image/ico" href="favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script type="text/javascript" src="live.js"></script>
	</head>

	<body>



<div class="container">
 <?php include_once('header.php') ?>
<div id="new-task">
				<h2>Create a new task</h2>
				<form align="center" action="task_handler.php?type=task" method="post">
					<p><input type="text" name="title" placeholder="Your awesome task" class="text" /></p>
					<label>List:
						<select name="list" label="list" class="select">
							<?php
							foreach ($lists['posts'] as $list) {
								if(!is_null($list['content']['name'])) {
									if (isset($_GET['list'])) {
										if ($list['id'] == $_GET['list']) {
											echo "<option SELECTED value='".$list['id']."'>".$list['content']['name']."</option>";
										}
										else {
											echo "<option value='".$list['id']."'>".$list['content']['name']."</option>";
										}
									}
									else {
										echo "<option value='".$list['id']."'>".$list['content']['name']."</option>";
									}
								}
							}
							?>
						</select>
					</label>
					<label>Priority: 
						<select name="priority" size="1" class="select">
							<option value="0">Low</option>
							<option SELECTED value="1">Average</option>
							<option value="2">High</option>
							<option value="3">Urgent</option>
						</select>
					</label>
					<label>Due date: 
						<!-- <input type="date" min="<?php echo date('Y-m-d', time()); ?>" name="duedate" class="select"> -->
						<input type="date" name="duedate" class="select">
					</label>
                    </p>
					<p><textarea name="notes" placeholder="Add a description (optional)" class="note"></textarea> </p>
					<p style="clear: both;">You can use <a href="https://tent.io/docs/post-types#markdown">Tent-flavored Markdown</a> in your notes to add links and style to the text.</p>	
                    <p style="clear: both; height: 15px;"><input type="submit" value="Add task" class="submit"></p>
				</form>
</div>

</div>

	</body>
</html>
