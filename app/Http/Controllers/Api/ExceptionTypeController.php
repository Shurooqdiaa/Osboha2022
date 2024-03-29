<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExceptionType;
use App\Models\Exception;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Exceptions\NotAuthorized;
use App\Exceptions\NotFound;
use App\Http\Resources\ExceptionTypeResource;

class ExceptionTypeController extends Controller
{
    use ResponseJson;

    public function index()
    {
        $exceptionTypes = ExceptionType::all();
        if($exceptionTypes->isNotEmpty()){
            return $this->jsonResponseWithoutMessage(ExceptionTypeResource::collection($exceptionTypes), 'data',200);
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
            ExceptionType::create($request->all());
            return $this->jsonResponseWithoutMessage("Exception-Type Created Successfully", 'data', 200);
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

        $exceptionType = ExceptionType::find($request->id);
        if($exceptionType){
            return $this->jsonResponseWithoutMessage(new ExceptionTypeResource($exceptionType), 'data',200);
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
            $exceptionType = ExceptionType::find($request->id);
            if($exceptionType){
                $exceptionType->update($request->all());
                return $this->jsonResponseWithoutMessage("Exception-Type Updated Successfully", 'data', 200);
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
            $exceptionType = ExceptionType::find($request->id);

            if($exceptionType){
                Exception::where('type_id',$request->id)
                    ->update(['type_id'=> 0]);
                    $exceptionType->delete();
                
                return $this->jsonResponseWithoutMessage("Group-Type Deleted Successfully", 'data', 200);
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
