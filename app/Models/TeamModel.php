<?php

namespace App\Models;

use CodeIgniter\Model;

class TeamModel extends Model
{
    protected $table = 'teams';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'created_by'];
    protected $useTimestamps = true;

    public function getMembers($teamId)
    {
        $db = db_connect();
        $builder = $db->table('team_members');
        $builder->select('users.id, users.name, users.email, team_members.role, team_members.joined_at');
        $builder->join('users', 'users.id = team_members.user_id');
        $builder->where('team_members.team_id', $teamId);
        return $builder->get()->getResultArray();
    }

    public function isMember($teamId, $userId)
    {
        $db = db_connect();
        $builder = $db->table('team_members');
        $builder->where('team_id', $teamId);
        $builder->where('user_id', $userId);
        return $builder->get()->getRow() !== null;
    }

    public function addMember($teamId, $userId, $role = 'member')
    {
        $db = db_connect();
        $data = [
            'team_id' => $teamId,
            'user_id' => $userId,
            'role' => $role,
            'joined_at' => date('Y-m-d H:i:s')
        ];
        $db->table('team_members')->insert($data);
        return $db->insertID();
    }
}