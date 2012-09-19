Newrelic and Gearman Workers
=====

Gearman Workers are processes that do not end when a job is finished.
New Relic agent does not send information to the collector until a process ends. 

From Newrelic Team:
> Since the instrumentation is inside a loop that never ends the data is never recovered.

> A work-around for this situation would be to place the operation you are performing in another non-php script that is called by the loop in your code and instrument that. When the secondary script ends, the data would be collected from it.
> For the future, the next release of the PHP agent will include features which should allow instrumentation of queued processes.

Collecting Metrics
----------

An implementation for sending newrelic metrics is available in the first example 01_helloworld
