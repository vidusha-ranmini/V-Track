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

    // Pass JSON-encoded data for the view to consume in JS
    // Use JSON_HEX_* flags to reduce XSS risk when embedding into a script
    $json = json_encode($families, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    return view('view_details', ['detailsData' => $json]);
    }
}
