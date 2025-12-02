<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}
include 'config.php';

// Tổng số sinh viên
$total_res = $conn->query("SELECT COUNT(*) AS total FROM sinhvien");
$total = $total_res ? $total_res->fetch_assoc()['total'] : 0;

// Điểm trung bình
$avg_res = $conn->query("SELECT AVG(diem) AS avg_diem FROM sinhvien");
$avg_diem = $avg_res ? round($avg_res->fetch_assoc()['avg_diem'],2) : 0;

// Sinh viên điểm cao nhất
$max_res = $conn->query("SELECT hoten, diem FROM sinhvien ORDER BY diem DESC LIMIT 1");
$max_sv = $max_res && $max_res->num_rows > 0 ? $max_res->fetch_assoc() : null;

// Sinh viên điểm thấp nhất
$min_res = $conn->query("SELECT hoten, diem FROM sinhvien ORDER BY diem ASC LIMIT 1");
$min_sv = $min_res && $min_res->num_rows > 0 ? $min_res->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thống kê sinh viên</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
body {
    font-family: Arial, sans-serif;
    background-image: url('asset/anh.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-position: center;
    margin:0; padding:0;
    position:relative;
}
body::before {
    content:"";
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(255,255,255,0.3);
    z-index:-1;
}

.header-vlu { 
    background-color: #C8102E; 
    color: white; 
    padding: 15px 20px; 
    font-weight:bold; 
    font-size:22px; 
    border-radius:0 0 12px 12px; 
    display:flex; 
    justify-content:space-between; 
    align-items:center; 
}

.menu-container {
    background:white;
    padding:10px 0;
}
.menu { 
    list-style:none; 
    margin:0; 
    padding:0; 
    display:flex; 
    justify-content:center; 
    gap:20px; 
}
.menu li a { 
    text-decoration:none; 
    color:#C8102E; 
    font-weight:bold; 
    transition:0.3s; 
}
.menu li a:hover { 
    color:#a60b24; 
    text-decoration:underline; 
}

.card-vlu {
    background: rgba(255,255,255,0.9);
    padding:25px;
    border-radius:12px;
    margin:30px auto;
    width:90%;
    max-width:700px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}

.stat-item {
    font-size:18px;
    margin-bottom:12px;
}

a.back-link {
    display:inline-block;
    margin-top:15px;
    text-decoration:none;
    color:#C8102E;
    font-weight:bold;
}
a.back-link:hover {
    color:#a60b24;
    text-decoration:underline;
}
</style>
</head>
<body>

<div class="header-vlu">
    🎓 Chào, <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)
</div>

<div class="menu-container">
    <ul class="menu">
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="giangvien.php">Giảng viên</a></li>
        <li><a href="sinhvien_monhoc.php">Sinh Viên-Môn Học</a></li>
        <li><a href="monhoc.php">Môn học</a></li>
        <li><a href="phonghoc.php">Phòng học</a></li>
        <li><a href="add.php">Thêm SV</a></li>
        <li><a href="tkb.php">Thời khóa biểu</a></li>
        <li><a href="sort.php">Sắp xếp</a></li>
        <li><a href="stats.php">Thống kê</a></li>
        <li><a href="logout.php">Đăng xuất</a></li>
    </ul>
</div>



<div class="card-vlu">
    <h2>Thống kê sinh viên</h2>
    <p class="stat-item">Tổng số sinh viên: <strong><?= $total ?></strong></p>
    <p class="stat-item">Điểm trung bình: <strong><?= $avg_diem ?></strong></p>
    <p class="stat-item">Sinh viên điểm cao nhất: 
        <strong><?= $max_sv ? htmlspecialchars($max_sv['hoten'])." ({$max_sv['diem']})" : '-' ?></strong>
    </p>
    <p class="stat-item">Sinh viên điểm thấp nhất: 
        <strong><?= $min_sv ? htmlspecialchars($min_sv['hoten'])." ({$min_sv['diem']})" : '-' ?></strong>
    </p>
    <a href="index.php" class="back-link">⬅ Quay lại</a>
</div>

</body>
</html>
