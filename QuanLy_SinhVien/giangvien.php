<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}
include 'config.php';

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// Lấy danh sách khoa
$khoa_res = $conn->query("SELECT * FROM khoa ORDER BY makhoa ASC");
$khoa_list = [];
if($khoa_res && $khoa_res->num_rows > 0){
    while($row = $khoa_res->fetch_assoc()){
        $khoa_list[] = $row;
    }
}

// Thêm giảng viên
if($action=='add' && $_SERVER['REQUEST_METHOD']=='POST'){
    $tengv = $_POST['tengv'];
    $makhoa = $_POST['makhoa'];
    $conn->query("INSERT INTO giangvien(tengv, makhoa) VALUES('$tengv', '$makhoa')");
    header("Location: giangvien.php");
    exit;
}

// Sửa giảng viên
if($action=='edit' && $id && $_SERVER['REQUEST_METHOD']=='POST'){
    $tengv = $_POST['tengv'];
    $makhoa = $_POST['makhoa'];
    $conn->query("UPDATE giangvien SET tengv='$tengv', makhoa='$makhoa' WHERE magv=$id");
    header("Location: giangvien.php");
    exit;
}

// Xóa giảng viên
if($action=='delete' && $id){
    $conn->query("DELETE FROM giangvien WHERE magv=$id");
    header("Location: giangvien.php");
    exit;
}

// Lấy thông tin giảng viên khi sửa
$edit_gv = null;
if($action=='edit' && $id){
    $res = $conn->query("SELECT * FROM giangvien WHERE magv=$id");
    $edit_gv = $res ? $res->fetch_assoc() : null;
}

// Lấy danh sách giảng viên kèm tên khoa
$res = $conn->query("
    SELECT gv.magv, gv.tengv, gv.makhoa, k.tenkhoa
    FROM giangvien gv
    LEFT JOIN khoa k ON gv.makhoa = k.makhoa
    ORDER BY gv.magv ASC
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản lý giảng viên</title>
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
    max-width:900px;
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

form input, form select { width:100%; padding:8px; margin-bottom:10px; border-radius:6px; border:1px solid #ccc; }
form button { padding:8px 15px; border:none; border-radius:6px; background:#C8102E; color:white; font-weight:bold; cursor:pointer; }
form button:hover { background:#a60b24; }

a.back-link { display:inline-block; margin-top:10px; text-decoration:none; color:#C8102E; font-weight:bold; }
a.back-link:hover { color:#a60b24; text-decoration:underline; }
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
<?php if($action=='add' || ($action=='edit' && $edit_gv)): ?>
    <h2><?= $action=='add' ? 'Thêm giảng viên' : 'Sửa giảng viên' ?></h2>
    <form method="POST">
        <label>Họ tên giảng viên</label>
        <input type="text" name="tengv" required value="<?= $edit_gv['tengv'] ?? '' ?>">

        <label>Chọn khoa</label>
        <select name="makhoa" required>
            <option value="">-- Chọn khoa --</option>
            <?php foreach($khoa_list as $khoa): ?>
                <option value="<?= $khoa['makhoa'] ?>" <?= isset($edit_gv['makhoa']) && $edit_gv['makhoa']==$khoa['makhoa'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($khoa['tenkhoa']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"><?= $action=='add' ? 'Thêm' : 'Cập nhật' ?></button>
    </form>
    <a href="giangvien.php" class="back-link">⬅ Quay lại danh sách</a>
<?php else: ?>
    <h2>Danh sách giảng viên</h2>
<a href="giangvien.php?action=add" class="btn-vlu btn-edit">+ Thêm giảng viên</a>
<table>
    <tr>
        <th>STT</th>
        <th>Họ tên</th>
        <th>Khoa</th>
        <th>Hành động</th>
    </tr>
    <?php if($res && $res->num_rows > 0): $i=1; ?>
        <?php while($row = $res->fetch_assoc()): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['tengv']) ?></td>
            <td>
                <?php if(!empty($row['tenkhoa'])): ?>
                    <span style="display:inline-block; padding:4px 10px; background:#C8102E; color:white; border-radius:6px; font-weight:bold;">
                        <?= htmlspecialchars($row['tenkhoa']) ?>
                    </span>
                <?php else: ?>
                    <span style="color:#555;">-</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="giangvien.php?action=edit&id=<?= $row['magv'] ?>" class="btn-vlu btn-edit">Sửa</a>
                <a href="giangvien.php?action=delete&id=<?= $row['magv'] ?>" class="btn-vlu btn-delete" onclick="return confirm('Xóa giảng viên này?')">Xóa</a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4">Chưa có giảng viên nào</td></tr>
    <?php endif; ?>
</table>

<?php endif; ?>
</div>

</body>
</html>
