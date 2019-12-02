<?php

namespace Faravel;

/**
 * @see \Illuminate\Bus\Dispatcher::dispatchToQueue 将任务加入队列
 */
abstract class Job
{
//    /**
//     * @var int 每次执行超时时间；通过 pcntl_alarm 在这个时间后触发 SIGALRM 信号；需要 pcntl 扩展支持
//     */
//    public $timeout;

//    /**
//     * @var int 时间戳；任务在超过这个时间后将不再重试并触发 failed 方法
//     */
//    public $timeoutAt;

//    /**
//     * @var int 最大重试次数，超过最大重试次数作业不在重试并后触发 failed 方法
//     */
//    public $tries;

//    /**
//     * @var int 每次失败重试间隔时间：秒；\Illuminate\Bus\Queueable::$delay 是初次加入队列后过多久再执行；只在初次加入队列时有效
//     * @see \Illuminate\Bus\Queueable::$delay
//     */
//    public $retryAfter;

    /**
     * constructor.
     */
    public function __construct()
    {
        if($this->_connection) {
            $this->onConnection($this->_connection);
        }

        if($this->_queue) {
            $this->onQueue($this->_queue);
        }
    }

//    /**
//     * 同 $timeoutAt，但是 $timeoutAt 优先级更高
//     * @see \Illuminate\Queue\Queue::createObjectPayload 构造payload
//     * @see \Illuminate\Queue\Queue::getJobExpiration
//     * @return int 时间戳，任务在超过这个时间后将不再重试并触发 failed 方法
//     */
//    public function retryUntil(): int
//    {
//
//    }

//    /**
//     * 每次失败重试间隔时间：秒；\Illuminate\Bus\Queueable::$delay 是初次加入队列后过多久再执行；只在初次加入队列时有效
//     * @see \Illuminate\Bus\Queueable::$delay
//     * @see \Illuminate\Queue\Queue::createObjectPayload 构造payload
//     * @see \Illuminate\Queue\Queue::getJobRetryDelay
//     * @return int 每次失败重试间隔
//     */
//    public function retryAfter():int
//    {
//
//    }

//    /**
//     * 任务名称（只在第一次加入队列时调用一次）
//     * @see \Illuminate\Queue\Queue::createObjectPayload 构造payload
//     * @return string
//     */
//    public function displayName(): string
//    {
//
//    }

//    /**
//     * 执行达到最大尝试次数或超时（$timeoutAt）后调用
//     * @see \Illuminate\Queue\Jobs\Job::fail
//     * @see \Illuminate\Queue\Jobs\Job::failed
//     * @param \Exception $e
//     */
//    public function failed(\Exception $e): void
//    {
//
//    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->_handle();
    }

    /**
     * Execute the job.
     * @return void
     */
    abstract protected function _handle();
}