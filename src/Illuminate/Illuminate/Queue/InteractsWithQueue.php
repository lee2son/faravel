<?php

namespace Faravel\Illuminate\Queue;

trait InteractsWithQueue {
    use \Illuminate\Queue\InteractsWithQueue;

    public function release($delay = -1)
    {
//        if(!$this->job) return;
//
//        if($delay >= 0) {
//            return $this->job->release($delay);
//        }
//
//        $delays = $this->delays ?? [];
//        $attempt = $this->attempts();
//
//        $delay = $delays[$attempt] ?? null;
//        if(is_null()) {
//            return false;
//        }
//
//        if ($this->job) {
//            return $this->job->release($delay);
//        }
    }
}