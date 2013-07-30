<?php
session_start();
if (!isset($_SESSION['entity'])) {
	$error = "You're not logged in!";
		header('Location: index.php?error='.urlencode($error));
}
else {
require_once('functions.php');
?>
<html>
	<head>
		<title>Tasky</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<meta charset="utf-8">
		<script type="text/javascript" src="live.js"></script>
	</head>

	<body>

    <?php include('header.php');?>

		<div id="body_wrap">
			<?php
			if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$nonce = uniqid('Tasky_', true);
			$entity_sub = substr_replace($_SESSION['entity'] ,"",-1);

			//Getting the current version of the post
			$current_url = str_replace("{entity}", urlencode($entity_sub), $_SESSION['single_post_endpoint']);
			$current_url = str_replace("{post}", $id, $current_url);
			$mac_current = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts/'.urlencode($entity_sub)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
			$ch_current = curl_init();
			curl_setopt($ch_current, CURLOPT_URL, $current_url);
			curl_setopt($ch_current, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch_current, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac_current, time(), $nonce, $_SESSION['client_id'])));
			$current_task_json = curl_exec($ch_current);
			curl_close($ch_current);
			$current_task = json_decode($current_task_json, true);
			?>
            <div id="new-task">
            <h2>Edit your task</h2>
			<form align="center" method="post" action="task_handler.php?type=update&id=<?php echo $current_task['post']['id']; ?>&parent=<?php echo $current_task['post']['version']['id']; ?>">
				<p> <input type="text" name="title" value="<?php echo $current_task['post']['content']['title']; ?>" class="text" placeholder="Your awesome task" /></p>
				<select name="status" class="select">
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
				</select>

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
					<input type="date" name="duedate" min="<?php echo date('Y-m-d', time()); ?>" <?php if(!is_null($current_task['post']['content']['duedate']) AND isset($current_task['post']['content']['duedate']) AND $current_task['post']['content']['duedate'] != '') {echo 'value="'.date('Y-m-d', $current_task['post']['content']['duedate']).'"';} ?>" class="select"> 
					<p><textarea name="notes" class="note"><?php if(!is_null($current_task['post']['content']['notes'])) {echo $current_task['post']['content']['notes'];} ?></textarea></p>
					<p>You can use <a href="https://tent.io/docs/post-types#markdown">Tent-flavored Markdown</a> in your notes to add links and style to the text</p>
					<p><input type="submit"></p>
			</form>
            </div>
            <?php
        	}
        	elseif (isset($_GET['list'])) {
        		$id = $_GET['list'];
				$entity_sub_list = substr_replace($_SESSION['entity'] ,"",-1);
				$current_url = str_replace("{entity}", urlencode($entity_sub_list), $_SESSION['single_post_endpoint']);
				$current_url = str_replace("{post}", $id, $current_url);
				$mac_current = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts/'.urlencode($entity_sub_list)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
				$ch_current = curl_init();
				curl_setopt($ch_current, CURLOPT_URL, $current_url);
				curl_setopt($ch_current, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch_current, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac_current, time(), $nonce, $_SESSION['client_id'])));
				$current_list = curl_exec($ch_current);
				curl_close($ch_current);
				$current_list = json_decode($current_list, true);
				?>
				<form align="center" method="post" action="task_handler.php?type=update_list&id=<?php echo $current_list['post']['id']; ?>&parent=<?php echo $current_list['post']['version']['id']; ?>">
					<p><input name="name" type="text" value="<?php echo $current_list['post']['content']['name']; ?>" /></p>
					<p><textarea name="description" class="notes"><?php echo $current_list['post']['content']['description']; ?></textarea></p>
					<p><input type="submit" /></p>
				</form>
        	<?php
        	}
            ?>
		</div>
<?php include('footer.php') ?>

<?php }
?>