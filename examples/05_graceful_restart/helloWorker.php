<?php declare(ticks = 1);

$terminate = false;

pcntl_signal(SIGTERM, function () use (&$terminate) { $terminate = true; });
pcntl_signal(SIGKILL, function () use (&$terminate) { $terminate = true; });

$worker= new GearmanWorker();
// $worker->addOptions(GEARMAN_WORKER_NON_BLOCKING);
$worker->setTimeout(10000);
$worker->addServer('localhost', 4730);
$worker->addFunction("02-async-hello", "helloSleep");

while (!$terminate && @$worker->work() || $worker->returnCode() == GEARMAN_IO_WAIT || $worker->returnCode() == GEARMAN_NO_JOBS || $worker->returnCode() == GEARMAN_TIMEOUT) {
// while (!$terminate && $worker->work()) {
 //    if (GEARMAN_SUCCESS != $worker->returnCode()) {
 //        echo "Worker failed: " . $worker->error() . PHP_EOL;
 //    } else {
	// 	echo 'Work OK'.PHP_EOL;
	// }
	if ($worker->returnCode() == GEARMAN_TIMEOUT) {
		// echo '.' . PHP_EOL;
	}
	// $worker->wait();
}

function helloSleep($job)
{
	$arguments = json_decode($job->workload());
	$sleepFor = $arguments->sleepFor;
	// sleep($sleepFor);
	$cnt = 1000*1000*$sleepFor;
	for ($i=0; $i<= ($cnt); $i++) {
		if ($i == 0) {
			echo 'Start';
		}
		if ($i % (40*200000) == 0) {
			echo PHP_EOL;			
		}

		if ($i == $cnt) {
			echo 'End' . PHP_EOL;
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
	file_put_contents($filePath, $content);
}