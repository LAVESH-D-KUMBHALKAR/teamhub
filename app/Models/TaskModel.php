<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $allowedFields = ['project_id', 'title', 'description', 'status', 'assigned_to', 'due_date', 'priority', 'created_by'];
    protected $useTimestamps = true;

    public function getProjectTasks($projectId)
    {
        $db = db_connect();
        $builder = $db->table('tasks');
        $builder->select('tasks.*, users.name as assigned_name, creator.name as creator_name');
        $builder->join('users as users', 'users.id = tasks.assigned_to', 'left');
        $builder->join('users as creator', 'creator.id = tasks.created_by');
        $builder->where('tasks.project_id', $projectId);
        $builder->orderBy('tasks.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function updateTaskStatus($taskId, $status)
    {
        return $this->update($taskId, ['status' => $status]);
    }

    public function reassignTask($taskId, $userId)
    {
        return $this->update($taskId, ['assigned_to' => $userId]);
    }
}