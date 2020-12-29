<?php

    $command = "git checkout master && git pull";
	(exec($command,$return));
	echo implode("\n",$return)."\n";
	unset($return);
	$command = "cp config/database.test.php config/database.php";
	(exec($command,$return));
	echo implode("\n",$return)."\n";
	unset($return);
	