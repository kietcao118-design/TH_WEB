<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}
include 'config.php';

$loi = "";
if(isset($_POST['add'])){
    $masv = trim($_POST['masv']);
    $hoten = trim($_POST['hoten']);
    $malop = intval($_POST['malop']);
    $diem = floatval($_POST['diem']);

    $stmt = $conn->prepare("INSERT INTO sinhvien (masv, hoten, malop, diem) VALUES (?,?,?,?)");
    $stmt->bind_param("ssid", $masv, $hoten, $malop, $diem);

    if($stmt->execute()){
        header("Location:index.php");
        exit;
    } else {
        $loi = "Lỗi thêm sinh viên!";
    }
}

// Lấy danh sách lớp
$lop_res = $conn->query("SELECT malop, tenlop FROM lop ORDER BY tenlop ASC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm sinh viên</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
body {
    font-family: Arial,sans-serif;
    margin:0; padding:0;
    background-image: url('asset/anh.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-position: center;
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
    background:#C8102E;
    color:white;
    padding:15px 20px;
    font-weight:bold;
    font-size:22px;
    border-radius:0 0 12px 12px;
}
.menu-container { background:white; padding:10px 0; }
.menu { list-style:none; display:flex; justify-content:center; gap:20px; padding:0; margin:0; }
.menu li a { text-decoration:none; color:#C8102E; font-weight:bold; }
.menu li a:hover { color:#a60b24; text-decoration:underline; }
.card-vlu {
    background: rgba(255,255,255,0.9);
    padding:25px;
    border-radius:12px;
    width:400px;
    margin:30px auto;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}
input, select {
    width:100%;
    padding:8px;
    margin-bottom:15px;
    border-radius:6px;
    border:1px solid #ccc;
}
.btn-vlu-submit {
    width:100%;
    padding:10px;
    background:#C8102E;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-weight:bold;
}
.btn-vlu-submit:hover { background:#a60b24; }
.text-danger { color:red; text-align:center; margin-bottom:10px; }
</style>
</head>
<body>

<div class="header-vlu">🎓 Chào, <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)</div>

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
<h2>Thêm sinh viên</h2>

<?php if($loi) echo "<p class='text-danger'>$loi</p>"; ?>

<form method="POST">
    <label>Mã SV</label>
    <input type="text" name="masv" required>

    <label>Họ Tên</label>
    <input type="text" name="hoten" required>

    <label>Lớp</label>
    <select name="malop" required>
        <option value="">-- Chọn lớp --</option>
        <?php while($l=$lop_res->fetch_assoc()): ?>
            <option value="<?= $l['malop'] ?>"><?= htmlspecialchars($l['tenlop']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Điểm</label>
    <input type="number" name="diem" step="0.1" min="0" max="10" required>

    <button type="submit" name="add" class="btn-vlu-submit">Thêm</button>
</form>

<a href="index.php">⬅ Quay lại</a>

</div>

</body>
</html>
