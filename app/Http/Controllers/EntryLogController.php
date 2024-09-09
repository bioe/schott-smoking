<?php

namespace App\Http\Controllers;

use App\Http\Requests\CostCenterUpdateRequest;
use App\Models\EntryLog;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class EntryLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Build Filter
        $filters = $this->filterSessions($request, 'entrylog', [
            'keyword' => null,
            'start' => date("Y-m-d"),
            'end' => null,
            'station_id' => null,
            'overstay' => null,
            'exit_time' => null,
        ]);

        $list = EntryLog::with(['employee', 'station'])
            ->when(!empty($filters['keyword']), function ($q) use ($filters) {
                $q->whereHas('employee', function ($q) use ($filters) {
                    $q->where(function ($q) use ($filters) {
                        $q->orWhere('name', 'like', '%' . $filters['keyword'] . '%');
                        $q->orWhere('card_id', 'like', '%' . $filters['keyword'] . '%');
                        $q->orWhere('staff_no', 'like', '%' . $filters['keyword'] . '%');
                    });
                });
            })->when(!empty($filters['start']), function ($q)  use ($filters) {
                $q->startOfDay('enter_time', $filters['start']);
            })->when(!empty($filters['end']) && $filters['exit_time'] != 'no', function ($q)  use ($filters) {
                $q->endOfDay('exit_time', $filters['end']);
            })->when(!empty($filters['overstay'] != null && $filters['overstay'] == 'yes'), function ($q) {
                $q->where('overstay_seconds', '>', 0);
            })->when(!empty($filters['overstay'] != null && $filters['overstay'] == 'no'), function ($q) {
                $q->where('overstay_seconds', 0);
            })->when(!empty($filters['station_id'] != null), function ($q) use ($filters) {
                $q->where('station_id', $filters['station_id']);
            })->when(!empty($filters['exit_time'] != null), function ($q) use ($filters) {
                if ($filters['exit_time'] == 'yes') {
                    $q->whereNotNull('exit_time');
                } else {
                    $q->whereNull('exit_time');
                }
            })->byCostCenter(Auth::user())->filterSort($filters)->orderBy('created_at', 'desc')->paginate(config('forms.paginate'));

        $station_list = Station::all();

        if ($request->is_export == 1) {
            $this->export($list);
        } else {
            return Inertia::render('EntryLog/Index', [
                'header' => EntryLog::header(),
                'filters' => $filters,
                'list' => $list,
                'station_list' => $station_list
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->edit(null);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CostCenterUpdateRequest $request)
    {
        return $this->update($request, null);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(string $id = null) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(CostCenterUpdateRequest $request, string $id = null) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntryLog $entrylog)
    {
        $entrylog->delete();
        return Redirect::route('entrylogs.index')->with('message', 'Log deleted successfully');
    }

    public function export($list)
    {
        header("Content-Type: text/csv;charset=utf-8");
        header("Content-Disposition: attachment;filename=\"cost_center_template.csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $file = fopen('php://output', 'w');
        header('Content-Type: text/csv');

        $header = ['Staff No', 'Name', 'Entry Time', 'Exit Time', 'Duration', 'Overstay'];
        fputcsv($file, $header);
        foreach ($list as $log) {
            fputcsv($file, [
                $log->employee->staff_no,
                $log->employee->name,
                $log->enter_time,
                $log->exit_time,
                getHoursMinutes($log->actual_stay_duration_seconds, false),
                $log->overstay_seconds > 0 ? getHoursMinutes($log->overstay_seconds, false) : '',
            ]);
        }

        fclose($file);
        exit();
    }
}
