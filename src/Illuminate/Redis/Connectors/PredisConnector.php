<?php

namespace Faravel\Illuminate\Redis\Connectors;

use Faravel\Illuminate\Redis\Connections\PredisClusterConnection;
use Faravel\Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Contracts\Redis\Connector;
use Illuminate\Support\Arr;
use Predis\Client;

class PredisConnector/* extends \Illuminate\Redis\Connectors\PredisConnector*/ implements Connector
{
    /**
     * @var string
     */
    public static $clientClass = Client::class;

    /**
     * Create a new clustered Predis connection.
     *
     * @param  array  $config
     * @param  array  $options
     * @return PredisConnection
     */
    public function connect(array $config, array $options)
    {
        $formattedOptions = array_merge(
            ['timeout' => 10.0], $options, Arr::pull($config, 'options', [])
        );

        return new PredisConnection($this->createClient($config, $formattedOptions));
    }

    /**
     * Create a new clustered Predis connection.
     *
     * @param  array  $config
     * @param  array  $clusterOptions
     * @param  array  $options
     * @return PredisClusterConnection
     */
    public function connectToCluster(array $config, array $clusterOptions, array $options)
    {
        $clusterSpecificOptions = Arr::pull($config, 'options', []);

        return new PredisClusterConnection($this->createClient(array_values($config), array_merge(
            $options, $clusterOptions, $clusterSpecificOptions
        )));
    }

    /**
     * Create a predis-client
     *
     * @param null $parameters
     * @param null $options
     * @return Client
     */
    public function createClient($parameters = null, $options = null): Client
    {
        $client = static::$clientClass;
        return new $client($parameters, $options);
    }
}