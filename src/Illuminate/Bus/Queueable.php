<?php

namespace Faravel\Illuminate\Bus;

use Illuminate\Support\Facades\Queue;

trait Queueable
{
    use \Illuminate\Bus\Queueable;

    /**
     * @see \Illuminate\Bus\Dispatcher::dispatchToQueue
     * @param \Illuminate\Contracts\Queue\Queue $queue
     * @param mixed $command
     */
    public function queue($queue, $command)
    {
        if(isset($command->queueConnection) && $queue->getConnectionName() !== $command->queueConnection) {
            $queue = Queue::connection($command->queueConnection);
        }

        if(method_exists($command, '_queue'))
        {
            return $command->_queue($queue, $command);
        }

        return $this->pushCommandToQueue($queue, $command);
    }

    /**
     * Push the command onto the given queue instance.
     *
     * @see \Illuminate\Bus\Dispatcher::pushCommandToQueue
     * @param \Illuminate\Contracts\Queue\Queue  $queue
     * @param mixed $command
     * @return mixed
     */
    protected function pushCommandToQueue($queue, $command)
    {
        $queueName = $command->queueName ?? ($command->queue ?? null);
        if ($queueName && isset($command->delay)) {
            return $queue->laterOn($queueName, $command->delay, $command);
        }

        if ($queueName) {
            return $queue->pushOn($queueName, $command);
        }

        if (isset($command->delay)) {
            return $queue->later($command->delay, $command);
        }

        return $queue->push($command);
    }
}