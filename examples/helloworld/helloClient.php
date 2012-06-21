<?php
$client = new GearmanClient();
$client->addServer('localhost', 4730);
 
$sleepFor = 1;

if (isset($argv[1])) {
	$sleepFor = $argv[1];
}
$arguments = array(
    'sleepFor' => $sleepFor
);

echo $client->do('hello-sleep', json_encode($arguments));
/*
$client->addTaskBackground('hellosleep', json_encode($arguments));
$client->runTasks();
 * 
*/