Intro
=====

This example illustrates how to make asynchronous job requests.

A client (asyncHelloClient.php) sends a job request and continues the code flow without waiting for a response from the server.
However it logs the unique identifier of the work

A worker (helloWorker.php) performs the job.

Another client (checkJobStatusClient.php)that checks the status of the work requested by asyncHelloClient