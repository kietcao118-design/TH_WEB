<?php
session_start();
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit;
}

include 'config.php';

if(!isset($_GET['id'])){
    die("Không tìm thấy ID để xóa!");
}

$id = intval($_GET['id']);

// Xóa các bản ghi liên quan (nếu có bảng sinhvien_monhoc)
$stmt_rel = $conn->prepare("DELETE FROM sinhvien_monhoc WHERE masv=?");
if($stmt_rel){
    $stmt_rel->bind_param("i", $id);
    $stmt_rel->execute();
    $stmt_rel->close();
}

// Xóa sinh viên
$stmt = $conn->prepare("DELETE FROM sinhvien WHERE id=?");
if(!$stmt){
    die("Lỗi chuẩn bị câu lệnh DELETE: " . $conn->error);
}

$stmt->bind_param("i", $id);
if(!$stmt->execute()){
    die("Lỗi khi xóa sinh viên: " . $stmt->error);
}

$stmt->close();
header("Location: index.php");
exit;
?>
