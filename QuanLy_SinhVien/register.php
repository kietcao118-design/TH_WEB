<?php
session_start();
include 'config.php';

if(isset($_POST['register'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role_id = intval($_POST['role_id']);

    // Kiểm tra username đã tồn tại chưa
    $stmt_check = $conn->prepare("SELECT * FROM users WHERE username=?");
    if(!$stmt_check) die("Lỗi prepare: ".$conn->error);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    if($res_check->num_rows > 0){
        $error = "Tên đăng nhập đã tồn tại!";
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)");
        if(!$stmt_insert) die("Lỗi prepare: ".$conn->error);
        $stmt_insert->bind_param("ssi", $username, $password, $role_id);
        if($stmt_insert->execute()){
            $success = "Đăng ký thành công! <a href='login.php'>Đăng nhập ngay</a>";
        } else {
            $error = "Đăng ký thất bại!";
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
}

// Lấy danh sách roles
$roles_res = $conn->query("SELECT role_id, role_name FROM roles");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng ký tài khoản</title>
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

.header-vlu { 
    background-color: #C8102E; 
    color: white; 
    padding: 15px 20px; 
    font-weight:bold; 
    font-size:22px; 
    border-radius:0 0 12px 12px; 
    text-align:center;
}

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
input[type=text], input[type=password], select {
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
.text-danger, .text-success { text-align:center; margin-bottom:10px; font-weight:bold; }
.text-danger { color:red; }
.text-success { color:green; }
.login-link { text-align:center; margin-top:10px; }
.login-link a { text-decoration:none; color:#C8102E; font-weight:bold; }
.login-link a:hover { text-decoration:underline; }
</style>
</head>
<body>

<div class="header-vlu">
    🎓 Đăng ký tài khoản
</div>

<div class="card-vlu">
<h2>Thông tin đăng ký</h2>
<?php 
if(isset($error)) echo "<p class='text-danger'>$error</p>";
if(isset($success)) echo "<p class='text-success'>$success</p>";
?>
<form method="POST">
    <label>Tên đăng nhập</label>
    <input type="text" name="username" required autofocus>

    <label>Mật khẩu</label>
    <input type="password" name="password" required>

    <label>Chọn vai trò</label>
    <select name="role_id" required>
        <option value="">-- Chọn vai trò --</option>
        <?php if($roles_res && $roles_res->num_rows>0):
            while($role = $roles_res->fetch_assoc()): ?>
            <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
        <?php endwhile; endif; ?>
    </select>

    <button type="submit" name="register" class="btn-vlu-submit">Đăng ký</button>
</form>

<div class="login-link">
    <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
</div>
</div>

</body>
</html>
