<?php
namespace App\Models;

use CodeIgniter\Model;

class HomeModel extends Model
{
    protected $table = 'homes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'home_number',
        'address',
        'road_id',
        'sub_road_id',
        'address_id',
        'no_of_members',
        'has_assessment',
        'assessment_number',
        'resident_type',
        'waste_disposal',
    ];
    // The `homes` table does not have `created_at`/`updated_at` columns,
    // so disable automatic timestamps to avoid SQLite errors on insert.
    protected $useTimestamps = false;
    protected $returnType = 'array';
}
