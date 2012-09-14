<?php
$worker= new GearmanWorker();
$worker->addServer('localhost', 4730);
$worker->addFunction("01-hello-sleep", "helloSleep");

while ($worker->work());

function helloSleep($job)
{
	$arguments = json_decode($job->workload());
	$sleepFor = $arguments->sleepFor;
	sleep($sleepFor);
	newrelic_custom_metric("GearmanDemoHelloWorker" . __FUNCTION__, 2);
	return "hello {$arguments->name}, I slept for $sleepFor seconds";
}