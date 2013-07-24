<?php

$terminate = false;

pcntl_signal(SIGTERM, function () use (&$terminate) { $terminate = true; });
pcntl_signal(SIGKILL, function () use (&$terminate) { $terminate = true; });

$worker= new GearmanWorker();
$worker->addOptions(GEARMAN_WORKER_NON_BLOCKING);
$worker->setTimeout(2000);
$worker->addServer('localhost', 4730);
$worker->addFunction("02-async-hello", "helloSleep");
$maxJobs = 2000;
$jobsFinished = 0;
$startTime = time();
$restartTimeout = $startTime + (0.5 * 60);

while (!$terminate && (@$worker->work() || $worker->returnCode() == GEARMAN_IO_WAIT || $worker->returnCode() == GEARMAN_NO_JOBS || $worker->returnCode() == GEARMAN_TIMEOUT)) {
// while (!$terminate && $worker->work()) {
 //    if (GEARMAN_SUCCESS != $worker->returnCode()) {
 //        echo "Worker failed: " . $worker->error() . PHP_EOL;
 //    } else {
	// 	echo 'Work OK'.PHP_EOL;
	// }
	if ($maxJobs <= $jobsFinished) {
		$terminate = true;
	}
	if ($restartTimeout <= time()) {
		$terminate = true;
	}

	if (!@$worker->wait()) {
		// echo $worker->timeout();
		if ($worker->returnCode() == GEARMAN_NO_ACTIVE_FDS) {
			continue;
		}
		if ($worker->returnCode() == GEARMAN_TIMEOUT) {
			echo '.';
			continue;
		}
		break;
	}
	if ($worker->work()) {
		$jobsFinished++;
	}
	// $worker->wait();
}

$worker->unregisterAll();

echo 'Worker died!' . PHP_EOL;

function helloSleep($job)
{
	$arguments = json_decode($job->workload());
	$sleepFor = $arguments->sleepFor;
	// sleep($sleepFor);
	$cnt = 1000*1000*$sleepFor;
	for ($i=0; $i<= ($cnt); $i++) {
		if ($i == 0) {
			echo "Started {$job->handle()}";
		}
		if ($i % (40*200000) == 0) {
			echo PHP_EOL;			
		}

		if ($i == $cnt) {
			echo "Ended {$job->handle()}" . PHP_EOL;
		}
	}

	$filePath = sprintf(__DIR__ . "/data/job-%s.txt", $job->handle());
	$content = sprintf("Hello %s,
	I am a worker on process: %s\n
	I slept for %d seconds before delivering this masterpiece of work\n",
		$arguments->name,
		posix_getpid(),
		$sleepFor);
	//"hello {$arguments->name}, " . PHP_EOL . "I am process:" . posix_getpid() . 			PHP_EOL . "I slept for $sleepFor seconds"
	try {
		file_put_contents($filePath, $content);
	} catch (Exception $e) {
		throw new Exception($e);
	}
}