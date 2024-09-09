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
    protected $signature = 'script:run {function} {value?}';

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
        $allow_function = ['email', 'card'];
        $function = $this->argument('function');

        if (!in_array($function, $allow_function)) {
            $this->comment("Available command");
            $this->comment("script:run email");
            $this->comment("script:run card {hex}");
        } else if ($function == "email") {
            $this->email();
        } else if ($function == "card") {
            $this->card();
        }
    }

    public function email()
    {
        $this->comment("START TEST EMAIL");

        $email = "chris.lim@schott.com";
        Mail::to($email)->send(new MailOverstayEmployee("THIS IS TEST", null));
        \Log::info("Email sent to " . $email);
        $this->comment("END TEST EMAIL");
    }

    public function card()
    {
        $this->comment("START CARD");
        $value = $this->argument("value");
        $this->comment('Value: ' . $value);

        $output = behindHexToNumber($value, 0);
        $this->comment($output);
    }
}
