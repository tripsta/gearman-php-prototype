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

$jobHandle = $client->doBackground('02-async-hello', json_encode($arguments));

echo "Job handle: $jobHandle".PHP_EOL;