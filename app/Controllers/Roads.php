<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Roads extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Get all roads
        try {
            $roadsArr = $db->table('roads')->select('id,name')->orderBy('name')->get()->getResultArray();
        } catch (\Exception $e) {
            $roadsArr = [];
        }
        
        // Get parent sub roads (where parent_sub_road_id IS NULL)
        try {
            $parentSubRoadsQuery = $db->table('sub_roads sr')
                ->select('sr.id, sr.name, sr.road_id, r.name as road_name')
                ->join('roads r', 'r.id = sr.road_id', 'left')
                ->where('sr.parent_sub_road_id', null)
                ->orderBy('r.name, sr.name')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            $parentSubRoadsQuery = [];
        }
        
        // Get child sub roads (sub-sub roads with development status)
        try {
            $childSubRoadsQuery = $db->table('sub_roads sr')
                ->select('sr.id, sr.name, sr.road_id, sr.parent_sub_road_id, sr.is_developed, r.name as road_name, psr.name as parent_sub_road_name')
                ->join('roads r', 'r.id = sr.road_id', 'left')
                ->join('sub_roads psr', 'psr.id = sr.parent_sub_road_id', 'left')
                ->where('sr.parent_sub_road_id IS NOT NULL')
                ->orderBy('r.name, psr.name, sr.name')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            $childSubRoadsQuery = [];
        }
        
        // Get all addresses
        try {
            $addressesQuery = $db->table('addresses a')
                ->select('a.id, a.address, a.road_id, a.sub_road_id, r.name as road_name, sr.name as sub_road_name')
                ->join('roads r', 'r.id = a.road_id', 'left')
                ->join('sub_roads sr', 'sr.id = a.sub_road_id', 'left')
                ->orderBy('r.name, sr.name, a.address')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            $addressesQuery = [];
        }
        
        return view('roads_details', [
            'roads' => $roadsArr,
            'parentSubRoads' => $parentSubRoadsQuery,
            'childSubRoads' => $childSubRoadsQuery,
            'addresses' => $addressesQuery
        ]);
    }
    
    public function toggleDevelopment($id)
    {
        $db = \Config\Database::connect();
        
        try {
            // Get current status
            $subRoad = $db->table('sub_roads')->where('id', $id)->get()->getRowArray();
            
            if ($subRoad) {
                // Toggle is_developed status
                $newStatus = $subRoad['is_developed'] == 1 ? 0 : 1;
                
                $db->table('sub_roads')
                    ->where('id', $id)
                    ->update(['is_developed' => $newStatus]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'is_developed' => $newStatus,
                    'message' => $newStatus == 1 ? 'Sub road marked as developed' : 'Sub road marked as undeveloped'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sub road not found'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Sub road toggle error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
