<?php
	function Tent_Markdown($status) {
		//Replacing *STRING* with <b>STRING</b>
		$markdown_status = preg_replace("/\*(.*)\*/", "<b>$1</b>", $status); 
		
		//Replacing _STRING_ with <em>STRING</em>
		$markdown_status = preg_replace("/\_(.*)\_/", "<em>$1</em>", $markdown_status);
		
		//Replacing ~STRING~ with <em>STRING</em>
		$markdown_status = preg_replace("/\~(.*)\~/", "<del>$1</del>", $markdown_status);
		
		//Replacing `STRING` with <code>STRING</code>
		$markdown_status = preg_replace("/\`(.*)\`/", "<code>$1</code>", $markdown_status);
		
		//Replacing [STRING1](STRING2) with <a href='STRING2'>STRING1</a>
		$markdown_status = preg_replace("/\[(.*)\]\((.*)\)/", "<a href='$2'>$1</a>", $markdown_status);
		
	return $markdown_status;
	}
?>