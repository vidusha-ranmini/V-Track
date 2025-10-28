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
        return view('view_details', ['detailsData' => json_encode($families)]);
    }
}
