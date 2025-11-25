-- Test Kullanıcıları İçin Şifre Hash Üretme
-- PHP ile çalıştır: php generate_hash.php

<?php
/**
 * Test kullanıcıları için bcrypt hash üretimi
 * Şifre: 123456
 */

$password = '123456';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "Şifre: {$password}\n";
echo "Hash: {$hash}\n\n";

echo "SQL Insert:\n";
echo "-- Personel kullanıcısı\n";
echo "INSERT INTO \"Users\" (\"Email\", \"Password\", \"full_name\", \"role\", \"is_approved\", \"created_at\")\n";
echo "VALUES ('personel@test.com', '{$hash}', 'Test Personel', 'personel', false, NOW());\n\n";

echo "-- Yönetici kullanıcısı (onaylı)\n";
echo "INSERT INTO \"Users\" (\"Email\", \"Password\", \"full_name\", \"role\", \"is_approved\", \"created_at\")\n";
echo "VALUES ('yonetici@test.com', '{$hash}', 'Test Yönetici', 'yonetici', true, NOW());\n\n";

echo "-- İkinci Yönetici (onaylı)\n";
echo "INSERT INTO \"Users\" (\"Email\", \"Password\", \"full_name\", \"role\", \"is_approved\", \"created_at\")\n";
echo "VALUES ('admin@dogu.com', '{$hash}', 'Sistem Yöneticisi', 'yonetici', true, NOW());\n";
?>
