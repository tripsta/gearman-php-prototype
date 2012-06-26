Intro
=====

This example illustrates how to integrate Gearman Manager.
Pecl Extension 0.8.1 will be used due to an issue of 1.0.2 with Gearman Manager as described in the section
'Installing gearman PHP Extension (PECL)' of the repository's main README file.

Run
============

Run from command line
---------------------

Run Gearman Manager

    cd examples/03_GearmanManager
    sudo ../../monitoring/GearmanManager/pecl-manager.php -vvv -c config.ini -l /tmp/GearmanManager.log

Start Workers

    php asyncHelloClient.php 2
