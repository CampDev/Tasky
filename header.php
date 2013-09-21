<?php 
        require_once('functions.php');
        if(isset($_SESSION['entity'])) {
            //Getting all the lists 
            $log = fopen('request.txt', 'w');
            $entity = $_SESSION['entity'];
            $entity_sub = str_replace("http://", "", $entity);
            $entity_sub = str_replace("https://", "", $entity_sub);
            $entity_sub = substr($entity_sub, 0, strlen($entity_sub)-1);
            $_SESSION['entity_sub'] = $entity_sub;
            $nonce = uniqid('Tasky_', true);
            $mac_lists = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/'.str_replace($_SESSION['entity'], "", $_SESSION['posts_feed_endpoint']).'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Flist%2Fv0.1', $entity_sub, '443', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
            $init_lists = curl_init();
            curl_setopt($init_lists, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Flist%2Fv0.1');
            curl_setopt($init_lists, CURLOPT_HTTPGET, 1);
            curl_setopt($init_lists, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($init_lists, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_lists.'", ts="'.time().'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"')); //Setting the HTTP header
            curl_setopt($init_lists, CURLOPT_VERBOSE, $log);
            $lists = curl_exec($init_lists);
            curl_close($init_lists);
            fclose($log);
            $lists = json_decode($lists, true);
?>

<head>
        <script type="text/javascript">
        <!--
        function toggle_visibility(id) {
            var e = document.getElementById(id);
            if(e.style.display == 'block')
                e.style.display = 'none';
            else
                e.style.display = 'block';
        }
        //-->
        </script>
</head>

        <div class="header">
        <div class="container">
            <div id="header-inner">
                <a href="http://dev.campnews.org/tasky" style="float: left; font-size: 32px; margin-top: 5px; font-family: 'Raleway', sans-serif; font-weight: 300;">
                <img src="img/logo-small.png" class="logo">
                </a>
                <select class="header-dropdown"
                    onchange="location = this.options[this.selectedIndex].value;">
                    <option SELECTED value="index.php">All Lists</option>
                    <?php foreach ($lists['posts'] as $list) {
                        if (isset($_GET['list'])) {
                            if ($list['id'] == $_GET['list']) {
                                echo "<option SELECTED value='index.php?list=".$list['id']."'>".$list['content']['name']."</option>";
                            }
                            else {
                                echo "<option value='index.php?list=".$list['id']."'>".$list['content']['name']."</option>";
                            }
                        }
                        else {
                            if(!is_null($list['content']['name'])) {
                                echo "<option value='index.php?list=".$list['id']."'>".$list['content']['name']."</option>";
                            }
                        }
                    } ?>
                </select>
<div class="header-navigation">
    <a href="#" onclick="toggle_visibility('mobile-menu');"><img class="menu-button" src="img/menu.svg"></a>
    <a class="javascript-nav" title="Create new task" rel="leanModal" href="#new_post"><img class="nav-icon" src="img/create-task.svg"></a>
    <a class="javascript-nav" title="Manage lists" rel="leanModal" href="#list_management"><img class="nav-icon" src="img/lists.svg"></a>
    <a class="javascript-nav" title="Settings" rel="leanModal" href="#settings"><img class="nav-icon" src="img/menu.svg"></a>
    <a class="javaless-nav" href="new_post_page.php"><img class="nav-icon" src="img/create-task.svg"></a>
    <a class="javaless-nav" href="list.php"><img class="nav-icon" src="img/lists.svg"></a>
                <?php } ?>
</div>

            </div>
        </div>
        </div>


				<div id="mobile-menu">
                    <?php include('sidebar.php'); ?>
                </div>
