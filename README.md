# gearman-php-prototype
At the time of this writing

> gearman job server (libgearman) 1.1.x or later (part of the 1.2 Series as described [in the series and milestones](https://launchpad.net/gearmand) is considered unstable and
> pecl client 1.1.x requires modules from libgearman 1.1.

Therefore the suggested installation is gearman job server **1.0.6**  and pecl **1.0.3**

This document describes the installation process and working samples for client, job server and workers for [Gearman] [1]

# Examples
## Example [01_helloworld](examples/01_helloworld)
start a client, start a worker (not necessarily in this order) and see the job get done.
## Example [02_async](examples/02_async)
start a client, start a worker and see the job get done.
The client script ends without waiting for the job to finish (work runs in background)
## Example [03_GearmanManager](examples/03_GearmanManager)
Spawn workers with [Gearman Manager] [3] (not maintained)
## Example [04_Supervisord](examples/04_Supervisord)
Spawn workers with [Supervisord] [2]
## Example [05_graceful_restart](examples/05_graceful_restart/)
### Gracefully restart gearman workers
* Ensure that workers don't drop started but unfinished jobs.
* terminate the worker based on number of jobs completed and maximum Duration

Monitoring: Gearman Monitor web application [Gearman Monitor] [4]




# Software Dependencies
## For Client and Server
```
sudo apt-get -y install libgearman-dev libevent-dev
```
## For Server
```
sudo apt-get -y install libboost-all-dev cloog-ppl
```

# Installing Job Server

## From sources Install 1.0.6 (recommended)

```
curl -L https://launchpad.net/gearmand/1.0/1.0.6/+download/gearmand-1.0.6.tar.gz > gearmand-1.0.6.tar.gz
tar xzf gearmand-1.0.6.tar.gz &&
cd gearmand-1.0.6 &&
./configure &&
make &&
sudo make install &&
cd ../ &&
rm -rf gearmand-1.0.6 &&
rm gearmand-1.0.6.tar.gz
```

### Setup Service
Switch to this directory
```cd setup```

```
sudo cp init.d-gearman-job-server /etc/init.d/gearman-job-server
sudo /etc/init.d/gearman-job-server start
```

## Install Dev version from 1.2 series
sudo apt-get install python-software-properties
sudo add-apt-repository ppa:gearman-developers/ppa
sudo apt-get update
sudo apt-get install gearman-job-server gearman-tools
### Setup service from dev
```
sudo /etc/init.d-gearman-job-server start
```

## Install default from packages
> the problem with this approach is that Ubuntu has a very old version of gearmand 0.27 or 0.33 depending on the distribution.

    sudo apt-get install gearman-job-server

### Job Server Status Changes
start

    sudo service gearman-job-server start

stop

    sudo service gearman-job-server start

check status

    sudo service gearman-job-server status

### Job Server Process
start

    gearmand -d -L 127.0.0.1 -l  /var/log/gearmand.log

stop

    (echo shutdown ; sleep 0.1) | netcat 127.0.0.1 4730 -w 1

check status in processes:

    lsof -i -P | grep gearmand

check status should output:

    gearmand 26501 root 8u IPv4 69854 0t0 TCP *:4730 (LISTEN)
    gearmand 26501 root 9u IPv6 69855 0t0 TCP *:4730 (LISTEN)

check status querying gearman's Administrative Protocol

check status

     (echo status; sleep 0.1) | netcat 127.0.0.1 4730 -w 1
check workers

      (echo workers; sleep 0.1) | netcat 127.0.0.1 4730 -w 1


## Installing gearman PHP Extension (PECL)

### from PECL
```
sudo pecl uninstall gearman &&
sudo pecl install gearman-1.0.3
```

### from sources
1. download from PECL

    curl http://pecl.php.net/get/gearman > pecl-gearman.latest.tgz

*NOTE*: if you plan to use GearmanManager pecl gearman 0.81 is recommended instead of 1.0.2 due to a bug
as described [here](https://bugs.launchpad.net/gearmand/+bug/917006). No need to worry about downgrading.
Installing 0.8.1 will overwrite previous installation.

    curl http://pecl.php.net/get/gearman-0.8.1.tgz > pecl-gearman.latest.tgz


1. install

    tar xzf pecl-gearman.latest.tgz
    cd gearman-X.Y
    phpize
    ./configure
    make
    make install

1. add php extension

in php.ini add
    extension="gearman.so"
or alternatively touch gearman.ini in conf.d directory adding this line:extension="gearman.so"

1. validate installation

execute this php command from your environment (apache, cli, php -a)
	print gearman_version() . "\n";
>0.33

********************************************************************************
********************************************************************************

## Gearman Monitor

Provides a web interface to display worker, server and queue status. Cloned from https://github.com/yugene/Gearman-Monitor

### Installation

1. Prerequisite pear Net_Gearman (at the time of the writing 0.2.3 alpha)

```
   pear install Net_Gearman-0.2.3
```
1. git clone https://github.com/yugene/Gearman-Monitor.git

1. Modify /etc/apache2/sites-available/
```
    <VirtualHost *:80>
	ServerName gearman-monitor.local
	DocumentRoot <path-to-project>/Gearman-Monitor
    </VirtualHost>
```
1. Setup Gearmonitor on Apache (modify /etc/hosts)
```
    127.0.0.1 gearman-monitor.local
```
1. modify _config.php with the server info
```
    $cfgServers[$i]['address'] = '127.0.0.1:4730';
    $cfgServers[$i]['name'] = 'Gearman server 1';
```
1. Restart Apache
```
   sudo service apache2 restart
```
1. browse the site

### gearman administrator script


monitoring/administator.php  a command line monitoring tool


## Gearman Manager

Gearman Manager will be tested at a later step
https://github.com/brianlmoon/GearmanManager

## Supervisord With Gearman

Supervisor provides monitoring and controlling of processes on Unix-like systems
Here an installation script is provided and instructions on how to manage gearman workers with Supervisord
http://supervisord.org/


## Resources


* http://gearman.org/index.php
* http://gearman.org/index.php?id=getting_started _(official installation instructions and example)_
* http://gearman.org/index.php?id=documentation  _(some documentation)_
* http://gearman.org/docs/dev/index.html  _(some sample in C)_
* https://launchpad.net/gearmand _(source code, issues and downloads)_
* https://launchpad.net/~gearman-developers/+archive/ppa _installation through PPA_
* http://www.perspectiverisk.com/blog/2012/02/creating-a-penetration-testing-web-server-using-gearman-supervisor-part-1-installation-basic-usage/ _very thorough installation process_
* http://java.dzone.com/news/gentle-introduction-gearman _good read for gearman_
* http://www.modernfidelity.co.uk/tech/installing-configuring-and-running-gearman-php-ubuntu _installation through apt source list update ... a bit confusing on whether it works or not_
* http://gearman.org/index.php?id=protocol _Gearman Administrative Protocol (search for section 'Administrative Protocol')_
* https://github.com/yugene/Gearman-Monitor Monitors Server, Workers and Queue
* http://supervisord.org/  Managing Gearman Processes (python application)
* https://github.com/yugene/Gearman-Monitor Managing Gearman Processes (php script)

[1]: http://gearman.org/index.php "Gearman Official Site"
[2]: http://supervisord.org/ "Supervisord"
[3]: https://github.com/brianlmoon/GearmanManager "Gearman Manager"
[4]: https://github.com/yugene/Gearman-Monitor "Gearman Monitor"
