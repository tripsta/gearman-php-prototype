## Intro

testing graceful restart

By graceful restart we mean the ability to restart the workers without leaving a job unfinished.
The idea is that the kill signal SIGTERM sent by supervisor updates only a flag $terminate as [suggested](http://dpb587.me/blog/2013/01/14/terminating-gearman-workers-in-php.html) by Danny Berger

Only When the work is finished the worker goes out of the $worker->work() loop.


# Requirements
* php pcntl module should be enabled
* supervisord was used to spawn the workers. For configuration setup/supervisord.conf was used.


# Worker setup
> See the whole code [here](helloWorker.php)
## Terminate Flag
The following code sets up the $terminate semaphore
```php
examples/05_graceful_restart/helloWorker.php

declare(ticks=1);
$terminate = false;

pcntl_signal(SIGTERM, function () use (&$terminate) { echo 'sigterm'; $terminate = true; });
$worker->addOptions(GEARMAN_WORKER_NON_BLOCKING);

```


## Worker Timeout
The following code only ensures that we will be entering the loop every 2 seconds and apply custom logic there.
No other action is taken on timeout.

```php
$worker->setTimeout(2000);
```

## Terminate Workers by maximum number of jobs and duration
Since php may have memory leaks, we want to be able to kill a worker when any of the following occur:
1. The worker has already executed X jobs
2. The worker is alive already for Y seconds.

```php
while (!$terminate && (@$worker->work() || $worker->returnCode() == GEARMAN_IO_WAIT || $worker->returnCode() == GEARMAN_NO_JOBS || $worker->returnCode() == GEARMAN_TIMEOUT)) {
	if (!$terminate && $maxJobs <= $jobsFinished) {
		echo 'maxJobs' . PHP_EOL;
		$terminate = true;
	}
	if (!$terminate && $restartTimeout <= time()) {
		echo 'restartTimeout' . PHP_EOL;
		$terminate = true;
	}

	if (!$terminate && !@$worker->wait()) {
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
}
```



# Test script

A test script functional_test/graceful_restart.php can send X number of jobs to GEARMAN_TIMEOUT

The script
## Sends Jobs

    sudo php functional_test/graceful_restart.php 5

* truncates the worker stdout log file in this example configuration '/var/log/workers/stdout.log'
* restarts supervisord
* creates the processes with the new jobs.


## Checks status
Provides reporting from worker stdout log (refreshed every 1 second)

    sudo php functional_test/graceful_restart.php status

The output looks like this
```
**************************************************
***** REPORT FROM WORKER_STDOUT ******************
Date 2013:07:25 15:45:53
started: 191
ended: 188
jobLoad: 0.4
worker exits: 15
exit due to maxjobs: 2
exit due to restart timeout: 4
exit due to sigterm signal: 5
**************************************************
```

