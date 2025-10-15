<?php

namespace App\Console\Commands;

use App\Modules\Users\Models\SchedulingState;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SimpleTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simple:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        $task = new SchedulingState();
        $task->task = "exemplo";
        $task->first_date = "15";
        $task->first_month = "03";
        $task->second_date = "20";
        $task->second_month = "03";
        $task->past_day = "15";
        $task->save();

    }
}
