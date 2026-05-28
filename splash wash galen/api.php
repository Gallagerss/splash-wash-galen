<?php
header('Content-Type: application/json; charset=utf-8');

 $host = 'localhost';
 $dbname = 'splashwash_db';
 $user = 'root';
 $pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database gagal: ' . $e->getMessage()]);
    exit;
}

 $action = isset($_GET['action']) ? $_GET['action'] : '';
 $method = $_SERVER['REQUEST_METHOD'];

if ($action === 'get_kendaraan') {
    $stmt = $pdo->query("SELECT * FROM kendaraan ORDER BY id");
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

if ($action === 'get_layanan') {
    if (isset($_GET['jenis']) && $_GET['jenis'] !== '') {
        $stmt = $pdo->prepare("SELECT * FROM layanan WHERE jenis = ? ORDER BY id");
        $stmt->execute([$_GET['jenis']]);
    } else {
        $stmt = $pdo->query("SELECT * FROM layanan ORDER BY id");
    }
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

if ($action === 'get_pesanan') {
    $stmt = $pdo->query("
        SELECT p.id, p.nama_customer, p.plat_nomor, p.total_bayar, p.status, p.created_at,
               l.nama AS layanan_nama, l.jenis, l.durasi_menit
        FROM pesanan p
        INNER JOIN layanan l ON p.layanan_id = l.id
        ORDER BY p.id DESC
    ");
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

if ($action === 'buat_pesanan' && $method === 'POST') {
    $layananId = (int)$_POST['layanan_id'];
    $nama = trim($_POST['nama_customer']);
    $plat = strtoupper(trim($_POST['plat_nomor']));

    if ($layananId < 1 || $nama === '' || $plat === '') {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
        exit;
    }

    $cek = $pdo->prepare("SELECT harga FROM layanan WHERE id = ?");
    $cek->execute([$layananId]);
    $row = $cek->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Layanan tidak ditemukan']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO pesanan (layanan_id, nama_customer, plat_nomor, total_bayar) VALUES (?, ?, ?, ?)");
    $stmt->execute([$layananId, $nama, $plat, $row['harga']]);

    echo json_encode(['success' => true, 'message' => 'Pesanan berhasil']);
    exit;
}

if ($action === 'selesaikan' && $method === 'POST') {
    $id = (int)$_POST['id'];
    if ($id < 1) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE pesanan SET status = 'SELESAI' WHERE id = ? AND status = 'PROSES'");
    $stmt->execute([$id]);
    echo json_encode($stmt->rowCount() > 0
        ? ['success' => true, 'message' => 'Selesai']
        : ['success' => false, 'message' => 'Gagal']
    );
    exit;
}

if ($action === 'hapus' && $method === 'POST') {
    $id = (int)$_POST['id'];
    if ($id < 1) {
        echo json_encode(['success' => false, 'message' => 'ID tidak valid']);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM pesanan WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode($stmt->rowCount() > 0
        ? ['success' => true, 'message' => 'Dihapus']
        : ['success' => false, 'message' => 'Gagal']
    );
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action salah']);