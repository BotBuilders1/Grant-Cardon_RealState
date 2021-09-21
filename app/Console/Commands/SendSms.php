<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


class SendSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio:SendSms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will send sms on schedule';

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
        app('App\Http\Controllers\HookController')->kartraWebHookCall();
        // app('App\Http\Controllers\IntrgrationController')->getSmsReadySchdule();
        
        // use for testing, Comment out the script above and uncomment the script under and it will send you message every minute
        // app('App\Http\Controllers\IntrgrationController')->testsmstome();
    }
}
