<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    function create (Request $request){
        $reason = $request->reason;
        $userid = null;
        $commentid = null;
        $postid = null;

        if(isset($request->userid)){
            $userid = $request->userid;
        }

        if(isset($request->commentid)){
            $commentid = $request->commentid;
        }

        if(issset($request->postid)){
            $postid = $request->postid;
        }

        try{
            $this->authorize('create', [Report::class,$userid]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'You do not have authorization to create a report', 'response' => '403']);
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
            Report::find($reportId)->firstOrFail()->delete();
        } catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return redirect()->route('admin.index')->with('success', 'Report Deleted successfully!');

        //return response()->json(['response' => '200', 'message' => 'Report removed sucessfully']);
    }

    function getReports(Request $request){
        try{
            $this->authorize('get', [Report::class]);
        } catch(AuthorizationException $e){
            return response()->json(['message' => 'You do not have authorization to view reports', 'response' => '403']);
        }

        try{
            $reports = Report::with('user')->paginate(10);
        } catch(\Exception $e){
            return response()->json(['response' => '500', 'message' => 'Server problem. Try again']);
        }

        return response()->json($reports);
    }
}
