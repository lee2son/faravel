<?php

namespace Faravel\Logger\Monolog\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

class DatabaseHandler extends AbstractProcessingHandler
{
    protected $connection;

    protected $table;

    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        $this->connection = config('faravel.log_to_db.connection');
        $this->table = config('faravel.log_to_db.table');
        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        $data = [
            'env' => app()->environment(),
            'is_running_unit_tests' => app()->runningUnitTests(),
            'is_running_in_console' => app()->runningInConsole(),
            'ips' => implode(',', request()->getClientIps()),
            'request_id' => request()->server(config('faravel.request_id')),
            'message' => $record['message'],
            'context' => json_encode($record['context']),
            'level' => $record['level'],
            'level_name' => $record['level_name'],
            'channel' => $record['channel'],
            'extra' => json_encode($record['extra']),
            'date' => $record['datetime']->format('Ymd'),
            'ts' => $record['datetime']->getTimestamp(),
        ];

        \Illuminate\Support\Facades\DB::connection($this->connection)->table($this->table)->insert($data);
    }
}