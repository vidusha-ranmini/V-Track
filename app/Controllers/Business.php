<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Business extends Controller
{
    public function create()
    {
        // Load roads and sub_roads for dropdowns
        $db = \Config\Database::connect();
        
        try {
            $roadsArr = $db->table('roads')->select('id,name')->orderBy('name')->get()->getResultArray();
        } catch (\Exception $e) {
            $roadsArr = [];
        }
        
        try {
            $subroadsArr = $db->table('sub_roads')->select('id,road_id,name')->orderBy('name')->get()->getResultArray();
        } catch (\Exception $e) {
            $subroadsArr = [];
        }
        
        try {
            $addressesArr = $db->table('addresses')->select('id,road_id,sub_road_id,address')->orderBy('address')->get()->getResultArray();
        } catch (\Exception $e) {
            $addressesArr = [];
        }
        
        // Get existing businesses with location details
        try {
            $businessesQuery = $db->table('businesses b')
                ->select('b.id, b.business_name, b.business_owner, b.business_type, b.is_deleted, b.created_at, r.name as road_name, sr.name as sub_road_name, a.address as address_line')
                ->join('roads r', 'r.id = b.road_id', 'left')
                ->join('sub_roads sr', 'sr.id = b.sub_road_id', 'left')
                ->join('addresses a', 'a.id = b.address_id', 'left')
                ->orderBy('b.is_deleted', 'ASC')
                ->orderBy('b.created_at', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            $businessesQuery = [];
        }
        
        // Organize data for JavaScript
        $roadIdToName = [];
        foreach ($roadsArr as $r) {
            $roadIdToName[$r['id']] = $r['name'];
        }
        
        $subRoadMapByRoadId = [];
        foreach ($subroadsArr as $sr) {
            if (!isset($subRoadMapByRoadId[$sr['road_id']])) {
                $subRoadMapByRoadId[$sr['road_id']] = [];
            }
            $subRoadMapByRoadId[$sr['road_id']][] = $sr;
        }
        
        $addressMapBySubRoadId = [];
        foreach ($addressesArr as $addr) {
            if (!isset($addressMapBySubRoadId[$addr['sub_road_id']])) {
                $addressMapBySubRoadId[$addr['sub_road_id']] = [];
            }
            $addressMapBySubRoadId[$addr['sub_road_id']][] = $addr;
        }
        
        return view('add_business', [
            'roads' => $roadsArr,
            'subRoads' => $subRoadMapByRoadId,
            'addresses' => $addressMapBySubRoadId,
            'roadsJson' => json_encode($roadsArr),
            'subRoadsJson' => json_encode($subRoadMapByRoadId),
            'addressesJson' => json_encode($addressMapBySubRoadId),
            'businesses' => $businessesQuery
        ]);
    }
    
    public function store()
    {
        $db = \Config\Database::connect();
        
        // Validate required fields
        $businessName = $this->request->getPost('business_name');
        $businessOwner = $this->request->getPost('business_owner');
        $roadId = $this->request->getPost('road_id');
        $subRoadId = $this->request->getPost('sub_road_id');
        $addressId = $this->request->getPost('address_id');
        $businessType = $this->request->getPost('business_type');
        
        if (empty($businessName) || empty($businessOwner) || empty($businessType)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Business name, owner, and type are required.'
            ]);
        }
        
        try {
            $data = [
                'business_name' => $businessName,
                'business_owner' => $businessOwner,
                'road_id' => $roadId ?: null,
                'sub_road_id' => $subRoadId ?: null,
                'address_id' => $addressId ?: null,
                'business_type' => $businessType,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('businesses')->insert($data);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Business details added successfully!'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Business store error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to save business details: ' . $e->getMessage()
            ]);
        }
    }
    
    public function get($id)
    {
        $db = \Config\Database::connect();
        
        try {
            $business = $db->table('businesses')
                ->where('id', $id)
                ->get()
                ->getRowArray();
            
            if ($business) {
                return $this->response->setJSON([
                    'success' => true,
                    'business' => $business
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Business not found'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function update()
    {
        $db = \Config\Database::connect();
        
        $businessId = $this->request->getPost('business_id');
        $businessName = $this->request->getPost('business_name');
        $businessOwner = $this->request->getPost('business_owner');
        $roadId = $this->request->getPost('road_id');
        $subRoadId = $this->request->getPost('sub_road_id');
        $addressId = $this->request->getPost('address_id');
        $businessType = $this->request->getPost('business_type');
        
        if (empty($businessId) || empty($businessName) || empty($businessOwner) || empty($businessType)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Business ID, name, owner, and type are required.'
            ]);
        }
        
        try {
            $data = [
                'business_name' => $businessName,
                'business_owner' => $businessOwner,
                'road_id' => $roadId ?: null,
                'sub_road_id' => $subRoadId ?: null,
                'address_id' => $addressId ?: null,
                'business_type' => $businessType,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $db->table('businesses')->where('id', $businessId)->update($data);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Business details updated successfully!'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Business update error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update business details: ' . $e->getMessage()
            ]);
        }
    }
    
    public function delete($id)
    {
        $db = \Config\Database::connect();
        
        try {
            // Soft delete - set is_deleted to 1
            $db->table('businesses')
                ->where('id', $id)
                ->update(['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Business marked as deleted'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Business delete error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete business: ' . $e->getMessage()
            ]);
        }
    }
}
