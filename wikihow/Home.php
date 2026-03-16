<?php
/* --- Simple One‑File PHP Wiki --- */
/* Requires a writable "pages" folder */

$dir = __DIR__ . "/pages";
if (!is_dir($dir)) mkdir($dir, 0777, true);

$page = $_GET['page'] ?? 'Home';
$slug = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $page);
$file = "$dir/$slug.txt";

/* Save page */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';
    file_put_contents($file, $content);
    header("Location: ?page=" . urlencode($page));
    exit;
}

/* Load content */
$content = file_exists($file) ? file_get_contents($file) : '';

/* List pages */
function list_pages($dir) {
    $out = "<ul>";
    foreach (scandir($dir) as $f) {
        if (str_ends_with($f, ".txt")) {
            $name = basename($f, ".txt");
            $out .= "<li><a href='?page=$name'>" . htmlspecialchars($name) . "</a></li>";
        }
    }
    return $out . "</ul>";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($page) ?> - Mini Wiki</title>
<style>
body { font-family: Arial; margin:40px; max-width:800px; }
textarea { width:100%; height:300px; }
a.button, button { padding:6px 12px; background:#007bff; color:white; text-decoration:none; border:none; border-radius:4px; }
a.button { display:inline-block; }
</style>
</head>
<body>

<h1><?= htmlspecialchars($page) ?></h1>

<?php if (isset($_GET['edit'])): ?>

    <form method="post">
        <textarea name="content"><?= htmlspecialchars($content) ?></textarea><br><br>
        <button type="submit">Save</button>
        <a class="button" href="?page=<?= urlencode($page) ?>">Cancel</a>
    </form>

<?php else: ?>

    <pre><?= htmlspecialchars($content ?: "This page does not exist yet.") ?></pre>
    <br>
    <a class="button" href="?page=<?= urlencode($page) ?>&edit=1">Edit</a>

    <h2>All Pages</h2>
    <?= list_pages($dir) ?>

    <h3>Create a new page</h3>
    <form method="get">
        <input type="text" name="page" placeholder="Page name">
        <button type="submit">Go</button>
    </form>

<?php endif; ?>

</body>
</html>
