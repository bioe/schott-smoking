<?php

namespace App\Http\Controllers;

use App\Http\Requests\HodUpdateRequest;
use App\Models\Hod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class HodController extends Controller
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

        $list = Hod::query()->when(!empty($filters['keyword']), function ($q) use ($filters) {
            $q->orWhere('name', 'like', '%' . $filters['keyword'] . '%');
        })->filterSort($filters)->paginate(config('forms.paginate'));

        return Inertia::render('Hod/Index', [
            'header' => Hod::header(),
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
            $data = new Hod;
        } else {
            $data = Hod::find($id);
        }

        return Inertia::render('Hod/Edit', [
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
            $data = Hod::create($data);
            return Redirect::route('hods.edit', $data->id)->with('message', 'HOD created successfully');
        } else {
            Hod::find($id)->update($data);
            return Redirect::route('hods.edit', $id)->with('message', 'HOD updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hod $hod)
    {
        $hod->delete();
        return Redirect::route('hods.index')->with('message', 'HOD deleted successfully');
    }
}
