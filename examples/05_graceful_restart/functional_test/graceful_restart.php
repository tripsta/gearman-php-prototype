<?php

define('APPLICATION_PATH', __DIR__ . '/../');
define('WORKER_STDOUT', '/var/log/workers/stdout-helloWorker.log');

require_once APPLICATION_PATH . '/functional_test/vendor/autoload.php';

use Symfony\Component\Process\Process;


truncate_worker_stdout();
sleep(5);

$numberOfWorkers = 3;
$numberOfJobs = $argv[1];
$jobLoad = 0.3;
$workerProcessTimeoutInSeconds = 100;

$workerCommand = sprintf("php %shelloWorker.php", APPLICATION_PATH);
$clientCommand = sprintf("php %sasyncHelloClient.php %s", APPLICATION_PATH, $jobLoad);
$workers = [];
$clients = [];

// for ($i=0; $i < $numberOfWorkers; $i++) {
// 	$workers[$i] = new Process($workerCommand);
// 	$workers[$i]->setTimeout($workerProcessTimeoutInSeconds);
// }

// foreach ($workers as $key => $worker) {
// 	$worker->start();
// 	echo 'k';
// }

// grep_workers();

for ($i=0; $i < $numberOfJobs; $i++) {
	$clients[$i] = new Process($clientCommand);
	echo $clients[$i]->run();
	echo 'client run';
}


echo 'done';


// foreach ($workers as $key => $worker) {
// 	$worker->stop(1, SIGKILL);
// 	echo 'k';
// }


// foreach ($workers as $key => $worker) {
// 	echo $worker->getStatus();
// 	echo 'k';
// }

// sleep (3);

// foreach ($workers as $key => $worker) {
// 	echo $worker->getStatus();
// 	echo 'k';
// }


function truncate_worker_stdout()
{
	$command = sprintf("sudo truncate --size=0 %s", WORKER_STDOUT);
	execute_command($command);
}
function grep_workers()
{
	$command = sprintf("ps aux | grep helloWorker");
	execute_command($command);
}

function execute_command($command)
{
	echo sprintf("running: %s\n", $command);
	$process = new Process($command);
	$process->run();	
	echo $process->getOutput() . PHP_EOL;
	echo sprintf("end run: %s\n", $command);
}