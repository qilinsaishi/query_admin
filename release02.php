<?php

$command = "git checkout master && git pull";
(exec($command,$return));
echo implode("\n",$return)."\n";
unset($return);
$command = "cp config/database.master.php config/database.php";
(exec($command,$return));
echo implode("\n",$return)."\n";
unset($return);
$command = "cp .env.master02 .env";
(exec($command,$return));
echo implode("\n",$return)."\n";
unset($return);
	
