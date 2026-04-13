<?php
session_start();
if (isset($_SESSION['UserID'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aplikasi Kasir Premium</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <div class="glass-panel login-box">
            <h1 style="text-align: center; margin-bottom: 2rem;">KasirPro</h1>
            
            <?php if (isset($_GET['pesan'])): ?>
                <?php if ($_GET['pesan'] == 'gagal'): ?>
                    <div class="alert alert-error">Username atau Password salah!</div>
                <?php elseif ($_GET['pesan'] == 'logout'): ?>
                    <div class="alert alert-success">Anda telah berhasil logout.</div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="auth.php" method="POST">
                <div class="form-group">
                    <input type="text" id="username" name="username" class="form-control" required placeholder=" ">
                    <label for="username" style="display:none;">Username</label>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Password">
                    <label for="password" style="display:none;">Password</label>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">Sign In</button>
            </form>
            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.875rem;">
                Administrator default: admin / admin123 <br>
                Petugas default: petugas / petugas123
            </p>
        </div>
    </div>
</body>
</html>
