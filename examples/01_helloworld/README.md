Intro
=====

This example creates a client and a worker.
THe client requests a a job  to Gearman server passing as arguments
* sleepFor: a time to sleep before the work gets completed and the
* name: name to say hello to
The worker will perform the task and the client will display the result returned from the server

Start Server
------------
    sudo service gearmand start

Start Client
------------
In terminal run the following scripts

    php helloClient.php 10 John

> Gearman Job Server received the request and wait until a worker can process it

Start Worker
------------
enable a worker
    php helloWorker.php
> Gearman Job Server identifies that there is now a worker that can do the job
The worker does the job and after 10 seconds the client ends echoing the message
    hello John, I slept for 10 seconds

The worker will work indefinitely until stopped (Ctrl+C)

Extending
---------
You can extend the sample by creating multiple workers and multiple clients simultaniously

Monitoring
----------
For monitoring [Gearman Monitor] [1] is a nice and easy to setup solution.
You can view there
* status of the Job Server,
* workers and the jobs they can perform
* how many items are currently being processed and how many are in the queue


[1]: https://github.com/yugene/Gearman-Monitor "Gearman Monitor"
