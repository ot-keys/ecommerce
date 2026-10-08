<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
require_admin();

function admin_json_response(array $response, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verify_csrf();
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'upload_image') {
            $image = upload_image('image_upload');
            if ($image === null) {
                throw new RuntimeException('Choose an image to upload.');
            }
            admin_json_response(['image' => $image]);
        }

        if ($action !== 'save_page') {
            throw new RuntimeException('This action is not supported.');
        }

        $decoded = json_decode((string) ($_POST['site_data'] ?? ''), true);
        if (!is_array($decoded) || !isset($decoded['hero'], $decoded['containers']) || !is_array($decoded['hero']) || !is_array($decoded['containers'])) {
            throw new RuntimeException('The page draft is invalid. Check the editor and try again.');
        }

        foreach ($decoded['containers'] as $container) {
            if (!is_array($container) || !isset($container['id'], $container['title'], $container['subtitle']) || !is_array($container['products'] ?? null)) {
                throw new RuntimeException('A product section is incomplete.');
            }
            if (isset($container['placement']) && !in_array($container['placement'], ['categories', 'top-sellers', 'power-plans', 'for-you', 'featured'], true)) {
                throw new RuntimeException('A product section has an unsupported card group.');
            }
            foreach ($container['products'] as $product) {
                if (!is_array($product) || !isset($product['id'], $product['name'], $product['description'], $product['image']) || !is_numeric($product['price'] ?? null) || !is_numeric($product['discount'] ?? null)) {
                    throw new RuntimeException('A product is incomplete or has an invalid price.');
                }
            }
        }

        save_site_data($decoded);
        admin_json_response(['message' => 'Your changes are live on the storefront.']);
    } catch (Throwable $exception) {
        admin_json_response(['error' => $exception->getMessage()], 400);
    }
}

$data = site_data();
$safeData = json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
if ($safeData === false) {
    throw new RuntimeException('The current page content could not be loaded.');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page editor | Power Solutions</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body class="editor-page">
    <header class="topbar editor-topbar">
        <a class="brand" href="../index.html">POWER SOLUTIONS <span>EDITOR</span></a>
        <div class="topbar-actions">
            <a class="button ghost" href="../index.html" target="_blank" rel="noopener">Open live site ↗</a>
            <a class="button ghost" href="logout.php">Log out</a>
        </div>
    </header>

    <main class="editor-shell">
        <section class="editor-toolbar" aria-label="Page editing tools">
            <div>
                <span class="eyebrow">STOREFRONT BUILDER</span>
                <h1>Edit your homepage</h1>
            </div>
            <div class="toolbar-actions">
                <button class="button small" id="undoButton" type="button" title="Undo (Ctrl+Z)" disabled>↶ Undo</button>
                <button class="button small" id="redoButton" type="button" title="Redo (Ctrl+Y or Ctrl+Shift+Z)" disabled>↷ Redo</button>
                <button class="button primary" id="saveButton" type="button">Save and publish</button>
            </div>
        </section>
        <div class="editor-status" id="editorStatus" role="status" aria-live="polite">Changes are saved only when you select Save and publish.</div>
        <div class="editor-layout">
            <aside class="editor-sidebar structure-sidebar" aria-label="Page structure">
                <div class="sidebar-heading"><div><span class="eyebrow">STRUCTURE</span><h2>Homepage</h2></div><button class="icon-button" id="addSectionButton" type="button" title="Add a product section" aria-label="Add a product section">+</button></div>
                <button class="structure-item selected" type="button" data-kind="hero"><span class="structure-icon">✦</span><span><strong>Hero banner</strong><small>Headline, description, image</small></span></button>
                <div id="sectionList"></div>
                <p class="sidebar-hint">Drag sections or products to change their order. Click an item to edit it.</p>
            </aside>

            <section class="preview-workspace" aria-label="Live draft preview">
                <div class="preview-heading"><div><span class="eyebrow">LIVE DRAFT</span><h2>Preview</h2></div><button class="preview-device" id="previewModeButton" type="button">Desktop preview</button></div>
                <iframe id="pagePreview" title="Preview of your unsaved storefront draft"></iframe>
            </section>

            <aside class="editor-sidebar inspector-sidebar" aria-label="Selected item settings">
                <div class="sidebar-heading"><div><span class="eyebrow">SETTINGS</span><h2 id="inspectorTitle">Hero banner</h2></div></div>
                <div id="inspectorFields"></div>
            </aside>
        </div>
    </main>
    <div class="toast" id="editorToast" role="status" aria-live="polite"></div>

    <script>window.initialSiteData = <?= $safeData ?>; window.adminCsrfToken = <?= json_encode(csrf_token()) ?>;</script>
    <script src="admin.js" defer></script>
</body>
</html>
