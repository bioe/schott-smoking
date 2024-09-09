<?php

namespace App\Console\Commands;

use App\Models\EntryLog;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EntryDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entry:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To delete entry that has no exit time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $msg = 'Start Delete Entry';
        $this->comment($msg);
        \Log::info($msg);

        $longest_seconds = 0;
        $stations = Station::all();
        //Get longest time they can stay in the station.
        foreach ($stations as $s) {
            if ($longest_seconds < $s->stay_duration_seconds) {
                $longest_seconds = $s->stay_duration_seconds;
            }
        }
        $longest_seconds += 3600; //Add another hour, prevent instant delete that just enter the station and haven't leave

        //Delete entry that do not have exit time and is create more than the longest_seconds.
        $entryLog = EntryLog::enterOnly()->where('created_at', '<=', Carbon::now()->subSeconds($longest_seconds))->get();
        $this->comment($entryLog->count());
        $entryLog = EntryLog::enterOnly()->where('created_at', '<=', Carbon::now()->subSeconds($longest_seconds))->delete();
        $msg = 'Delete Entry Complete';
        $this->comment($msg);
        \Log::info($msg);
    }
}
