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

class EmailOverstay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:overstay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify HOD';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::with('cost_centers')->whereHas('cost_centers')->get();

        foreach ($users as $user) {
            $list = EntryLog::with('employee')->whereHas('employee', function ($q) use ($user) {
                $q->whereIn('cost_center_id', $user->cost_centers->pluck('id'));
            })->where('overstay_seconds', '>', 0)
                ->where('created_at', '>=', Carbon::now()->startOfDay())
                ->get();

            if ($list->count() > 0) {
                Mail::to($user->email)->send(new MailOverstayEmployee("Eric", $list));
                \Log::info("Email sent to " . $user->email);
            }
        }

        $msg = 'Email Overstay Complete';
        $this->comment($msg);
        \Log::info($msg);
    }
}
