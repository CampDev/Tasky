        <?php 
        if(isset($_SESSION['entity'])) { 
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
            <div id="header-inner" class="container">
                <a href="index.php">
                    <img src="" style="width: 40px; height: 40px; margin-top: -5px;">
                </a>

                <select style="height: 30px; width: 150px;border: 1px solid white;margin-left: 20px;position: absolute;top: 10px;">
                    <option>All lists</option>
                    <?php foreach ($lists['posts'] as $list) {
                        if(!is_null($list['content']['name'])) {
                            echo "<option> <a href='index.php?list=".$list['id']."'>".$list['content']['name']."</a> </option>";
                        }
                    } ?>
                </select>

                <a href="new_post_page.php"><img src="img/createpost.png" style="float: right;"></a>
                <a href="logout.php" style="float: right;">Logout</a>
                <?php } ?>
            </div>
        </div>