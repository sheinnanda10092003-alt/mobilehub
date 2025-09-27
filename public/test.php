<?php
echo json_encode([
    'status' => 'working',
    'php_version' => PHP_VERSION,
    'timestamp' => date('Y-m-d H:i:s'),
    'message' => 'PHP is working on Render!'
]);
?>