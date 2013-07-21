<?php
	session_start();
	if (isset($_GET['type']) AND isset($_SESSION['entity'])) {
		require_once('functions.php');
		$entity_sub = substr_replace($_SESSION['entity'] ,"",-1);
		$nonce = uniqid('Tasky_', true);
		switch ($_GET['type']) {
				case 'complete': //Post completed
					//Getting the current version of the post
					$id = $_GET['id'];
					$nonce = uniqid('Tasky_', true);
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
					$parent_version = $_GET['parent'];

					//Building the new task
					$completed_post_raw = array(
						'id' => $id,
						'entity' => substr($_SESSION['entity'], 0, strlen($_SESSION['entity']) -1),
						'type' => 'http://cacauu.de/tasky/task/v0.1#done',
						'content' => array(
							'title' => $current_task['post']['content']['title'],
							'status' => 'Done',
							'priority' => $current_task['post']['content']['priority'],
							'list' => $current_task['post']['content']['list'],
							'assignee' => '',
							'duedate' => $current_task['post']['content']['duedate'],
							'notes' => $current_task['post']['content']['notes'],
						),
						'version' => array(
							'parents' => array(
								array(
									'version' => $parent_version,
								),
							),
						),
					);
					$completed_post = json_encode($completed_post_raw);
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'PUT', '/posts/'.urlencode($entity_sub)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']."/".urlencode($entity_sub)."/".$id);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
					curl_setopt($ch, CURLOPT_POSTFIELDS, $completed_post);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id'])."\n".'Content-Type: application/vnd.tent.post.v0+json; type="http://cacauu.de/tasky/task/v0.1#done"'));
					$complete_task = curl_exec($ch);
					curl_close($ch);
					if (!isset($complete_task['error'])) {
						$_SESSION['completed_task'] = $current_task['post']['content']['title'];
						header('Location: index.php');
					}
					break;

				case 'update': //Updated post sent
					$id = $_GET['id'];
					$parent = $_GET['parent'];
					if (is_null($_SESSION['duedate'])) {
						$_SESSION['duedate'] = '';
					}
					if (is_null($_POST['notes'])) {
						$_POST['notes'] = '';
					}
					$updated_post_raw = array(
						'id' => $id,
						'entity' => substr($_SESSION['entity'], 0, strlen($_SESSION['entity']) -1),
						'type' => 'http://cacauu.de/tasky/task/v0.1#'.$_POST['status'],
						'content' => array(
							'title' => $_POST['title'],
							'status' => $_POST['status'],
							'priority' => $_POST['priority'],
							'list' => $_SESSION['list'],
							'assignee' => '',
							'duedate' => $_SESSION['duedate'],
							'notes' => $_POST['notes'],
						),
						'version' => array(
							'parents' => array(
								array(
									'version' => $parent,
								),
							),
						),
					);
					$updated_post = json_encode($updated_post_raw);
					unset($_SESSION['duedate']);
					unset($_SESSION['list']);
					$mac = generate_mac('hawk.1.header', time(), $nonce, 'PUT', '/posts/'.urlencode($entity_sub)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']."/".urlencode($entity_sub)."/".$id);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
					curl_setopt($ch, CURLOPT_POSTFIELDS, $updated_post);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id'])."\n".'Content-Type: application/vnd.tent.post.v0+json; type="http://cacauu.de/tasky/task/v0.1#'.$_POST['status'].'"'));
					$update_task = curl_exec($ch);
					curl_close($ch);
					if (!isset($update_task['error'])) {
						$_SESSION['updated'] = $_POST['title'];
						header('Location: index.php');
					}
					break;

				case 'delete':
					$id = $_GET['id'];
					$mac = generate_mac('hawk.1.header', time(), $nonce, "DELETE", '/posts/'.urlencode($entity_sub)."/".$id, $_SESSION['entity_sub'], '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']."/".urlencode($entity_sub)."/".$id);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
					curl_setopt($ch, CURLOPT_VERBOSE, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(generate_auth_header($_SESSION['access_token'], $mac, time(), $nonce, $_SESSION['client_id'])."\n".'Content-Type: application/vnd.tent.post.v0+json;'));
					$delete = curl_exec($ch);
					curl_close($ch);
					if (!isset($delete['error'])) {
						$_SESSION['deleted'] = true;
						header('Location: index.php');
					}
					break;

				case 'task':
					$post_raw = array(
						'type' => 'http://cacauu.de/tasky/task/v0.1#todo',
						'permissions' => array(
							'public' => false,
						),
						'content' => array(
							'title' => $_POST['title'],
							'priority' => $_POST['priority'],
							'notes' => $_POST['notes'],
							'list' => $_POST['list'],
							'status' => 'To Do',
							'duedate' => strtotime($_POST['duedate']),
						),
						'mentions' => array(
							array(
								'post' => $_POST['list'],
							),
						),
					);
					$post_json = json_encode($post_raw);
					$entity = $_SESSION['entity'];
					$entity_sub_task = $_SESSION['entity_sub'];
					$mac_send = generate_mac('hawk.1.header', time(), $nonce, 'POST', '/posts', $entity_sub_task, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_send.'", ts="'.time().'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"'."\n".'Content-Type: application/vnd.tent.post.v0+json; type="http://cacauu.de/tasky/task/v0.1#todo"')); //Setting the HTTP header
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$new_task = json_decode(curl_exec($ch), true);
					curl_close($ch);
					if (!isset($new_task['error'])) {
						$_SESSION['new_task'] = $_POST['title'];
						header('Location: index.php');
					}					
					break;

				case 'list':
					$post_raw = array(
						'type' => 'http://cacauu.de/tasky/list/v0.1#',
						'permissions' => array(
							'public' => false,
						),
						'content' => array(
							'name' => $_POST['list_name'],
							'description' => '',
						)
					);
					$post_json = json_encode($post_raw);
					$entity = $_SESSION['entity'];
					$entity_sub_list = $_SESSION['entity_sub'];
					$mac_send = generate_mac('hawk.1.header', time(), $nonce, 'POST', '/posts', $entity_sub_list, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $_SESSION['new_post_endpoint']);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_send.'", ts="'.time().'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"'."\n".'Content-Type: application/vnd.tent.post.v0+json; type="http://cacauu.de/tasky/list/v0.1#"')); //Setting the HTTP header
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$new_list = json_decode(curl_exec($ch), true);
					curl_close($ch);
					if (!isset($new_list['error'])) {
						$_SESSION['new_list'] = $_POST['list_name'];
						header('Location: index.php');
					}
					break;
				
				default: //Shouldn't happen
					# code...
					break;
			}	
	}
	elseif (!isset($_SESSION['entity'])) {
		$error = "You're not logged in!";
		header('Location: index.php?error='.urlencode($error));
	}
?>