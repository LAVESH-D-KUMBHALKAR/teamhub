<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use App\Models\TaskModel;
use App\Models\ProjectModel;
use App\Models\TeamModel;
use App\Models\UserModel;

class TaskController extends BaseController
{
    use ResponseTrait;

    protected $taskModel;
    protected $projectModel;
    protected $teamModel;
    protected $userModel;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->projectModel = new ProjectModel();
        $this->teamModel = new TeamModel();
        $this->userModel = new UserModel();
    }

    public function index($projectId)
    {
        $project = $this->projectModel->find($projectId);
        
        if (!$project) {
            return $this->failNotFound('Project not found');
        }

        // Check if user is member of the team
        if (!$this->teamModel->isMember($project['team_id'], session()->get('user_id'))) {
            return $this->failUnauthorized('You are not a member of this team');
        }

        $tasks = $this->taskModel->getProjectTasks($projectId);

        return $this->respond([
            'status' => 'success',
            'data' => $tasks
        ]);
    }

    public function create()
    {
        $data = [
            'project_id' => $this->request->getVar('project_id'),
            'title' => $this->request->getVar('title'),
            'description' => $this->request->getVar('description'),
            'status' => $this->request->getVar('status') ?? 'todo',
            'assigned_to' => $this->request->getVar('assigned_to'),
            'due_date' => $this->request->getVar('due_date'),
            'priority' => $this->request->getVar('priority') ?? 'medium',
            'created_by' => session()->get('user_id')
        ];

        // Verify project exists and user has access
        $project = $this->projectModel->find($data['project_id']);
        if (!$project) {
            return $this->failNotFound('Project not found');
        }

        if (!$this->teamModel->isMember($project['team_id'], session()->get('user_id'))) {
            return $this->failUnauthorized('You are not a member of this team');
        }

        if (!$this->taskModel->save($data)) {
            return $this->fail($this->taskModel->errors());
        }

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'Task created successfully',
            'data' => ['id' => $this->taskModel->getInsertID()]
        ]);
    }

    public function updateStatus($taskId)
    {
        $status = $this->request->getVar('status');
        
        if (!in_array($status, ['todo', 'in_progress', 'done'])) {
            return $this->fail('Invalid status');
        }

        $task = $this->taskModel->find($taskId);
        if (!$task) {
            return $this->failNotFound('Task not found');
        }

        // Check access via project
        $project = $this->projectModel->find($task['project_id']);
        if (!$this->teamModel->isMember($project['team_id'], session()->get('user_id'))) {
            return $this->failUnauthorized('You are not a member of this team');
        }

        $this->taskModel->updateTaskStatus($taskId, $status);

        return $this->respond([
            'status' => 'success',
            'message' => 'Task status updated'
        ]);
    }

    public function reassign($taskId)
    {
        $userId = $this->request->getVar('user_id');
        
        $task = $this->taskModel->find($taskId);
        if (!$task) {
            return $this->failNotFound('Task not found');
        }

        // Check if new user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->failNotFound('User not found');
        }

        // Check access via project
        $project = $this->projectModel->find($task['project_id']);
        if (!$this->teamModel->isMember($project['team_id'], session()->get('user_id'))) {
            return $this->failUnauthorized('You are not a member of this team');
        }

        // Check if new user is member of the team
        if (!$this->teamModel->isMember($project['team_id'], $userId)) {
            return $this->fail('Cannot assign task to non-team member');
        }

        $this->taskModel->reassignTask($taskId, $userId);

        return $this->respond([
            'status' => 'success',
            'message' => 'Task reassigned successfully'
        ]);
    }

    public function update($taskId)
    {
        $task = $this->taskModel->find($taskId);
        if (!$task) {
            return $this->failNotFound('Task not found');
        }

        // Check access via project
        $project = $this->projectModel->find($task['project_id']);
        if (!$this->teamModel->isMember($project['team_id'], session()->get('user_id'))) {
            return $this->failUnauthorized('You are not a member of this team');
        }

        $data = $this->request->getJSON(true);
        
        if (!$this->taskModel->update($taskId, $data)) {
            return $this->fail($this->taskModel->errors());
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Task updated successfully'
        ]);
    }

    public function delete($taskId)
    {
        $task = $this->taskModel->find($taskId);
        if (!$task) {
            return $this->failNotFound('Task not found');
        }

        // Check access via project
        $project = $this->projectModel->find($task['project_id']);
        if (!$this->teamModel->isMember($project['team_id'], session()->get('user_id'))) {
            return $this->failUnauthorized('You are not a member of this team');
        }

        // Only creator or admin can delete
        if ($task['created_by'] != session()->get('user_id')) {
            return $this->failUnauthorized('Only the creator can delete this task');
        }

        $this->taskModel->delete($taskId);

        return $this->respond([
            'status' => 'success',
            'message' => 'Task deleted successfully'
        ]);
    }
}