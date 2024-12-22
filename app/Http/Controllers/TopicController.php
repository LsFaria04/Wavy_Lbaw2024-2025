<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class TopicController extends Controller
{   
    /*
    Used to add a topic on the database. Only admins can add new topics
    */
    function create(Request $request) {

        //check if an user is authorized to create a topic
        try { 
        $this->authorize('create', Topic::class);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => 'Your not authorized to create topics', 'response' => '403']);
        }

        try{
            $request->validate([
                'topicname' => 'required|string|max:30'
            ]);

            Topic::create(['topicname' => $request->topicname]);

            $newId = Topic::where('topicname', $request->topicname)->firstOrFail()->topicid;

            return response()->json(['message' => 'Topic added sucessfully', 'response' => '200', 'topicname' => $request->topicname, 'topicid' => $newId]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Problem', 'response' => '500']);

        }
    }

    /*
    Used to delete topics on the database. Only admins can delete topics
    */
    function delete(Request $request, $topicid) {
        try{
            $topic = Topic::findOrFail($topicid);
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'Topic does not exist', 'response' => '404']);
        }

        //check if an user is authorized to delete a topic
        try{
            $this->authorize('delete', Topic::class);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Your not authorized to delete Topics', 'response' => '403']);
        }

        try{
            //update the topics in the posts and give them the general topic if they don't have any
            $posts = Post::whereHas('topics', function ($query) use ($topicid) {$query->where('topic.topicid', $topicid); })->get();

            foreach($posts as $post) {
                $post->topics()->detach($topicid);
                if($post->topics()->count() == 0) {
                    $post->topics()->attach(1);
                }
            }

            $topic->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Serve Problem', 'response' => '500']);
        }

        return response()->json(['message' => 'Topic deleted sucessfully', 'response' => '200']);


    }

    //gets topics that a user can user
    function getTopicsToAdd(Request $request, $userId) {
        try{
            $this->authorize('userTopics', [Topic::class,$userId]);
        } catch(AuthorizationException $e) {
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
        }catch(\Exception $e) {
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
    }

    /*
    Returns the topics that are associated to an user
    */
    function getUserTopics(Request $request, $userId) {

        try{
            $this->authorize('userTopics', [Topic::class,$userId]);
        } catch(AuthorizationException $e) {
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
        } catch(\Exception $e) {
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
    }

    /*
    Returns all the topics that can be added to a new post
    */
    function getAllTopics(Request $request) {
        try{
            $topics = Topic::paginate(10);
        }catch(\Exception $e) {
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json($topics);
    }

    /*
    Returns all the topics that can be added to a new post
    */
    function getAllTopicsToPost(Request $request, $postid) {
        try{
            $topics = DB::table('topic')
            ->leftjoin('post_topics', function($join) use ($postid) { 
                $join->on('topic.topicid', '=', 'post_topics.topicid') 
                    ->where('post_topics.postid', '=', $postid);
            })
            ->WhereNull('post_topics.postid')
            ->select('topic.*')
            ->distinct()
            ->paginate(10);

        }catch(\Exception $e) {
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json($topics);
    }

    /*
    Associates a topic to a user
    */
    function addTopicToUser(Request $request, $topicId, $userid) {
        try{
            $this->authorize('userTopics', [Topic::class,$userid]);
        } catch(AuthorizationException $e) {
            return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
        }

        try{
            DB::table('user_topics')
                ->insert([
                    'topicid' => $topicId,
                    'userid' => $userid
                ]);
        } catch(\Exception $e) {
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }
        
        return response()->json(['response' => '200' ,'message' => 'Topic added successfully']);  
    }

    /*
    Removes the association of a topic to a user
    */
    function removeTopicFromUser(Request $request,$topicId, $userid) {
        try{
            $this->authorize('userTopics', [Topic::class,$userid]);
        } catch(AuthorizationException $e) {
            return response()->json(['message' => 'Cannot access other users topics', 'response' => '403']);
        }

        try{
        DB::table('user_topics')
            ->where([
            'topicid' => $topicId,
            'userid' => $userid
            ])
            ->delete();
        }catch(\Exception $e) {
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }
        return response()->json(['response' => '200', 'message' => 'Topic removed successfully']); 
    }

    /*
    Searches for topics that belong to a user using a search query
    */
    function searchUserTopic(Request $request, $userid) {
        $query = $request->input('q');
        //sanitizes the query to separate the words
        $sanitizedQuery = str_replace("'", "''", $query);

        try{
            $this->authorize('userTopics', [Topic::class,$userid]);
        } catch(AuthorizationException $e) {
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
        }catch(\Exception $e) {
            return response()->json(['response' => '500', 'message' => 'Server Problem. Try again']);
        }
        return response()->json(['response' => '403', 'message' => 'Cannot access other users topics']);    
    }

    /*
    Searches for topics that a user can add using a search query
    */
    function searchTopicsToAdd(Request $request, $userid) {
        $query = $request->input('q');
        //sanitizes the query to separate the words
        $sanitizedQuery = str_replace("'", "''", $query);

        try{
            $this->authorize('userTopics', [Topic::class,$userid]);
        } catch(AuthorizationException $e) {
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

        }catch(\Exception $e) {
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }
        return response()->json(['response' => '403', 'message' => 'Cannot search other users topics']);   
        
    }

    /*
    Searches all the topics that can be added to a post  using a search query
    */
    function searchAllTopicsToPost(Request $request, $postid) {
        $query = $request->input('q');
        //sanitizes the query to separate the words
        $sanitizedQuery = str_replace("'", "''", $query);

        try{
            $topics = DB::table('topic')
            ->leftjoin('post_topics', function($join) use ($postid) { 
                $join->on('topic.topicid', '=', 'post_topics.topicid') 
                    ->where('post_topics.postid', '=', $postid);
            })
            ->WhereNull('post_topics.postid')
            ->whereRaw("search @@ plainto_tsquery('english', ?)")
            ->select('topic.*')
            ->distinct()  
            ->setBindings([$query])
            ->paginate(10);

        }catch(\Exception $e) {
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json($topics);
    }

    function searchAllTopics(Request $request) {
        $query = $request->input('q');
        //sanitizes the query to separate the words
        $sanitizedQuery = str_replace("'", "''", $query);

        try{
            $topics = Topic::whereRaw("search @@ plainto_tsquery('english', ?)")
                        ->setBindings([$query])
                        ->paginate(10);
        }catch(\Exception $e) {
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json($topics);
    }



}
