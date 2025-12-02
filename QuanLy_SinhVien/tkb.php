<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}
include 'config.php';

// ----- Hàm thêm dữ liệu mẫu nếu bảng trống -----
function ensureSampleData($conn) {
    // Bảng lớp
    $res = $conn->query("SELECT COUNT(*) as cnt FROM lop");
    if($res->fetch_assoc()['cnt'] == 0){
        $conn->query("INSERT INTO lop(tenlop) VALUES ('CNTT1'), ('CNTT2')");
    }

    // Bảng môn học
    $res = $conn->query("SELECT COUNT(*) as cnt FROM monhoc");
    if($res->fetch_assoc()['cnt'] == 0){
        $conn->query("INSERT INTO monhoc(tenmon) VALUES ('Toán'), ('Lập trình PHP')");
    }

    // Bảng khoa
    $res = $conn->query("SELECT COUNT(*) as cnt FROM khoa");
    if($res->fetch_assoc()['cnt'] == 0){
        $conn->query("INSERT INTO khoa(tenkhoa) VALUES ('CNTT'), ('Điện tử')");
    }

    // Bảng giảng viên
    $res = $conn->query("SELECT COUNT(*) as cnt FROM giangvien");
    if($res->fetch_assoc()['cnt'] == 0){
        $conn->query("INSERT INTO giangvien(tengv, makhoa) VALUES ('Nguyễn Văn A',1), ('Trần Thị B',2)");
    }

    // Bảng phòng học
    $res = $conn->query("SELECT COUNT(*) as cnt FROM phonghoc");
    if($res->fetch_assoc()['cnt'] == 0){
        $conn->query("INSERT INTO phonghoc(tenphong) VALUES ('A101'), ('B202')");
    }
}

ensureSampleData($conn);

// ----- Xử lý thêm TKB -----
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $malop = $_POST['malop'];
    $mamon = $_POST['mamon'];
    $magv = $_POST['magv'];
    $maphong = $_POST['maphong'];
    $thu = $_POST['thu'];
    $tiet_bd = $_POST['tiet_bd'];
    $so_tiet = $_POST['so_tiet'];

    $sql = "INSERT INTO thoikhoabieu(malop,mamon,magv,maphong,thu,tiet_bd,so_tiet)
            VALUES('$malop','$mamon','$magv','$maphong','$thu','$tiet_bd','$so_tiet')";
    if($conn->query($sql)){
        header("Location: tkb.php");
        exit;
    } else {
        echo "<p style='color:red;'>Lỗi khi thêm TKB: " . $conn->error . "</p>";
    }
}

// ----- Lấy dữ liệu dropdown -----
$lop_res = $conn->query("SELECT * FROM lop ORDER BY malop ASC");
$monhoc_res = $conn->query("SELECT * FROM monhoc ORDER BY tenmon ASC");
$gv_res = $conn->query("SELECT * FROM giangvien ORDER BY tengv ASC");
$phong_res = $conn->query("SELECT * FROM phonghoc ORDER BY tenphong ASC");

// ----- Lấy danh sách TKB -----
$res = $conn->query("
    SELECT tkb.id, l.tenlop, m.tenmon, gv.tengv, p.tenphong, tkb.thu, tkb.tiet_bd, tkb.so_tiet
    FROM thoikhoabieu tkb
    LEFT JOIN lop l ON tkb.malop = l.malop
    LEFT JOIN monhoc m ON tkb.mamon = m.mamon
    LEFT JOIN giangvien gv ON tkb.magv = gv.magv
    LEFT JOIN phonghoc p ON tkb.maphong = p.maphong
    ORDER BY tkb.id ASC
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thời khóa biểu</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
body { font-family: Arial, sans-serif; background-image: url('asset/anh.jpg'); background-size: cover; background-attachment: fixed; margin:0; padding:0; }
body::before { content:""; position:fixed; top:0; left:0; width:100%; height:100%; background: rgba(255,255,255,0.3); z-index:-1; }
.header-vlu { background-color: #C8102E; color:white; padding:15px 20px; font-weight:bold; font-size:22px; border-radius:0 0 12px 12px; display:flex; justify-content:space-between; align-items:center; }
.menu-container { background:white; padding:10px 0; }
.menu { list-style:none; margin:0; padding:0; display:flex; justify-content:center; gap:20px; }
.menu li a { text-decoration:none; color:#C8102E; font-weight:bold; transition:0.3s; }
.menu li a:hover { color:#a60b24; text-decoration:underline; }
.card-vlu { background: rgba(255,255,255,0.9); padding:25px; border-radius:12px; margin:30px auto; width:95%; max-width:1000px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
table { width:100%; border-collapse: collapse; margin-top:15px; }
th, td { border:1px solid #ccc; padding:8px; text-align:center; }
th { background-color:#C8102E; color:white; }
form select, form input { width:100%; padding:6px; margin-bottom:10px; border-radius:6px; border:1px solid #ccc; }
form button { padding:8px 15px; border:none; border-radius:6px; background:#C8102E; color:white; font-weight:bold; cursor:pointer; }
form button:hover { background:#a60b24; }
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

<!-- ===== Danh sách TKB lên đầu ===== -->
<h2>Danh sách Thời khóa biểu</h2>
<table>
    <tr>
        <th>STT</th>
        <th>Lớp</th>
        <th>Môn học</th>
        <th>Giảng viên</th>
        <th>Phòng</th>
        <th>Thứ</th>
        <th>Tiết bắt đầu</th>
        <th>Số tiết</th>
    </tr>
    <?php if($res && $res->num_rows>0): $i=1; ?>
        <?php while($row=$res->fetch_assoc()): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['tenlop']) ?></td>
            <td><?= htmlspecialchars($row['tenmon']) ?></td>
            <td><?= htmlspecialchars($row['tengv']) ?></td>
            <td><?= htmlspecialchars($row['tenphong']) ?></td>
            <td><?= htmlspecialchars($row['thu']) ?></td>
            <td><?= $row['tiet_bd'] ?></td>
            <td><?= $row['so_tiet'] ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8">Chưa có thời khóa biểu nào</td></tr>
    <?php endif; ?>
</table>

<!-- ===== Form thêm TKB ===== -->
<h2>Thêm thời khóa biểu</h2>
<form method="POST">
    <label>Lớp</label>
    <select name="malop" required>
        <option value="">-- Chọn lớp --</option>
        <?php while($lop=$lop_res->fetch_assoc()): ?>
            <option value="<?= $lop['malop'] ?>"><?= htmlspecialchars($lop['tenlop']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Môn học</label>
    <select name="mamon" required>
        <option value="">-- Chọn môn học --</option>
        <?php while($m=$monhoc_res->fetch_assoc()): ?>
            <option value="<?= $m['mamon'] ?>"><?= htmlspecialchars($m['tenmon']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Giảng viên</label>
    <select name="magv" required>
        <option value="">-- Chọn giảng viên --</option>
        <?php while($gv=$gv_res->fetch_assoc()): ?>
            <option value="<?= $gv['magv'] ?>"><?= htmlspecialchars($gv['tengv']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Phòng học</label>
    <select name="maphong" required>
        <option value="">-- Chọn phòng --</option>
        <?php while($p=$phong_res->fetch_assoc()): ?>
            <option value="<?= $p['maphong'] ?>"><?= htmlspecialchars($p['tenphong']) ?></option>
        <?php endwhile; ?>
    </select>

    <label>Thứ</label>
    <select name="thu" required>
        <option value="">-- Chọn thứ --</option>
        <option value="Hai">Hai</option>
        <option value="Ba">Ba</option>
        <option value="Tư">Tư</option>
        <option value="Năm">Năm</option>
        <option value="Sáu">Sáu</option>
        <option value="Bảy">Bảy</option>
    </select>

    <label>Tiết bắt đầu</label>
    <input type="number" name="tiet_bd" min="1" max="10" required>

    <label>Số tiết</label>
    <input type="number" name="so_tiet" min="1" max="10" required>

    <button type="submit">Thêm</button>
</form>

</div>


</body>
</html>
