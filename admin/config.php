<?php
declare(strict_types=1);

const SITE_DATA_FILE = __DIR__ . '/../data/site.json';
const UPLOAD_DIRECTORY = __DIR__ . '/../images/uploads';
const ADMIN_PASSWORD = 'admin123';

function site_data(): array
{
    if (!is_file(SITE_DATA_FILE)) {
        return ['hero' => [], 'containers' => []];
    }

    $contents = file_get_contents(SITE_DATA_FILE);
    $data = json_decode($contents ?: '', true);
    return is_array($data) ? $data : ['hero' => [], 'containers' => []];
}

function save_site_data(array $data): void
{
    require_once __DIR__ . '/../includes/db.php';
    require_once __DIR__ . '/../includes/product_table.php';
    sync_product_table($mysqli, $data);

    $directory = dirname(SITE_DATA_FILE);
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
        throw new RuntimeException('The data directory could not be created.');
    }

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false || file_put_contents(SITE_DATA_FILE, $json, LOCK_EX) === false) {
        throw new RuntimeException('The site data could not be saved.');
    }
}

function require_admin(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['admin_authenticated'])) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        throw new RuntimeException('Your form session expired. Please try again.');
    }
}

function slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    return trim($value ?: uniqid('item-'), '-');
}

function upload_image(string $field, ?string $existing = null): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return $existing;
    }
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The image upload failed.');
    }
    if ($_FILES[$field]['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Images must be 5MB or smaller.');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES[$field]['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) {
        throw new RuntimeException('Only JPG, PNG, and WEBP images are supported.');
    }

    if (!is_dir(UPLOAD_DIRECTORY) && !mkdir(UPLOAD_DIRECTORY, 0755, true)) {
        throw new RuntimeException('The upload directory could not be created.');
    }
    $filename = slug(pathinfo($_FILES[$field]['name'], PATHINFO_FILENAME)) . '-' . bin2hex(random_bytes(4)) . '.' . $extensions[$mime];
    $target = UPLOAD_DIRECTORY . '/' . $filename;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
        throw new RuntimeException('The uploaded image could not be stored.');
    }
    return 'images/uploads/' . $filename;
} 