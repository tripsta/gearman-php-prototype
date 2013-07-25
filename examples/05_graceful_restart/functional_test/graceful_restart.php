<?php

define('APPLICATION_PATH', __DIR__ . '/../');
define('WORKER_STDOUT', '/var/log/workers/stdout.log');

require_once APPLICATION_PATH . '/functional_test/vendor/autoload.php';

use Symfony\Component\Process\Process;


$jobLoad = 0.4;

$argument1 = isset($argv[1]) ? $argv[1]: '';
if ($argument1 == 'status') {
	while (true) {
		print_status();
		sleep(1);
	}
	exit;
}

truncate_worker_stdout();
supervisor_restart();
sleep(5);


$numberOfJobs = $argument1;

echo sprintf("Sending jobs\n");


$clients = [];

for ($i=0; $i < $numberOfJobs; $i++) {
	$clientCommand = sprintf("php %sasyncHelloClient.php %s", APPLICATION_PATH, $jobLoad * (rand(1,100)+50)/100);
	// echo $clientCommand;
	$clients[$i] = new Process($clientCommand);
	$clients[$i]->run();
	echo "#";
}


echo sprintf("Sent %s jobs with a jobload of %s\n", $numberOfJobs, $jobLoad);
echo "\nprocessfinished\n";


function truncate_worker_stdout()
{
	$command = sprintf("sudo truncate --size=0 %s", WORKER_STDOUT);
	execute_command($command);
}

function supervisor_restart()
{
	$command = sprintf("sudo service supervisord restart");
	execute_command($command);
}

function print_status()
{
	global $jobLoad;

	$started = count_of('Started');
	$ended = count_of('Ended');
	$workerExits = count_of('exit');
	$maxJobs = count_of('maxJobs');
	$restartTimeout = count_of('restartTimeout');
	$sigTerm = count_of('sigterm');
	echo sprintf("**************************************************\n");
	echo sprintf("***** REPORT FROM WORKER_STDOUT ******************\n");
	echo sprintf("Date %s\n", date('Y:m:d H:i:s'));
	echo sprintf("started: %s\n", $started);
	echo sprintf("ended: %s\n", $ended);
	echo sprintf("jobLoad: %s\n", $jobLoad);
	echo sprintf("worker exits: %s\n", $workerExits);
	echo sprintf("exit due to maxjobs: %s\n", $maxJobs);
	echo sprintf("exit due to restart timeout: %s\n", $restartTimeout);
	echo sprintf("exit due to sigterm signal: %s\n", $sigTerm);
	echo sprintf("**************************************************\n");
}

function count_of($pattern)
{
	$command = sprintf("rgrep -c %s %s", $pattern, WORKER_STDOUT);
	$output =  execute_command($command);
	return preg_replace('/\n/','',$output);
}

function execute_command($command)
{
	$now = time();
	echo sprintf("running: %s\n", $command);
	$process = new Process($command);
	$process->run();	
	$output = $process->getOutput() . PHP_EOL;
	echo $output . PHP_EOL;
	echo sprintf("end run: %s duration:%s\n", $command, time() - $now);
	return $output;
}