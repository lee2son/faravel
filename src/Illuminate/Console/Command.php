<?php

namespace Faravel\Illuminate\Console;

abstract class Command extends \Illuminate\Console\Command
{
    public function handle()
    {
        try {
            $this->_handle();
        } catch (\Exception $e) {
            $this->onError($e);
        }
    }

    protected function onError(\Throwable $e) {
        throw $e;
    }

    /**
     * 处理函数
     * @return mixed
     */
    abstract protected function _handle();
}