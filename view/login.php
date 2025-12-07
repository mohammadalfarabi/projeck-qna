<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

<style>
.auth-container {
    background: #FFE6E6;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    margin-top: 25px;
    font-family: 'Segoe UI', sans-serif;
    margin-top: 35px;
}

.auth-container h2 {
    font-size: 25px;
    margin-bottom: 10px;
    color: #000000ff;
    text-align: center;
}

input, select {
    background: #DAEAF1;
}

.auth-container button {
    background: #C6DCE4;
    border-radius: 3px;
    border: 1px solid black;
    color: black;
    padding: 5px 12px;
    cursor: pointer;
}

.auth-container button:hover {
    background: #8da5adff;
}

</style>

</head>
<body>
<?php include 'header.php'; ?>
<div class="auth-container">
    <h2>Login</h2>
    <form action="../controller/auth_controller.php" method="POST">
        <input type="hidden" name="action" value="login">
        
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label>Role:</label>
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>
        
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account?<a href="../controller/auth_controller.php?action=register">Register in here</a></p>
</div>
<?php include 'footer.php'; ?>
</body>
</html>