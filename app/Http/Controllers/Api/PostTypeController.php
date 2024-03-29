<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostType;
use App\Models\Post;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Exceptions\NotAuthorized;
use App\Exceptions\NotFound;
use App\Http\Resources\PostTypeResource;

class PostTypeController extends Controller
{
    use ResponseJson;

    public function index()
    {
        $postTypes = PostType::all();
        if($postTypes->isNotEmpty()){
            return $this->jsonResponseWithoutMessage(PostTypeResource::collection($postTypes), 'data',200);
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
            PostType::create($request->all());
            return $this->jsonResponseWithoutMessage("Post-Type Created Successfully", 'data', 200);
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

        $postType = PostType::find($request->id);
        if($postType){
            return $this->jsonResponseWithoutMessage(new PostTypeResource($postType), 'data',200);
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
            $postType = PostType::find($request->id);
            if($postType){
                $postType->update($request->all());
                return $this->jsonResponseWithoutMessage("Post-Type Updated Successfully", 'data', 200);
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
            $postType = PostType::find($request->id);

            if($postType){
                Post::where('type_id',$request->id)
                    ->update(['type_id'=> 0]);
                    $postType->delete();
                
                return $this->jsonResponseWithoutMessage("Post-Type Deleted Successfully", 'data', 200);
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
