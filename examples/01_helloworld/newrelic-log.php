<?php
if (extension_loaded('newrelic')) {
	$metricName = $argv[1];
	$metricDuration = $argv[2];
	newrelic_custom_metric($metricName, $metricDuration);
}