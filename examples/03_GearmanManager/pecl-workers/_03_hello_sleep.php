<?php

function _03_hello_sleep($job, &$log)
{
	$arguments = json_decode($job->workload());
	$sleepFor = $arguments->sleepFor;
	sleep($sleepFor);

	$filePath = sprintf("data/job-%s.txt", $job->handle());
	$content = sprintf("Hello %s,
	I am a worker on process: %s\n
	I slept for %d seconds before delivering this masterpiece of work\n",
		$arguments->name,
		posix_getpid(),
		$sleepFor);
	file_put_contents($filePath, $content);
	$log[] = "Success: ".$content;

}