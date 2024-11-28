<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /*
    Returns the topics that are associated to an user
    */
    function getUserTopics(Request $request, $userId){
        if(!Auth::check()){
            return redirect()->route('home')
            ->with('error', 'Please authenticate to access this feature');
        }

        //Can only access the topics if the authenticated user has the same id has the one sent in the request
        if(Auth::user()->userid == $userId){

            $topics = DB::table('topic')
                        ->join('user_topics', 'topic.topicid', '=', 'user_topics.topicid')
                        ->join('users', 'users.userid', '=', 'user_topics.userid')
                        ->where('users.userid','?')
                        ->select('topic.*')
                        ->setBindings([$userId])
                        ->get();

            return $topics;
        }

        return redirect()->route('home')
            ->with('error', 'Your not allowed to access the topics of another user!');

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

        DB::table('user_topics')
            ->insert([
                'topicid' => $topicId,
                '$userid' => $userid
            ]);
        
        return redirect()->route('home')
            ->with('success', 'Your topic was successfully added');  
    }

    /*
    Removes the association of a topic to a user
    */
    function removeTopicFromUser(Request $request,$topicId, $userid){
        DB::table('user_topics')
            ->where('topicid', $topicId)
            ->where('userid', $userId)
            ->delete();
        
        return redirect()->route('home')
            ->with('success', 'Your topic was successfully deleted');  
    }



}
