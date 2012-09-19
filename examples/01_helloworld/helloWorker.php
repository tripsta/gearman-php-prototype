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
	$metricName = "GearmanDemoHelloWorker" . __FUNCTION__;
	$metricDuration = $sleepFor * 1000;
	exec("php newrelic-log.php $metricName $metricDuration");
	return "hello {$arguments->name}, I slept for $sleepFor seconds";
}