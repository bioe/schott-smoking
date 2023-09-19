<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeUpdateRequest;
use App\Models\Employee;
use App\Models\Hod;
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
            'keyword' => '',
            'hod_id' => '',
        ]);
        $list = Employee::query()->with('hod')->when(!empty($filters['keyword']), function ($q) use ($filters) {
            $q->orWhere('name', 'like', '%' . $filters['keyword'] . '%');
            $q->orWhere('card_id', 'like', '%' . $filters['keyword'] . '%');
        })->when(!empty($filters['hod_id']), function ($q) use ($filters) {
            $filters['hod_id'] == 'is_null' ?  $q->whereNull('hod_id') : $q->where('hod_id',  $filters['hod_id']);
        })->byHod(Auth::user())->filterSort($filters)->paginate(config('forms.paginate'));

        $hod_list = Hod::byHod(Auth::user())->get();

        return Inertia::render('Employee/Index', [
            'header' => Employee::header(),
            'filters' => $filters,
            'list' => $list,
            'hod_list' => $hod_list
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
        $hod_list = Hod::byHod(Auth::user())->get();

        return Inertia::render('Employee/Edit', [
            'data' => $data,
            'hod_list' => $hod_list,
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
