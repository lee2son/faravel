<?php

namespace Faravel\Illuminate\Queue\Console;

use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Worker;
use Illuminate\Support\Carbon;

class WorkCommand extends \Illuminate\Queue\Console\WorkCommand
{
    /**
     * Format the status output for the queue worker.
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  string  $status
     * @param  string  $type
     * @return void
     */
    protected function writeStatus(Job $job, $status, $type)
    {
        $this->output->writeln(sprintf(
            "<{$type}>[%s][%s] %s</{$type}> %s <- %s attempts:%s",
            Carbon::now()->format('Y-m-d H:i:s'),
            $job->getJobId(),
            str_pad("{$status}:", 11),
            $job->resolveName(),
            Carbon::createFromTimestamp($job->payload()['time'])->format('Y-m-d H:i:s'),
            $job->attempts()
        ));
    }
}