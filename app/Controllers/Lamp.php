<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Lamp extends Controller
{
    public function index()
    {
        // Load roads and lamps for display and filters
        $db = \Config\Database::connect();
        
        try {
            $roadsArr = $db->table('roads')->select('id,name')->orderBy('name')->get()->getResultArray();
        } catch (\Exception $e) {
            $roadsArr = [];
        }
        
        // Get all lamps with location details
        try {
            $lampsQuery = $db->table('lamps l')
                ->select('l.id, l.lamp_number, l.is_broken, r.name as road_name, sr.name as sub_road_name, a.address as address_line, l.road_id, l.sub_road_id, l.address_id')
                ->join('roads r', 'r.id = l.road_id', 'left')
                ->join('sub_roads sr', 'sr.id = l.sub_road_id', 'left')
                ->join('addresses a', 'a.id = l.address_id', 'left')
                ->orderBy('l.lamp_number', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            $lampsQuery = [];
        }
        
        return view('road_lamps', [
            'roads' => $roadsArr,
            'lamps' => $lampsQuery
        ]);
    }
    
    public function toggleStatus($id)
    {
        $db = \Config\Database::connect();
        
        try {
            // Get current status
            $lamp = $db->table('lamps')->where('id', $id)->get()->getRowArray();
            
            if ($lamp) {
                // Toggle is_broken status
                $newStatus = $lamp['is_broken'] == 1 ? 0 : 1;
                
                $db->table('lamps')
                    ->where('id', $id)
                    ->update([
                        'is_broken' => $newStatus,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                
                return $this->response->setJSON([
                    'success' => true,
                    'is_broken' => $newStatus,
                    'message' => $newStatus == 1 ? 'Lamp marked as broken' : 'Lamp marked as working'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Lamp not found'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Lamp toggle error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
