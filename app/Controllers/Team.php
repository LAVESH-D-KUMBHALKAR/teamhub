<?php

namespace App\Controllers;

use App\Models\TeamModel;

class Team extends BaseController
{
    protected $teamModel;

    public function __construct()
    {
        $this->teamModel = new TeamModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Teams'
        ];
        
        return view('teams/index', $data);
    }

    public function show($id)
    {
        $team = $this->teamModel->find($id);
        
        if (!$team) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if user is member
        if (!$this->teamModel->isMember($id, session()->get('user_id'))) {
            return redirect()->to('/teams')->with('error', 'You are not a member of this team');
        }

        $members = $this->teamModel->getMembers($id);
        $data = [
            'title' => $team['name'],
            'team' => $team,
            'members' => $members
        ];
        
        return view('teams/show.php', $data);
    }
}