<?php
$worker= new GearmanWorker();
$worker->addServer('localhost', 4730);
$worker->addFunction("hello-sleep", "helloSleep");
while ($worker->work());

function helloSleep($job)
{
	$arguments = json_decode($job->workload());
	$sleepFor = $arguments->sleepFor;
	sleep($sleepFor);
	return "hello {$arguments->name}, I slept for $sleepFor seconds";
}