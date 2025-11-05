<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class Pages extends Controller
{
    public function landing()
    {
        return view('landing');
    }
    public function login()
    {
        return view('login');
    }
    public function sidebar()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        return view('sidebar');
    }
    public function add_details()
    {
        return view('add_details');
    }
    public function dashboard()
    {
        // Provide aggregated data for dashboard charts
        $model = new \App\Models\DashboardModel();
        $job = $model->getOccupationCounts();
        $age = $model->getAgeBuckets();
        $resident = $model->getResidentCounts();
        $waste = $model->getWasteCounts();
        $offers = $model->getOfferCounts();
        $disabled = $model->getDisabledCounts();

        return view('dashboard', [
            'jobData' => json_encode($job),
            'ageData' => json_encode($age),
            'residentData' => json_encode($resident),
            'wasteData' => json_encode($waste),
            'offerData' => json_encode($offers),
            'disabledData' => json_encode($disabled),
        ]);
    }

    public function generateReport()
    {
        // Build a CSV report of homes and members (one row per member)
        $model = new \App\Models\ViewDetailsModel();
        $families = $model->getAllFamilies();

        // Prepare CSV
        $filename = 'vtrack_report_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

    $columns = ['Location','Address','Resident Type','Member Name','NIC','Age','Occupation','Offers','WhatsApp','Disabled','CV Filename'];

        // Build CSV in memory and return as a standard response. Using a streamed response
        // helper caused issues on some environments, so assemble and return the full CSV.
        $fh = fopen('php://temp', 'r+');
        // BOM for UTF-8
        fwrite($fh, "\xEF\xBB\xBF");
        fputcsv($fh, $columns);
        foreach ($families as $fam) {
            $house = $fam['location'] ?? '';
            $address = $fam['address'] ?? '';
            $resident = $fam['resident_type'] ?? '';
            foreach ($fam['members'] as $m) {
                $offers = is_array($m['offers']) ? implode('|', $m['offers']) : '';
                $row = [$house, $address, $resident, $m['name'] ?? '', $m['nic'] ?? '', $m['age'] ?? '', $m['occupation'] ?? '', $offers, $m['whatsapp'] ?? '', $m['disabled'] ?? '', $m['cv'] ?? ''];
                fputcsv($fh, $row);
            }
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        // Return response with appropriate headers
        $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setBody($csv);
        return $this->response;
    }
    public function view_details()
    {
        // Ensure this route always uses the ViewDetails controller which prepares
        // the DB-backed road/sub-road data. Redirect here to avoid rendering the
        // view without the required JS variables when Pages::view_details is
        // invoked directly.
        return redirect()->to('/view-details');
    }
}
