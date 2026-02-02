<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table = 'projects';
    protected $primaryKey = 'id';
    protected $allowedFields = ['team_id', 'name', 'description', 'status', 'created_by'];
    protected $useTimestamps = true;

    public function getTeamProjects($teamId)
    {
        return $this->where('team_id', $teamId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getProjectWithTeam($projectId)
    {
        $db = db_connect();
        $builder = $db->table('projects');
        $builder->select('projects.*, teams.name as team_name');
        $builder->join('teams', 'teams.id = projects.team_id');
        $builder->where('projects.id', $projectId);
        return $builder->get()->getRowArray();
    }
}