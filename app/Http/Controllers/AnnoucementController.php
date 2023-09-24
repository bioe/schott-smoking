<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnoucementUpdateRequest;
use App\Models\Annoucement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AnnoucementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Build Filter
        $filters = $this->filterSessions($request, 'annoucement', [
            'keyword' => null
        ]);

        $list = Annoucement::query()->when(!empty($filters['keyword']), function ($q) use ($filters) {
            $q->orWhere('title', 'like', '%' . $filters['keyword'] . '%');
            $q->orWhere('content', 'like', '%' . $filters['keyword'] . '%');
        })->filterSort($filters)->paginate(config('forms.paginate'));

        return Inertia::render('Annoucement/Index', [
            'header' => Annoucement::header(),
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
    public function store(AnnoucementUpdateRequest $request)
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
            $data = new Annoucement;
        } else {
            $data = Annoucement::find($id);
        }

        return Inertia::render('Annoucement/Edit', [
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnnoucementUpdateRequest $request, string $id = null)
    {
        $data = $request->validated();
        if (null == $id) {
            $data = Annoucement::create($data);
            return Redirect::route('annoucements.edit', $data->id)->with('message', 'Annoucement created successfully');
        } else {
            Annoucement::find($id)->update($data);
            return Redirect::route('annoucements.edit', $id)->with('message', 'Annoucement updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Annoucement $annoucement)
    {
        $annoucement->delete();
        return Redirect::route('annoucements.index')->with('message', 'Annoucement deleted successfully');
    }
}
