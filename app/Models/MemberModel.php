<?php
namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'home_id',
        'full_name',
        'name_with_initial',
        'age',
        'member_type',
        'nic',
        'gender',
        'occupation',
        'occupation_other',
        'school',
        'grade',
        'university_name',
        'disabled',
        'land_house_status',
        'whatsapp',
        'cv',
    ];
    // The `members` table in this database does not include timestamp columns
    // (created_at/updated_at). Disable automatic timestamps to prevent
    // SQLite errors when inserting records.
    protected $useTimestamps = false;
    protected $returnType = 'array';
}
