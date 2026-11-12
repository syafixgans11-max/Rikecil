<?php
/**
 * Authentication & Helper Functions
 */

if (!ob_get_level()) {
    ob_start();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if admin is logged in
 */
function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require login or redirect
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Get current admin data from session or database
 */
function get_current_admin() {
    return [
        'id' => $_SESSION['admin_id'] ?? null,
        'username' => $_SESSION['admin_username'] ?? 'admin',
        'name' => $_SESSION['admin_name'] ?? 'Administrator',
    ];
}

function get_logged_in_admin($pdo) {
    $adminId = $_SESSION['admin_id'] ?? null;
    if ($adminId) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$adminId]);
        $row = $stmt->fetch();
        if ($row) return $row;
    }
    // Fallback if not found by ID
    $adminUser = $_SESSION['admin_username'] ?? 'admin';
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$adminUser]);
    $row = $stmt->fetch();
    if ($row) return $row;

    // Fallback first admin
    $stmt = $pdo->query("SELECT * FROM admins ORDER BY id ASC LIMIT 1");
    return $stmt->fetch() ?: [
        'id' => 1,
        'username' => 'admin',
        'name' => 'Administrator',
        'password' => ''
    ];
}

/**
 * Flash messaging system
 */
function set_flash($type, $message) {
    $_SESSION['flash_msg'] = [
        'type' => $type, // 'success', 'danger', 'info'
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash_msg'])) {
        $flash = $_SESSION['flash_msg'];
        unset($_SESSION['flash_msg']);
        return $flash;
    }
    return null;
}

/**
 * Handle image upload with validation
 */
function handle_image_upload($file_input_name, $destination_dir = 'uploads/') {
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $file = $_FILES[$file_input_name];
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'ico'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_extensions)) {
        return false;
    }

    // Target directory relative to project root
    $project_root = dirname(__DIR__);
    $target_dir = $project_root . '/' . ltrim($destination_dir, '/');
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $new_filename = 'img_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
    $target_path = $target_dir . '/' . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ltrim($destination_dir, '/') . $new_filename;
    }

    return false;
}
