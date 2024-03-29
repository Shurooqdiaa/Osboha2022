<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseJson;
use App\Exceptions\NotAuthorized;
use App\Exceptions\NotFound;
use App\Http\Resources\FriendResource;
use Illuminate\Support\Facades\DB;


class FriendController extends Controller
{   
    use ResponseJson;
    

    /**
     * list all user`s freinds.
     *
     * @return jsonResponseWithoutMessage ;
     */
    public function index()
    {
        $friends = DB::table('friends')
        ->where('user_id', Auth::id())
        ->orWhere('friend_id', Auth::id())
        ->groupBy('friend_id')
        ->get();
        
        return $this->jsonResponseWithoutMessage($friends, 'data', 200);
            //return $this->jsonResponseWithoutMessage(FriendResource::collection($friends), 'data', 200);
    }

    /**
     * send freind request if no frienship is exsist.
     *
     * @param  Request  $request
     * @return jsonResponseWithoutMessage ;
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'friend_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }
        if (User::where('id',$request->friend_id)->exists()) {
            $friend=$request->friend_id;

            $friendship=Friend::where(function($q) {
                $q->where('user_id', Auth::id())
                ->orWhere('friend_id', Auth::id());
                })
                ->where(function($q) use ($friend){
                    $q->where('user_id',$friend)
                    ->orWhere('friend_id',$friend);
                    })->get();
                if ($friendship->isNotEmpty()) {
                    return $this->jsonResponseWithoutMessage("Friendship already exsits", 'data', 200);

                } else {
                $input = $request->all();
                $input['user_id'] = Auth::id();
                Friend::create($input);

                $msg = "You have new friend request";
                (new NotificationController)->sendNotification($request->friend_id, $msg);
                return $this->jsonResponseWithoutMessage("Friendship Created Successfully", 'data', 200);

                } 
        }
        else{
            return $this->jsonResponseWithoutMessage("user dose not exists", 'data', 200);
        }

    }
    
    /**
     * show frienship if exsist.
     *
     * @param  Request  $request
     * @return jsonResponseWithoutMessage ;
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'friendship_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }

        $friend = Friend::find($request->Friendship);
        if ($friend) {
            return $this->jsonResponseWithoutMessage($friend, 'data', 200);
            //return $this->jsonResponseWithoutMessage(new FriendResource($friend), 'data', 200);
        } else {
            throw new NotFound;
        }
    }

    /**
     * accept freind request [only friend_id = Auth can accept].
     *
     * @param  Request  $request
     * @return jsonResponseWithoutMessage ;
     */

    public function accept(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'friendship_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }    

        $friendship = Friend::find($request->friendship_id);
        if($friendship){
            // to accept user should not be the auther of the relation
            if(Auth::id() != $friendship->user_id || Auth::id() == $friendship->friend_id){
                $friendship->status=1;
                $friendship->save();
                return $this->jsonResponseWithoutMessage("Friend Accepted Successfully", 'data', 200);
            }
            else{
                throw new NotAuthorized;   
            }
        }
        else{
            throw new NotFound;   
        }
    
    }
    
    
    /**
     * delete frienship.
     *
     * @param  Request  $request
     * @return jsonResponseWithoutMessage ;
     */

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'friendship_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponseWithoutMessage($validator->errors(), 'data', 500);
        }

        $friendship = Friend::find($request->friendship_id);
        if ($friendship) {
            if (Auth::id() == $friendship->user_id || Auth::id() == $friendship->friend_id) {
                $friendship->delete();
                return $this->jsonResponseWithoutMessage("Friendship Deleted Successfully", 'data', 200);
            } else {
                throw new NotAuthorized;
            }
        } 
        else {
            throw new NotFound;
            
        }
        
        
    }
}
