<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\playerDetails;
use App\Models\teamDetails;
use App\Models\playerTeam;

class playerController extends Controller
{
    public function index(){
        $ids = DB::table('playerteam')
            ->pluck('playerid')
            ->toArray();
        $players = DB::table('playerdetails')
            ->select('id','name', 'skill')
            ->whereNotIn('id', $ids)
            ->get();
        return response()->json($players);


    }

    public function getTeamOwnerCap(Request $request){
        $team=$request->input('teamName');

        $players=DB::table('teamdetails')
        ->select('teamOwners','teamCaptain')
        ->where('teamName',$team)
        ->get();
        return response()->json($players);
    }
    public function getDataBasedOnTeam(Request $request){



        $team=$request->input('teamName');
        $teamData = teamDetails::where('teamName',$team)->with('players')->get();
        // $ran = $teamOwners->players;
        // $players =DB::table('playerdetails as pd')

        // ->select('pd.name','pd.skill','td.teamOwners','td.teamCaptain','pt.playerid','pt.teamid')
        // ->join('playerteam as pt', 'pd.id', '=', 'pt.playerid')
        // ->join('teamdetails as td', 'pt.teamid', '=', 'td.id')
        // ->where('td.teamName', '=', $team)
        // ->get();
        return response()->json([
            "teamData"=> $teamData,
        ]);
    }

    public function getDataBasedOnSkill(Request $request) {
        $skill = $request->input('skill');

        
        if( $skill == "Batter")
            $skill = "Batting";
        else if($skill == 'Bowller')
            $skill = "Bowling";
        else if($skill == 'player'){
            return $this->index();
        }
            
        $players =DB::table('playerdetails as pd')
        ->select('pd.*')
        ->where('pd.skill', $skill)
        ->whereNotIn('pd.id', function($query) {
            $query->select('playerid')
                ->from('playerteam');
        })
        ->get(); 
    
        return response()->json($players);
      }

      public function assignTeam(Request $request){
         $teamName = $request->input('teamName');
         $playerId = $request->input('id');
         $teamid=DB::table('teamdetails as td')
         ->select('td.id')
         ->where('td.teamName',$teamName)
         ->get();
         DB::table('playerteam')->insert([
            ['playerid' => $playerId,'teamid' => $teamid[0]->id, 'year'=>'2023','random'=>'2']
        ]);
        
         return response()->json(["Mess" =>"succss inserted"]);
      }

      public function getTeams(){
        $teams=DB::table('teamdetails')
        ->select('teamName')
        ->get();
        return response()->json($teams);
      }


      public function deleteFromPlayerTeam(Request $request){
            $teamid  = $request->input('teamid');
            $playerid = $request->input('playerid');

            DB::table('playerTeam')
            ->where('playerId',$playerid)
            ->where('teamId',$teamid)
            ->delete();

            
        return response()->json(["msg"=>"player with id $playerid deleted succesfully from playerteam table"]);
            
      }
      
      
}
