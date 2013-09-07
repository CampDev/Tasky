<?php 
        require_once('functions.php');
        if(isset($_SESSION['entity'])) {
            //Getting all the lists 
            $log = fopen('request.txt', 'w');
            $entity = $_SESSION['entity'];
            $entity_sub = $_SESSION['entity_sub'];
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
                <a class="javascript-nav" title="New task" rel="leanModal" href="#new_post">
<svg alt="New post" version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'
	 width='36px' height='36px' viewBox='0 0 512 512' enable-background='new 0 0 512 512' xml:space='preserve'>
<path style='fill: #ffffff;' id='note-25-icon' d='m 369.162,49.667 c -45.725,0 -82.794,37.066 -82.794,82.791 0,45.725 37.069,82.791 82.794,82.791 45.723,0 82.79,-37.066 82.79,-82.791 0,-45.725 -37.067,-82.791 -82.79,-82.791 z m 50.881,97.834 -35.837,0 0,35.838 -27.678,0 0,-35.838 -35.837,0 0,-27.676 35.837,0 0,-35.836 27.678,0 0,35.836 35.837,0 z m -0.661,86.01 0,78.352 c 0,61.402 -84.153,150.471 -152.318,150.471 l -207.016,0 0,-412 231.806,0 c -0.824,0.777 -1.643,1.564 -2.447,2.369 -10.931,10.93 -19.334,23.711 -24.934,37.631 l -164.425,0 0,332 c 118.342,0 134.344,0 166.406,0 58.859,0 35,-76.092 35,-76.092 0,0 77.928,25.289 77.928,-37.832 0,-9.807 0,-34.365 0,-63.621 14.057,-1.257 27.576,-5.091 40,-11.278 z'
	/>
</svg></a>
                <a class="javascript-nav" title="List management" rel="leanModal" href="#list_management">

<svg alt="List management" version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'
	 width='36px' height='36px' viewBox='0 0 512 512' enable-background='new 0 0 512 512' xml:space='preserve'>
<path style='fill: #ffffff;' id='note-17-icon' d='M370.845,339.166H209.821v-30h161.023V339.166z M370.845,280.749H209.821v-30h161.023V280.749z
	 M370.845,223.416H209.821v-30h161.023V223.416z M170.166,421.825V156.714H409.5c0,0,0,133.5,0,165.25
	c0,50.953-70.109,33.833-70.109,33.833s16.609,66.028-32,66.028C275.328,421.825,288.508,421.825,170.166,421.825z M449.5,320.417
	V116.714H130.166v345.111H308C376.165,461.825,449.5,381.819,449.5,320.417z M97.5,420.942V85.333h311V50.175h-346v370.768H97.5z'/>
</svg>

</a>
                <a class="javaless-nav" href="new_post_page.php">
<svg alt="New post" version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'
	 width='36px' height='36px' viewBox='0 0 512 512' enable-background='new 0 0 512 512' xml:space='preserve'>
<path style='fill: #ffffff;' id='note-25-icon' d='m 369.162,49.667 c -45.725,0 -82.794,37.066 -82.794,82.791 0,45.725 37.069,82.791 82.794,82.791 45.723,0 82.79,-37.066 82.79,-82.791 0,-45.725 -37.067,-82.791 -82.79,-82.791 z m 50.881,97.834 -35.837,0 0,35.838 -27.678,0 0,-35.838 -35.837,0 0,-27.676 35.837,0 0,-35.836 27.678,0 0,35.836 35.837,0 z m -0.661,86.01 0,78.352 c 0,61.402 -84.153,150.471 -152.318,150.471 l -207.016,0 0,-412 231.806,0 c -0.824,0.777 -1.643,1.564 -2.447,2.369 -10.931,10.93 -19.334,23.711 -24.934,37.631 l -164.425,0 0,332 c 118.342,0 134.344,0 166.406,0 58.859,0 35,-76.092 35,-76.092 0,0 77.928,25.289 77.928,-37.832 0,-9.807 0,-34.365 0,-63.621 14.057,-1.257 27.576,-5.091 40,-11.278 z'
	/>
</svg>
</a>
                <a class="javaless-nav" href="list.php">
<svg alt="List management" version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'
	 width='36px' height='36px' viewBox='0 0 512 512' enable-background='new 0 0 512 512' xml:space='preserve'>
<path style='fill: #ffffff;' id='note-17-icon' d='M370.845,339.166H209.821v-30h161.023V339.166z M370.845,280.749H209.821v-30h161.023V280.749z
	 M370.845,223.416H209.821v-30h161.023V223.416z M170.166,421.825V156.714H409.5c0,0,0,133.5,0,165.25
	c0,50.953-70.109,33.833-70.109,33.833s16.609,66.028-32,66.028C275.328,421.825,288.508,421.825,170.166,421.825z M449.5,320.417
	V116.714H130.166v345.111H308C376.165,461.825,449.5,381.819,449.5,320.417z M97.5,420.942V85.333h311V50.175h-346v370.768H97.5z'/>
</svg>
</a>
                <?php } ?>
</div>

            </div>
        </div>
        </div>
