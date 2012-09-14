<?php
$client = new GearmanClient();
$client->addServer('localhost', 4730);

$sleepFor = 1;
$name = 'Dimitris';

if (isset($argv[1])) {
	$sleepFor = $argv[1];
}
if (isset($argv[2])) {
	$name = $argv[2];
}

$arguments = array(
    'sleepFor' => $sleepFor,
    'name' => $name
);
echo $client->do('01-hello-sleep', json_encode($arguments)).PHP_EOL;
/*
$client->addTaskBackground('hellosleep', json_encode($arguments));
$client->runTasks();
 *
*/