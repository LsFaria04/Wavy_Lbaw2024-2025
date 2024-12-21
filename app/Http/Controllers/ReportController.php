<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    function create (Request $request){
        $reason = $request->reason;
        $userid = $request->userid;
        $commentid = null;
        $postid = null;

        if(isset($request->commentid)){
            $commentid = $request->commentid;
        }

        if(isset($request->postid)){
            $postid = $request->postid;
        }

        try{
            $this->authorize('create', [Report::class]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'You do not have authorization to create a report', 'response' => '403']);
        }

        try{
            if($commentid === null) {
                Log::info("here");
                $this->authorize('alreadyReported', [Report::class,$postid, true]);
            }
            else {
                $this->authorize('alreadyReported', [Report::class,$commentid, false]);
            }
        } catch(\Exception $e){
            return response()->json(['message' => 'You already reported this post', 'response' => '403']);
        }


        try{
            Report::create([
                'reason' => $reason,
                'postid' => $postid,
                'commentid' => $commentid,
                'userid' => $userid
            ]);
        } catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json(['response' => '200', 'message' => 'Report submitted sucessfully']);
    }

    function delete (Request $request, $reportId){
        try{
            $this->authorize('delete',[Report::class]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'You do not have authorization to delete a report', 'response' => '403']);
        }

        try{
            $report = Report::find($reportId)->delete();
        } catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json(['response' => '200', 'message' => 'Report removed sucessfully']);
    }

    function getReports(Request $request){
        try{
            $this->authorize('get', [Report::class]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'You do not have authorization to view reports', 'response' => '403']);
        }

        try{
            $reports = Report::with('user')->paginate(10);
            for($i = 0; $i < count($reports); $i++){
                $report = $reports[$i];

                if($report->commentid === null){
                    continue;
                }

                //gets the comments parent post when they are subcomments
                $parentPost = null;
                $comment = Comment::find($report->commentid);
                while($parentPost === null){
                    if($comment->postid !== null){
                        $parentPost = $comment->postid;
                        break;
                    }
                    $comment = Comment::find($comment->parentcommentid);
                }

                $report->postid = $parentPost;
                $reports[$i] = $report;
            }
        } catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json($reports);
    }

    function searchReports(Request $request){
        $query = $request->input('q');
        //sanitizes the query to separate the words
        $sanitizedQuery = str_replace("'", "''", $query);
        try{
            $this->authorize('get', [Report::class]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'You do not have authorization to view reports', 'response' => '403']);
        }

        try{
            $reports = Report::with('user')
                    ->whereHas('user', function ($q) use ($sanitizedQuery) { $q->where('users.username', $sanitizedQuery);})
                    ->orWhereRaw("search @@ plainto_tsquery('english', ?)", [$sanitizedQuery])
                    ->paginate(10);

            for($i = 0; $i < count($reports); $i++){
                $report = $reports[$i];

                if($report->commentid === null){
                    continue;
                }

                //gets the comments parent post when they are subcomments
                $parentPost = null;
                $comment = Comment::find($report->commentid);
                while($parentPost === null){
                    if($comment->postid !== null){
                        $parentPost = $comment->postid;
                        break;
                    }
                    $comment = Comment::find($comment->parentcommentid);
                }

                $report->postid = $parentPost;
                $reports[$i] = $report;
            }
        } catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json($reports);


    }
}
