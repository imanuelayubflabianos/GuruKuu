<?php
// Sesuaikan path target (folder storage/app/public projek Laravel kamu)
$targetFolder = __DIR__ . '/../storage/app/public'; 

// Folder link yang akan dibuat di public_html
$linkFolder = __DIR__ . '/storage';

if (symlink($targetFolder, $linkFolder)) {
    echo 'Symlink berhasil dibuat!';
} else {
    echo 'Gagal membuat symlink, periksa permission atau path.';
}
