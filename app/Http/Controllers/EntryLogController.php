<?php

namespace App\Http\Controllers;

use App\Http\Requests\HodUpdateRequest;
use App\Models\EntryLog;
use Illuminate\Http\Request;
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
        $filters = $this->filterSessions($request, 'hod', [
            'keyword' => ''
        ]);

        $list = EntryLog::query()->when(!empty($filters['keyword']), function ($q) use ($filters) {
            $q->orWhere('name', 'like', '%' . $filters['keyword'] . '%');
        })->filterSort($filters)->paginate(config('forms.paginate'));

        return Inertia::render('EntryLog/Index', [
            'header' => EntryLog::header(),
            'filters' => $filters,
            'list' => $list,
        ]);
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
    public function store(HodUpdateRequest $request)
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

    public function edit(string $id = null)
    {
        if (null == $id) {
            $data = new EntryLog;
        } else {
            $data = EntryLog::find($id);
        }

        return Inertia::render('EntryLog/Edit', [
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HodUpdateRequest $request, string $id = null)
    {
        $data = $request->validated();
        if (null == $id) {
            $data = EntryLog::create($data);
            return Redirect::route('entrylogs.edit', $data->id)->with('message', 'HOD created successfully');
        } else {
            EntryLog::find($id)->update($data);
            return Redirect::route('entrylogs.edit', $id)->with('message', 'HOD updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntryLog $entryLog)
    {
        $entryLog->delete();
        return Redirect::route('EntryLog.index')->with('message', 'Log deleted successfully');
    }
}
