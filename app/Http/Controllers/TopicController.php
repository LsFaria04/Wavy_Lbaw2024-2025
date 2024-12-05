<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TopicController extends Controller
{   
    /*
    Used to add a topic on the database. Only admins can add new topics
    */
    function create(Request $request){

        //check if an user is authorized to create a topic
        try { 
        $this->authorize('create', Auth::user());
        } catch (AuthorizationException $e) {
            return redirect()->route('home')->with('error', 'You are not authorized to create new topics.');
        }

        try{
            $request->validate([
                'topicmessage' => 'required|string|max:30'
            ]);

            Topic::create(['topicmessage' => $request->topicname]);

            return redirect()->route('home')
            ->with('success', 'Your new topic was created successfully');
        } catch (\Exception $e) {
            return redirect()->route('home')
            ->with('error', 'Something went wrong. Please verify that the topic name has less than 30 characters');
        }
    }

    /*
    Used to delete topics on the database. Only admins can delete topics
    */
    function delete(Request $request, Topic $topic){

        //check if an user is authorized to delete a topic
        try{
            $this->authorize('delete', Auth::user());
        } catch (AuthorizationException $e) {
            return redirect()->route('home')->with('error', 'You are not authorized to delete topics.');
        }

        try{
            $topic->delete();
        } catch (\Exception $e) {
            return redirect()->route('home')
            ->with('error', 'Something went wrong when deleting the topic. Please try again');
        }


    }

    //gets topics that a user can user
    function getTopicsToAdd(Request $request, $userId){
        if(!Auth::check()){
            return response()->json(['message' => 'Not authenticated', 'response' => '403']);
        }

        //Can only access the topics if the authenticated user has the same id has the one sent in the request
        if(Auth::user()->userid == $userId){
            Log::info("here3");
            $topics = DB::table('topic')
                        ->leftjoin('user_topics', function($join) use ($userId) { 
                            $join->on('topic.topicid', '=', 'user_topics.topicid') 
                                ->where('user_topics.userid', '=', $userId);
                        })
                        ->WhereNull('user_topics.userid')
                        ->select('topic.*')
                        ->distinct()
                        ->paginate(10);
            return response()->json($topics);
        }

        return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
    }

    /*
    Returns the topics that are associated to an user
    */
    function getUserTopics(Request $request, $userId){
        if(!Auth::check()){
            return response()->json(['message' => 'Not authenticated', 'response' => '403']);
        }

        //Can only access the topics if the authenticated user has the same id has the one sent in the request
        if(Auth::user()->userid == $userId){
            $topics = DB::table('topic')
                        ->join('user_topics', 'topic.topicid', '=', 'user_topics.topicid')
                        ->where('user_topics.userid','?')
                        ->select('topic.*')
                        ->setBindings([$userId])
                        ->paginate(10);
            return response()->json($topics);
        }

        return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
    }

    /*
    Returns the topics that are associated with a post
    */
    function getPostTopics(Request $request, $postId){
            $topics = DB::table('topic')
                        ->join('post_topics', 'topic.topicid', '=', 'post_topics.topicid')
                        ->join('post', 'post.postid', '=', 'post_topics.posyid')
                        ->where('post.postid','?')
                        ->select('topic.*')
                        ->setBindings([$postId])
                        ->get();

            return $topics;
    }

    /*
    Associates a topic to a user
    */
    function addTopicToUser(Request $request, $topicId, $userid){
        if(!Auth::check()){
            return response()->json(['response' => '403']);
        }
        try{
            DB::table('user_topics')
                ->insert([
                    'topicid' => $topicId,
                    'userid' => $userid
                ]);
        } catch(\Exception $e){
            return response()->json(['response' => '500']);
        }
        
        return response()->json(['response' => '200']);  
    }

    /*
    Removes the association of a topic to a user
    */
    function removeTopicFromUser(Request $request,$topicId, $userid){
        if(!Auth::check()){
            return response()->json(['response' => '403']);
        }
        try{
        DB::table('user_topics')
            ->where([
            'topicid' => $topicId,
            'userid' => $userid
            ])
            ->delete();
        }catch(\Exception $e){
            return response()->json(['response' => '500']);
        }
        return response()->json(['response' => '200']); 
    }



}
