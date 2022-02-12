<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Media;
use App\Traits\ResponseJson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\File;


class CommentController extends Controller
{
    use ResponseJson;

    public function create(Request $request){

        $validator = Validator::make($request->all(), [
            'body' => 'required',
            'post_id' => 'required_without_all:comment_id',
            'comment_id' => 'required_without_all:post_id',
            'type' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }
        $input=$request->all();
        $input['user_id']= Auth::id();    
        if($request->type == 'thesis'){
            // calculate mark
            // create thesis
        }
        else{
            $comment = Comment::create($input);
            if($request->hasFile('image')){
                // if comment has media
                // upload media
                $imageName = time().'.'. $request->file('image')->extension();  
                $request->file('image')->move(public_path('assets/images'), $imageName);    
                // link media with comment
                $media = new Media();
                $media->media = $imageName; 
                $media->type = 'image'; 
                $media->user_id= Auth::id();
                $media->comment_id = $comment->id;
                $media->save();
            }
            return $this->jsonResponseWithoutMessage("Comment Craeted Successfully", 'data', 200);  
        }

    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }    

        $comment = Comment::find($request->comment_id);
        if($comment){
            return $this->jsonResponseWithoutMessage($comment, 'data',200);
        }
        else{
           // throw new NotFound;
        }
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required',
            'user_id' => 'required',
            'comment_id' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }
        $comment = Comment::find($request->comment_id);
        if(Auth::id() == $comment->user_id){
            if($comment->type != "thesis"){
                if($request->hasFile('image')){
                    // if comment has media
                    //delete old Media
                    $currentMedia= Media::where('comment_id', $comment->id)->first();
                    File::delete(public_path('assets/images/'.$currentMedia->media));

                    // upload media
                    $imageName = time().'.'. $request->file('image')->extension();  
                    $request->file('image')->move(public_path('assets/images'), $imageName);    

                    // update current media
                    $currentMedia->media = $imageName; 
                    $currentMedia->save();
                    
                }
                else{
                    $comment->update($request->all());
                }
                return $this->jsonResponseWithoutMessage("Comment Updated Successfully", 'data', 200);
            }
            else{
                //get thesis
                // check week id 
                // re-calculate mark
            }
        }          
        else{
            //throw new NotAuthorized;   
        }
        
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }  

        $comment = Comment::find($request->comment_id);
        if(Auth::user()->can('delete comment') || Auth::id() == $comment->user_id){
            if($comment->type != "thesis"){
                $currentMedia= Media::where('comment_id', $comment->id)->first();
                File::delete(public_path('assets/images/'.$currentMedia->media));
                $currentMedia->delete();
                $comment->delete();
                return $this->jsonResponseWithoutMessage("Comment Updated Successfully", 'data', 200);
            }
            else{
                //get thesis
                // check week id 
                // re calculate mark
            }
        }
        else{
            //throw new NotAuthorized;   
        }
        
    }
}
