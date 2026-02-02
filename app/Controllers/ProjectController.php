<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\ProjectModel;
use App\Models\TeamModel;

class ProjectController extends BaseController
{
    use ResponseTrait;

    protected $projectModel;
    protected $teamModel;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
        $this->teamModel = new TeamModel();
    }

    public function index($teamId = null)
    {
        $userId = session()->get('user_id');
        
        if ($teamId) {
            // Check if user is member of this team
            if (!$this->teamModel->isMember($teamId, $userId)) {
                return $this->failUnauthorized('You are not a member of this team');
            }
            
            $projects = $this->projectModel->getTeamProjects($teamId);
        } else {
            // Get all projects from user's teams
            $userTeams = $this->teamModel->where('created_by', $userId)->findAll();
            $teamIds = array_column($userTeams, 'id');
            
            if (!empty($teamIds)) {
                $projects = $this->projectModel->whereIn('team_id', $teamIds)->findAll();
            } else {
                $projects = [];
            }
        }

        return $this->respond([
            'status' => 'success',
            'data' => $projects
        ]);
    }

    public function create()
    {
        $data = [
            'team_id' => $this->request->getVar('team_id'),
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
            'created_by' => session()->get('user_id'),
            'status' => 'active'
        ];

        // Check if user is member of the team
        if (!$this->teamModel->isMember($data['team_id'], session()->get('user_id'))) {
            return $this->failUnauthorized('You are not a member of this team');
        }

        if (!$this->projectModel->save($data)) {
            return $this->fail($this->projectModel->errors());
        }

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Project created successfully',
            'data' => ['id' => $this->projectModel->getInsertID()]
        ]);
    }

    public function show($id)
    {
        $project = $this->projectModel->getProjectWithTeam($id);
        
        if (!$project) {
            return $this->failNotFound('Project not found');
        }

        // Check if user is member of the team
        if (!$this->teamModel->isMember($project['team_id'], session()->get('user_id'))) {
            return $this->failUnauthorized('You are not a member of this team');
        }

        return $this->respond([
            'status' => 'success',
            'data' => $project
        ]);
    }

    public function update($id)
    {
        $project = $this->projectModel->find($id);
        
        if (!$project) {
            return $this->failNotFound('Project not found');
        }

        // Check if user is member of the team
        if (!$this->teamModel->isMember($project['team_id'], session()->get('user_id'))) {
            return $this->failUnauthorized('You are not a member of this team');
        }

        $data = $this->request->getJSON(true);
        
        if (!$this->projectModel->update($id, $data)) {
            return $this->fail($this->projectModel->errors());
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Project updated successfully'
        ]);
    }

    public function delete($id)
    {
        $project = $this->projectModel->find($id);
        
        if (!$project) {
            return $this->failNotFound('Project not found');
        }

        // Check if user is the creator or admin
        if ($project['created_by'] != session()->get('user_id')) {
            return $this->failUnauthorized('Only the creator can delete this project');
        }

        $this->projectModel->delete($id);

        return $this->respond([
            'status' => 'success',
            'message' => 'Project deleted successfully'
        ]);
    }
}