<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}
include 'config.php';

// XÓA
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM sinhvien_monhoc WHERE id = $id");
    header("Location: sinhvien_monhoc.php");
    exit;
}

// THÊM
if (isset($_POST['add'])) {
    $sv = $_POST['sinhvien_id'];
    $mh = $_POST['monhoc_id'];
    $diem = $_POST['diem'];

    $conn->query("INSERT INTO sinhvien_monhoc(sinhvien_id, monhoc_id, diem)
                  VALUES('$sv', '$mh', '$diem')");
    header("Location: sinhvien_monhoc.php");
    exit;
}

// SỬA
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $sv = $_POST['sinhvien_id'];
    $mh = $_POST['monhoc_id'];
    $diem = $_POST['diem'];

    $conn->query("UPDATE sinhvien_monhoc 
                  SET sinhvien_id='$sv', monhoc_id='$mh', diem='$diem' 
                  WHERE id=$id");
    header("Location: sinhvien_monhoc.php");
    exit;
}

// LẤY DỮ LIỆU
$res = $conn->query("
    SELECT sm.id, sv.hoten AS tensv, mh.tenmon, sm.diem
    FROM sinhvien_monhoc sm
    JOIN sinhvien sv ON sm.sinhvien_id = sv.id
    JOIN monhoc mh ON sm.monhoc_id = mh.mamon
");

// Danh sách sinh viên & môn học
$sv_list = $conn->query("SELECT id, hoten FROM sinhvien ORDER BY hoten ASC");
$mh_list = $conn->query("SELECT mamon, tenmon FROM monhoc ORDER BY tenmon ASC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sinh viên - Môn học</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>
body {
    font-family: Arial, sans-serif;
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
    width:95%;
    max-width:1000px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}

table { width:100%; border-collapse: collapse; margin-top:15px; }
th, td { border:1px solid #ccc; padding:8px; text-align:center; }
th { background-color:#C8102E; color:white; }

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
.btn-add { background:#198754; }
.btn-add:hover { background:#0f5a36; }
</style>
</head>

<body>

<!-- HEADER -->
<div class="header-vlu">
    📚 Quản lý Sinh viên - Môn học
    <span>👤 <?= $_SESSION['username'] ?> (<?= $_SESSION['role'] ?>)</span>
</div>

<!-- MENU -->
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


<!-- CONTENT -->
<div class="card-vlu">
    <h2>Sinh viên - Môn học</h2>

    <!-- FORM -->
    <form method="POST" class="row g-3 mt-2">
        <input type="hidden" name="id" id="edit_id">

        <div class="col-md-4">
            <label>Sinh viên</label>
            <select name="sinhvien_id" id="edit_sv" class="form-control" required>
                <option value="">-- Chọn --</option>
                <?php while($sv = $sv_list->fetch_assoc()): ?>
                    <option value="<?= $sv['id'] ?>"><?= $sv['hoten'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label>Môn học</label>
            <select name="monhoc_id" id="edit_mh" class="form-control" required>
                <option value="">-- Chọn --</option>
                <?php while($mh = $mh_list->fetch_assoc()): ?>
                    <option value="<?= $mh['mamon'] ?>"><?= $mh['tenmon'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label>Điểm</label>
            <input type="number" step="0.1" name="diem" id="edit_diem" class="form-control" required>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button name="add" id="btnAdd" class="btn-vlu btn-add w-100">Thêm</button>
            <button name="update" id="btnUpdate" class="btn-vlu btn-edit w-100 d-none">Cập nhật</button>
        </div>
    </form>

    <!-- TABLE -->
    <table class="mt-4">
        <tr>
            <th>STT</th>
            <th>Sinh viên</th>
            <th>Môn học</th>
            <th>Điểm</th>
            <th>Hành động</th>
        </tr>

        <?php 
        $i = 1;
        while($row = $res->fetch_assoc()): 
        ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= $row['tensv'] ?></td>
                <td><?= $row['tenmon'] ?></td>
                <td><?= $row['diem'] ?></td>
                <td>
                    <button class="btn-vlu btn-edit"
                        onclick="editItem(<?= $row['id'] ?>, <?= $row['diem'] ?>)">
                        Sửa
                    </button>

                    <a href="?delete=<?= $row['id'] ?>" 
                       onclick="return confirm('Xóa mục này?')" 
                       class="btn-vlu btn-delete">Xóa</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
function editItem(id, diem) {
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_diem").value = diem;

    document.getElementById("btnAdd").classList.add("d-none");
    document.getElementById("btnUpdate").classList.remove("d-none");
}
</script>

</body>
</html>
