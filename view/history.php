<?php require 'view/header.php'; ?>
<div class="card">
  <h2>History Pertanyaan</h2>
  <?php while($row = mysqli_fetch_assoc($q)): ?>
    <div style="margin-bottom:10px">
      <a href="index.php?page=view_question&id=<?php echo $row['question_id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a>
      <div style="color:var(--muted)"><?php echo $row['created_at']; ?></div>
    </div>
  <?php endwhile; ?>
</div>
<?php require 'view/footer.php'; ?>
        