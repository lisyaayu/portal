<?php
require_once 'db.php';

// Otomatis buat folder uploads jika belum ada
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// 1. Tambah Mata Kuliah Manual ke Database
if (isset($_POST['add_course'])) {
    $course_name = trim($_POST['course_name']);
    if (!empty($course_name)) {
        $stmt = $conn->prepare("INSERT IGNORE INTO courses (course_name) VALUES (?)");
        $stmt->bind_param("s", $course_name);
        $stmt->execute();
    }
    header("Location: index.php");
    exit;
}

// 2. Hapus Mata Kuliah
if (isset($_GET['delete_course'])) {
    $id = intval($_GET['delete_course']);
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// 3. Upload Tugas Baru
if (isset($_POST['submit_assignment'])) {
    $title = trim($_POST['title']);
    $course = trim($_POST['course']);
    $link = trim($_POST['link']);
    $notes = trim($_POST['notes']);
    
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['file']['name'];
        $fileTmp = $_FILES['file']['tmp_name'];
        $fileType = $_FILES['file']['type'];
        $uniqueName = time() . '_' . basename($fileName);
        $targetPath = 'uploads/' . $uniqueName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            $now = date('Y-m-d H:i:s');
            $stmt = $conn->prepare("INSERT INTO submissions (title, course, link, notes, file_name, file_path, file_type, submission_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $title, $course, $link, $notes, $fileName, $targetPath, $fileType, $now);
            $stmt->execute();
        }
    }
    header("Location: index.php");
    exit;
}

// 4. Update Status Penilaian Dosen
if (isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    $progress = intval($_POST['progress']);

    $stmt = $conn->prepare("UPDATE submissions SET status = ?, progress = ? WHERE id = ?");
    $stmt->bind_param("sii", $status, $progress, $id);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// 5. Hapus Tugas
if (isset($_GET['delete_submission'])) {
    $id = intval($_GET['delete_submission']);
    $res = $conn->query("SELECT file_path FROM submissions WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        if (file_exists($row['file_path'])) unlink($row['file_path']);
    }
    $conn->query("DELETE FROM submissions WHERE id = $id");
    header("Location: index.php");
    exit;
}

// Ambil Data dari Database MySQL
$courses = $conn->query("SELECT * FROM courses ORDER BY course_name ASC");
$submissions = $conn->query("SELECT * FROM submissions ORDER BY submission_date DESC");

// Hitung Statistik
$total_sub = $conn->query("SELECT COUNT(*) as cnt FROM submissions")->fetch_assoc()['cnt'];
$completed_sub = $conn->query("SELECT COUNT(*) as cnt FROM submissions WHERE status = 'Selesai'")->fetch_assoc()['cnt'];
$pending_sub = $conn->query("SELECT COUNT(*) as cnt FROM submissions WHERE status = 'Menunggu Review'")->fetch_assoc()['cnt'];
$revision_sub = $conn->query("SELECT COUNT(*) as cnt FROM submissions WHERE status = 'Perlu Revisi'")->fetch_assoc()['cnt'];
?>

<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Portal - Lisya Ayu Mutiara Ramadhani</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #FFF0F5;
            color: #4A3E4E;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 182, 193, 0.25) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(255, 105, 180, 0.15) 0%, transparent 25%);
            background-attachment: fixed;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 182, 193, 0.45);
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
<body class="min-h-screen pb-16">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

        <!-- Navigasi Atas -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-2xl gradient-pastel-btn flex items-center justify-center text-white text-base shadow-md">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <span class="font-extrabold text-lg tracking-tight font-poppins text-pink-600">
                    Tasky<span class="text-pink-400 font-light">MySQL</span>
                </span>
            </div>
            <div class="inline-flex items-center gap-2 bg-pink-100 text-pink-600 px-3.5 py-1.5 rounded-full text-xs font-semibold border border-pink-200">
                <i class="fa-solid fa-database"></i> Connected to MySQL Server
            </div>
        </div>

        <!-- Header Profile Banner -->
        <header class="glass-panel rounded-3xl p-6 sm:p-8 mb-8 relative overflow-hidden shadow-xl border-pink-200">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-6 relative z-10">
                <div class="text-center lg:text-left space-y-2 max-w-xl">
                    <div class="inline-flex items-center gap-2 bg-pink-100 text-pink-600 px-3.5 py-1.5 rounded-full text-xs font-semibold border border-pink-200">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Academic Task Portal ✨
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-gray-800 font-poppins leading-tight">
                        Portal Pengumpulan <span class="bg-gradient-to-r from-pink-500 to-rose-500 bg-clip-text text-transparent">Tugas Kuliah</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">
                        Sistem pengumpulan dan rekapitulasi tugas kuliah terpusat berbasis MySQL database.
                    </p>
                </div>

                <!-- Kartu Identitas Lisya -->
                <div class="w-full lg:w-auto glass-panel p-5 rounded-2xl border border-pink-200 shadow-md flex items-center gap-4">
                    <img src="https://api.dicebear.com/7.x/adventurer/svg?seed=Lisya&backgroundColor=ffcce0,ffdfbf" alt="Avatar" class="w-16 h-16 rounded-2xl bg-pink-100 border-2 border-pink-300 object-cover shadow-inner">
                    <div class="text-left">
                        <h2 class="font-bold text-gray-800 text-sm sm:text-base font-poppins flex items-center gap-1.5">
                            Lisya Ayu Mutiara Ramadhani
                            <i class="fa-solid fa-circle-check text-pink-500 text-xs" title="Verified Student"></i>
                        </h2>
                        <div class="text-xs text-gray-500 space-y-0.5 mt-1 font-medium">
                            <p><span class="text-pink-500 font-semibold">NIM:</span> 240511108 <span class="mx-1">•</span> <span class="text-pink-500 font-semibold">Kelas:</span> TI24D</p>
                            <p><span class="text-pink-500 font-semibold">Prodi:</span> Teknik Informatika</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Widgets Statistik -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <div class="glass-panel p-5 rounded-2xl border-l-4 border-l-pink-400 shadow-sm">
                <span class="text-xs font-semibold text-gray-500 uppercase">Total Tugas</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-gray-800 mt-1"><?= $total_sub; ?></div>
            </div>
            <div class="glass-panel p-5 rounded-2xl border-l-4 border-l-emerald-400 shadow-sm">
                <span class="text-xs font-semibold text-gray-500 uppercase">Selesai</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-gray-800 mt-1"><?= $completed_sub; ?></div>
            </div>
            <div class="glass-panel p-5 rounded-2xl border-l-4 border-l-amber-400 shadow-sm">
                <span class="text-xs font-semibold text-gray-500 uppercase">Dalam Review</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-gray-800 mt-1"><?= $pending_sub; ?></div>
            </div>
            <div class="glass-panel p-5 rounded-2xl border-l-4 border-l-rose-400 shadow-sm">
                <span class="text-xs font-semibold text-gray-500 uppercase">Perlu Revisi</span>
                <div class="text-2xl sm:text-3xl font-extrabold text-gray-800 mt-1"><?= $revision_sub; ?></div>
            </div>
        </section>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Sisi Kiri: Form Kirim Tugas & Input Matkul -->
            <div class="lg:col-span-5">
                <div class="glass-panel rounded-3xl p-6 sm:p-7 shadow-xl border-pink-200 sticky top-6 space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-pink-100">
                        <div class="w-10 h-10 rounded-2xl gradient-pastel-btn text-white flex items-center justify-center text-lg shadow-md">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gray-800 font-poppins">Kirim Tugas Baru</h3>
                            <p class="text-xs text-gray-500">Isi form pengumpulan dokumen tugas</p>
                        </div>
                    </div>

                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Tugas / Pertemuan *</label>
                            <input type="text" name="title" required placeholder="Contoh: Tugas 1 - SRS & Flowchart" class="w-full bg-white/80 border border-pink-200 rounded-2xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-pink-400">
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Mata Kuliah *</label>
                                <button type="button" onclick="document.getElementById('course-modal').classList.remove('hidden')" class="text-[11px] font-bold text-pink-500 hover:text-pink-600 flex items-center gap-1">
                                    <i class="fa-solid fa-plus-circle"></i> Input Matkul Manual
                                </button>
                            </div>
                            <select name="course" required class="w-full bg-white/80 border border-pink-200 rounded-2xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-pink-400">
                                <option value="" disabled selected>-- Pilih Mata Kuliah --</option>
                                <?php 
                                $courses->data_seek(0);
                                if ($courses->num_rows === 0): ?>
                                    <option value="" disabled>Belum ada matkul, klik (+ Input Matkul Manual)</option>
                                <?php else:
                                    while($c = $courses->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($c['course_name']); ?>"><?= htmlspecialchars($c['course_name']); ?></option>
                                    <?php endwhile;
                                endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Link Repository / Cloud Drive (Opsional)</label>
                            <input type="url" name="link" placeholder="https://github.com/... atau Drive" class="w-full bg-white/80 border border-pink-200 rounded-2xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-pink-400">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Catatan Tambahan</label>
                            <textarea name="notes" rows="2" placeholder="Tuliskan instruksi atau catatan khusus..." class="w-full bg-white/80 border border-pink-200 rounded-2xl px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-pink-400"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Lampiran Berkas (PDF / Gambar / Zip) *</label>
                            <input type="file" name="file" required class="w-full bg-white/80 border border-pink-200 rounded-2xl p-2 text-xs">
                        </div>

                        <button type="submit" name="submit_assignment" class="w-full gradient-pastel-btn text-white font-bold py-3.5 rounded-2xl text-xs uppercase tracking-wider transition-all">
                            <i class="fa-solid fa-paper-plane mr-1"></i> Simpan Ke MySQL Database
                        </button>
                    </form>
                </div>
            </div>

            <!-- Sisi Kanan: Daftar Histori Tugas -->
            <div class="lg:col-span-7 space-y-4">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-bold text-lg text-gray-800 font-poppins flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-pink-500"></i> Riwayat Pengumpulan Tugas
                    </h3>
                </div>

                <?php if ($submissions->num_rows === 0): ?>
                    <div class="glass-panel text-center py-12 rounded-3xl border-pink-200">
                        <div class="w-16 h-16 bg-pink-100 text-pink-400 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <h4 class="font-bold text-gray-700 font-poppins text-sm">Belum Ada Tugas Tersimpan</h4>
                        <p class="text-xs text-gray-400 mt-1">Silakan input mata kuliah manual dan kirimkan tugas pertama Anda.</p>
                    </div>
                <?php else: ?>
                    <?php while($row = $submissions->fetch_assoc()): 
                        $badge = 'bg-amber-100 text-amber-700 border-amber-300';
                        if ($row['status'] === 'Selesai') $badge = 'bg-emerald-100 text-emerald-700 border-emerald-300';
                        else if ($row['status'] === 'Perlu Revisi') $badge = 'bg-rose-100 text-rose-700 border-rose-300';
                    ?>
                        <div class="glass-panel rounded-3xl p-5 shadow-lg border-pink-200 space-y-3">
                            <div class="flex justify-between items-start gap-3">
                                <div>
                                    <span class="inline-block bg-pink-100 text-pink-600 text-[10px] font-extrabold px-2.5 py-0.5 rounded-full mb-1">
                                        <?= htmlspecialchars($row['course']); ?>
                                    </span>
                                    <h4 class="font-bold text-gray-800 font-poppins text-sm sm:text-base"><?= htmlspecialchars($row['title']); ?></h4>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold border <?= $badge; ?> whitespace-nowrap">
                                    <?= htmlspecialchars($row['status']); ?>
                                </span>
                            </div>

                            <div class="text-xs text-gray-500 space-y-1">
                                <p><i class="fa-solid fa-quote-left text-pink-400 mr-1"></i><?= htmlspecialchars($row['notes']); ?></p>
                                <?php if (!empty($row['link'])): ?>
                                    <p><a href="<?= htmlspecialchars($row['link']); ?>" target="_blank" class="text-pink-500 hover:underline inline-flex items-center gap-1 font-medium"><i class="fa-solid fa-link text-[10px]"></i> Link Repository/Drive</a></p>
                                <?php endif; ?>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-pink-100 text-[11px]">
                                <button onclick="openPreviewModal('<?= htmlspecialchars($row['file_path']); ?>', '<?= htmlspecialchars($row['file_name']); ?>', '<?= htmlspecialchars($row['file_type']); ?>')" class="bg-pink-100 hover:bg-pink-200 text-pink-700 font-semibold px-3 py-1.5 rounded-xl border border-pink-200 transition-all flex items-center gap-1.5">
                                    <i class="fa-solid fa-eye text-pink-500"></i> Buka Berkas (<?= htmlspecialchars($row['file_name']); ?>)
                                </button>

                                <div class="flex items-center gap-2 ml-auto">
                                    <span class="text-[10px] text-gray-400 font-mono"><?= date('d M Y H:i', strtotime($row['submission_date'])); ?></span>
                                    <button onclick="openEditModal(<?= $row['id']; ?>, '<?= htmlspecialchars($row['status']); ?>', <?= $row['progress']; ?>)" class="p-1.5 bg-pink-100 text-pink-700 rounded-lg hover:bg-pink-200" title="Ubah Status (Dosen)"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <a href="?delete_submission=<?= $row['id']; ?>" onclick="return confirm('Hapus tugas ini?')" class="p-1.5 bg-rose-100 text-rose-700 rounded-lg hover:bg-rose-200" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

        </div>

    </div>

    <!-- Modal Input Matkul Manual -->
    <div id="course-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="glass-panel bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border-2 border-pink-200 space-y-4">
            <div class="flex justify-between items-center pb-2 border-b border-pink-100">
                <h3 class="font-bold text-pink-600 font-poppins text-sm">Kelola Mata Kuliah Manual</h3>
                <button onclick="document.getElementById('course-modal').classList.add('hidden')" class="text-gray-400 hover:text-pink-500"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form action="" method="POST" class="space-y-3">
                <input type="text" name="course_name" placeholder="Nama Mata Kuliah Baru..." required class="w-full bg-white border border-pink-200 rounded-xl px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-pink-400">
                <button type="submit" name="add_course" class="w-full gradient-pastel-btn text-white font-bold py-2 rounded-xl text-xs uppercase">Simpan Matkul ke MySQL</button>
            </form>

            <div class="pt-3 border-t border-pink-100">
                <h4 class="font-bold text-xs text-gray-700 mb-2">Daftar Mata Kuliah Tersimpan:</h4>
                <div class="max-h-36 overflow-y-auto space-y-1.5 text-xs">
                    <?php 
                    $courses->data_seek(0);
                    while($c = $courses->fetch_assoc()): ?>
                        <div class="flex justify-between items-center bg-pink-50 p-2 rounded-xl border border-pink-200">
                            <span class="font-semibold text-gray-700"><?= htmlspecialchars($c['course_name']); ?></span>
                            <a href="?delete_course=<?= $c['id']; ?>" onclick="return confirm('Hapus mata kuliah ini?')" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-trash-can"></i></a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal File Viewer / Pembaca Berkas -->
    <div id="file-modal" class="fixed inset-0 bg-black/60 backdrop-blur-md z-50 flex items-center justify-center hidden p-4">
        <div class="glass-panel bg-white rounded-3xl p-6 max-w-3xl w-full h-[85vh] flex flex-col shadow-2xl border-2 border-pink-200">
            <div class="flex justify-between items-center pb-3 border-b border-pink-100">
                <h3 id="modal-file-title" class="font-bold text-pink-600 font-poppins text-sm truncate">Pratinjau Berkas</h3>
                <div class="flex items-center gap-2">
                    <a id="modal-file-download" download href="#" class="px-3 py-1.5 bg-pink-100 text-pink-700 font-semibold rounded-xl text-xs hover:bg-pink-200 flex items-center gap-1">
                        <i class="fa-solid fa-download"></i> Unduh File
                    </a>
                    <button onclick="document.getElementById('file-modal').classList.add('hidden')" class="text-gray-400 hover:text-pink-500"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
            </div>
            <div id="modal-file-body" class="flex-1 mt-4 rounded-2xl overflow-hidden bg-gray-100 flex items-center justify-center"></div>
        </div>
    </div>

    <!-- Modal Edit Status Dosen -->
    <div id="status-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="glass-panel bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border-2 border-pink-200 space-y-4">
            <h3 class="font-bold text-pink-600 font-poppins text-sm">Update Status Penilaian Tugas</h3>
            <form action="" method="POST" class="space-y-3 text-xs">
                <input type="hidden" name="id" id="edit-id">
                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Status Review</label>
                    <select name="status" id="edit-status" class="w-full bg-white border border-pink-200 rounded-xl p-2 font-medium">
                        <option value="Menunggu Review">Menunggu Review</option>
                        <option value="Selesai">Selesai (Disetujui)</option>
                        <option value="Perlu Revisi">Perlu Revisi</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-gray-600 mb-1">Progress Review (%)</label>
                    <input type="number" name="progress" id="edit-progress" min="0" max="100" class="w-full bg-white border border-pink-200 rounded-xl p-2 font-medium">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('status-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded-xl">Batal</button>
                    <button type="submit" name="update_status" class="gradient-pastel-btn text-white font-bold px-4 py-2 rounded-xl">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPreviewModal(path, name, type) {
            document.getElementById('modal-file-title').innerText = name;
            document.getElementById('modal-file-download').href = path;

            const body = document.getElementById('modal-file-body');
            body.innerHTML = '';

            if (type.includes('pdf')) {
                body.innerHTML = `<iframe src="${path}" class="w-full h-full border-none"></iframe>`;
            } else if (type.includes('image')) {
                body.innerHTML = `<img src="${path}" class="max-h-full max-w-full object-contain p-2">`;
            } else {
                body.innerHTML = `
                    <div class="text-center p-6 text-gray-600 space-y-2">
                        <i class="fa-solid fa-file-arrow-down text-4xl text-pink-400"></i>
                        <p class="font-bold text-xs">Pratinjau langsung tidak tersedia untuk tipe file ini.</p>
                        <p class="text-[11px] text-gray-400">Silakan klik "Unduh File" untuk melihat berkas.</p>
                    </div>
                `;
            }
            document.getElementById('file-modal').classList.remove('hidden');
        }

        function openEditModal(id, status, progress) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-status').value = status;
            document.getElementById('edit-progress').value = progress;
            document.getElementById('status-modal').classList.remove('hidden');
        }
    </script>
</body>
</html>