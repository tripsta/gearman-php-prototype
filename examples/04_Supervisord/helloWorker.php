<?php
$worker= new GearmanWorker();
$worker->addServer('localhost', 4730);
$worker->addFunction("02-async-hello", "helloSleep");

while ($worker->work()) {
    if (GEARMAN_SUCCESS != $worker->returnCode()) {
        echo "Worker failed: " . $worker->error() . PHP_EOL;
    } else {
		echo 'Work OK'.PHP_EOL;
	}
}
function helloSleep($job)
{
	$arguments = json_decode($job->workload());
	$sleepFor = $arguments->sleepFor;
	sleep($sleepFor);

	$filePath = sprintf(__DIR__ . "/data/job-%s.txt", $job->handle());
	$content = sprintf("Hello %s,
	I am a worker on process: %s\n
	I slept for %d seconds before delivering this masterpiece of work\n",
		$arguments->name,
		posix_getpid(),
		$sleepFor);
	//"hello {$arguments->name}, " . PHP_EOL . "I am process:" . posix_getpid() . 			PHP_EOL . "I slept for $sleepFor seconds"
	file_put_contents($filePath, $content);
}