<?php

namespace Faravel\Illuminate\Bus;

use Illuminate\Contracts\Queue\Queue;

class Dispatcher extends \Illuminate\Bus\Dispatcher
{
    /**
     * Dispatch a command to its appropriate handler behind a queue.
     *
     * @param  mixed  $command
     * @return mixed
     * @see \Illuminate\Bus\Dispatcher::dispatchToQueue
     * @throws \RuntimeException
     */
    public function dispatchToQueue($command)
    {
        $command->connection = $command->connection ?? ($command->queueConnection ?? null);
        $command->queue = $command->queue ?? ($command->queueName ?? null);
        return parent::dispatchToQueue($command);
    }
}
