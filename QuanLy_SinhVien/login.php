<?php
session_start();
include 'config.php';

if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT u.user_id,u.username,u.password,r.role_name 
                            FROM users u 
                            JOIN roles r ON u.role_id=r.role_id 
                            WHERE u.username=?");
    if(!$stmt) die("Lỗi prepare: ".$conn->error);
    
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows==1){
        $row = $res->fetch_assoc();
        if($password === $row['password']){
            $_SESSION['username']=$username;
            $_SESSION['role']=$row['role_name'];
            header("Location:index.php"); exit;
        } else $error="Sai mật khẩu!";
    } else $error="Người dùng không tồn tại!";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập hệ thống</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-image: url('asset/anh.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    margin:0; padding:0;
}
body::before {
    content:"";
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(255,255,255,0.3);
    z-index:-1;
}

/* Header đỏ chỉ ghi "Đăng nhập hệ thống" */
.header-vlu { 
    background-color: #C8102E; 
    color: white; 
    padding: 15px 20px; 
    font-weight:bold; 
    font-size:22px; 
    border-radius:0 0 12px 12px; 
    text-align:center;
}

/* Form login */
.card-vlu {
    background: rgba(255,255,255,0.95);
    padding: 30px;
    border-radius: 12px;
    max-width: 400px;
    margin: 40px auto;
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
}
.card-vlu h2 { 
    text-align:center; 
    color:#333; 
    margin-bottom:20px; 
}
input[type=text], input[type=password] {
    width:100%; padding:10px; margin-bottom:15px; border-radius:6px; border:1px solid #ccc;
    font-size:14px; color:#333;
}
.btn-vlu-submit {
    width:100%; 
    background:#C8102E; 
    color:white; 
    border:none; 
    padding:10px; 
    border-radius:6px; 
    font-weight:bold; 
    cursor:pointer;
}
.btn-vlu-submit:hover { background:#a60b24; }
.text-danger { color:red; text-align:center; margin-bottom:10px; font-weight:bold; }
.register-link { text-align:center; margin-top:10px; }
.register-link a { text-decoration:none; color:#C8102E; font-weight:bold; }
.register-link a:hover { text-decoration:underline; }
</style>
</head>
<body>

<div class="header-vlu">
    🎓 Đăng nhập hệ thống
</div>

<div class="card-vlu">
<h2>Thông tin đăng nhập</h2>
<?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
<form method="POST">
    <label>Tên đăng nhập</label>
    <input type="text" name="username" required autofocus>

    <label>Mật khẩu</label>
    <input type="password" name="password" required>

    <button type="submit" name="login" class="btn-vlu-submit">Đăng nhập</button>
</form>
<div class="register-link">
    <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
</div>
</div>

</body>
</html>
