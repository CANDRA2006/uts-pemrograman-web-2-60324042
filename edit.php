<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori - UTS Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 2px 8px rgba(0,0,0,.08); border: none; }
    </style>
</head>
<body>
<?php
require_once 'config/database.php';

//  Ambil & validasi ID dari GET 
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php?error=" . urlencode("ID tidak valid."));
    exit;
}

// Ambil data kategori berdasarkan ID (prepared statement) 
$cek_id = $conn->prepare("SELECT id_kategori, kode_kategori, nama_kategori, deskripsi, status FROM kategori WHERE id_kategori = ?");
$cek_id->bind_param('i', $id);
$cek_id->execute();
$res_id = $cek_id->get_result();

if ($res_id->num_rows === 0) {
    $cek_id->close();
    header("Location: index.php?error=" . urlencode("Kategori tidak ditemukan."));
    exit;
}

$data = $res_id->fetch_assoc();
$cek_id->close();

// Pre-fill nilai awal dari database
$errors    = [];
$kode      = $data['kode_kategori'];
$nama      = $data['nama_kategori'];
$deskripsi = $data['deskripsi'] ?? '';
$status    = $data['status'];

//  Proses POST (update) 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Ambil & sanitasi data dari form
    $kode      = trim(htmlspecialchars($_POST['kode_kategori'] ?? '', ENT_QUOTES, 'UTF-8'));
    $nama      = trim(htmlspecialchars($_POST['nama_kategori'] ?? '', ENT_QUOTES, 'UTF-8'));
    $deskripsi = trim(htmlspecialchars($_POST['deskripsi']     ?? '', ENT_QUOTES, 'UTF-8'));
    $status    = trim(htmlspecialchars($_POST['status']        ?? '', ENT_QUOTES, 'UTF-8'));

    // Validasi kode_kategori
    if ($kode === '') {
        $errors['kode'] = 'Kode kategori wajib diisi.';
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors['kode'] = 'Kode kategori harus 4–10 karakter.';
    } elseif (!preg_match('/^KAT-/', $kode)) {
        $errors['kode'] = 'Kode kategori harus diawali dengan "KAT-".';
    } else {
        // Cek duplikasi, exclude record yang sedang diedit
        $dup = $conn->prepare("SELECT id_kategori FROM kategori WHERE kode_kategori = ? AND id_kategori != ?");
        $dup->bind_param('si', $kode, $id);
        $dup->execute();
        $dup->store_result();
        if ($dup->num_rows > 0) {
            $errors['kode'] = 'Kode kategori sudah digunakan oleh data lain.';
        }
        $dup->close();
    }

    //  Validasi nama_kategori
    if ($nama === '') {
        $errors['nama'] = 'Nama kategori wajib diisi.';
    } elseif (strlen($nama) < 3) {
        $errors['nama'] = 'Nama kategori minimal 3 karakter.';
    } elseif (strlen($nama) > 50) {
        $errors['nama'] = 'Nama kategori maksimal 50 karakter.';
    }

    //Validasi deskripsi
    if ($deskripsi !== '' && strlen($deskripsi) > 200) {
        $errors['deskripsi'] = 'Deskripsi maksimal 200 karakter.';
    }

    // Validasi status
    if (!in_array($status, ['Aktif', 'Nonaktif'], true)) {
        $errors['status'] = 'Status harus Aktif atau Nonaktif.';
    }

    // Jika tidak ada error → update
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE kategori
             SET kode_kategori = ?, nama_kategori = ?, deskripsi = ?, status = ?
             WHERE id_kategori = ?"
        );
        $stmt->bind_param('ssssi', $kode, $nama, $deskripsi, $status, $id);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php?pesan=" . urlencode("Kategori \"$nama\" berhasil diperbarui."));
            exit;
        } else {
            $errors['db'] = 'Gagal memperbarui data: ' . $conn->error;
            $stmt->close();
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center mb-3">
                <a href="index.php" class="btn btn-outline-secondary btn-sm me-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <h4 class="fw-bold mb-0">
                    <i class="bi bi-pencil-square text-warning me-2"></i>Edit Kategori
                    <small class="text-muted fs-6 ms-1">#<?= $id ?></small>
                </h4>
            </div>

            <div class="card">
                <div class="card-body p-4">

                    <?php if (!empty($errors['db'])): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <?= $errors['db'] ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" novalidate>

                        <!-- Kode Kategori -->
                        <div class="mb-3">
                            <label for="kode_kategori" class="form-label fw-semibold">
                                Kode Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="kode_kategori"
                                   name="kode_kategori"
                                   class="form-control <?= isset($errors['kode']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($kode) ?>"
                                   placeholder="Contoh: KAT-004"
                                   maxlength="10"
                                   required>
                            <?php if (isset($errors['kode'])): ?>
                                <div class="invalid-feedback"><?= $errors['kode'] ?></div>
                            <?php else: ?>
                                <div class="form-text">Format: diawali "KAT-", panjang 4–10 karakter.</div>
                            <?php endif; ?>
                        </div>

                        <!-- Nama Kategori -->
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label fw-semibold">
                                Nama Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="nama_kategori"
                                   name="nama_kategori"
                                   class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($nama) ?>"
                                   placeholder="Contoh: Pemrograman"
                                   maxlength="50"
                                   required>
                            <?php if (isset($errors['nama'])): ?>
                                <div class="invalid-feedback"><?= $errors['nama'] ?></div>
                            <?php else: ?>
                                <div class="form-text">Minimal 3 karakter, maksimal 50 karakter.</div>
                            <?php endif; ?>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                            <textarea id="deskripsi"
                                      name="deskripsi"
                                      rows="3"
                                      class="form-control <?= isset($errors['deskripsi']) ? 'is-invalid' : '' ?>"
                                      placeholder="Keterangan singkat (opsional)"
                                      maxlength="200"><?= htmlspecialchars($deskripsi) ?></textarea>
                            <?php if (isset($errors['deskripsi'])): ?>
                                <div class="invalid-feedback"><?= $errors['deskripsi'] ?></div>
                            <?php else: ?>
                                <div class="form-text">Opsional. Maksimal 200 karakter.</div>
                            <?php endif; ?>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <?php if (isset($errors['status'])): ?>
                                <div class="text-danger small mb-1"><?= $errors['status'] ?></div>
                            <?php endif; ?>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusAktif"
                                           value="Aktif" <?= $status === 'Aktif' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="statusAktif">
                                        <span class="badge bg-success">Aktif</span>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusNonaktif"
                                           value="Nonaktif" <?= $status === 'Nonaktif' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="statusNonaktif">
                                        <span class="badge bg-danger">Nonaktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-save me-1"></i> Perbarui
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
