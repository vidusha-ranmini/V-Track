<?php
namespace App\Controllers;

use App\Models\HomeModel;
use App\Models\MemberModel;
use CodeIgniter\Controller;

class Details extends Controller
{
    public function create()
    {
        // Show the form
        return view('add_details');
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $homeModel = new HomeModel();
        $memberModel = new MemberModel();

        // Determine whether we're adding to an existing home or creating a new one
        $addToExisting = $this->request->getPost('add_to_existing') === '1' ? true : false;
        $existingHomeNumber = $this->request->getPost('existing_home_number_hidden');

        // Basic home data (for new home case)
        $data = [
            'home_number'    => $this->request->getPost('home_number'),
            'address'        => $this->request->getPost('address'),
            'no_of_members'  => $this->request->getPost('no_of_members'),
            'has_assessment' => $this->request->getPost('has_assessment') ?? 'no',
            'assessment_number' => $this->request->getPost('assessment_number') ?: null,
            'resident_type'  => $this->request->getPost('resident_type'),
            'waste_disposal' => $this->request->getPost('waste_disposal'),
        ];

        // If adding to an existing home, validate the provided existing home number and find the home
        if ($addToExisting) {
            if (empty($existingHomeNumber)) {
                return redirect()->back()->with('error', 'Existing house number is required when adding to an existing home.');
            }
            $existingHome = $homeModel->where('home_number', $existingHomeNumber)->first();
            if (!$existingHome) {
                return redirect()->back()->with('error', 'No home found with that house number.');
            }
            $homeId = $existingHome['id'];
        } else {
            // New home flow: require home_number and address
            if (empty($data['home_number']) || empty($data['address'])) {
                return redirect()->back()->with('error', 'Home number and address are required.');
            }
            // Use transaction to ensure atomicity
            $db->transStart();
            $homeId = $homeModel->insert($data);
            // we will continue the transaction below for members
        }

        // Members come from hidden members_json field
        $membersJson = $this->request->getPost('members_json');
        $members = [];
        if ($membersJson) {
            $decoded = json_decode($membersJson, true);
            if (is_array($decoded)) {
                $members = $decoded;
            }
        }

        // Gather uploaded CV files (if any). Expect files named cv_files[] matching members order.
        $cvFiles = [];
        try {
            $cvFiles = $this->request->getFileMultiple('cv_files');
        } catch (\Exception $e) {
            $cvFiles = [];
        }

        // Ensure uploads directory exists
        $uploadPath = WRITEPATH . 'uploads';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

    foreach ($members as $idx => $m) {
            $member = [
                'home_id' => $homeId,
                'full_name' => $m['full_name'] ?? null,
                'name_with_initial' => $m['name_with_initial'] ?? null,
                'member_type' => $m['member_type'] ?? null,
                // NIC is optional
                'nic' => isset($m['nic']) && $m['nic'] !== '' ? $m['nic'] : null,
                'gender' => $m['gender'] ?? null,
                'occupation' => $m['occupation'] ?? null,
                'occupation_other' => $m['occupation_other'] ?? null,
                'school' => $m['school'] ?? null,
                'grade' => $m['grade'] ?? null,
                'university_name' => $m['university_name'] ?? null,
                    // Do not store offers in the members table; the relation is stored in member_offers.
                    'age' => isset($m['age']) ? (int) $m['age'] : 0,
                'disabled' => $m['disabled'] ?? 'no',
                'land_house_status' => $m['land_house_status'] ?? null,
                'whatsapp' => $m['whatsapp'] ?? null,
                'cv' => null,
            ];

            // Handle uploaded CV for this member if provided
            if (isset($cvFiles[$idx]) && $cvFiles[$idx]->isValid()) {
                $cvFile = $cvFiles[$idx];
                // Generate a safe filename
                $newName = $cvFile->getRandomName();
                try {
                    $cvFile->move($uploadPath, $newName);
                    $member['cv'] = $newName;
                } catch (\Exception $e) {
                    // Could not move file; leave cv null and continue
                    log_message('error', 'Failed to move uploaded CV: ' . $e->getMessage());
                }
            }

            $newMemberId = $memberModel->insert($member);

            // Persist offers into member_offers table (normalized relation)
            if ($newMemberId && isset($m['offers']) && is_array($m['offers']) && count($m['offers']) > 0) {
                $offersTable = $db->table('member_offers');
                foreach ($m['offers'] as $offer) {
                    // sanitize/validate offer string a little
                    $offerValue = is_string($offer) ? $offer : (string) $offer;
                    try {
                        $offersTable->insert([
                            'member_id' => $newMemberId,
                            'offer' => $offerValue,
                        ]);
                    } catch (\Exception $e) {
                        // Log and continue - transaction rollback will handle failure overall
                        log_message('error', 'Failed to insert member_offer: ' . $e->getMessage());
                    }
                }
            }
        }

        // If we were adding to an existing home, update that home's member count (optional)
        if ($addToExisting) {
            try {
                $added = count($members);
                if ($added > 0) {
                    $homeModel->set('no_of_members', "no_of_members + $added", false)->where('id', $homeId)->update();
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to update existing home member count: ' . $e->getMessage());
            }
        } else {
            // complete the transaction started for new-home flow
            $db->transComplete();
            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Failed to save details.');
            }
        }

        return redirect()->to('/view-details')->with('success', 'Details saved successfully.');
    }
}
