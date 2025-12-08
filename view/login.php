<style>
/* ==== Background Grid ==== */
body {
    background: #FFE6E6;
    margin: 0;
    padding: 0;
    font-family: "Poppins", sans-serif;
    background-image: 
        linear-gradient(rgba(255, 255, 255, 0.25) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.25) 1px, transparent 1px);
    background-size: 35px 35px;
}

/* ==== Fade-in Animasi ==== */
.fade-in {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInEase 0.8s ease-out forwards;
}
@keyframes fadeInEase {
    to { opacity: 1; transform: translateY(0); }
}

/* ==== Card Login ==== */
.auth-container {
    width: 380px;
    background: #DAEAF1;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 6px 25px rgba(198, 220, 228, 0.6);
    margin: 35px auto;
    font-family: 'Segoe UI', sans-serif;
}

.auth-container h2 {
    font-size: 25px;
    margin-bottom: 15px;
    color: #000;
    text-align: center;
}

/* ==== Input & Select ==== */
.form-group { margin-bottom: 18px; }

.form-group label {
    display: block;
    font-size: 14px;
    color: #6a6a6a;
    margin-bottom: 6px;
    font-weight: 500;
}

.auth-container input,
.auth-container select {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #C6DCE4;
    border-radius: 8px;
    background: #FAFAFA;
    transition: all 0.35s ease;
}

.auth-container input:hover,
.auth-container select:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(198, 220, 228, 0.4);
    background: #fff;
}

.auth-container input:focus,
.auth-container select:focus {
    border-color: #F2D1D1;
    transform: translateY(-3px);
    box-shadow: 0 6px 14px rgba(242, 209, 209, 0.6);
}

/* ==== Tombol Login ==== */
.auth-container button {
    width: 100%;
    padding: 10px 12px;
    background: #C6DCE4;
    border-radius: 6px;
    border: 1px solid #000;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s ease;
    position: relative;
    overflow: hidden;
    color: black;
}

.auth-container button:hover {
    background: #8da5ad;
    transform: translateY(-3px) scale(1.02);
}

/* ==== Spinner ==== */
.spinner {
    display: none;
    width: 18px;
    height: 18px;
    border: 3px solid rgba(255, 255, 255, 0.6);
    border-top: 3px solid white;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    margin: auto;
}

@keyframes spin { 100% { transform: rotate(360deg); } }

/* Saat loading aktif */
button.loading .btn-text { visibility: hidden; }
button.loading .spinner { display: block; }
button.loading { transform: scale(0.98); background: #AFC8D7; }

/* Link bawah */
.auth-container p {
    text-align: center;
    margin-top: 12px;
}

.auth-container p a {
    color: #7BA4B9;
    font-weight: 600;
    text-decoration: none;
}
.auth-container p a:hover {
    text-decoration: underline;
}
</style>


<?php include 'header.php'; ?>
<div class="auth-container">

    <h2>Login</h2>
    
    <?php 
    $error = get_message('error');
    $success = get_message('success');
    if ($error): ?>
        <div style="color: red; margin-bottom: 10px;"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="color: green; margin-bottom: 10px;"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="index.php?controller=auth&action=login" method="POST">
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

    <p>Don't have an account?<a href="index.php?controller=auth&action=register">Register in here</a></p>
</div>
<?php include 'footer.php'; ?>
