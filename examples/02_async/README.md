Intro
=====

This example illustrates how to make asynchronous job requests.

A client (asyncHelloClient.php) sends a job request and continues the code flow without waiting for a response from the server.
However it logs the unique identifier of the work

A worker (helloWorker.php) performs the job.

Another client (checkJobStatusClient.php) checks the status of the work requested by asyncHelloClient

Receiving Asynchronous Data
===========================

It seems that the Gearman Job Server does not hold output of the workers for asynchronous requests.

Therefore the worker should persist the data (file system, mysql, memcached etc). in a way that the checkJobStatusClient can get it.
In this example the worker saves the work output in a file in data folder.
Then the checkJobStatusClient.php is looping to wait for a job to be finished. 
When it is finished it will echo the job's output
