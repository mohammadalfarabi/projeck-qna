<style>
    .container {
    background: #FFE6E6;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    margin-top: 25px;
    font-family: 'Segoe UI', sans-serif;
}

.container h2 {
    font-size: 25px;
    margin-bottom: 10px;
    color: #000000ff;
    text-align: center;
}

input, select{
     background: #DAEAF1;
}

.container button {
    background: #C6DCE4 ;
    border-radius: 3px;
    border: 1px solid black;
    color: black;
}

.container button:hover{
    background: #8da5adff;
}

.container a#hapus {
    display: inline-block;
    padding: 6px 12px;
    text-decoration: none;
    background: #ff5752ff;
    border-radius: 3px;
    border: 1px solid black;
    color: black;
}

.container a#hapus:hover {
    background: #ac3632ff;
}

</style>

<?php include 'header.php'; ?>
<div class="container">
    <h2>Edit User</h2>

    <form method="POST" action="index.php?controller=teacher&action=edit_user&id=<?= $user_data['user_id']; ?>">
        <div class="form-group">
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user_data['name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user_data['email']); ?>" required>
        </div>
        <div class="form-group">
            <label>Role:</label>
            <select name="role" required>
                <option value="student" <?= $user_data['role'] == 'student' ? 'selected' : ''; ?>>Student</option>
                <option value="teacher" <?= $user_data['role'] == 'teacher' ? 'selected' : ''; ?>>Teacher</option>
            </select>
        </div>
        <div class="form-group">
            <label>School:</label>
            <select name="school_id" required>
                <?php
                $schools = get_all_schools();
                foreach ($schools as $school) {
                    echo "<option value='" . $school['school_id'] . "' " . ($user_data['school_id'] == $school['school_id'] ? 'selected' : '') . ">" . htmlspecialchars($school['school_name']) . "</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit">Update User</button>
        <a id="hapus" href="index.php?controller=teacher&action=dashboard">Cancel</a>
    </form>
</div>
<?php include 'footer.php'; ?>
