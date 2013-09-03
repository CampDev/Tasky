<?php 
        require_once('functions.php');
        if(isset($_SESSION['entity'])) {
            //Getting all the lists 
            $log = fopen('request.txt', 'w');
            $entity = $_SESSION['entity'];
            $entity_sub = $_SESSION['entity_sub'];
            $nonce = uniqid('Tasky_', true);
            $mac_lists = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Flist%2Fv0.1', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
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

        <div class="header">
        <div class="container">
            <div id="header-inner">
                <select class="header-dropdown"
                    onchange="location = this.options[this.selectedIndex].value;">
                    <option SELECTED value="">Choose a list</option>
                    <option value="index.php">All Lists</option>
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
                <a class="javascript-nav" title="Manage Lists" rel="leanModal" href="#new_post"><img src="img/createpost.png" style="margin-left: 20px; width: 28px;" alt="New post"></a>
                <a class="javascript-nav" title="New Task" rel="leanModal" href="#list_management"><img src="img/list.png" style="margin-left: 20px; width: 28px;" alt="List management"></a>
                <a class="javaless-nav" href="new_post_page.php"><img src="img/createpost.png" style="margin-left: 20px; width: 28px;" alt="New post"></a>
                <a class="javaless-nav" href="list.php"><img src="img/list.png" style="margin-left: 20px; width: 28px;" alt="List management"></a>
<!-- <img src="" style="width: 40px; height: 40px; margin-top: -5px; float: right;"> -->
                <?php } ?>
</div>

            </div>
        </div>
        </div>
