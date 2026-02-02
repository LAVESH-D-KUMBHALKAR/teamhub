<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\TeamModel;
use App\Models\UserModel;

class TeamController extends BaseController
{
    use ResponseTrait;

    protected $teamModel;
    protected $userModel;

    public function __construct()
    {
        $this->teamModel = new TeamModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $teams = $this->userModel->getTeams($userId);
        
        return $this->respond([
            'status' => 'success',
            'data' => $teams
        ]);
    }

    public function create()
    {
        $data = [
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
            'created_by' => session()->get('user_id')
        ];

        if (!$this->teamModel->save($data)) {
            return $this->fail($this->teamModel->errors());
        }

        $teamId = $this->teamModel->getInsertID();
        
        // Add creator as admin member
        $this->teamModel->addMember($teamId, session()->get('user_id'), 'admin');

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Team created successfully',
            'data' => ['id' => $teamId]
        ]);
    }

    public function show($id)
    {
        $team = $this->teamModel->find($id);
        
        if (!$team) {
            return $this->failNotFound('Team not found');
        }

        $members = $this->teamModel->getMembers($id);

        return $this->respond([
            'status' => 'success',
            'data' => [
                'team' => $team,
                'members' => $members
            ]
        ]);
    }

    public function invite($teamId)
    {
        $email = $this->request->getVar('email');
        
        // Check if team exists
        $team = $this->teamModel->find($teamId);
        if (!$team) {
            return $this->failNotFound('Team not found');
        }

        // Check if user is admin of the team
        if (!$this->teamModel->isMember($teamId, session()->get('user_id'))) {
            return $this->failUnauthorized('You are not a member of this team');
        }

        // Mock invitation (in real app, you would send an email)
        $db = db_connect();
        $token = bin2hex(random_bytes(32));
        
        $invitationData = [
            'team_id' => $teamId,
            'email' => $email,
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $db->table('invitations')->insert($invitationData);

        return $this->respond([
            'status' => 'success',
            'message' => 'Invitation sent to ' . $email,
            'data' => [
                'invitation_token' => $token // For demo purposes
            ]
        ]);
    }
}