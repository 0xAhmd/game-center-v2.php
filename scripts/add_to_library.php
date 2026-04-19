<?php
// scripts/add_to_library.php
// Games are added to the library automatically when an admin marks an order as "completed".
// Direct calls to this endpoint are intentionally blocked.
header('Content-Type: application/json');
http_response_code(403);
echo json_encode(['error' => 'Games are added to your library automatically after your order is marked as completed.']);
