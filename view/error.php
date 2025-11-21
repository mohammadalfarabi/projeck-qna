<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Error</title>

<link rel="stylesheet" href="assets/css/base.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/components.css">
<link rel="stylesheet" href="assets/css/pages/error.css">

</head>
<body>

<div class="error-container">

    <div class="error-card">
        <h1>⚠️ Terjadi Kesalahan</h1>

        <p><?= $message ?? "Terjadi error yang tidak diketahui." ?></p>

        <a href="index.php?page=dashboard" class="btn-primary mt-20">Kembali ke Dashboard</a>
    </div>

</div>

</body>
</html>
