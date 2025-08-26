<?php
// index.php - الصفحة الرئيسية للمتجر
// ضع هذا الملف في نفس المجلد مع store.php و config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تضمين config بأمان لو موجود
if (file_exists(__DIR__ . '/config.php')) {
    $cfg = include __DIR__ . '/config.php';
    if (is_array($cfg)) {
        $allowed_emails = $cfg['allowed_emails'] ?? [];
    }
}

// للاختبار مؤقتاً: فكّ التعليق لو تريد تجربة دخول وهمي
// $_SESSION['user'] = ['username'=>'Khalil','email'=>'youremail@gmail.com','avatar'=>'images/avatar.png'];

$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SouthLand Rp — الرئيسية</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#071226;--accent:#2f81f7;--card:#0f2233;--muted:#bfcbdc}
    body{margin:0;font-family:'Cairo',sans-serif;background:linear-gradient(180deg,#061026 0%, #071226 100%);color:#fff;min-height:100vh}
    header{display:flex;justify-content:space-between;align-items:center;padding:20px 28px;border-bottom:1px solid rgba(255,255,255,0.03)}
    .brand{font-weight:900;color:var(--accent);font-size:1.4rem}
    .nav{display:flex;gap:12px;align-items:center}
    .btn{background:transparent;border:1px solid rgba(255,255,255,0.06);padding:8px 14px;border-radius:10px;color:var(--muted);text-decoration:none}
    .hero{padding:60px 20px;text-align:center}
    .hero h1{font-size:2.4rem;margin:0 0 8px;color:#fff}
    .hero p{max-width:900px;margin:0 auto;color:var(--muted)}
    .controls{margin-top:18px;display:flex;gap:10px;justify-content:center}
    .primary{background:linear-gradient(90deg,#2d6cdf,#1246a3);border:none;padding:10px 18px;border-radius:10px;color:#fff}
    .card{background:var(--card);max-width:1100px;margin:28px auto;padding:20px;border-radius:12px;border:1px solid rgba(255,255,255,0.02)}
    footer{text-align:center;padding:20px;color:var(--muted);margin-top:20px}
    .profile{display:flex;gap:8px;align-items:center}
    .avatar{width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.04)}
    @media(max-width:700px){.hero h1{font-size:1.6rem}}
  </style>
</head>
<body>
  <header>
    <div class="brand">SouthLand Rp — Future Life</div>
    <div class="nav">
      <a class="btn" href="store.php">المتجر</a>
      <a class="btn" href="#features">الميزات</a>
      <?php if ($user): ?>
        <div class="profile">
          <img class="avatar" src="<?php echo htmlspecialchars($user['avatar'] ?? 'images/default-avatar.png'); ?>" alt="avatar">
          <span><?php echo htmlspecialchars($user['username'] ?? $user['email']); ?></span>
          <form method="post" action="logout.php" style="display:inline;margin-left:8px">
            <button class="btn" type="submit">تسجيل الخروج</button>
          </form>
        </div>
      <?php else: ?>
        <a class="btn" href="oauth.php">تسجيل دخول ديسكورد</a>
      <?php endif; ?>
    </div>
  </header>

  <main>
    <section class="hero">
      <h1>مرحباً بك في شبكة SouthLand Roleplay</h1>
      <p>تجربة ألعاب حياة واقعية مميزة — مابات ومودات حصرية، دعم فني سريع، ومتجر فخم لمنتجات حصرية. المتجر والفوتر من تنفيذ خليل.</p>
      <div class="controls">
        <a class="primary" href="store.php">ادخل المتجر الفخم</a>
        <a class="btn" href="#contact">تواصل معنا</a>
      </div>
    </section>

    <section class="card" id="features">
      <h2 style="margin:0 0 8px">لماذا تختارنا؟</h2>
      <p style="color:var(--muted);margin:0 0 12px">سيرفر مستقر، تحديثات دورية، ومجتمع محترم — كل ذلك ضمن تجربة لعب منظمة وجذابة.</p>
      <div style="display:flex;flex-wrap:wrap;gap:12px">
        <div style="flex:1;min-width:200px;background:rgba(255,255,255,0.02);padding:12px;border-radius:10px">✨ مودات حصرية</div>
        <div style="flex:1;min-width:200px;background:rgba(255,255,255,0.02);padding:12px;border-radius:10px">🚚 تسليم سريع</div>
        <div style="flex:1;min-width:200px;background:rgba(255,255,255,0.02);padding:12px;border-radius:10px">🛡 ضمان جودة</div>
      </div>
    </section>
  </main>

  <footer>
    © <?php echo date('Y'); ?> SouthLand Roleplay — من تنفيذ خليل
  </footer>
</body>
</html>
