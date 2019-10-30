<?php

namespace Faravel\Console;

abstract class Command extends \Illuminate\Console\Command
{

    public function handle()
    {
        $this->_handle();
    }

    /**
     * 处理函数
     * @return mixed
     */
    abstract protected function _handle();
}