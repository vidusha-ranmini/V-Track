<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\MemberModel;

class MemberController extends Controller
{
    public function update()
    {
        $request = service('request');
        $id = $request->getPost('id');
        if (!$id) {
            return $this->response->setStatusCode(400, 'Missing member id');
        }

        $memberModel = new MemberModel();

        $data = [
            'full_name' => $request->getPost('full_name'),
            'name_with_initial' => $request->getPost('name_with_initial'),
            'member_type' => $request->getPost('member_type'),
            'nic' => $request->getPost('nic'),
            'gender' => $request->getPost('gender'),
            'occupation' => $request->getPost('occupation'),
            'occupation_other' => $request->getPost('occupation_other'),
            'school' => $request->getPost('school'),
            'grade' => $request->getPost('grade'),
            'university_name' => $request->getPost('university_name'),
            'disabled' => $request->getPost('disabled') ?? 'no',
            'land_house_status' => $request->getPost('land_house_status'),
            'whatsapp' => $request->getPost('whatsapp'),
            'age' => $request->getPost('age') ?: 0,
            // cv will be set below if a new file is uploaded; keep posted cv filename (existing file) otherwise
            'cv' => $request->getPost('cv') ?: null,
        ];

        try {
            // Handle uploaded CV replacement if a file was submitted
            $cvFile = $request->getFile('cv_file');
            if ($cvFile && $cvFile->isValid() && !$cvFile->hasMoved()) {
                // Basic validation: accept PDF and image mime types
                $mime = $cvFile->getClientMimeType();
                $allowed = [
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'image/webp'
                ];
                if (!in_array($mime, $allowed, true)) {
                    return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid file type for CV.']);
                }

                // Ensure uploads directory exists
                $uploadPath = WRITEPATH . 'uploads';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Move the uploaded file with a random name
                $newName = $cvFile->getRandomName();
                try {
                    $cvFile->move($uploadPath, $newName);
                    // Delete previous file if provided and exists
                    $old = $request->getPost('cv');
                    if ($old) {
                        $oldPath = $uploadPath . DIRECTORY_SEPARATOR . $old;
                        if (is_file($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                    $data['cv'] = $newName;
                } catch (\Exception $e) {
                    log_message('error', 'Failed to move uploaded CV: ' . $e->getMessage());
                    return $this->response->setStatusCode(500)->setJSON(['error' => 'Failed to save uploaded CV.']);
                }
            }

            $memberModel->update($id, $data);

            // Update offers: replace existing member_offers for this member
            $db = \Config\Database::connect();
            $offersTable = $db->table('member_offers');
            $offersTable->where('member_id', $id)->delete();
            $offers = $request->getPost('offers');
            if ($offers) {
                // offers may be comma-separated or array
                if (!is_array($offers)) {
                    $offers = explode(',', $offers);
                }
                foreach ($offers as $off) {
                    $o = trim($off);
                    if ($o === '') continue;
                    $offersTable->insert(['member_id' => $id, 'offer' => $o]);
                }
            }

            return $this->response->setJSON(['status' => 'ok']);
        } catch (\Exception $e) {
            log_message('error', 'Member update failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500, 'Update failed');
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            return $this->response->setStatusCode(400, 'Missing member id');
        }

        $memberModel = new MemberModel();
        try {
            $memberModel->delete($id);
            return $this->response->setJSON(['status' => 'ok']);
        } catch (\Exception $e) {
            log_message('error', 'Member delete failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500, 'Delete failed');
        }
    }
}
