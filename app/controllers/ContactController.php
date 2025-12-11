<?php
// app/controllers/ContactController.php

require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Email.php';

class ContactController {

    public function submit() {
        header('Content-Type: application/json');

        // 1. Check Auth
        if (!isset($_SESSION['user'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit;
        }

        // 2. Verify CSRF
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            echo json_encode(['status' => 'error', 'message' => 'Security token expired. Please refresh the page.']);
            exit;
        }

        // 3. Sanitize Input
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        if (empty($subject) || empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Subject and message are required.']);
            exit;
        }

        // 4. Get Current User Info
        $userEmail = $_SESSION['user']['email'] ?? null;
        $userName  = $_SESSION['user']['full_name'] ?? 'User';

        if (empty($userEmail)) {
            echo json_encode(['status' => 'error', 'message' => 'You must have an email address linked to your account settings to contact support.']);
            exit;
        }

        // 5. Find System Admin Email
        $config = require __DIR__ . '/../../config/database.php';
        $db = new Database($config);
        $GLOBALS['db'] = $db; // for log_action

        try {
            // Get the email of the first System Admin found
            $stmt = $db->getPdo()->prepare("
                SELECT u.email 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE r.role_name = 'System Admin' 
                AND u.email IS NOT NULL AND u.email != '' 
                LIMIT 1
            ");
            $stmt->execute();
            $adminEmail = $stmt->fetchColumn();

            if (!$adminEmail) {
                // Fallback email if no admin has one configured in DB
                // Ideally set this in your .env as ADMIN_FALLBACK_EMAIL
                $adminEmail = $_ENV['ADMIN_FALLBACK_EMAIL'] ?? 'admin@example.com'; 
            }

            // 6. Send Email
            $emailService = new Email();
            $sent = $emailService->sendSupportMessage($adminEmail, $userEmail, $userName, $subject, $message);

            if ($sent) {
                log_action('INFO', 'SUPPORT_REQ_SENT', "User '{$userName}' sent a support request.");
                echo json_encode(['status' => 'success', 'message' => 'Your message has been sent to support!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Please try again later.']);
            }

        } catch (Exception $e) {
            log_action('ERROR', 'CONTACT_CTRL_ERR', $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'An internal error occurred.']);
        }
    }
}