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
        $this->authorize('create', Topic::class);
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
            $this->authorize('delete', Topic::class);
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
        try{
            $this->authorize('userTopics', [Topic::class,$userId]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
        }

        //Can only access the topics if the authenticated user has the same id has the one sent in the request
        try{
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
        }catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
    }

    /*
    Returns the topics that are associated to an user
    */
    function getUserTopics(Request $request, $userId){

        try{
            $this->authorize('userTopics', [Topic::class,$userId]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
        }

        //Can only access the topics if the authenticated user has the same id has the one sent in the request
        try{
            $topics = DB::table('topic')
                        ->join('user_topics', 'topic.topicid', '=', 'user_topics.topicid')
                        ->where('user_topics.userid','?')
                        ->select('topic.*')
                        ->setBindings([$userId])
                        ->paginate(10);
            return response()->json($topics);
        } catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
    }

    /*
    Returns the topics that are associated with a post
    */
    function getPostTopics(Request $request, $postId){
            $topics = DB::table('topic')
                        ->join('post_topics', 'topic.topicid', '=', 'post_topics.topicid')
                        ->join('post', 'post.postid', '=', 'post_topics.postid')
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
        try{
            $this->authorize('userTopics', [Topic::class,$userid]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
        }

        try{
            DB::table('user_topics')
                ->insert([
                    'topicid' => $topicId,
                    'userid' => $userid
                ]);
        } catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }
        
        return response()->json(['response' => '200']);  
    }

    /*
    Removes the association of a topic to a user
    */
    function removeTopicFromUser(Request $request,$topicId, $userid){
        try{
            $this->authorize('userTopics', [Topic::class,$userid]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
        }

        try{
        DB::table('user_topics')
            ->where([
            'topicid' => $topicId,
            'userid' => $userid
            ])
            ->delete();
        }catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }
        return response()->json(['response' => '200']); 
    }

    /*
    Searches for topics that belong to a user using a search query
    */
    function searchUserTopic(Request $request, $userid){
        $query = $request->input('q');
        //sanitizes the query to separate the words
        $sanitizedQuery = str_replace("'", "''", $query);

        try{
            $this->authorize('userTopics', [Topic::class,$userid]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
        }
        try{
            $topics = DB::table('topic')
                ->join('user_topics', 'topic.topicid', '=', 'user_topics.topicid')
                ->where('user_topics.userid','?')
                ->whereRaw("topic.search @@ plainto_tsquery('english', ?)")
                ->select('topic.*')
                ->setBindings([$userid, $sanitizedQuery])
                ->paginate(10);

            return response()->json($topics);
        }catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server Problem. Try again']);
        }
        return response()->json(['response' => '403', 'message' => 'Cannot access other users topics']);    
    }

    /*
    Searches for topics that a user can add using a search query
    */
    function searchTopicsToAdd(Request $request, $userid){
        $query = $request->input('q');
        //sanitizes the query to separate the words
        $sanitizedQuery = str_replace("'", "''", $query);

        try{
            $this->authorize('userTopics', [Topic::class,$userid]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
        }
        try{
            $topics = DB::table('topic')
            ->leftjoin('user_topics', function($join) use ($userid) { 
                $join->on('topic.topicid', '=', 'user_topics.topicid') 
                    ->where('user_topics.userid', '=', $userid);
            })
            ->WhereNull('user_topics.userid')
            ->whereRaw("topic.search @@ plainto_tsquery('english', ?)")
            ->select('topic.*')
            ->setBindings([$sanitizedQuery])
            ->distinct()
            ->paginate(10);

            return response()->json($topics);

        }catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }
        return response()->json(['response' => '403', 'message' => 'Cannot search other users topics']);   
        
    }



}
