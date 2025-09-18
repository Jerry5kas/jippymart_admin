<?php
echo "<h2>PHP ZipArchive Test</h2>";

echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

echo "<p><strong>ZipArchive Class Exists:</strong> " . (class_exists('ZipArchive') ? 'YES' : 'NO') . "</p>";

echo "<p><strong>Zip Extension Loaded:</strong> " . (extension_loaded('zip') ? 'YES' : 'NO') . "</p>";

echo "<p><strong>Loaded Extensions:</strong></p>";
$extensions = get_loaded_extensions();
sort($extensions);
echo "<ul>";
foreach ($extensions as $ext) {
    if (stripos($ext, 'zip') !== false) {
        echo "<li><strong style='color: green;'>$ext</strong></li>";
    } else {
        echo "<li>$ext</li>";
    }
}
echo "</ul>";

echo "<p><strong>PHP Configuration File:</strong> " . php_ini_loaded_file() . "</p>";

if (php_ini_scanned_files()) {
    echo "<p><strong>Additional INI Files:</strong> " . php_ini_scanned_files() . "</p>";
}
?>
