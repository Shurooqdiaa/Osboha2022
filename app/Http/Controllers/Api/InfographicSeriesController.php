<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\NotAuthorized;
use App\Exceptions\NotFound;
use App\Http\Controllers\Controller;
use App\Http\Resources\InfographicSeriesResource;
use App\Models\InfographicSeries;
use App\Models\Media;
use App\Traits\MediaTraits;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InfographicSeriesController extends Controller
{
    use ResponseJson;
    use MediaTraits;

    public function index()
    {
        #######ASMAA#######
        //get and display all the series
        $series = InfographicSeries::all();
        if ($series->isNotEmpty()) {
            // found series response
            return $this->jsonResponseWithoutMessage(InfographicSeriesResource::collection($series), 'data', 200);
        } else {
            //not found series response
            throw new NotFound;
        }
    }

    public function create(Request $request)
    {
        #######ASMAA#######

        //create new series and store it in the database

        //validate requested data
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
            'section_id' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
        ]);

        //validator errors response
        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }

        //authorized user
        if (Auth::user()->can('create infographicSeries')) {
            //create new series
            $infographicSeries = infographicSeries::create($request->all());

            //create media for infographic 
            $this->createMedia($request->file('image'), $infographicSeries->id, 'infographicSeries');

            //success response after creating new infographic Series
            return $this->jsonResponse(new InfographicSeriesResource($infographicSeries), 'data', 200, "infographic Series Created Successfully");
        } else {
            //unauthorized user
            throw new NotAuthorized;
        }
    }

    public function show(Request $request)
    {
        #######ASMAA#######

        //validate series id
        $validator = Validator::make($request->all(), [
            'series_id' => 'required',
        ]);

        //validator errors response
        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }

        //find needed series
        $series = InfographicSeries::find($request->series_id);
        if ($series) {
            //found series response (display its data)
            return $this->jsonResponseWithoutMessage(new InfographicSeriesResource($series), 'data', 200);
        } else {
            //not found series response
            throw new NotFound;
        }
    }

    public function update(Request $request)
    {
        #######ASMAA#######

        //validate requested data
        $validator = Validator::make($request->all(), [
            'title'    => 'required',
            'section_id' => 'required',
            'series_id' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
        ]);

        //validator errors response
        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }

        //authorized user
        if (Auth::user()->can('edit infographicSeries')) {
            //find needed series
            $series = InfographicSeries::find($request->series_id);

            if ($series) {
                //updated found series
                $series->update($request->all());

                //retrieve InfographicSeries media 
                $infographicSeriesMedia = Media::where('infographic_series_id', $series->id)->first();

                //update media
                if ($infographicSeriesMedia) {
                    $this->updateMedia($request->file('image'), $infographicSeriesMedia->id);
                }
            } else {
                //not found series response
                throw new NotFound;
            }
            //success response after update
            return $this->jsonResponse(new InfographicSeriesResource($series), 'data', 200, "Infographic Series Updated Successfully");
        } else {
            //unauthorized user response
            throw new NotAuthorized;
        }
    }

    public function delete(Request $request)
    {
        #######ASMAA#######

        //validate series id 
        $validator = Validator::make($request->all(), [
            'series_id' => 'required',
        ]);

        //validator errors response
        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }

        if (Auth::user()->can('delete infographicSeries')) {
            //find needed series
            $series = InfographicSeries::find($request->series_id);

            if ($series) {
                //retrieve InfographicSeries media 
                $infographicSeriesMedia = Media::where('infographic_series_id', $series->id)->first();

                //keep media with no series id
                if ($infographicSeriesMedia) {
                    $infographicSeriesMedia->infographic_series_id = null;
                    $infographicSeriesMedia->save();
                }

                //delete found series
                $series->delete();

                //delete media
                // $this->deleteMedia($infographicSeriesMedia->id);
            } else {
                //not found series response
                throw new NotFound;
            }
            //success response after delete
            return $this->jsonResponse(new InfographicSeriesResource($series), 'data', 200, "infographic Series Deleted Successfully");
        } else {
            //unauthorized user response
            throw new NotAuthorized;
        }
    }
    public function SeriesBySection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }
        $infographicSeries = infographicSeries::where('section_id', $request->section_id)->get();
        if ($infographicSeries->isNotEmpty()) {
            return $this->jsonResponseWithoutMessage(InfographicSeriesResource::collection($infographicSeries), 'data', 200);
        } else {
            throw new NotFound;
        }
    }
}