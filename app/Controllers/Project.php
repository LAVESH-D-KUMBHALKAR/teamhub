<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use App\Models\TeamModel;

class Project extends BaseController
{
    protected $projectModel;
    protected $teamModel;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
        $this->teamModel = new TeamModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Projects'
        ];
        
        return view('projects/index', $data);
    }

    public function show($id)
    {
        $project = $this->projectModel->getProjectWithTeam($id);
        
        if (!$project) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Check if user is member of the team
        if (!$this->teamModel->isMember($project['team_id'], session()->get('user_id'))) {
            return redirect()->to('/projects')->with('error', 'You are not a member of this team');
        }

        $data = [
            'title' => $project['name'],
            'project' => $project
        ];
        
        return view('projects/show', $data);
    }
}