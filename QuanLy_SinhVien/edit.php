<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

include 'config.php';

// Lấy ID sinh viên
$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM sinhvien WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();
if(!$row) die("Sinh viên không tồn tại!");

// Lấy danh sách lớp
$lop_res = $conn->query("SELECT malop, tenlop FROM lop");
$lop_list = $lop_res ? $lop_res->fetch_all(MYSQLI_ASSOC) : [];

if(isset($_POST['update'])){
    $masv = htmlspecialchars(trim($_POST['masv']));
    $hoten = htmlspecialchars(trim($_POST['hoten']));
    $malop = intval($_POST['malop']);
    $diem = floatval($_POST['diem']);

    // Kiểm tra lớp tồn tại
    $stmt_check = $conn->prepare("SELECT malop FROM lop WHERE malop=?");
    $stmt_check->bind_param("i", $malop);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    if($res_check->num_rows == 0){
        $error = "Lớp không hợp lệ!";
    } else {
        $stmt_check->close();
        $stmt = $conn->prepare("UPDATE sinhvien SET masv=?, hoten=?, malop=?, diem=? WHERE id=?");
        $stmt->bind_param("sssdi", $masv, $hoten, $malop, $diem, $id);
        if($stmt->execute()){
            $stmt->close();
            header("Location: index.php");
            exit;
        } else {
            $error = "Lỗi: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa sinh viên - Văn Lang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .menu {
            list-style:none; 
            margin:0; 
            padding:0; 
            display:flex; 
            gap:15px;
        }
        .menu li { display:inline; }
        .menu li a { text-decoration:none; color:white; font-weight:bold; transition:0.3s; }
        .menu li a:hover { text-decoration:underline; color:#fffccc; }

        .card-vlu {
            background: rgba(255,255,255,0.9); 
            padding:25px; 
            border-radius:12px; 
            margin:30px auto; 
            width:90%; 
            max-width:500px; 
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        .btn-vlu-primary {
            background-color:#C8102E; 
            color:white; 
            border:none; 
            padding:10px; 
            border-radius:6px; 
            font-weight:bold; 
            cursor:pointer; 
            width:100%;
        }
        .btn-vlu-primary:hover { background-color:#a60b24; }

        .btn-secondary { 
            display:block; 
            width:100%; 
            padding:10px; 
            text-align:center; 
            background:#ccc; 
            border-radius:6px; 
            text-decoration:none; 
            color:#000; 
            margin-top:10px;
        }
        .btn-secondary:hover { background:#bbb; }
    </style>
</head>
<body>

<!-- HEADER + MENU -->
<div class="header-vlu">
    <div>🎓 Quản lý sinh viên</div>
    <ul class="menu">
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="add.php">Thêm sinh viên</a></li>
        <li><a href="sort.php">Sắp xếp</a></li>
        <li><a href="stats.php">Thống kê</a></li>
        <li><a href="logout.php">Đăng xuất</a></li>
    </ul>
</div>

<!-- FORM SỬA SINH VIÊN -->
<div class="card-vlu">
    <h2>Sửa sinh viên</h2>
    <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Mã sinh viên</label>
            <input type="text" name="masv" class="form-control" required value="<?= htmlspecialchars($row['masv']) ?>">
        </div>
        <div class="mb-3">
            <label>Họ Tên</label>
            <input type="text" name="hoten" class="form-control" required value="<?= htmlspecialchars($row['hoten']) ?>">
        </div>
        <div class="mb-3">
            <label>Lớp</label>
            <select name="malop" class="form-control" required>
                <option value="">-- Chọn lớp --</option>
                <?php foreach($lop_list as $lop): ?>
                    <option value="<?= $lop['malop'] ?>" <?= $lop['malop']==$row['malop']?'selected':'' ?>><?= $lop['tenlop'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Điểm</label>
            <input type="number" step="0.01" name="diem" class="form-control" required value="<?= $row['diem'] ?>">
        </div>
        <button type="submit" name="update" class="btn btn-vlu-primary">Cập nhật</button>
        <a href="index.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

</body>
</html>
