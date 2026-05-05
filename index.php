<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori - UTS Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 2px 8px rgba(0,0,0,.08); border: none; }
        .table thead th { background-color: #0d6efd; color: #fff; border: none; }
        .badge-status { font-size: .8rem; padding: .35em .7em; }
    </style>
</head>
<body>
<?php
require_once 'config/database.php';

// Tampilkan pesan sukses / error dari session-like GET 
$pesan_sukses = isset($_GET['pesan']) ? htmlspecialchars($_GET['pesan']) : '';
$pesan_error  = isset($_GET['error']) ? htmlspecialchars($_GET['error'])  : '';

// Query semua kategori (prepared statement, ORDER BY terbaru di atas) 
$stmt = $conn->prepare("SELECT id_kategori, kode_kategori, nama_kategori, deskripsi, status, created_at
                         FROM kategori
                         ORDER BY id_kategori DESC");
if (!$stmt) {
    die("Prepare gagal: " . $conn->error);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-journal-bookmark-fill text-primary me-2"></i>Daftar Kategori Buku</h2>
        <a href="create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
        </a>
    </div>

    <?php if ($pesan_sukses): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> <?= $pesan_sukses ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($pesan_error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i> <?= $pesan_error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="50"  class="ps-3">No</th>
                        <th width="110">Kode</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th width="110" class="text-center">Status</th>
                        <th width="160" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows === 0):
                ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                            Belum ada data kategori.
                        </td>
                    </tr>
                <?php
                else:
                    $no = 1;
                    while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td class="ps-3"><?= $no++ ?></td>
                        <td><code><?= htmlspecialchars($row['kode_kategori']) ?></code></td>
                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td class="text-muted" style="max-width:260px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            <?= htmlspecialchars($row['deskripsi'] ?? '-') ?>
                        </td>
                        <td class="text-center">
                            <?php if ($row['status'] === 'Aktif'): ?>
                                <span class="badge bg-success badge-status">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger badge-status">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="edit.php?id=<?= (int)$row['id_kategori'] ?>"
                               class="btn btn-warning btn-sm me-1" title="Edit">
                                <i class="bi bi-pencil-fill"></i> Edit
                            </a>
                            <button type="button"
                                    class="btn btn-danger btn-sm"
                                    title="Hapus"
                                    onclick="confirmDelete(<?= (int)$row['id_kategori'] ?>)">
                                <i class="bi bi-trash-fill"></i> Hapus
                            </button>
                        </td>
                    </tr>
                <?php
                    endwhile;
                endif;
                $stmt->close();
                ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function confirmDelete(id) {
    if (confirm('Yakin ingin menghapus kategori ini?\nData yang dihapus tidak dapat dikembalikan.')) {
        window.location.href = 'delete.php?id=' + id;
    }
}

</script>
</body>
</html>
