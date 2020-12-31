<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VideoUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update video';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Amqp::consume('ms-encoder/video/converted', function($message, $resolver){
            $data = json_decode($message->getBody());
            \Log::info($data);
            $resolver->acknowledge($message);
        }, [
            'persistent' => true,
            'exchange' => 'amq.topic',
            'exchange_type' => 'topic',
            'routing' => 'models.video.converted'
        ]);
    }
}
