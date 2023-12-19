<?php

namespace App\RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    protected AMQPStreamConnection $connection;
    protected AMQPChannel $channel;
    protected string $exchange;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.username'),
            config('rabbitmq.password')
        );

        $this->exchange = config('rabbitmq.exchange');

        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare($this->exchange, 'direct', false, true, false);
    }

    public function publish(string $queue, mixed $data): void
    {
        $msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);

        $this->createQueue($queue);

        $this->channel->basic_publish($msg, $this->exchange, $queue);
    }

    public function consume(string $queue, callable $callback): void
    {
        $this->createQueue($queue);

        $this->channel->basic_qos(null, 1, false);
        $this->channel->basic_consume($queue, '', false, false, false, false, $callback);
        $this->channel->consume();
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }

    private function createQueue(string $queue):void
    {
        $this->channel->queue_declare($queue, false, true, false, false);
        $this->channel->queue_bind($queue, $this->exchange, $queue);
    }
}