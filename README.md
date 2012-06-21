gearman-php-prototype
=====================

Providing an easy to follow installation guide, and working samples for client, job server and workers is the purpose of this repo.
gearmand (written in C) and the PECL extension will be used instead of PEAR

********************************************************************************
********************************************************************************

Installing Job Server
=====================
gearmand 0.33 is used for this project,


The default ubuntu repositories have, at the time of this writing,  a really old version (0.14) therefore decided to move with custom installation.
`sudo apt-get install gearman-job-server` will produce
$ gearmand -V
> gearmand 0.14 - https://launchpad.net/gearmand


From PPA (recommended)
----------------------

    sudo add-apt-repository ppa:gearman-developers/ppa
    sudo apt-get update

in /etc/apt/sources.list add following lines

    deb http://ppa.launchpad.net/gearman-developers/ppa/ubuntu oneiric main
    deb-src http://ppa.launchpad.net/gearman-developers/ppa/ubuntu oneiric main

where oneiric is the codename of the OS (for ubuntu 11.10)

    sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 1C73E014
    sudo apt-get install gearman-job-server libgearman-dev gearman-tools


Uninstall (from PPA)
--------------------

    sudo apt-get remove  gearman-job-server libgearman-dev gearman-tools
    sudo apt-get autoremove

From Sources
------------

install dependencies

    sudo apt-get install libboost-thread1.46-dev libcloog-ppl0 libboost-program-options-dev

Download

https://launchpad.net/gearmand/trunk/0.33/+download/gearmand-0.33.tar.gz

    tar xzf gearmand-X.Y.tar.gz
    cd gearmand-X.Y
    ./configure
    make
    make install
    ldconfig

Uninstall (from sources)
--------------------

    make uninstall


Job Server Status Changes
=========================

Daemon
======

start

    sudo service gearman-job-server start

stop

    sudo service gearman-job-server start

check status

    sudo service gearman-job-server status

Job Server Process
==================

> *note*: not fully tested
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


Installing gearman PHP Extension (PECL)
=======================================

1. download from PECL

    curl http://pecl.php.net/get/gearman > pecl-gearman.latest.tgz

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

Gearman Monitor
===============
Provides a web interface to display worker, server and queue status. Cloned from https://github.com/yugene/Gearman-Monitor

Installation
------------

1. prerequisite pear Net_Gearman (at the time of the writing 0.2.3 alpha)

    pear install Net_Gearman

*or* of there is an error due to the alpha status pear install Net_Gearman-0.2.3

1. git clone https://github.com/yugene/Gearman-Monitor.git

1. setup Gearmonitor on /etc/hosts and apache

1. modify _config.php with the server info

    $cfgServers[$i]['address'] = '127.0.0.1:4730';
    $cfgServers[$i]['name'] = 'Gearman server 1';

1. browse the site

gearman administrator script
============================

monitoring/administator.php  a command line monitoring tool

********************************************************************************
********************************************************************************
Sample
********************************************************************************
********************************************************************************

Gearman Manager
===============
Gearman Manager will be tested at a later step
https://github.com/brianlmoon/GearmanManager

********************************************************************************
Resources
=========

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
* https://github.com/yugene/Gearman-Monitor
