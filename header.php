
	<div style="width: 100%;height: 30px; padding: 10px; color: white; background: #2980b9;">
        <div id="header-inner" class="container"><a href="index.php">Tasky</a>
	        <?php if(isset($_SESSION['entity'])) { ?>
            <a href="new_post_page.php"><img src="img/createpost.png" style="float: right;"></a>
            <a href="logout.php" style="float: right;">Logout</a>
	        <?php } ?>
        </div>
	</div>

