<?php

<<<<<<< HEAD
    $command = "git checkout main && git pull";
	(exec($command,$return));
	echo implode("\n",$return)."\n";
	unset($return);
	$command = "cp .env.master .env";
	(exec($command,$return));
	echo implode("\n",$return)."\n";
	unset($return);
	$command = "php artisan config:cache";
	(exec($command,$return));
	echo implode("\n",$return)."\n";
	unset($return);
=======
$command = "git checkout master && git pull";
(exec($command,$return));
echo implode("\n",$return)."\n";
unset($return);
$command = "cp config/database.master.php config/database.php";
(exec($command,$return));
echo implode("\n",$return)."\n";
unset($return);
>>>>>>> 84a3df3708ce3725a79b19e7c7143545dccf0da0
