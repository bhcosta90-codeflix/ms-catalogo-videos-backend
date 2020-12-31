<?php

namespace App\Jobs;

use Bschmitt\Amqp\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncModelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $routingKey;
    private array $data;
    /**
     * Create a new job instance.
     *
     * @param string $routingKey
     * @param array $data
     */
    public function __construct(
        string $routingKey,
        array $data
    )
    {
        $this->routingKey = $routingKey;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = new Message(json_encode($this->data), [
            'content_type' => 'application/json',
            'delivery_mode' => Message::DELIVERY_MODE_PERSISTENT,
        ]);

        \Amqp::publish($this->routingKey, $message, [
            'exchange' => 'amq.topic',
            'exchange_type' => 'topic'
        ]);
    }
}
