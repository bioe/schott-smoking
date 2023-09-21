<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeUpdateRequest;
use App\Models\Employee;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Auth;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Build Filter
        $filters = $this->filterSessions($request, 'employee', [
            'keyword' => null,
            'cost_center_id' => null,
        ]);
        $list = Employee::query()->with('cost_center')->when(!empty($filters['keyword']), function ($q) use ($filters) {
            $q->orWhere('name', 'like', '%' . $filters['keyword'] . '%');
            $q->orWhere('card_id', 'like', '%' . $filters['keyword'] . '%');
        })->when(!empty($filters['cost_center_id']), function ($q) use ($filters) {
            $filters['cost_center_id'] == 'is_null' ?  $q->whereNull('cost_center_id') : $q->where('cost_center_id',  $filters['cost_center_id']);
        })->byCostCenter(Auth::user())->filterSort($filters)->paginate(config('forms.paginate'));

        $cost_center_list = CostCenter::byCostCenter(Auth::user())->get();

        return Inertia::render('Employee/Index', [
            'header' => Employee::header(),
            'filters' => $filters,
            'list' => $list,
            'cost_center_list' => $cost_center_list
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
    public function store(EmployeeUpdateRequest $request)
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
            $data = new Employee;
        } else {
            $data = Employee::find($id);
        }
        $cost_center_list = CostCenter::byCostCenter(Auth::user())->get();

        return Inertia::render('Employee/Edit', [
            'data' => $data,
            'cost_center_list' => $cost_center_list,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeUpdateRequest $request, string $id = null)
    {
        $data = $request->validated();
        if (null == $id) {
            $data = Employee::create($data);
            return Redirect::route('employees.edit', $data->id)->with('message', 'Employee created successfully');
        } else {
            Employee::find($id)->update($data);
            return Redirect::route('employees.edit', $id)->with('message', 'Employee updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return Redirect::route('employees.index')->with('message', 'Employee deleted successfully');
    }
}
