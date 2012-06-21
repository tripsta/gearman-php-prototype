<?php
$client = new GearmanClient();
$client->addServer('localhost', 4730);

if (isset($argv[1])) {
	$jobHandle = $argv[1];
}

$status = $client->jobStatus($jobHandle);
var_dump($status);
