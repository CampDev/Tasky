	<div class="header">
        <div id="header-inner" class="container">
	        <?php if(isset($_SESSION['entity'])) { ?>
            <a href="index.php"><img src="" style="width: 40px; height: 40px; margin-top: -5px;"></a>

            <select style="height: 30px; width: 150px;border: 1px solid white;margin-left: 20px;position: absolute;top: 10px;">
                <option>All lists</option><?php foreach ($lists['posts'] as $list) {
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