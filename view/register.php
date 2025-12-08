<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../koneksi.php';
include 'header.php';
require_once __DIR__ . '/../model/school_model.php';

// Load schools data for dropdown
$schools = get_all_schools();
?>

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

/* ==== Card Register ==== */
.auth-container {
    width: 380px;
    margin: 35px auto;
    padding: 30px;
    background: #DAEAF1;
    border-radius: 14px;
    box-shadow: 0 6px 25px rgba(198, 220, 228, 0.6);
    font-family: 'Segoe UI', sans-serif;
}

.auth-container h2 {
    font-size: 25px;
    margin-bottom: 15px;
    color: #000;
    text-align: center;
}

/* ==== Form Group ==== */
.form-group {
    margin-bottom: 18px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-size: 14px;
    color: #6a6a6a;
    font-weight: 500;
}

/* ==== Input & Select ==== */
input,
select {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #C6DCE4;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    background: #FAFAFA;
    transition: all 0.35s ease;
}

/* Hover */
input:hover,
select:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(198, 220, 228, 0.4);
    background-color: #FFFFFF;
}

/* Focus */
input:focus,
select:focus {
    border-color: #F2D1D1;
    transform: translateY(-3px);
    box-shadow: 0 6px 14px rgba(242, 209, 209, 0.6);
}

/* ==== Tombol ==== */
.auth-container button {
    width: 100%;
    padding: 12px;
    background: #C6DCE4;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.35s ease;
    border: 1px solid black;
    color: black;
}

/* Hover tombol */
.auth-container button:hover {
    background: #8da5ad;
    transform: translateY(-4px) scale(1.02);
}

/* Click tombol */
.auth-container button:active {
    transform: translateY(-1px) scale(0.99);
}

/* ==== Link bawah ==== */
.auth-container p {
    text-align: center;
    margin-top: 15px;
}

.auth-container p a {
    color: #7BA4B9;
    text-decoration: none;
    font-weight: 600;
}

.auth-container p a:hover {
    text-decoration: underline;
}
</style>

<div class="auth-container">

    <h2>Register New User</h2>
    
    <?php 
    $error = get_message('error');
    $success = get_message('success');
    if ($error): ?>
        <div style="color: red; margin-bottom: 10px;"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="color: green; margin-bottom: 10px;"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="index.php?controller=auth&action=register" method="POST" class="auth-form" id="registerForm">
        <input type="hidden" name="action" value="register">
        
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" required>
        </div>
        
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
                <option value="">Pilih Peran</option>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>School:</label>
            <select name="school_id" id="schoolSelect" required>
                <option value="">Pilih Sekolah</option>
                <?php foreach ($schools as $school): ?>
                    <option value="<?php echo htmlspecialchars($school['school_id']); ?>">
                        <?php echo htmlspecialchars($school['school_name']); ?>
                    </option>
                <?php endforeach; ?>
                <option value="new_school">Tambah Sekolah Baru</option>
            </select>
        </div>

        <div class="form-group" id="newSchoolDiv" style="display:none;">
            <label>Nama Sekolah Baru:</label>
            <input type="text" name="new_school_name" id="newSchoolName">
        </div>
        
        <button type="submit">Register</button>
    </form>

    <p>Already have an account?<a href="index.php?controller=auth&action=login">Login in here</a></p>
</div>

<script>
document.getElementById('schoolSelect').addEventListener('change', function() {
    var newSchoolDiv = document.getElementById('newSchoolDiv');
    if (this.value === 'new_school') {
        newSchoolDiv.style.display = 'block';
        document.getElementById('newSchoolName').setAttribute('required', 'required');
        this.removeAttribute('required');
    } else {
        newSchoolDiv.style.display = 'none';
        document.getElementById('newSchoolName').removeAttribute('required');
        this.setAttribute('required', 'required');
    }
});
</script>

<?php include 'footer.php'; ?>
