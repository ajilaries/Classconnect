<?php
// Change this to your new superadmin password
$newPassword = "IAMTHEGODCC";

// Generate hash
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

// Show result
echo "Plain Password: $newPassword\n";
echo "Generated Hash: $hash\n";
?>
