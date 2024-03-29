<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimelineType;
use App\Models\Timeline;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Exceptions\NotAuthorized;
use App\Exceptions\NotFound;
use App\Http\Resources\TimelineTypeResource;

class TimelineTypeController extends Controller
{
    use ResponseJson;

    public function index()
    {
        $timelineTypes = TimelineType::all();
        if($timelineTypes->isNotEmpty()){
            return $this->jsonResponseWithoutMessage(TimelineTypeResource::collection($timelineTypes), 'data',200);
        }
        else{
            throw new NotFound;
        }
    }

    public function create(Request $request){

        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }    
        if(Auth::user()->can('create type')){
            TimelineType::create($request->all());
            return $this->jsonResponseWithoutMessage("Timeline-Type Created Successfully", 'data', 200);
        }
        else{
            throw new NotAuthorized;   
        }
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }    

        $timelineType = TimelineType::find($request->id);
        if($timelineType){
            return $this->jsonResponseWithoutMessage(new TimelineTypeResource($timelineType), 'data',200);
        }
        else{
            throw new NotFound;
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }
        if(Auth::user()->can('edit type')){
            $timelineType = TimelineType::find($request->id);
            if($timelineType){
                $timelineType->update($request->all());
                return $this->jsonResponseWithoutMessage("Timeline-Type Updated Successfully", 'data', 200);
            }
            else{
                throw new NotFound;   
            }
        }
        else{
            throw new NotAuthorized;   
        }
        
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }  

        if(Auth::user()->can('delete type')){
            $timelineType = TimelineType::find($request->id);

            if($timelineType){
                Timeline::where('type_id',$request->id)
                    ->update(['type_id'=> 0]);
                    $timelineType->delete();
                
                return $this->jsonResponseWithoutMessage("Timeline-Type Deleted Successfully", 'data', 200);
            }
            else{
                throw new NotFound;
            }
        }
        else{
            throw new NotAuthorized;
        }
    }

}
