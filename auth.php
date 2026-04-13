<?php
session_start();
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE Username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['Password'])) {
        // Login berhasil
        $_SESSION['UserID'] = $user['UserID'];
        $_SESSION['Username'] = $user['Username'];
        $_SESSION['Role'] = $user['Role'];
        
        header("Location: dashboard.php");
        exit();
    } else {
        // Login gagal
        header("Location: index.php?pesan=gagal");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
