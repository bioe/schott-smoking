<?php

namespace App\Console\Commands;

use App\Mail\MailOverstayEmployee;
use App\Models\Employee;
use App\Models\EntryLog;
use App\Models\User;
use App\Models\XportalEmployee;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class Script extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Usage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->comment("START TEST EMAIL");

        $email = "chris.lim@schott.com";
        Mail::to($email)->send(new MailOverstayEmployee("THIS IS TEST", null));
        \Log::info("Email sent to " . $email);
        $this->comment("END TEST EMAIL");
    }
}
