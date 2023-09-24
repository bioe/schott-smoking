<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerUpdateRequest;
use App\Models\Banner;
use App\Traits\UploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class BannerController extends Controller
{
    use UploadTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Build Filter
        $filters = $this->filterSessions($request, 'banner', [
            'keyword' => null
        ]);

        $list = Banner::query()->when(!empty($filters['keyword']), function ($q) use ($filters) {
            $q->orWhere('title', 'like', '%' . $filters['keyword'] . '%');
        })->filterSort($filters)->paginate(config('forms.paginate'));

        return Inertia::render('Banner/Index', [
            'header' => Banner::header(),
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
    public function store(BannerUpdateRequest $request)
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
            $data = new Banner;
        } else {
            $data = Banner::find($id);
        }

        return Inertia::render('Banner/Edit', [
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BannerUpdateRequest $request, string $id = null)
    {
        $data = $request->validated();

        $media = $request->file('media');

        $file_data = $this->saveFile($media, 'banners/');

        if (!empty($file_data['path'])) {
            $data['type'] = $file_data['type'];
            $data['filename'] = $file_data['name'];
            $data['path'] = $file_data['path'];

            if (null == $id) {
                $data = Banner::create($data);
                return Redirect::route('banners.edit', $data->id)->with('message', 'Banner created successfully');
            } else {
                Banner::find($id)->update($data);
                return Redirect::route('banners.edit', $id)->with('message', 'Banner updated successfully');
            }
        } else {
            return Redirect::route('banners.create')->with('message', 'Fail to upload.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();
        return Redirect::route('banners.index')->with('message', 'Banner deleted successfully');
    }
}
