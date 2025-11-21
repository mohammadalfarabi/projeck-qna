    </main>

    <aside class="rightnav" aria-label="User history">
      <?php if(isset($_SESSION['user_id'])): 
        $uid = (int)$_SESSION['user_id'];
        $r = mysqli_query($GLOBALS['conn'], "SELECT q.* FROM question q WHERE q.user_id=$uid ORDER BY q.created_at DESC LIMIT 10");
        echo '<h3 style="margin-top:0">Riwayat Pertanyaan</h3><ul style="padding-left:16px">';
        while($row = mysqli_fetch_assoc($r)){
          echo '<li style="margin-bottom:6px"><a href="index.php?page=view_question&id='.$row['question_id'].'">'.htmlspecialchars($row['title']).'</a></li>';
        }
        echo '</ul>';
      endif; ?>
    </aside>
  </div>

  <script>
  const btn = document.getElementById("darkToggle");

  btn.addEventListener("click", () => {
    document.body.classList.toggle("dark");

    // simpan preferensi di localStorage
    if (document.body.classList.contains("dark")) {
      localStorage.setItem("darkmode", "on");
    } else {
      localStorage.setItem("darkmode", "off");
    }
  });

  // load preferensi
  if (localStorage.getItem("darkmode") === "on") {
    document.body.classList.add("dark");
  }
</script>

</body>
</html>
