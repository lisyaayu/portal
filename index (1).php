<?php
session_start();

if (!isset($_SESSION['user_login'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aesthetic Academic Portal - Lisya Ayu</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        pastel: {
                            rose: '#FFE4E8',
                            blush: '#FFC0CB',
                            pink: '#FFB6C1',
                            hot: '#FF69B4',
                            deep: '#E05297',
                            dark: '#2A1B24',
                            cardDark: '#362230',
                            textDark: '#FCE7F3'
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        poppins: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-primary: #FFF0F5;
            --card-bg: rgba(255, 255, 255, 0.85);
            --border-color: rgba(255, 182, 193, 0.45);
            --text-main: #4A3E4E;
        }

        .dark {
            --bg-primary: #1F141A;
            --card-bg: rgba(42, 27, 36, 0.85);
            --border-color: rgba(224, 82, 151, 0.3);
            --text-main: #FCE7F3;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        .glass-panel {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
        }

        .gradient-pastel-btn {
            background: linear-gradient(135deg, #FFB6C1 0%, #FF69B4 50%, #E05297 100%);
            box-shadow: 0 10px 20px -5px rgba(255, 105, 180, 0.4);
        }

        .gradient-pastel-btn:hover {
            box-shadow: 0 15px 25px -3px rgba(255, 105, 180, 0.6);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row">

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>

    <!-- SIDEBAR NAVIGASI LENGKAP -->
    <aside class="w-full md:w-72 glass-panel p-5 flex flex-col justify-between shrink-0 border-r border-pink-200/60 dark:border-pink-900/40 h-auto md:h-screen sticky top-0 overflow-y-auto">
        <div>
            <!-- Brand Logo -->
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-pink-200/50">
                <div class="w-10 h-10 rounded-2xl gradient-pastel-btn flex items-center justify-center text-white text-base shadow-md">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h1 class="font-bold text-gray-800 dark:text-pink-100 text-sm font-poppins">Tasky<span class="text-pink-500">Pastel</span></h1>
                    <p class="text-[10px] text-gray-500 dark:text-pink-300/70 font-medium">Portal Akademik UMC</p>
                </div>
            </div>

            <!-- Menu Kategori Sidebar -->
            <nav class="space-y-1.5 text-xs">
                <p class="text-[10px] font-bold uppercase text-gray-400 dark:text-pink-300/50 tracking-wider mb-2 px-2">Menu Utama</p>
                
                <button onclick="switchTab('tugas', 'ALL')" id="nav-tugas-ALL" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl font-bold transition-all bg-pink-500 text-white shadow-md">
                    <i class="fa-solid fa-house w-5 text-center"></i> Semua Tugas
                </button>
                
                <button onclick="switchTab('tugas', 'Selesai')" id="nav-tugas-Selesai" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl font-bold transition-all text-gray-600 dark:text-pink-200 hover:bg-pink-100/60 dark:hover:bg-pink-900/30">
                    <i class="fa-solid fa-circle-check w-5 text-center text-emerald-500"></i> Tugas Selesai
                    <span id="badge-selesai" class="ml-auto bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full text-[10px]">0</span>
                </button>

                <button onclick="switchTab('tugas', 'Menunggu Review')" id="nav-tugas-Menunggu Review" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl font-bold transition-all text-gray-600 dark:text-pink-200 hover:bg-pink-100/60 dark:hover:bg-pink-900/30">
                    <i class="fa-solid fa-clock w-5 text-center text-amber-500"></i> Menunggu Review
                    <span id="badge-review" class="ml-auto bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-[10px]">0</span>
                </button>

                <button onclick="switchTab('tugas', 'Perlu Revisi')" id="nav-tugas-Perlu Revisi" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl font-bold transition-all text-gray-600 dark:text-pink-200 hover:bg-pink-100/60 dark:hover:bg-pink-900/30">
                    <i class="fa-solid fa-triangle-exclamation w-5 text-center text-rose-500"></i> Perlu Revisi
                    <span id="badge-revisi" class="ml-auto bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full text-[10px]">0</span>
                </button>

                <p class="text-[10px] font-bold uppercase text-gray-400 dark:text-pink-300/50 tracking-wider mt-4 mb-2 px-2">Akademik & Perkuliahan</p>

                <button onclick="switchTab('matkul')" id="nav-matkul" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl font-bold transition-all text-gray-600 dark:text-pink-200 hover:bg-pink-100/60 dark:hover:bg-pink-900/30">
                    <i class="fa-solid fa-book-open w-5 text-center text-pink-500"></i> Mata Kuliah
                </button>

                <button onclick="switchTab('materi')" id="nav-materi" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl font-bold transition-all text-gray-600 dark:text-pink-200 hover:bg-pink-100/60 dark:hover:bg-pink-900/30">
                    <i class="fa-solid fa-file-lines w-5 text-center text-purple-500"></i> Materi Kuliah
                </button>

                <button onclick="switchTab('nilai')" id="nav-nilai" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl font-bold transition-all text-gray-600 dark:text-pink-200 hover:bg-pink-100/60 dark:hover:bg-pink-900/30">
                    <i class="fa-solid fa-award w-5 text-center text-amber-500"></i> Rekap Nilai & KHS
                </button>

                <button onclick="switchTab('jadwal')" id="nav-jadwal" class="nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl font-bold transition-all text-gray-600 dark:text-pink-200 hover:bg-pink-100/60 dark:hover:bg-pink-900/30">
                    <i class="fa-solid fa-calendar-days w-5 text-center text-blue-500"></i> Jadwal Kuliah
                </button>
            </nav>
        </div>

        <!-- Profil & Tombol Logout di Sidebar -->
        <div class="pt-4 border-t border-pink-200/60 dark:border-pink-900/40 mt-6 space-y-3">
            <div class="flex items-center gap-3">
                <img src="https://api.dicebear.com/7.x/micah/svg?seed=LisyaCuteGirlBright&skinColor=fdbcb4&backgroundColor=ffb6c1,ffc0cb,ffe4e8" class="w-9 h-9 rounded-xl bg-pink-100 border border-pink-300">
                <div class="overflow-hidden">
                    <h4 class="font-bold text-xs text-gray-800 dark:text-pink-100 truncate"><?php echo $_SESSION['nama']; ?></h4>
                    <p class="text-[10px] text-gray-500 dark:text-pink-300/70"><?php echo $_SESSION['nim']; ?> • <?php echo $_SESSION['kelas']; ?></p>
                </div>
            </div>

            <div class="flex items-center justify-between gap-2">
                <button onclick="toggleDarkMode()" class="glass-panel p-2 rounded-xl text-pink-500 dark:text-pink-300 hover:scale-105 transition-all text-xs flex-1 flex items-center justify-center gap-1.5 font-semibold">
                    <i id="theme-icon" class="fa-solid fa-moon"></i> Tema
                </button>
                <a href="index.php?action=logout" onclick="return confirm('Keluar dari sesi ini?')" class="p-2 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-xl text-xs flex items-center justify-center font-bold transition-all" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- KONTEN UTAMA HALAMAN -->
    <main class="flex-1 p-6 md:p-8 overflow-y-auto">

        <!-- Header Banner -->
        <header class="glass-panel rounded-3xl p-6 mb-8 shadow-sm flex flex-col lg:flex-row justify-between items-center gap-6 border-pink-200/50">
            <div class="space-y-1 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-pink-100/80 dark:bg-pink-950/60 text-pink-600 dark:text-pink-300 px-3 py-1 rounded-full text-xs font-semibold border border-pink-200">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Selamat Datang, <?php echo $_SESSION['nama']; ?>! ✨
                </div>
                <h2 id="page-title" class="text-xl sm:text-2xl font-extrabold text-gray-800 dark:text-pink-100 font-poppins">
                    Portal Pengumpulan <span class="text-pink-500">Tugas Kuliah</span>
                </h2>
            </div>
            <div class="glass-panel px-4 py-3 rounded-2xl shadow-inner flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-pink-200 dark:bg-pink-900 text-pink-600 dark:text-pink-200 flex items-center justify-center text-lg font-bold">
                    <i class="fa-solid fa-university"></i>
                </div>
                <div>
                    <h3 class="font-bold text-xs text-gray-800 dark:text-pink-100">Teknik Informatika</h3>
                    <p class="text-[10px] text-pink-500 font-semibold">Universitas Muhammadiyah Cirebon</p>
                </div>
            </div>
        </header>

        <!-- CONTAINER UTAMA -->
        <div id="main-view-container">
            <!-- TAMPILAN TUGAS (DEFAULT) -->
            <div id="view-tugas">
                <!-- Widgets Statistik -->
                <section class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="glass-panel p-4 rounded-2xl border-l-4 border-l-pink-400">
                        <span class="text-[11px] font-semibold text-gray-500 dark:text-pink-300 uppercase">Total Tugas</span>
                        <div class="text-2xl font-extrabold text-gray-800 dark:text-pink-100 mt-1" id="stat-total">0</div>
                    </div>
                    <div class="glass-panel p-4 rounded-2xl border-l-4 border-l-emerald-400">
                        <span class="text-[11px] font-semibold text-gray-500 dark:text-pink-300 uppercase">Selesai</span>
                        <div class="text-2xl font-extrabold text-gray-800 dark:text-pink-100 mt-1" id="stat-completed">0</div>
                    </div>
                    <div class="glass-panel p-4 rounded-2xl border-l-4 border-l-amber-400">
                        <span class="text-[11px] font-semibold text-gray-500 dark:text-pink-300 uppercase">Dalam Review</span>
                        <div class="text-2xl font-extrabold text-gray-800 dark:text-pink-100 mt-1" id="stat-pending">0</div>
                    </div>
                    <div class="glass-panel p-4 rounded-2xl border-l-4 border-l-rose-400">
                        <span class="text-[11px] font-semibold text-gray-500 dark:text-pink-300 uppercase">Perlu Revisi</span>
                        <div class="text-2xl font-extrabold text-gray-800 dark:text-pink-100 mt-1" id="stat-revision">0</div>
                    </div>
                </section>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Form Upload (Kiri) -->
                    <div class="lg:col-span-5">
                        <div class="glass-panel rounded-3xl p-6 shadow-xl sticky top-6 space-y-4">
                            <div class="flex items-center gap-2 pb-3 border-b border-pink-100 dark:border-pink-900/50">
                                <i class="fa-solid fa-cloud-arrow-up text-pink-500 text-lg"></i>
                                <h3 class="font-bold text-base text-gray-800 dark:text-pink-100 font-poppins">Kirim Tugas Baru</h3>
                            </div>

                            <form id="submission-form" onsubmit="handleFormSubmit(event)" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-pink-200 uppercase mb-1">Nama Tugas <span class="text-pink-500">*</span></label>
                                    <input type="text" id="assignment-title" placeholder="Contoh: Tugas 4 - Linked List" required class="w-full bg-white/70 dark:bg-pink-950/40 border border-pink-200 dark:border-pink-800 rounded-xl px-4 py-2.5 text-xs text-gray-800 dark:text-pink-100 focus:outline-none focus:ring-2 focus:ring-pink-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-pink-200 uppercase mb-1">Mata Kuliah <span class="text-pink-500">*</span></label>
                                    <input type="text" id="course" list="course-history-list" placeholder="Ketik atau pilih matkul..." required class="w-full bg-white/70 dark:bg-pink-950/40 border border-pink-200 dark:border-pink-800 rounded-xl px-4 py-2.5 text-xs text-gray-800 dark:text-pink-100 focus:outline-none focus:ring-2 focus:ring-pink-400">
                                    <datalist id="course-history-list"></datalist>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-pink-200 uppercase mb-1">Link Repository / Cloud Drive (Opsional)</label>
                                    <input type="url" id="project-link" placeholder="https://github.com/..." class="w-full bg-white/70 dark:bg-pink-950/40 border border-pink-200 dark:border-pink-800 rounded-xl px-4 py-2.5 text-xs text-gray-800 dark:text-pink-100 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-pink-200 uppercase mb-1">Catatan Tambahan</label>
                                    <textarea id="notes" rows="2" placeholder="Catatan khusus..." class="w-full bg-white/70 dark:bg-pink-950/40 border border-pink-200 dark:border-pink-800 rounded-xl px-4 py-2.5 text-xs text-gray-800 dark:text-pink-100 focus:outline-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-pink-200 uppercase mb-1">Upload Berkas <span class="text-pink-500">*</span></label>
                                    <input type="file" id="file-input" required class="w-full text-xs text-gray-500 dark:text-pink-300 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-pink-100 file:text-pink-700 hover:file:bg-pink-200" onchange="handleFileSelect(event)">
                                </div>
                                <button type="submit" class="w-full gradient-pastel-btn text-white font-bold py-3 px-6 rounded-2xl text-xs uppercase tracking-wider transition-all">
                                    <i class="fa-solid fa-paper-plane mr-1"></i> Simpan ke Portal
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Daftar List Tugas & Pencarian (Kanan) -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="glass-panel p-4 rounded-2xl shadow-sm">
                            <div class="relative w-full">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-pink-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                                <input type="text" id="search-input" oninput="renderSubmissions()" placeholder="Cari nama tugas, matkul, atau tanggal..." class="w-full bg-white/70 dark:bg-pink-950/40 border border-pink-200 rounded-xl pl-9 pr-3 py-2 text-xs text-gray-800 dark:text-pink-100 focus:outline-none">
                            </div>
                        </div>

                        <div id="cards-container" class="space-y-4"></div>

                        <div id="empty-state" class="hidden glass-panel text-center py-12 rounded-3xl">
                            <i class="fa-solid fa-box-open text-3xl text-pink-400 mb-2"></i>
                            <h4 class="font-bold text-gray-700 dark:text-pink-200 text-sm">Belum Ada Tugas Tersimpan</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAMPILAN MATA KULIAH DINAMIS -->
            <div id="view-matkul" class="hidden space-y-4">
                <div class="glass-panel rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-pink-100">
                        <h3 class="font-bold text-base text-gray-800 dark:text-pink-100 flex items-center gap-2">
                            <i class="fa-solid fa-book-open text-pink-500"></i> Daftar Mata Kuliah Aktif
                        </h3>
                        <span class="text-xs text-gray-400">Otomatis bertambah dari riwayat tugas</span>
                    </div>
                    <div id="dynamic-matkul-container" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs"></div>
                </div>
            </div>

            <!-- TAMPILAN MATERI KULIAH (Bisa Ditambah Sendiri) -->
            <div id="view-materi" class="hidden space-y-4">
                <div class="glass-panel rounded-3xl p-6 shadow-sm space-y-6">
                    <div class="flex justify-between items-center pb-3 border-b border-pink-100">
                        <h3 class="font-bold text-base text-gray-800 dark:text-pink-100 flex items-center gap-2">
                            <i class="fa-solid fa-file-lines text-purple-500"></i> Modul & Materi Pembelajaran
                        </h3>
                        <button onclick="openAddModal('materi')" class="px-3 py-1.5 gradient-pastel-btn text-white rounded-xl text-xs font-bold">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah Materi
                        </button>
                    </div>
                    <div id="materi-container" class="space-y-3 text-xs"></div>
                </div>
            </div>

            <!-- TAMPILAN REKAP NILAI (Bisa Ditambah Sendiri) -->
            <div id="view-nilai" class="hidden space-y-4">
                <div class="glass-panel rounded-3xl p-6 shadow-sm space-y-6">
                    <div class="flex justify-between items-center pb-3 border-b border-pink-100">
                        <h3 class="font-bold text-base text-gray-800 dark:text-pink-100 flex items-center gap-2">
                            <i class="fa-solid fa-award text-amber-500"></i> Rekap Nilai Sementara (KHS)
                        </h3>
                        <button onclick="openAddModal('nilai')" class="px-3 py-1.5 gradient-pastel-btn text-white rounded-xl text-xs font-bold">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah Nilai
                        </button>
                    </div>
                    <div class="overflow-x-auto text-xs">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-pink-200 text-pink-600">
                                    <th class="py-2.5 px-3">Mata Kuliah</th>
                                    <th class="py-2.5 px-3">SKS</th>
                                    <th class="py-2.5 px-3">Nilai Angka</th>
                                    <th class="py-2.5 px-3">Huruf</th>
                                    <th class="py-2.5 px-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="nilai-table-body" class="divide-y divide-pink-100 dark:divide-pink-900/40"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAMPILAN JADWAL KULIAH (Bisa Ditambah Sendiri) -->
            <div id="view-jadwal" class="hidden space-y-4">
                <div class="glass-panel rounded-3xl p-6 shadow-sm space-y-6">
                    <div class="flex justify-between items-center pb-3 border-b border-pink-100">
                        <h3 class="font-bold text-base text-gray-800 dark:text-pink-100 flex items-center gap-2">
                            <i class="fa-solid fa-calendar-days text-blue-500"></i> Jadwal Perkuliahan Aktif
                        </h3>
                        <button onclick="openAddModal('jadwal')" class="px-3 py-1.5 gradient-pastel-btn text-white rounded-xl text-xs font-bold">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah Jadwal
                        </button>
                    </div>
                    <div id="jadwal-container" class="space-y-3 text-xs"></div>
                </div>
            </div>

        </div>
    </main>

    <!-- Modal Detail & Update Status Tugas -->
    <div id="detail-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="glass-panel bg-white dark:bg-pastel-cardDark rounded-3xl p-6 max-w-lg w-full shadow-2xl border-2 border-pink-200 space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-pink-100 dark:border-pink-900/50">
                <h3 class="font-bold text-pink-600 dark:text-pink-300 font-poppins text-base">Detail & Update Status Tugas</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-pink-500"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <div id="modal-body" class="space-y-3 text-xs"></div>
            <div class="pt-3 border-t border-pink-100 dark:border-pink-900/50 flex justify-end gap-2">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 dark:bg-pink-950 text-gray-700 dark:text-pink-200 font-semibold rounded-xl text-xs">Tutup</button>
                <button onclick="saveModalChanges()" class="gradient-pastel-btn text-white font-semibold px-4 py-2 rounded-xl text-xs">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Data Universal (Materi, Nilai, Jadwal) -->
    <div id="add-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="glass-panel bg-white dark:bg-pastel-cardDark rounded-3xl p-6 max-w-md w-full shadow-2xl border-2 border-pink-200 space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-pink-100">
                <h3 id="add-modal-title" class="font-bold text-pink-600 font-poppins text-base">Tambah Data</h3>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-pink-500"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form id="add-dynamic-form" onsubmit="handleDynamicAddSubmit(event)" class="space-y-3 text-xs">
                <div id="add-form-fields" class="space-y-3"></div>
                <div class="pt-3 border-t border-pink-100 flex justify-end gap-2">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="gradient-pastel-btn text-white font-semibold px-4 py-2 rounded-xl">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Utama -->
    <script>
        let submissions = [];
        let courseHistory = [];
        let materiList = [];
        let nilaiList = [];
        let jadwalList = [];

        let currentStatusFilter = 'ALL';
        let selectedFileObj = null;
        let activeModalId = null;
        let activeAddType = '';

        window.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('lisya_aesthetic_portal_data')) submissions = JSON.parse(localStorage.getItem('lisya_aesthetic_portal_data'));
            if (localStorage.getItem('lisya_course_history')) courseHistory = JSON.parse(localStorage.getItem('lisya_course_history'));
            if (localStorage.getItem('lisya_materi_data')) materiList = JSON.parse(localStorage.getItem('lisya_materi_data'));
            if (localStorage.getItem('lisya_nilai_data')) nilaiList = JSON.parse(localStorage.getItem('lisya_nilai_data'));
            if (localStorage.getItem('lisya_jadwal_data')) jadwalList = JSON.parse(localStorage.getItem('lisya_jadwal_data'));

            if (localStorage.getItem('theme') === 'dark') {
                document.documentElement.classList.add('dark');
                document.getElementById('theme-icon').className = 'fa-solid fa-sun';
            }

            updateCourseDatalist();
            renderSubmissions();
            renderDynamicMatkul();
            renderMateri();
            renderNilai();
            renderJadwal();
        });

        function saveAllStorage() {
            localStorage.setItem('lisya_aesthetic_portal_data', JSON.stringify(submissions));
            localStorage.setItem('lisya_course_history', JSON.stringify(courseHistory));
            localStorage.setItem('lisya_materi_data', JSON.stringify(materiList));
            localStorage.setItem('lisya_nilai_data', JSON.stringify(nilaiList));
            localStorage.setItem('lisya_jadwal_data', JSON.stringify(jadwalList));
        }

        function switchTab(viewName, filterStatus = 'ALL') {
            document.getElementById('view-tugas').classList.add('hidden');
            document.getElementById('view-matkul').classList.add('hidden');
            document.getElementById('view-materi').classList.add('hidden');
            document.getElementById('view-nilai').classList.add('hidden');
            document.getElementById('view-jadwal').classList.add('hidden');

            document.querySelectorAll('.nav-btn').forEach(btn => {
                btn.className = 'nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl font-bold transition-all text-gray-600 dark:text-pink-200 hover:bg-pink-100/60 dark:hover:bg-pink-900/30';
            });

            const pageTitle = document.getElementById('page-title');

            if (viewName === 'tugas') {
                document.getElementById('view-tugas').classList.remove('hidden');
                currentStatusFilter = filterStatus;
                const activeBtn = document.getElementById(`nav-tugas-${filterStatus}`);
                if (activeBtn) activeBtn.className = 'nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl font-bold transition-all bg-pink-500 text-white shadow-md';
                
                let titleText = 'Semua Tugas Kuliah';
                if (filterStatus === 'Selesai') titleText = 'Daftar Tugas Selesai';
                if (filterStatus === 'Menunggu Review') titleText = 'Tugas Dalam Review';
                if (filterStatus === 'Perlu Revisi') titleText = 'Tugas Perlu Revisi';
                pageTitle.innerHTML = `Portal Pengumpulan <span class="text-pink-500">${titleText}</span>`;
                renderSubmissions();
            } else {
                document.getElementById(`view-${viewName}`).classList.remove('hidden');
                const activeBtn = document.getElementById(`nav-${viewName}`);
                if (activeBtn) activeBtn.className = 'nav-btn w-full flex items-center gap-3 px-3.5 py-2.5 rounded-2xl font-bold transition-all bg-pink-500 text-white shadow-md';

                if(viewName === 'matkul') renderDynamicMatkul();
                if(viewName === 'materi') renderMateri();
                if(viewName === 'nilai') renderNilai();
                if(viewName === 'jadwal') renderJadwal();

                let titles = {
                    'matkul': 'Informasi Mata Kuliah',
                    'materi': 'Unduh Materi & Modul',
                    'nilai': 'Rekap Nilai & Transkrip Akademik',
                    'jadwal': 'Jadwal Kuliah Semester Ini'
                };
                pageTitle.innerHTML = `Menu Akademik <span class="text-pink-500">${titles[viewName]}</span>`;
            }
        }

        function updateCourseDatalist() {
            const datalist = document.getElementById('course-history-list');
            if(!datalist) return;
            datalist.innerHTML = '';
            courseHistory.forEach(course => {
                const opt = document.createElement('option');
                opt.value = course;
                datalist.appendChild(opt);
            });
        }

        function renderDynamicMatkul() {
            const container = document.getElementById('dynamic-matkul-container');
            if(!container) return;
            container.innerHTML = '';

            if (courseHistory.length === 0) {
                container.innerHTML = `<p class="text-gray-400 italic col-span-2">Belum ada mata kuliah. Silakan kirim tugas dengan nama mata kuliah baru.</p>`;
                return;
            }

            courseHistory.forEach(course => {
                const card = document.createElement('div');
                card.className = 'bg-white/60 dark:bg-pink-950/40 p-4 rounded-2xl border border-pink-200 space-y-2 shadow-sm';
                card.innerHTML = `
                    <span class="bg-pink-100 text-pink-600 px-2 py-0.5 rounded-md font-bold text-[10px]">Aktif</span>
                    <h4 class="font-bold text-sm text-gray-800 dark:text-pink-100">${escapeHtml(course)}</h4>
                    <p class="text-gray-500">Program Studi Teknik Informatika (TI24D)</p>
                `;
                container.appendChild(card);
            });
        }

        // Render & Manajemen Materi
        function renderMateri() {
            const container = document.getElementById('materi-container');
            if(!container) return;
            container.innerHTML = '';

            if(materiList.length === 0) {
                container.innerHTML = `<p class="text-gray-400 italic text-center py-6">Belum ada materi. Klik tombol "Tambah Materi" di atas.</p>`;
                return;
            }

            materiList.forEach((item, index) => {
                container.innerHTML += `
                    <div class="flex items-center justify-between p-4 bg-white/60 dark:bg-pink-950/40 rounded-2xl border border-pink-200">
                        <div>
                            <h4 class="font-bold text-gray-800 dark:text-pink-100">${escapeHtml(item.title)}</h4>
                            <p class="text-gray-400 mt-0.5">${escapeHtml(item.course)} • ${escapeHtml(item.size)}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            ${item.fileUrl ? `<a href="${item.fileUrl}" target="_blank" class="px-3 py-1.5 bg-pink-500 text-white rounded-xl font-medium"><i class="fa-solid fa-download mr-1"></i> Unduh</a>` : ''}
                            <button onclick="deleteItem('materi', ${index})" class="p-2 bg-rose-100 text-rose-700 rounded-xl"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                `;
            });
        }

        // Render & Manajemen Nilai
        function renderNilai() {
            const tbody = document.getElementById('nilai-table-body');
            if(!tbody) return;
            tbody.innerHTML = '';

            if(nilaiList.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-6 text-gray-400 italic">Belum ada rekap nilai. Klik "Tambah Nilai".</td></tr>`;
                return;
            }

            nilaiList.forEach((item, index) => {
                tbody.innerHTML += `
                    <tr>
                        <td class="py-3 px-3 font-semibold">${escapeHtml(item.course)}</td>
                        <td class="py-3 px-3">${escapeHtml(item.sks)}</td>
                        <td class="py-3 px-3">${escapeHtml(item.angka)}</td>
                        <td class="py-3 px-3 font-bold text-emerald-600">${escapeHtml(item.huruf)}</td>
                        <td class="py-3 px-3 text-right">
                            <button onclick="deleteItem('nilai', ${index})" class="p-1.5 bg-rose-100 text-rose-700 rounded-lg"><i class="fa-solid fa-trash text-[10px]"></i></button>
                        </td>
                    </tr>
                `;
            });
        }

        // Render & Manajemen Jadwal
        function renderJadwal() {
            const container = document.getElementById('jadwal-container');
            if(!container) return;
            container.innerHTML = '';

            if(jadwalList.length === 0) {
                container.innerHTML = `<p class="text-gray-400 italic text-center py-6">Belum ada jadwal kuliah. Klik "Tambah Jadwal".</p>`;
                return;
            }

            jadwalList.forEach((item, index) => {
                container.innerHTML += `
                    <div class="p-4 bg-white/60 dark:bg-pink-950/40 rounded-2xl border border-pink-200 flex justify-between items-center">
                        <div>
                            <span class="text-pink-500 font-bold">${escapeHtml(item.waktu)}</span>
                            <h4 class="font-bold text-gray-800 dark:text-pink-100 text-sm mt-0.5">${escapeHtml(item.course)}</h4>
                            <p class="text-gray-400">${escapeHtml(item.ruang)}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-pink-100 text-pink-700 rounded-xl font-bold">Aktif</span>
                            <button onclick="deleteItem('jadwal', ${index})" class="p-2 bg-rose-100 text-rose-700 rounded-xl"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                `;
            });
        }

        // Modal Tambah Dinamis Handler
        function openAddModal(type) {
            activeAddType = type;
            const titleEl = document.getElementById('add-modal-title');
            const fieldsEl = document.getElementById('add-form-fields');
            fieldsEl.innerHTML = '';

            if (type === 'materi') {
                titleEl.innerText = 'Tambah Materi Kuliah';
                fieldsEl.innerHTML = `
                    <div><label class="font-semibold">Nama Modul / Materi</label><input type="text" id="add-materi-title" required class="w-full border border-pink-200 rounded-xl p-2 mt-1"></div>
                    <div><label class="font-semibold">Mata Kuliah</label><input type="text" id="add-materi-course" required class="w-full border border-pink-200 rounded-xl p-2 mt-1"></div>
                    <div><label class="font-semibold">File Berkas / Modul</label><input type="file" id="add-materi-file" required class="w-full text-xs mt-1"></div>
                `;
            } else if (type === 'nilai') {
                titleEl.innerText = 'Tambah Rekap Nilai';
                fieldsEl.innerHTML = `
                    <div><label class="font-semibold">Mata Kuliah</label><input type="text" id="add-nilai-course" required class="w-full border border-pink-200 rounded-xl p-2 mt-1"></div>
                    <div><label class="font-semibold">Jumlah SKS</label><input type="number" id="add-nilai-sks" value="3" required class="w-full border border-pink-200 rounded-xl p-2 mt-1"></div>
                    <div><label class="font-semibold">Nilai Angka</label><input type="text" id="add-nilai-angka" placeholder="85.5" required class="w-full border border-pink-200 rounded-xl p-2 mt-1"></div>
                    <div><label class="font-semibold">Nilai Huruf</label><input type="text" id="add-nilai-huruf" placeholder="A" required class="w-full border border-pink-200 rounded-xl p-2 mt-1"></div>
                `;
            } else if (type === 'jadwal') {
                titleEl.innerText = 'Tambah Jadwal Kuliah';
                fieldsEl.innerHTML = `
                    <div><label class="font-semibold">Mata Kuliah</label><input type="text" id="add-jadwal-course" required class="w-full border border-pink-200 rounded-xl p-2 mt-1"></div>
                    <div><label class="font-semibold">Hari & Jam</label><input type="text" id="add-jadwal-waktu" placeholder="Senin, 08:00 - 10:30 WIB" required class="w-full border border-pink-200 rounded-xl p-2 mt-1"></div>
                    <div><label class="font-semibold">Ruangan</label><input type="text" id="add-jadwal-ruang" placeholder="Lab Komputer 2" required class="w-full border border-pink-200 rounded-xl p-2 mt-1"></div>
                `;
            }

            document.getElementById('add-modal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('add-modal').classList.add('hidden');
            activeAddType = '';
        }

        function handleDynamicAddSubmit(e) {
            e.preventDefault();
            if (activeAddType === 'materi') {
                const title = document.getElementById('add-materi-title').value;
                const course = document.getElementById('add-materi-course').value;
                const fileInput = document.getElementById('add-materi-file');
                const file = fileInput.files[0];
                const fileUrl = file ? URL.createObjectURL(file) : '';
                const size = file ? formatBytes(file.size) : '1.0 MB';

                materiList.push({ title, course, size, fileUrl });
                renderMateri();
                showToast('Materi berhasil ditambahkan!', 'success');
            } else if (activeAddType === 'nilai') {
                const course = document.getElementById('add-nilai-course').value;
                const sks = document.getElementById('add-nilai-sks').value;
                const angka = document.getElementById('add-nilai-angka').value;
                const huruf = document.getElementById('add-nilai-huruf').value;

                nilaiList.push({ course, sks, angka, huruf });
                renderNilai();
                showToast('Nilai berhasil ditambahkan!', 'success');
            } else if (activeAddType === 'jadwal') {
                const course = document.getElementById('add-jadwal-course').value;
                const waktu = document.getElementById('add-jadwal-waktu').value;
                const ruang = document.getElementById('add-jadwal-ruang').value;

                jadwalList.push({ course, waktu, ruang });
                renderJadwal();
                showToast('Jadwal berhasil ditambahkan!', 'success');
            }

            saveAllStorage();
            closeAddModal();
        }

        function deleteItem(type, index) {
            if(confirm('Hapus item ini?')) {
                if(type === 'materi') materiList.splice(index, 1), renderMateri();
                if(type === 'nilai') nilaiList.splice(index, 1), renderNilai();
                if(type === 'jadwal') jadwalList.splice(index, 1), renderJadwal();
                saveAllStorage();
                showToast('Data berhasil dihapus.', 'info');
            }
        }

        function toggleDarkMode() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-icon');
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                icon.className = 'fa-solid fa-moon';
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                icon.className = 'fa-solid fa-sun';
            }
        }

        function handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) selectedFileObj = file;
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            const title = document.getElementById('assignment-title').value;
            const course = document.getElementById('course').value.trim();
            const link = document.getElementById('project-link').value;
            const notes = document.getElementById('notes').value || 'Tidak ada catatan.';
            const fileInput = document.getElementById('file-input');

            if (!selectedFileObj && fileInput.files.length === 0) {
                showToast('Mohon lampirkan berkas tugas!', 'error');
                return;
            }

            const file = selectedFileObj || fileInput.files[0];
            const fileObjectURL = URL.createObjectURL(file);

            if (course && !courseHistory.includes(course)) {
                courseHistory.push(course);
                updateCourseDatalist();
                renderDynamicMatkul();
            }

            const now = new Date();
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            const dateStr = `${now.toLocaleDateString('id-ID', options)} pukul ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;

            const newItem = {
                id: Date.now().toString(),
                title, course, link, notes,
                fileName: file.name,
                fileSize: formatBytes(file.size),
                fileUrl: fileObjectURL,
                date: dateStr,
                status: 'Menunggu Review',
                progress: 50
            };

            submissions.unshift(newItem);
            saveAllStorage();
            renderSubmissions();

            document.getElementById('submission-form').reset();
            selectedFileObj = null;
            showToast('Tugas & Mata Kuliah berhasil ditambahkan! ✨', 'success');
        }

        function formatBytes(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }

        function renderSubmissions() {
            const container = document.getElementById('cards-container');
            const emptyState = document.getElementById('empty-state');
            const searchInput = document.getElementById('search-input');
            if(!container) return;

            const searchQuery = searchInput ? searchInput.value.toLowerCase() : '';
            container.innerHTML = '';

            const filtered = submissions.filter(item => {
                const matchStatus = currentStatusFilter === 'ALL' || item.status === currentStatusFilter;
                const matchSearch = item.title.toLowerCase().includes(searchQuery) || item.course.toLowerCase().includes(searchQuery);
                return matchStatus && matchSearch;
            });

            if (filtered.length === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
                filtered.forEach(item => {
                    let badgeColor = 'bg-amber-100 text-amber-700 border-amber-300';
                    if (item.status === 'Selesai') badgeColor = 'bg-emerald-100 text-emerald-700 border-emerald-300';
                    if (item.status === 'Perlu Revisi') badgeColor = 'bg-rose-100 text-rose-700 border-rose-300';

                    const card = document.createElement('div');
                    card.className = 'glass-panel rounded-3xl p-5 shadow-sm space-y-3';
                    card.innerHTML = `
                        <div class="flex justify-between items-start gap-3">
                            <div>
                                <span class="inline-block bg-pink-100 dark:bg-pink-950 text-pink-600 dark:text-pink-300 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full mb-1">${escapeHtml(item.course)}</span>
                                <h4 class="font-bold text-gray-800 dark:text-pink-100 font-poppins text-sm">${escapeHtml(item.title)}</h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold border ${badgeColor}">${item.status}</span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-pink-300/70 space-y-1">
                            <p class="font-medium text-pink-500"><i class="fa-regular fa-calendar-days mr-1"></i> ${escapeHtml(item.date)}</p>
                            <p><i class="fa-solid fa-quote-left text-pink-400 mr-1"></i>${escapeHtml(item.notes)}</p>
                            ${item.link ? `<p><a href="${escapeHtml(item.link)}" target="_blank" class="text-pink-500 underline font-medium">Buka Tautan Project</a></p>` : ''}
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-pink-100 text-[11px]">
                            <span class="bg-white/80 dark:bg-pink-950/60 px-2.5 py-1 rounded-xl border border-pink-200 font-mono text-[10px]">
                                <i class="fa-solid fa-paperclip text-pink-400 mr-1"></i>${escapeHtml(item.fileName)} (${item.fileSize})
                            </span>
                            <div class="flex items-center gap-1.5 ml-auto">
                                ${item.fileUrl ? `<a href="${item.fileUrl}" target="_blank" class="px-3 py-1.5 bg-pink-500 hover:bg-pink-600 text-white rounded-xl font-medium inline-flex items-center gap-1"><i class="fa-solid fa-eye text-[10px]"></i> Lihat File</a>` : ''}
                                <button onclick="openModal('${item.id}')" class="p-2 bg-pink-100 hover:bg-pink-200 text-pink-700 rounded-xl"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button onclick="deleteSubmission('${item.id}')" class="p-2 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-xl"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    `;
                    container.appendChild(card);
                });
            }

            const total = submissions.length;
            const selesai = submissions.filter(s => s.status === 'Selesai').length;
            const review = submissions.filter(s => s.status === 'Menunggu Review').length;
            const revisi = submissions.filter(s => s.status === 'Perlu Revisi').length;

            if(document.getElementById('stat-total')) {
                document.getElementById('stat-total').innerText = total;
                document.getElementById('stat-completed').innerText = selesai;
                document.getElementById('stat-pending').innerText = review;
                document.getElementById('stat-revision').innerText = revisi;
            }

            if(document.getElementById('badge-selesai')) {
                document.getElementById('badge-selesai').innerText = selesai;
                document.getElementById('badge-review').innerText = review;
                document.getElementById('badge-revisi').innerText = revisi;
            }
        }

        function openModal(id) {
            activeModalId = id;
            const item = submissions.find(s => s.id === id);
            if (!item) return;

            document.getElementById('modal-body').innerHTML = `
                <div><label class="font-semibold">Mata Kuliah</label><p class="font-bold">${escapeHtml(item.course)}</p></div>
                <div><label class="font-semibold">Nama Tugas</label><p class="font-semibold text-pink-600">${escapeHtml(item.title)}</p></div>
                <div>
                    <label class="font-semibold">Ubah Status</label>
                    <select id="modal-status" class="w-full border border-pink-200 rounded-xl p-2 mt-1">
                        <option value="Menunggu Review" ${item.status === 'Menunggu Review' ? 'selected' : ''}>Menunggu Review</option>
                        <option value="Selesai" ${item.status === 'Selesai' ? 'selected' : ''}>Selesai</option>
                        <option value="Perlu Revisi" ${item.status === 'Perlu Revisi' ? 'selected' : ''}>Perlu Revisi</option>
                    </select>
                </div>
            `;
            document.getElementById('detail-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('detail-modal').classList.add('hidden');
            activeModalId = null;
        }

        function saveModalChanges() {
            if (!activeModalId) return;
            const item = submissions.find(s => s.id === activeModalId);
            if (item) {
                item.status = document.getElementById('modal-status').value;
                saveAllStorage();
                renderSubmissions();
                showToast('Status berhasil diperbarui!', 'success');
            }
            closeModal();
        }

        function deleteSubmission(id) {
            if (confirm('Hapus tugas ini?')) {
                submissions = submissions.filter(s => s.id !== id);
                saveAllStorage();
                renderSubmissions();
                showToast('Tugas dihapus.', 'info');
            }
        }

        function showToast(msg, type = 'success') {
            const container = document.getElementById('toast-container');
            if(!container) return;
            const toast = document.createElement('div');
            toast.className = `flex items-center gap-2 px-4 py-3 rounded-2xl text-xs font-medium shadow-xl ${type === 'error' ? 'bg-rose-600 text-white' : 'bg-pink-600 text-white'}`;
            toast.innerHTML = `<span>${msg}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }
    </script>
</body>
</html>