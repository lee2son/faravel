<?php

namespace Faravel\Illuminate\Queue;

trait InteractsWithQueue
{
    use \Illuminate\Queue\InteractsWithQueue;

    public function release($delay = null)
    {
        if(is_null($delay)) {
            $delay = isset($this->delays) ? ($this->delays[$this->attempts()] ?? null) : 0;
            if(is_null($delay)) {
                return null;
            }
        }

        if ($this->job) {
            $this->job->release($delay);
            return $delay;
        }

        return null;
    }
}