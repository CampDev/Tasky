<?php
session_start();
$time = time();
if (!isset($_SESSION['entity'])) {
	$error = "You're not logged in!";
		header('Location: index.php?error='.urlencode($error));
}
else {
require_once('functions.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Tasky</title>
        <link rel="icon" type="image/ico" href="favicon.ico" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<meta charset="utf-8">
		<script type="text/javascript" src="live.js"></script>
	</head>

	<body>

    <?php include('header.php');?>

		<div id="body_wrap">
			<?php
			$entity_sub = substr_replace($_SESSION['entity'] ,"",-1);
			if (isset($_GET['id'])) {
				$id = $_GET['id'];
				$nonce = uniqid('Tasky_', true);
				$current_url = str_replace("{entity}", urlencode($entity_sub), $_SESSION['single_post_endpoint']);
				$current_url = str_replace("{post}", $id, $current_url);
				$mac_current = generate_mac('hawk.1.header', $time, $nonce, 'GET', str_replace($_SESSION['entity'], "/", $current_url), $_SESSION['entity_sub'], '443', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
				$log = fopen('update_request.txt', 'w');
				$ch_current = curl_init();
				curl_setopt($ch_current, CURLOPT_URL, $current_url);
				curl_setopt($ch_current, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch_current, CURLOPT_VERBOSE, 1);
				curl_setopt($ch_current, CURLOPT_STDERR, $log);
				curl_setopt($ch_current, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac_current, $time, $nonce, $_SESSION['client_id'])));
				$current_task_json = curl_exec($ch_current);
				curl_close($ch_current);
				fclose($log);
				$current_task = json_decode($current_task_json, true);
			?>
            <div id="new-task">
            <h2>Edit your task</h2>
            <p align="center">Created at: <?php echo created_date($current_task['post']['published_at']); ?></p>
			<form align="center" method="post" action="task_handler.php?type=update&id=<?php echo $current_task['post']['id']; ?>&parent=<?php echo $current_task['post']['version']['id']; ?>">
				<p><input type="text" name="title" value="<?php echo $current_task['post']['content']['title']; ?>" class="text" placeholder="Your awesome task" /></p>
				<label>Status: <select name="status" class="select">
					<?php
						if ($current_task['post']['content']['status'] == 'todo') {
							echo "<option SELECTED value='todo'>To Do</option>";
							echo "<option value='done'>Done</option>";
						}
						else {
							echo "<option value='todo'>To Do</option>";
							echo "<option SELECTED value='done'>Done</option>";
						} 
						?>
				</select></label>
				<label>List: 
                    <select name="list" class="select">
						<?php
						foreach ($lists['posts'] as $list) {
							if(!is_null($list['content']['name'])) {
								if ($list['id'] == $current_task['post']['content']['list']) {
									echo "<option SELECTED value='".$list['id']."'>".$list['content']['name']."</option>";
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
						<?php
							switch ($current_task['post']['content']['priority']) {
								case '0':
									echo "<option SELECTED value='0'>Low</option>";
									echo "<option value='1'>Average</option>";
									echo "<option value='2'>High</option>";
									echo "<option value='3'>Urgent</option>";
									break;

								case '1':
									echo "<option value='0'>Low</option>";
									echo "<option SELECTED value='1'>Average</option>";
									echo "<option value='2'>High</option>";
									echo "<option value='3'>Urgent</option>";
									break;

								case '2':
									echo "<option value='0'>Low</option>";
									echo "<option value='1'>Average</option>";
									echo "<option SELECTED value='2'>High</option>";
									echo "<option value='3'>Urgent</option>";
									break;

								case '3':
									echo "<option value='0'>Low</option>";
									echo "<option value='1'>Average</option>";
									echo "<option value='2'>High</option>";
									echo "<option SELECTED value='3'>Urgent</option>";
									break;
								
								default: //Shouldn't happen
									echo "<option value='0'>Low</option>";
									echo "<option SELECTED value='1'>Average</option>";
									echo "<option value='2'>High</option>";
									echo "<option value='3'>Urgent</option>";
									break;
							}
						?>
					</select>
				</label>
				<label>Due date: 
					<input type="date" name="duedate" <?php if(!is_null($current_task['post']['content']['duedate']) AND isset($current_task['post']['content']['duedate']) AND $current_task['post']['content']['duedate'] != '') {echo 'value="'.date('Y-m-d', $current_task['post']['content']['duedate']).'"';} ?>" class="select"> 
				</label>
					<p><textarea name="notes" class="note"><?php if(!is_null($current_task['post']['content']['notes'])) {echo $current_task['post']['content']['notes'];} ?></textarea></p>
					<p>You can use <a href="https://tent.io/docs/post-types#markdown">Tent-flavored Markdown</a> in your notes to add links and style to the text</p>
					<p><input type="submit" class="submit" value="Save changes"></p>
			</form>
			<p><a href="task_handler.php?type=delete&id=<?php echo $current_task['post']['id']; ?>"  style='color: red'>Delete task</a></p>
            </div>
            <?php
        	}
        	elseif (isset($_GET['list'])) {
        		$id = $_GET['list'];
				$entity_sub_list = substr_replace($_SESSION['entity'] ,"",-1);
				$current_url = str_replace("{entity}", urlencode($entity_sub_list), $_SESSION['single_post_endpoint']);
				$current_url = str_replace("{post}", $id, $current_url);
				$mac_current = generate_mac('hawk.1.header', time(), $nonce, 'GET', str_replace($_SESSION['entity'], "/", $current_url), $_SESSION['entity_sub'], '443', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
				$ch_current = curl_init();
				curl_setopt($ch_current, CURLOPT_URL, $current_url);
				curl_setopt($ch_current, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch_current, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac_current, time(), $nonce, $_SESSION['client_id'])));
				$current_list = curl_exec($ch_current);
				curl_close($ch_current);
				$current_list = json_decode($current_list, true);
				?>
            <div id="new-task">
            <h2>Edit your list</h2>
				<form align="center" method="post" action="task_handler.php?type=update_list&id=<?php echo $current_list['post']['id']; ?>&parent=<?php echo $current_list['post']['version']['id']; ?>">
					<p><input name="name" type="text" class="text" value="<?php echo $current_list['post']['content']['name']; ?>" />
					<input type="submit" class="submit" value="Update list" /></p>
				</form>
				<p><a href="task_handler.php?type=delete&id=<?php echo $current_list['post']['id']; ?>" style='color: red'>Delete list</a></p>
        	<?php
        	}
            ?>
            </div>
		</div>
<?php include('footer.php') ?>

<?php }
?>
