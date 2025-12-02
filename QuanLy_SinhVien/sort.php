<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}
include 'config.php';

// Kiểu sắp xếp
$kieu = isset($_GET['kieu']) ? $_GET['kieu'] : 'hoten_asc';
$order = "";
switch($kieu){
    case "hoten_asc":  $order = "ORDER BY hoten ASC"; break;
    case "hoten_desc": $order = "ORDER BY hoten DESC"; break;
    case "diem_asc":   $order = "ORDER BY diem ASC"; break;
    case "diem_desc":  $order = "ORDER BY diem DESC"; break;
    case "id_asc":     $order = "ORDER BY id ASC"; break;
    case "id_desc":    $order = "ORDER BY id DESC"; break;
}

// Lấy dữ liệu
$sql = "SELECT sv.*, l.tenlop 
        FROM sinhvien sv
        LEFT JOIN lop l ON sv.malop = l.malop
        $order";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sắp xếp sinh viên</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
body {
    font-family: Arial, sans-serif;
    margin:0; padding:0;
    background-image: url('asset/anh.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-position: center;
    position: relative;
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
    max-width:900px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}
table { width:100%; border-collapse: collapse; margin-top:15px; }
th, td { border:1px solid #ccc; padding:8px; text-align:center; }
th { background-color:#C8102E; color:white; }
select {
    padding: 8px;
    border-radius: 6px;
    margin-bottom: 15px;
}
.btn-vlu {
    padding:4px 10px; 
    border-radius:6px; 
    font-size:14px; 
    font-weight:bold; 
    text-decoration:none; 
    color:white; 
    display:inline-block;
}
.btn-edit { background:#007bff; }
.btn-edit:hover { background:#0056b3; }
.btn-delete { background:#dc3545; }
.btn-delete:hover { background:#a71d2a; }
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
<h2>Sắp xếp sinh viên</h2>

<form method="GET">
    <label>Chọn kiểu sắp xếp:</label>
    <select name="kieu" onchange="this.form.submit()">
        <option value="hoten_asc"  <?= $kieu=="hoten_asc"?"selected":"" ?>>Tên A → Z</option>
        <option value="hoten_desc" <?= $kieu=="hoten_desc"?"selected":"" ?>>Tên Z → A</option>
        <option value="diem_asc"   <?= $kieu=="diem_asc"?"selected":"" ?>>Điểm tăng dần</option>
        <option value="diem_desc"  <?= $kieu=="diem_desc"?"selected":"" ?>>Điểm giảm dần</option>
        <option value="id_asc"     <?= $kieu=="id_asc"?"selected":"" ?>>ID tăng dần</option>
        <option value="id_desc"    <?= $kieu=="id_desc"?"selected":"" ?>>ID giảm dần</option>
    </select>
</form>

<table>
    <tr>
        <th>STT</th>
        <th>Mã SV</th>
        <th>Họ Tên</th>
        <th>Lớp</th>
        <th>Điểm</th>
        <th>Hành động</th>
    </tr>
    <?php if($result && $result->num_rows > 0): ?>
        <?php $i=1; while($row=$result->fetch_assoc()): ?>
        <tr>
            <td><?= $i++; ?></td>
            <td><?= htmlspecialchars($row['masv']) ?></td>
            <td><?= htmlspecialchars($row['hoten']) ?></td>
            <td><?= htmlspecialchars($row['tenlop'] ?? '-') ?></td>
            <td><?= $row['diem'] ?? '-' ?></td>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn-vlu btn-edit">Sửa</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn-vlu btn-delete" onclick="return confirm('Bạn có chắc muốn xóa sinh viên này?')">Xóa</a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6">Chưa có sinh viên nào</td></tr>
    <?php endif; ?>
</table>
</div>

</body>
</html>
