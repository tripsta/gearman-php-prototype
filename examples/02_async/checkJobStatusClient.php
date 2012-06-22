<?php
$client = new GearmanClient();
$client->addServer('localhost', 4730);

if (isset($argv[1])) {
	$jobHandle = $argv[1];
}

$running = true;

do 
{
	$status = $client->jobStatus($jobHandle);
	if (!$status[0]) {
		$running = false;
	}
	usleep(500*1000); //500ms
	echo '.';
} while ($running);

echo file_get_contents(sprintf("data/job-%s.txt", $jobHandle));
