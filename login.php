<?php
session_start();

if (isset($_SESSION['user_login'])) {
    header("Location: index.php");
    exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Akun default (Dapat disesuaikan)
    if ($username === 'lisya' && $password === '123456') {
        $_SESSION['user_login'] = true;
        $_SESSION['nama'] = 'Lisya Ayu Mutiara Ramadhani';
        $_SESSION['nim'] = '240511108';
        $_SESSION['kelas'] = 'TI24D';
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau password salah! Gunakan: lisya / 123456";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal Tugas Akademik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #FFF0F5; font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255, 182, 193, 0.5); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="glass w-full max-w-md p-8 rounded-3xl shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 bg-pink-500 text-white rounded-2xl flex items-center justify-center mx-auto text-xl shadow-lg">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Masuk ke Portal</h2>
            <p class="text-xs text-pink-600 font-medium">Sistem Pengumpulan Tugas Akademik</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="p-3 bg-rose-100 text-rose-700 text-xs rounded-xl font-semibold text-center border border-rose-200">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required class="w-full bg-white border border-pink-200 rounded-xl px-4 py-2.5 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-pink-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required class="w-full bg-white border border-pink-200 rounded-xl px-4 py-2.5 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-pink-400">
            </div>
            <button type="submit" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 rounded-xl text-xs uppercase tracking-wider shadow-md transition-all">
                <i class="fa-solid fa-right-to-bracket mr-1"></i> Masuk Sekarang
            </button>
        </form>

        <div class="text-center pt-2">
            <span class="text-[11px] text-gray-500">Default Login: <strong class="text-pink-600">lisya</strong> / Password: <strong class="text-pink-600">123456</strong></span>
        </div>
    </div>
</body>
</html>