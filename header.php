<?php 
        require_once('functions.php');
        if(isset($_SESSION['entity'])) {
            //Getting all the lists 
            $entity = $_SESSION['entity'];
            $entity_sub = $_SESSION['entity_sub'];
            $nonce = uniqid('Tasky_', true);
            $mac_lists = generate_mac('hawk.1.header', time(), $nonce, 'GET', '/posts?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Flist%2Fv0.1', $entity_sub, '80', $_SESSION['client_id'], $_SESSION['hawk_key'], false);
            $init_lists = curl_init();
            curl_setopt($init_lists, CURLOPT_URL, $_SESSION['posts_feed_endpoint'].'?types=http%3A%2F%2Fcacauu.de%2Ftasky%2Flist%2Fv0.1');
            curl_setopt($init_lists, CURLOPT_HTTPGET, 1);
            curl_setopt($init_lists, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($init_lists, CURLOPT_HTTPHEADER, array('Authorization: Hawk id="'.$_SESSION['access_token'].'", mac="'.$mac_lists.'", ts="'.time().'", nonce="'.$nonce.'", app="'.$_SESSION['client_id'].'"')); //Setting the HTTP header
            $lists = curl_exec($init_lists);
            curl_close($init_lists);
            $lists = json_decode($lists, true);     
?>

        <div class="header">
        <div class="container">
            <div id="header-inner">
                <a href="index.php">
                </a>
                <select style="height: 50px;
width: 220px;
margin-left: -10px;
position: absolute;
top: 0px;
background: #386194;
border: none;
color: white;
padding: 10px;
-webkit-appearance: none;
" onchange="location = this.options[this.selectedIndex].value;">
                    <option SELECTED value="">Choose a list</option>
                    <option value="index.php">All Lists</option>
                    <?php foreach ($lists['posts'] as $list) {
                        if(!is_null($list['content']['name'])) {
                            echo "<option value='index.php?list=".$list['id']."'>".$list['content']['name']."</option>";
                        }
                    } ?>
                </select>

                <a href="new_post_page.php"><img src="img/createpost.png" style="margin-left: 240px; width: 28px;" alt="New post"></a>
                <a href="list.php"><img src="img/list.png" style="margin-left: 20px; width: 28px;" alt="List management"></a>
                    <img src="" style="width: 40px; height: 40px; margin-top: -5px; float: right;">
                <?php } ?>
            </div>
        </div>
        </div>