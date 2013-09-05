<?php
session_start();
if (!isset($_SESSION['entity'])) {
	header('Location: landing.php');
}
require_once('functions.php');
require_once('tent-markdown.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Tasky</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<meta charset="utf-8">
		<script type="text/javascript" src="jquery.min.js"></script>
		<script type="text/javascript" src="jquery.leanModal.min.js"></script>
		<script>
			$(function() {
    			$('a[rel*=leanModal]').leanModal({ top : 200, closeButton: ".modal_close" });		
			});
		</script>
	</head>

	<body>

	<?php include('header.php'); ?>

			<div class="container" style="background: rgb(245, 245, 245); border-left: 1px solid #ddd; border-right: 1px solid #ddd;">
				<div class="sidebar">
                	All tasks<br><br>
                    Inbox<br><br>
                	Due today<br><br>
                	Upcoming<br><br>
                	Calendar
                		</div>

				<div class='task-list'>

				<div class="filters" style="text-align: left; font-size: 18px;">Tasks</div>

				<?php
				if (!isset($_GET['list'])) {
					unset($_SESSION['redirect_list']);
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/'.str_replace($_SESSION['entity'], "", $_SESSION['posts_feed_endpoint']).'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1', $entity_sub, '443', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1');
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					echo "<div>";
					if ($posts['posts'] == array()) { ?>
						<h2>No tasks. <a href="new_post_page.php">Add one!</a></h2>
					<?php }
					else {
						foreach ($posts['posts'] as $task) {
							$content = $task['content'];
							echo "<div id='single-task'>";
                        	echo "<div id='single-task-inner' class='".strtolower($content['status'])."'>";


							if (isset($content['status']) AND $content['status'] == 'To Do' OR $content['status'] == 'todo') {
								echo "<span style='color: #219807; float: left; margin-top: 12px; height: 28px;  margin-right: 20px;'><a href='task_handler.php?type=complete&id=".$task['id']."&parent=".$task['version']['id']."'>

<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='24px' height='24px' viewBox='0 0 512 512' enable-background='new 0 0 512 512' xml:space='preserve'> <path style='fill: gray;' id='checkbox-11-icon' d='M256,111c38.73,0,75.144,15.083,102.53,42.47S401,217.27,401,256s-15.083,75.144-42.47,102.53
	S294.73,401,256,401s-75.144-15.083-102.53-42.47S111,294.73,111,256s15.083-75.144,42.47-102.53S217.27,111,256,111z M256,71
	C153.827,71,71,153.828,71,256s82.827,185,185,185c102.172,0,185-82.828,185-185S358.172,71,256,71z'></path> </svg>

                            </a></span>";	
							}
							elseif (isset($content['status']) AND $content['status'] == 'Done' OR $content['status'] == 'done') {
								echo "<span style='color: #aaa; float: left; margin-top: 12px; height: 28px; margin-right: 20px;'><a href='task_handler.php?type=uncomplete&id=".$task['id']."&parent=".$task['version']['id']."'>

<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='24px' height='24px' viewBox='0 0 512 512' enable-background='new 0 0 512 512' xml:space='preserve'> <path style='fill: gray;' id='checkbox-11-icon' d='M457.861,145.415L251.546,351.744L137.86,238.028l48.407-48.415l65.276,65.295l157.89-157.89
L457.861,145.415z M384.02,261.689c-1.402,36.596-16.309,70.8-42.35,96.841C314.283,385.917,277.869,401,239.139,401
s-75.144-15.083-102.53-42.47s-42.47-63.8-42.47-102.53s15.083-75.144,42.47-102.53s63.8-42.47,102.53-42.47
c31.297,0,61.076,9.853,85.805,28.082l28.529-28.528C322.002,85.78,282.297,71,239.139,71c-102.173,0-185,82.828-185,185
s82.827,185,185,185c102.172,0,185-82.828,185-185c0-10.814-0.938-21.409-2.719-31.714L384.02,261.689z'></path> </svg>

                            </a></span>";

							}
							else {
								echo "";
							}


							echo "<div class='task-body'><div class='title'><a class='edit' href='edit.php?type=update&id=".$task['id']."'>".$content['title'];
                        	echo "</div>";

							if ($content['notes'] != '' AND !is_null($content['notes'])) {
								echo "<i><div class='note'>".Tent_Markdown($content['notes'])."</div></i></a></div>";
							}
							else {
								echo "</div>";
							}

						/*	if (isset($content['duedate']) AND $content['duedate'] != '') {
								if (date('d/M/Y', $content['duedate']) == date('d/M/Y', time())) {
									echo "<span style='color: cd0d00;'>Today</span>";
								}
								else {
									echo "<div class='date'>".date('d/M/Y', $content['duedate'])."</div>";
								}
							}
							else {
								echo "";
							}               */
							echo "<a class='delete' href='task_handler.php?type=delete&id=".$task['id']."'>

<svg style='float: right; margin-top: -26px; margin-right: 10px;' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='16px' height='16px' viewBox='0 0 512 512' enable-background='new 0 0 512 512' xml:space='preserve'> <polygon style='fill: rgb(82, 82, 82);' id='x-mark-icon' points='438.393,374.595 319.757,255.977 438.378,137.348 374.595,73.607 255.995,192.225 137.375,73.622
73.607,137.352 192.246,255.983 73.622,374.625 137.352,438.393 256.002,319.734 374.652,438.378 '></polygon> </svg>

                            </a>";

							echo "</div></div>";

						}
					}

					echo "</div></div><div style='clear: both;'></div>";
				}
				elseif (isset($_GET['list'])) {
					$_SESSION['redirect_list'] = $_GET['list'];
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

					//Getting tasks from the chosen list
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['list'], $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$init = curl_init();
					curl_setopt($init, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Ftask%2Fv0.1&mentions='.urlencode($_SESSION['entity_sub']).'+'.$_GET['list']);
					curl_setopt($init, CURLOPT_HTTPGET, 1);
					curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($init, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id']))); //Setting the HTTP header
					$posts = curl_exec($init);
					curl_close($init);
					$posts = json_decode($posts, true);
					if ($posts['posts'] != array()) {
						echo "<table>";
						foreach ($posts['posts'] as $task) {
							$content = $task['content'];
							echo "<tr class='".$content['status']."'>";

                    		if (isset($content['priority'])) {
                    			echo "<td style='width: 10px;'><div class='prio_".$content['priority']."'></div></td>";
                    		}
                  			else {
                  				echo "<td></td>";
                  			}

							echo "<td><a class='edit' href='edit.php?type=update&id=".$task['id']."'>".$content['title'];

							if ($content['notes'] != '' AND !is_null($content['notes'])) {
								echo "<br><i><div style='font-size: 11px;'>".Tent_Markdown($content['notes'])."</div></i></a></td>";
							}
							else {
								echo "</td>";
							}

							if (isset($content['duedate']) AND $content['duedate'] != '') {
								if (date('d/M/Y', $content['duedate']) == date('d/M/Y', time())) {
									echo "<td style='color: cd0d00;'>Today</td>";
								}
								else {
									echo "<td>".date('d/M/Y', $content['duedate'])."</td>";
								}
							}
							else {
								echo "<td></td>";
							}

							if (isset($content['status']) AND $content['status'] == 'To Do' OR $content['status'] == 'todo') {
								echo "<td style='color: #219807;'><a href='task_handler.php?type=complete&id=".$task['id']."&parent=".$task['version']['id']."'>

<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='24px' height='24px' viewBox='0 0 512 512' enable-background='new 0 0 512 512' xml:space='preserve'> <path style='fill: gray;' id='checkbox-11-icon' d='M256,111c38.73,0,75.144,15.083,102.53,42.47S401,217.27,401,256s-15.083,75.144-42.47,102.53
	S294.73,401,256,401s-75.144-15.083-102.53-42.47S111,294.73,111,256s15.083-75.144,42.47-102.53S217.27,111,256,111z M256,71
	C153.827,71,71,153.828,71,256s82.827,185,185,185c102.172,0,185-82.828,185-185S358.172,71,256,71z'></path> </svg>

                            </a></td>";	
							}
							elseif (isset($content['status']) AND $content['status'] == 'Done') {
								echo "<td style='color: #aaa;'><a href='task_handler.php?type=uncomplete&id=".$task['id']."&parent=".$task['version']['id']."'>

<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='24px' height='24px' viewBox='0 0 512 512' enable-background='new 0 0 512 512' xml:space='preserve'> <path style='fill: gray;' id='checkbox-11-icon' d='M457.861,145.415L251.546,351.744L137.86,238.028l48.407-48.415l65.276,65.295l157.89-157.89
L457.861,145.415z M384.02,261.689c-1.402,36.596-16.309,70.8-42.35,96.841C314.283,385.917,277.869,401,239.139,401
s-75.144-15.083-102.53-42.47s-42.47-63.8-42.47-102.53s15.083-75.144,42.47-102.53s63.8-42.47,102.53-42.47
c31.297,0,61.076,9.853,85.805,28.082l28.529-28.528C322.002,85.78,282.297,71,239.139,71c-102.173,0-185,82.828-185,185
s82.827,185,185,185c102.172,0,185-82.828,185-185c0-10.814-0.938-21.409-2.719-31.714L384.02,261.689z'></path> </svg>

                            </a></td>";	
							}
							else {
								echo "<td></td>";
							}

							echo "<td style='color: #cd0d00;'><a class='delete' href='task_handler.php?type=delete&id=".$task['id']."'>

<svg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='16px' height='16px' viewBox='0 0 512 512' enable-background='new 0 0 512 512' xml:space='preserve'> <polygon style='fill: rgb(82, 82, 82);' id='x-mark-icon' points='438.393,374.595 319.757,255.977 438.378,137.348 374.595,73.607 255.995,192.225 137.375,73.622
73.607,137.352 192.246,255.983 73.622,374.625 137.352,438.393 256.002,319.734 374.652,438.378 '></polygon> </svg>

                            </a></td>";
							echo "</tr>";
						}
						echo "</table>";
					}
					else { ?>
						<h2>No tasks in <?php echo $current_list['post']['content']['name']; ?>. <a href="new_post_page.php">Add one!</h2>
					<?php }
				}
				?>
                </div>
                <div style='clear: both;'></div>
		<?php include('footer.php') ?>
        </div>

	</body>
</html>
