<?php

namespace App\Http\Controllers;

use App\Http\Requests\StationUpdateRequest;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class StationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Build Filter
        $filters = $this->filterSessions($request, 'station', [
            'keyword' => ''
        ]);

        $list = Station::query()->when(!empty($filters['keyword']), function ($q) use ($filters) {
            $q->orWhere('name', 'like', '%' . $filters['keyword'] . '%');
            $q->orWhere('code', 'like', '%' . $filters['keyword'] . '%');
        })->filterSort($filters)->paginate(config('forms.paginate'));

        return Inertia::render('Station/Index', [
            'header' => Station::header(),
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
    public function store(StationUpdateRequest $request)
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
            $data = new Station;
        } else {
            $data = Station::find($id);
        }

        return Inertia::render('Station/Edit', [
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StationUpdateRequest $request, string $id = null)
    {
        $data = $request->validated();
        if (null == $id) {
            $data = Station::create($data);
            return Redirect::route('stations.edit', $data->id)->with('message', 'Station created successfully');
        } else {
            Station::find($id)->update($data);
            return Redirect::route('stations.edit', $id)->with('message', 'Station updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Station $station)
    {
        $station->delete();
        return Redirect::route('stations.index')->with('message', 'Station deleted successfully');
    }
}
