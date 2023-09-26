<?php

namespace App\Http\Controllers;

use App\Http\Requests\CostCenterUpdateRequest;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CostCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Build Filter
        $filters = $this->filterSessions($request, 'costcenter', [
            'keyword' => null
        ]);

        $list = CostCenter::query()->when(!empty($filters['keyword']), function ($q) use ($filters) {
            $q->orWhere('name', 'like', '%' . $filters['keyword'] . '%');
        })->filterSort($filters)->paginate(config('forms.paginate'));

        return Inertia::render('CostCenter/Index', [
            'header' => CostCenter::header(),
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

    public function edit(string $id = null)
    {
        if (null == $id) {
            $data = new CostCenter;
        } else {
            $data = CostCenter::find($id);
        }

        return Inertia::render('CostCenter/Edit', [
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CostCenterUpdateRequest $request, string $id = null)
    {
        $data = $request->validated();
        if (null == $id) {
            $data = CostCenter::create($data);
            return Redirect::route('costcenters.edit', $data->id)->with('message', 'Cost Center created successfully');
        } else {
            CostCenter::find($id)->update($data);
            return Redirect::route('costcenters.edit', $id)->with('message', 'Cost Center updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CostCenter $costCenter)
    {
        $costCenter->delete();
        return Redirect::route('costcenters.index')->with('message', 'Cost Center deleted successfully');
    }

    public function getImport()
    {
        return Inertia::render('CostCenter/Import', []);
    }

    public function postImport(Request $request)
    {
        if ($request->hasFile('csv')) {
            $file = fopen($request->file('csv'), "r");

            $data = [];
            while (($column = fgetcsv($file)) != false) {
                $data[] = ['code' => strtoupper($column[0]), 'name' => $column[1]];
            }
            CostCenter::upsert($data, ['code'], ['name']);
            return Redirect::route('costcenters.index')->with('message', 'Import successfully');
        }
        return Redirect::route('costcenters.import');
    }

    public function getTemplate()
    {
        header("Content-Type: text/csv;charset=utf-8");
        header("Content-Disposition: attachment;filename=\"cost_center_template.csv\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $file = fopen('php://output', 'w');
        header('Content-Type: text/csv');

        $header = ['Code', 'Name'];
        fputcsv($file, $header);
        fclose($file);
        exit();
    }
}
