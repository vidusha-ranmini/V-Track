<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ViewDetailsModel;

class ViewDetails extends Controller
{
    public function index()
    {
        $model = new ViewDetailsModel();
        $families = $model->getAllFamilies();
        // Prepare JSON for the view
        $json = json_encode($families, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        // Also load roads and sub_roads from DB so the view can populate filters from persisted lists
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

        $roadNames = [];
        $roadIdToName = [];
        foreach ($roadsArr as $r) { $roadIdToName[$r['id']] = $r['name']; $roadNames[] = $r['name']; }
        $subRoadMapByRoadName = [];
        foreach ($subroadsArr as $sr) {
            $rn = isset($roadIdToName[$sr['road_id']]) ? $roadIdToName[$sr['road_id']] : null;
            if ($rn) {
                if (!isset($subRoadMapByRoadName[$rn])) $subRoadMapByRoadName[$rn] = [];
                $subRoadMapByRoadName[$rn][] = $sr['name'];
            }
        }

        $roadsJson = json_encode(array_values(array_unique($roadNames)), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $subRoadMapJson = json_encode($subRoadMapByRoadName, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        // Count members for a quick server-side check
        $serverMembersCount = 0;
        foreach ($families as $f) { $serverMembersCount += count($f['members']); }

        return view('view_details', ['detailsData' => $json, 'roadsJson' => $roadsJson, 'subRoadMapJson' => $subRoadMapJson, 'serverMembersCount' => $serverMembersCount]);
    }
}
