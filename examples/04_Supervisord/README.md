Intro
##

This example illustrates how to use Supervisor to handle starting/stopping of worker processes.

Setup Supervisor
###
``` bash
cd examples/04_Supervisord/setup
sudo ./setup
```
The command above installs supervisor, creates the service script and the configuration script and then starts the supervisord service


Registering Workers
###
The following section in supervisord.conf creates 4 workers(numprocs) that are registered to accept gearman jobs
``` ini
	[program:helloWorker]
	command=php /home/dbaltas/projects/rnd/gearman-php-prototype/examples/04_Supervisord/helloWorker.php
	numprocs=4                    ; number of processes copies to start (def 1)
	process_name=%(program_name)s%(process_num)s ; process_name expr (default %(program_name)s)
```

Note: After changing the configuration file at examples/04_Supervisord/setup/supervisord.conf make sure
the file is copied over to /etc/supervisord.conf and then restart the supervisord service.
Both these steps can be achieved by rerunnig the setup script

Validating Solution
###
Create jobs by running
``` bash
cd examples/04_Supervisord
php asyncHelloClient.php [some-number-of-seconds-defaults-to-1]
```
Verify that data files are created at examples/04_Supervisord/data