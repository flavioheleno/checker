<?php

	if (isset($_SERVER['QUERY_STRING']))
		echo $_SERVER['QUERY_STRING'];
	else
		echo 'Feed4Tweet-Checker';

?>
