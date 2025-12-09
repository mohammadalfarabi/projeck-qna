<style>
    body {
        background-color: #F2D1D1 !important;
        /* warna background baru */
    }

    /* ============================
   CONTAINER
=============================== */
    .container {
        background: #FFE6E6;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
        margin-top: 30px;
        font-family: 'Poppins', sans-serif;
    }

    .container h2 {
        font-size: 30px;
        margin-bottom: 25px;
        color: #2a2a2a;
        text-align: center;
        font-weight: 700;
    }

    /* ============================
   TABLE
=============================== */
    table {
        width: 100%;
        border-collapse: collapse;
        background: #F2D1D1;
        border-radius: 12px;
        overflow: hidden;
    }

    th {
        background: #C6DCE4;
        padding: 14px;
        text-align: center;
        font-size: 16px;
        color: #222;
    }

    td {
        padding: 12px;
        text-align: center;
        font-size: 15px;
        color: #333;
        background: #F9E8E8;
    }

    /* Row Hover */
    tbody tr:hover {
        background: #FFE6E6 !important;
        transition: 0.25s ease;
    }

    /* ============================
   BUTTONS
=============================== */
    .action-btn {
        padding: 8px 14px;
        border-radius: 8px;
        background: #DAEAF1;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: 0.25s ease;
    }

    .action-btn:hover {
        background: #C6DCE4;
    }

    /* Delete Button */
    .delete {
        background: #E8A1A1;
    }

    .delete:hover {
        background: #E29B9B;
    }

    /* Anchor inside button */
    .action-btn a {
        text-decoration: none;
        color: #222;
        display: block;
    }

    /* ============================
   EMPTY STATE
=============================== */
    .empty-msg {
        padding: 15px;
        background: #DAEAF1;
        border-radius: 10px;
        font-size: 16px;
        margin-top: 10px;
    }
</style>

<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit;
}

if (!isset($users)) {
    $users = [];
}
?>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Teacher Dashboard - Manage Users</h2>


    <form method="GET" action="index.php?controller=teacher&action=dashboard"
        style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
        <label style="flex-basis: 100%; font-weight: 600;">Search by Name:</label>
        <input type="text" name="search_name"
            value="<?= htmlspecialchars($_GET['search_name'] ?? ''); ?>"
            placeholder="Enter name"
            style="flex: 1; padding: 10px; border-radius: 6px; border: 1px solid #9CAFAA; background: #FBF3D5; font-size: 15px; transition: 0.2s;">
        <input type="hidden" name="controller" value="teacher">
        <input type="hidden" name="action" value="dashboard">
        <button type="submit"
            style="background: #D6A99D; color: white; padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer; transition: 0.2s;">
            Search
        </button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>School</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5" class="empty-msg">No users found in your school.</td>
                </tr>

            <?php else: ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= htmlspecialchars($user['role']); ?></td>
                        <td><?= htmlspecialchars($user['school_name']); ?></td>
                        <td>
                            <button class="action-btn">

                                <a href="index.php?controller=teacher&action=edit_user&id=<?= $user['user_id']; ?>">
                                    Edit
                                </a>
                            </button>

                            <button class="action-btn delete">
                                <a href="index.php?controller=teacher&action=delete_user&id=<?= $user['user_id']; ?>"
                                    onclick="return confirm('Are you sure you want to delete this user?');">
                                    Delete
                                </a>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</table>

</div>

<?php include 'footer.php'; ?>