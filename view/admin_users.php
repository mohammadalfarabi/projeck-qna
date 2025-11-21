<?php
include "koneksi.php";

// Handle Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    header("Location: admin_users.php?msg=deleted");
    exit;
}

// Handle Tambah
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $nama     = $_POST['nama'];
    $password = md5($_POST['password']);
    $level    = $_POST['level'];

    mysqli_query($conn, "INSERT INTO users(username, nama, password, level)
                         VALUES('$username','$nama','$password','$level')");
    header("Location: admin_users.php?msg=added");
    exit;
}

// Handle Edit
if (isset($_POST['edit_user'])) {
    $id       = $_POST['id'];
    $username = $_POST['username'];
    $nama     = $_POST['nama'];
    $level    = $_POST['level'];

    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        mysqli_query($conn, "UPDATE users SET 
            username='$username', nama='$nama', password='$password', level='$level'
            WHERE id='$id'");
    } else {
        mysqli_query($conn, "UPDATE users SET 
            username='$username', nama='$nama', level='$level'
            WHERE id='$id'");
    }

    header("Location: admin_users.php?msg=updated");
    exit;
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background: #f5f7fa; }
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        table tbody tr:hover {
            background: #eef3ff;
            transition: 0.2s;
        }
    </style>
</head>

<body>

<div class="container py-4">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="m-0">👤 Manajemen User</h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                + Tambah User
            </button>
        </div>

        <table class="table table-hover">
            <thead class="table-primary">
                <tr>
                    <th width="50">ID</th>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Level</th>
                    <th width="150">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = mysqli_fetch_assoc($users)) { ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= $u['username'] ?></td>
                        <td><?= $u['nama'] ?></td>
                        <td><span class="badge bg-info"><?= $u['level'] ?></span></td>
                        <td>
                            <button 
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $u['id'] ?>"
                            >
                                Edit
                            </button>

                            <a href="?hapus=<?= $u['id'] ?>" 
                               onclick="return confirm('Yakin hapus user ini?')"
                               class="btn btn-sm btn-danger">
                                Hapus
                            </a>
                        </td>
                    </tr>

                    <!-- MODAL EDIT -->
                    <div class="modal fade" id="editModal<?= $u['id'] ?>">
                        <div class="modal-dialog">
                            <form method="POST" class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit User</h5>
                                    <button class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">

                                    <div class="mb-2">
                                        <label>Username</label>
                                        <input type="text" name="username" value="<?= $u['username'] ?>" class="form-control" required>
                                    </div>

                                    <div class="mb-2">
                                        <label>Nama</label>
                                        <input type="text" name="nama" value="<?= $u['nama'] ?>" class="form-control" required>
                                    </div>

                                    <div class="mb-2">
                                        <label>Password (kosongkan jika tidak ganti)</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>

                                    <div class="mb-2">
                                        <label>Level</label>
                                        <select name="level" class="form-control">
                                            <option <?= $u['level']=='admin'?'selected':'' ?>>admin</option>
                                            <option <?= $u['level']=='user'?'selected':'' ?>>user</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button class="btn btn-primary" name="edit_user">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL TAMBAH USER -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah User Baru</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-2">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-2">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>

                <div class="mb-2">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-2">
                    <label>Level</label>
                    <select name="level" class="form-control">
                        <option>admin</option>
                        <option>user</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" name="add_user">Tambah</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
