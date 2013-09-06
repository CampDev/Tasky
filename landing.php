<?php
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Tasky</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<meta charset="utf-8">
	</head>

	<body>


        <div class="header" style="background: url('../img/logobg.png'); background-position: center;">
        <div class="container">
            <div id="header-inner">
                <span style="font-size: 12px;margin-left: 5px;">α</span>
                <a href="http://dev.campnews.org/tasky" style="float: left; font-size: 32px; margin-top: 5px; font-family: 'Raleway', sans-serif; font-weight: 300;">
                <img src="img/logo-small.png" style="float: left; margin-top: -5px;">
                Tasky
                </a>
                <a href="#" style="float: right; font-size: 16px; margin-top: 14px;">About</a>
            </div>
        </div>
        </div>


	<div id="fadeinfast" class="padded-container" style="padding-top: 75px; padding-bottom: 75px;">
		<h2>Clear your mind and become productive!</h2>
		<?php if (isset($_GET['error'])) {
			echo "<h2 class='error'>".urldecode($_GET['error'])."</h2>";
		}
		if(!isset($_SESSION['entity'])) { ?>
			<p><form align="center" action="auth.php" method="get" style="margin-top: 35px;"> 
				<input type="url" name="entity" placeholder="https://name.tent-provider.com" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 200px;"/> 
				<input type="submit" class="sign-in" value="Sign in with Tent" />
			</form></p>
		<?php } ?>
	</div>

	<div id="features" style="background: #F7F7F7; border-top: 1px solid #ddd; padding-top: 0px;">
        <div class="padded-container" id="fadeinslow"> 
		<h2 style="margin-bottom: 50px;">Tasky is a free <b>Task Managment</b> App...</h2>
		<img src="img/tasks.png" class="landing-image">
        <div style="clear: both;">
		<div id="feature"><h3>Create tasks</h3>They can be anything! From dentist appointments to groceries, from serious business to feeding your kitten.</div>
		<div id="feature"><h3>Organise them</h3>Tasky provides you with all the features you need to stay organised, no matter your workload.</div>
		<div id="feature"><h3>Be productive</h3>Thanks to tent syncing and our optimised website, your tasks are available anywhere at any time.</div>
        </div>
        <div  style="clear: both; height: 1px;"></div>
	</div></div>

	<div id="features" class="padded-container">
<h2 style="color: black;">*** Tasky is still under development and as such most features are not ready yet ***</h2>
    </div>

	<div id="features" style="background: #27ae60;">
		<div class="padded-container">
		<h2 style="color: white;">... made for everyone with a <b>life</b>...</h2>
		<div id="feature"><img src="img/personal.svg" style="width: 150px;"><h3 style="color: white;">Personal</h3>Use Tasky for your personal ToDo's, such as groceries, chores around the house and birthday presents. Share them with your partner in life.</div>
		<div id="feature"><img src="img/project.svg" style="width: 200px;"><h3 style="color: white; margin-top: -30px;">Projects</h3>Weddings, school projects, birthday parties or even holiday preparations; if it involves many tasks and many people, you need Tasky.</div>
		<div id="feature"><img src="img/business.svg" style="width: 150px;"><h3 style="color: white;">Business</h3>Tell your collegue to call his customer back and keep an eye on his progress in doing so. Tasky is rock solid and business-proof.</div>
        <div style="clear: both;"></div>	
    </div>
    </div>

	<div id="features">
        <div class="padded-container">
        <h2 style="">... sporting tons of cool <b>features</b>...</h2>
		<div id="feature"><h3>Filtering</h3>Every task is part of a list, which can be filtered based upon priority, deadline, completion or labels.</div>
		<div id="feature"><h3>Quick views</h3>See what needs to be done in the blink of an eye. Only fiddle with filters when you have to.</div>
		<div id="feature"><h3>Collaboration</h3>Assign tasks and subtasks to other people, add attachments, check their progress and discuss in the comment section.</div>
        <div style="clear: both;"></div>
    </div>
    </div>

	<div id="features" style="background: #27ae60;">
        <div class="padded-container">
        <h2 style="color: white;">... and powered by the social <b>tent</b> protocol.</h2>
		<div id="feature" style="color: white;"><h3>All in one place</h3>Your data is stored on your tent server, among all your other data. Other apps may present this data in new and exciting ways.</div>
		<div id="feature" style="color: white;"><h3>Social</h3>Tent is inherently social and so is Tasky. Using your existing friendlist we can show you what your friends are up to.</div>
		<div id="feature" style="color: white;"><h3>Open source</h3>Like the tent protocol, Tasky is an open source project. Please contribute to our project if you can. :)</div>
        <div style="clear: both;"></div>	
    </div>
    </div>

	<div id="features"><h2 style=""><b>Give it a try!</b></h2>
		<?php if(!isset($_SESSION['entity'])) { ?>
			<p><form align="center" action="auth.php" method="get" style="margin-top: 35px;"> 
				<input type="url" name="entity" placeholder="https://name.tent-provider.com" style="padding: 10px; border: 1px solid #ccc; border-radius: 5px; width: 200px;"/> 
				<input type="submit" class="sign-in" value="Sign in with Tent" />
			</form></p>
		<?php } ?>

	</div>
</div>

<footer>Tasky is an open source project by CampDev. Graphics courtesy to iconmonstr.com.</footer>
