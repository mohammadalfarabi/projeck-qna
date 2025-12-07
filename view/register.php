<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../koneksi.php';
include 'header.php';
require_once '../model/school_model.php';
$schools = get_all_schools();
?>

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
<div class="auth-container">
    <h2>Register New User</h2>
    <?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red;">' . htmlspecialchars($_SESSION['error']) . '</p>';
        unset($_SESSION['error']);
    } elseif (isset($_SESSION['success'])) {
        echo '<p style="color:green;">' . htmlspecialchars($_SESSION['success']) . '</p>';
        unset($_SESSION['success']);
    }
    ?>
    <form action="../controller/auth_controller.php" method="POST" class="auth-form" id="registerForm">
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
    <p>Already have an account?<a href="../controller/auth_controller.php?action=login">Login in here</a></p>
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
