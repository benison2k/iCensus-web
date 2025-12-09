<?php
// app/controllers/ResidentController.php

require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../models/Residents.php';

class ResidentController {
    
    // Helper function to initialize DB connection quickly
    private function getDb() {
        $config = require __DIR__ . '/../../config/database.php';
        $db = new Database($config); 
        $GLOBALS['db'] = $db; 
        return $db;
    }
    
    public function index() {
        if (!isset($_SESSION['user'])) { header('Location: /login'); exit; }

        $user_role = $_SESSION['user']['role_name'] ?? '';
        if (!in_array($user_role, ['Barangay Admin', 'Encoder'])) {
             http_response_code(403);
             die("<h1>403 Forbidden</h1>");
        }

        $db = $this->getDb(); 
        $auth = new Auth($db);
        $auth->refreshUserSession($_SESSION['user']['id']);

        $residentModel = new Resident($db);
        
        $viewMode = $_GET['view'] ?? 'approved';
        $isPendingView = ($user_role === 'Barangay Admin' && $viewMode === 'pending');

        $data_lists = [
            'household_heads' => $residentModel->getHouseholdHeads(),
            'civil_statuses' => $residentModel->getDistinctValues('civil_status'),
            'blood_types' => $residentModel->getDistinctValues('blood_type'),
            'nationalities' => $residentModel->getDistinctValues('nationality'),
            'residency_statuses' => $residentModel->getDistinctValues('residency_status'),
            'relationships' => $residentModel->getDistinctValues('relationship'),
            'educations' => $residentModel->getDistinctValues('educational_attainment'),
            'occupations' => $residentModel->getDistinctValues('occupation'),
            'ownership_statuses' => $residentModel->getDistinctValues('ownership_status'),
        ];
        
        $pending_count = ($user_role === 'Barangay Admin') ? $residentModel->getPendingCount() : 0;
        
        if ($isPendingView) {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $pageSize = isset($_GET['pageSize']) ? (int)$_GET['pageSize'] : 10;
            $pendingData = $residentModel->getPendingPaginated($page, $pageSize);
            $residents = $pendingData['residents'];
            $totalResidents = $pendingData['total'];
            $totalPages = $pendingData['totalPages'];

            $data = array_merge($data_lists, [
                'residents' => $residents,
                'totalResidents' => $totalResidents,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'pageSize' => $pageSize
            ]);
        } else {
            $residents = $residentModel->getAll();
            $data = array_merge($data_lists, [
                'residents' => $residents,
                'totalResidents' => count($residents), 
                'totalPages' => 1, 
                'currentPage' => 1,
                'pageSize' => 10,
            ]);
        }
        
        $data = array_merge($data, [
            'user' => $_SESSION['user'],
            'theme' => $_SESSION['user']['theme'] ?? 'light',
            'isPendingView' => $isPendingView,
            'pending_count' => $pending_count,
            'modalMessage' => $_SESSION['modal']['message'] ?? '',
            'modalType' => $_SESSION['modal']['type'] ?? ''
        ]);
        
        unset($_SESSION['modal']);

        view('residents/index', $data);
    }
    
    public function findByAddress() {
        header('Content-Type: application/json');
        $house_no = $_GET['house_no'] ?? '';
        $street = $_GET['street'] ?? '';
        $purok = $_GET['purok'] ?? '';

        if (empty($house_no) || empty($street) || empty($purok)) {
            echo json_encode([]);
            exit;
        }

        $db = $this->getDb();
        $residentModel = new Resident($db);

        $residents = $residentModel->findByAddress($house_no, $street, $purok);
        echo json_encode($residents);
        exit;
    }

    public function process() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        $db = $this->getDb();
        $residentModel = new Resident($db);
        
        $action = $_REQUEST['action'] ?? 'save';

        try {
            switch ($action) {
                
                case 'check_duplicate':
                    $fname = trim($_POST['first_name'] ?? '');
                    $lname = trim($_POST['last_name'] ?? '');
                    $dob   = $_POST['dob'] ?? '';
                    $id    = $_POST['resident_id'] ?? null;

                    if ($fname && $lname && $dob) {
                        $duplicate = $residentModel->findDuplicate($fname, $lname, $dob, $id);
                        if ($duplicate) {
                            echo json_encode(['status' => 'found', 'resident' => $duplicate]);
                        } else {
                            echo json_encode(['status' => 'ok']);
                        }
                    } else {
                        echo json_encode(['status' => 'ok']); 
                    }
                    break;
                
                case 'save':
                    // CSRF Check
                    if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
                        echo json_encode(['status' => 'error', 'message' => 'Security Token Invalid. Please reload.']);
                        exit;
                    }

                    // Server-Side Validation
                    $validationError = $this->validateResidentData($_POST);
                    if ($validationError) {
                        echo json_encode(['status' => 'error', 'message' => $validationError]);
                        exit;
                    }

                    // Sanitize inputs
                    $_POST = array_map(function($value) {
                        return is_string($value) ? trim($value) : $value;
                    }, $_POST);

                    $resident_id_post = $_POST['resident_id'] ?? null;
                    $is_new = empty($resident_id_post);
                    
                    if (!$is_new) {
                        $resident_id_post = (int)$resident_id_post;
                        $old_data = method_exists($residentModel, 'findAnyStatus') ? $residentModel->findAnyStatus($resident_id_post) : $residentModel->find($resident_id_post);
                        $_POST['resident_id'] = $resident_id_post; 
                    }
                    
                    if ($is_new) {
                        $_POST['encoded_by'] = $_SESSION['user']['id'];
                    }
                    
                    unset($_POST['csrf_token']);

                    $residentId = $residentModel->save($_POST);
                    $full_name = htmlspecialchars($_POST['first_name'] . ' ' . $_POST['last_name']);
                    $message = $is_new ? 'New resident added successfully!' : 'Resident updated successfully!';
                    
                    if ($is_new) {
                        log_action('INFO', 'RESIDENT_CREATE', "New resident record created: {$full_name} (ID#{$residentId}).");
                    } else {
                        $new_data = method_exists($residentModel, 'findAnyStatus') ? $residentModel->findAnyStatus($residentId) : $residentModel->find($residentId);
                        
                        $safe_old_data = is_array($old_data) ? $old_data : [];
                        $safe_new_data = is_array($new_data) ? $new_data : [];

                        $changes = array_diff_assoc($safe_new_data, $safe_old_data);
                        $log_details = "Updated resident ID#{$residentId}.";
                        if (!empty($changes)) {
                            $log_details .= " Changes: ";
                            foreach($changes as $key => $value) {
                                $old_value = isset($safe_old_data[$key]) ? $safe_old_data[$key] : 'N/A (Data Not Found)';
                                $log_details .= "{$key} changed from '{$old_value}' to '{$value}', ";
                            }
                            $log_details = rtrim($log_details, ', ');
                            $log_details .= ".";
                        }
                        
                        $MAX_LOG_LENGTH = 500; 
                        $final_log_details = (strlen($log_details) > $MAX_LOG_LENGTH) ? substr($log_details, 0, $MAX_LOG_LENGTH - 3) . '...' : $log_details;

                        log_action('INFO', 'RESIDENT_UPDATE', $final_log_details);
                    }
                    
                    $savedResident = method_exists($residentModel, 'findAnyStatus') ? $residentModel->findAnyStatus($residentId) : $residentModel->find($residentId);

                    echo json_encode(['status' => 'success', 'message' => $message, 'resident' => $savedResident, 'is_new' => $is_new]);
                    break;

                case 'get':
                    $resident_id = $_REQUEST['id'] ?? $_REQUEST['resident_id'] ?? null;
                    if ($resident_id) {
                         $resident = method_exists($residentModel, 'findAnyStatus') ? $residentModel->findAnyStatus((int)$resident_id) : $residentModel->find((int)$resident_id); 
                         if ($resident) {
                             echo json_encode(['status' => 'success', 'resident' => $resident]);
                         } else {
                             http_response_code(404);
                             echo json_encode(['status' => 'error', 'message' => 'Resident not found.']);
                         }
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Resident ID is missing.']);
                    }
                    break;
                    
                case 'delete':
                    if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
                        echo json_encode(['status' => 'error', 'message' => 'Security Token Invalid. Please reload.']);
                        exit;
                    }
                    if ($_SESSION['user']['role_name'] === 'Encoder') {
                        echo json_encode(['status' => 'error', 'message' => 'You do not have permission to delete residents.']);
                        exit;
                    }
                    $id_to_delete = (int)($_POST['id'] ?? 0);
                    $resident_to_delete = $residentModel->find($id_to_delete);
                    if($resident_to_delete) {
                        $residentModel->delete($id_to_delete);
                        $full_name = htmlspecialchars($resident_to_delete['first_name'] . ' ' . $resident_to_delete['last_name']);
                        log_action('INFO', 'RESIDENT_DELETE', "Resident record for {$full_name} (ID#{$id_to_delete}) was deleted.");
                        echo json_encode(['status' => 'success', 'message' => 'Resident deleted successfully.']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Resident not found or already deleted.']);
                    }
                    break;

                default:
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
                    break;
            }
        } catch (Exception $e) {
            log_action('ERROR', 'DB_ERROR', 'Error in ResidentController process: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'An internal error occurred: ' . $e->getMessage()]);
        }
        exit;
    }

    private function validateResidentData($data) {
        $allowedGender = ['Male', 'Female'];
        if (!in_array($data['gender'], $allowedGender)) {
            return "Invalid gender selected.";
        }

        if (!empty($data['civil_status'])) {
            $allowedCivil = ['Single', 'Married', 'Widowed', 'Separated'];
            if (!in_array($data['civil_status'], $allowedCivil)) {
                return "Invalid civil status selected.";
            }
        }

        $dob = new DateTime($data['dob']);
        $now = new DateTime();
        if ($dob > $now) {
            return "Date of birth cannot be in the future.";
        }

        if (!empty($data['contact_number'])) {
            if (!preg_match('/^09\d{9}$/', $data['contact_number'])) {
                return "Contact number must match the format 09XXXXXXXXX.";
            }
        }

        return null;
    }

    public function approve() {
        if ($_SESSION['user']['role_name'] !== 'Barangay Admin') { die("Forbidden"); }
        
        $db = $this->getDb();
        $residentModel = new Resident($db);

        $residentId = (int)($_GET['id'] ?? 0);
        
        if ($residentId) {
            $residentModel->approve($residentId, $_SESSION['user']['id']);
            log_action('INFO', 'RESIDENT_APPROVED', "Admin approved resident entry ID#{$residentId}.");
            $_SESSION['modal'] = ['message' => 'Resident approved successfully.', 'type' => 'success'];
        }
        
        header("Location: /residents?view=pending");
        exit;
    }
    
    public function reject() {
        if ($_SESSION['user']['role_name'] !== 'Barangay Admin') { die("Forbidden"); }

        $db = $this->getDb();
        $residentModel = new Resident($db);
        
        $residentId = (int)($_GET['id'] ?? 0);
        
        if ($residentId) {
            $residentModel->reject($residentId);
            log_action('INFO', 'RESIDENT_REJECTED', "Admin rejected pending resident entry ID#{$residentId}.");
            $_SESSION['modal'] = ['message' => 'Resident entry rejected.', 'type' => 'success'];
        }

        header("Location: /residents?view=pending");
        exit;
    }

    public function approveAll() {
        if ($_SESSION['user']['role_name'] !== 'Barangay Admin') { die("Forbidden"); }
        
        $db = $this->getDb();
        $residentModel = new Resident($db);

        $approvedCount = $residentModel->approveAll($_SESSION['user']['id']);
        
        if ($approvedCount > 0) {
            log_action('INFO', 'RESIDENT_APPROVE_ALL', "Admin approved all {$approvedCount} pending resident entries.");
            $_SESSION['modal'] = ['message' => "Successfully approved all {$approvedCount} residents.", 'type' => 'success'];
        } else {
            $_SESSION['modal'] = ['message' => "No pending residents to approve.", 'type' => 'info'];
        }
        
        header("Location: /residents?view=pending");
        exit;
    }
    
    public function searchHeads() {
        header('Content-Type: application/json');
        $term = $_GET['term'] ?? '';
    
        $db = $this->getDb();
        $residentModel = new Resident($db);
    
        $heads = $residentModel->searchHeads($term);
        echo json_encode($heads);
        exit;
    }
}