<?php
session_start();
// تأكد أنك تملك ملف config.php يحتوي على متغير $allowed_emails كمصفوفة أو الاتصال بقاعدة بيانات
// مثال: include 'config.php';
include_once 'config.php'; // إذا لم يكن لديك هذا الملف، سننشئه لاحقاً

// helper لتتحقق من صلاحية الايميل
function is_allowed($email) {
    global $allowed_emails;
    if (empty($email)) return false;
    if (!isset($allowed_emails) || !is_array($allowed_emails)) return false;
    return in_array(strtolower($email), array_map('strtolower', $allowed_emails));
}

$user = $_SESSION['user'] ?? null; // نتوقع $_SESSION['user']['email'] و username و avatar
$can_add = $user && is_allowed($user['email']);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>متجر - Southland Rp</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@500;700;900&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#061221;
      --card:#071827;
      --accent:#8ac6ff;
      --gold:#ffd166;
      --glass: rgba(255,255,255,0.04);
      --muted: rgba(255,255,255,0.6);
    }
    *{box-sizing:border-box}
    body{
      margin:0;padding:0;font-family:'Tajawal',sans-serif;background:linear-gradient(135deg,#020617 0%, #061221 60%);color:#fff;min-height:100vh;
      background-attachment:fixed;
    }
    header{display:flex;align-items:center;justify-content:space-between;padding:30px 40px;border-bottom:1px solid rgba(255,255,255,0.03)}
    .logo{font-weight:900;font-size:1.6rem;letter-spacing:1px;color:var(--gold)}
    .tag{font-size:0.85rem;color:var(--muted)}

    .hero{padding:60px 20px;text-align:center}
    .hero h1{font-size:2.6rem;margin:0 0 10px;color:#ffffff}
    .hero p{max-width:900px;margin:0 auto;color:var(--muted);font-weight:600}

    .store-controls{display:flex;gap:15px;justify-content:center;margin:30px 20px}

    .store-btn{background:linear-gradient(90deg,#2d6cdf 0%, #1246a3 100%);border:none;padding:12px 22px;border-radius:12px;color:#fff;font-weight:700;cursor:pointer;box-shadow:0 8px 30px rgba(18,70,163,0.25);transition:transform .15s ease}
    .store-btn:active{transform:translateY(2px)}

    .store-btn.secondary{background:transparent;border:1px solid rgba(255,255,255,0.06);}

    .store-wrapper{max-width:1100px;margin:20px auto;padding:20px;display:grid;grid-template-columns:1fr 340px;gap:30px}

    .products{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px}
    .product-card{background:linear-gradient(180deg,rgba(255,255,255,0.02), rgba(255,255,255,0.01));border-radius:14px;padding:14px;border:1px solid rgba(255,255,255,0.03);backdrop-filter: blur(6px);box-shadow:0 10px 30px rgba(2,6,12,0.6)}
    .product-card img{width:100%;height:150px;object-fit:cover;border-radius:8px}
    .product-title{font-weight:800;margin:10px 0 4px}
    .product-price{color:var(--gold);font-weight:800}
    .product-desc{color:var(--muted);font-size:0.95rem}

    .sidebar{background:var(--card);padding:18px;border-radius:12px;height:max-content;border:1px solid rgba(255,255,255,0.03)}
    .sidebar h3{margin:0 0 12px}
    .dev-note{margin-top:20px;padding:12px;border-radius:10px;background:linear-gradient(90deg, rgba(255,209,102,0.06), rgba(138,198,255,0.02));color:var(--gold);font-weight:700;text-align:center}

    /* add product modal */
    .modal{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);z-index:9999}
    .modal-card{width:100%;max-width:720px;background:linear-gradient(180deg,#07182a,#0b2032);padding:20px;border-radius:12px;border:1px solid rgba(255,255,255,0.04)}
    .form-row{display:flex;gap:10px}
    .form-input, .form-textarea{width:100%;padding:10px;border-radius:8px;background:var(--glass);border:1px solid rgba(255,255,255,0.04);color:#fff}
    .form-textarea{min-height:120px}
    .file-preview{width:100%;height:160px;border-radius:8px;object-fit:cover;background:linear-gradient(90deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));display:block;margin-bottom:10px}

    .muted-note{font-size:0.85rem;color:var(--muted);margin-top:8px}

    /* disabled look */
    .disabled{opacity:0.45;pointer-events:none}

    footer{margin-top:40px;padding:20px;text-align:center;color:var(--muted)}

    @media(max-width:900px){.store-wrapper{grid-template-columns:1fr} .hero h1{font-size:2rem}}
  </style>
</head>
<body>
  <header>
    <div class="logo">Southland Rp</div>
    <div class="tag">متجر فخم - شبكة Future Life</div>
  </header>

  <section class="hero">
    <h1>مرحباً بك في متجر <span style="color:var(--gold)">Future Life</span></h1>
    <p>هنا تلاقي أفضل المنتجات الحصرية، طرق دفع مرنة وتسليم فوري بعد التأكد. المتجر من برمجة <strong>خليل</strong> — جودة وذوق راقي.</p>
  </section>

  <div class="store-controls">
    <?php if ($can_add): ?>
      <button class="store-btn" id="open-add">➕ إضافة منتج</button>
    <?php else: ?>
      <button class="store-btn secondary disabled" title="غير مسموح">➕ إضافة منتج (مقيد)</button>
    <?php endif; ?>

    <a href="index.php" class="store-btn secondary">العودة للرئيسية</a>
  </div>

  <div class="store-wrapper">
    <div>
      <div class="products" id="products">
        <!-- منتجات تجريبية (يمكن عرض المنتجات من DB لاحقاً) -->
        <?php
        // لو كان عندك db, هني تجيب المنتجات من جدول products
        // مثال: while($row = $result->fetch_assoc()){ ... }
        // الآن نعرض عينات ثابتة للستايل
        $samples = [
          ['title'=>'إيدية نادرة','price'=>'15.00','img'=>'https://picsum.photos/seed/id1/800/600','desc'=>'إيدية حصرية مع خصائص فريدة.'],
          ['title'=>'سيارة كلاسيك','price'=>'120.00','img'=>'https://picsum.photos/seed/car2/800/600','desc'=>'مركبة فاخرة بتصميم كلاسيكي.'],
          ['title'=>'حزمة مودات','price'=>'40.00','img'=>'https://picsum.photos/seed/mod3/800/600','desc'=>'حزمة مودات متميزة لتعزيز تجربتك.']
        ];
        foreach($samples as $p){
          echo '<div class="product-card">';
          echo '<img src="'.htmlspecialchars($p['img']).'" alt="'.htmlspecialchars($p['title']).'">';
          echo '<div class="product-title">'.htmlspecialchars($p['title']).'</div>';
          echo '<div class="product-price">'.htmlspecialchars($p['price']).' €</div>';
          echo '<div class="product-desc">'.htmlspecialchars($p['desc']).'</div>';
          echo '</div>';
        }
        ?>
      </div>
    </div>

    <aside class="sidebar">
      <h3>معلومات المتجر</h3>
      <p class="muted-note">تسليم فوري بعد الدفع — دعم عبر ديسكورد — ضمان على المنتجات.</p>

      <div style="margin-top:12px">
        <strong>حالة دخولك:</strong>
        <div class="muted-note">
          <?php if ($user): ?>
            مرحباً، <?php echo htmlspecialchars($user['username'] ?? $user['email']); ?> <br>
            <?php echo htmlspecialchars($user['email'] ?? ''); ?>
          <?php else: ?>
            غير مسجل — <a href="oauth.php">سجل دخول</a> لعرض أدوات التحكم.
          <?php endif; ?>
        </div>
      </div>

      <div class="dev-note">من برمجة: خليل — تصميم راقٍ وتجربة ساحرة ✨</div>

      <div style="margin-top:16px">
        <small class="muted-note">إدارة الصلاحيات: يمكنك التحكم بمن يملك صلاحية إضافة المنتجات عبر ملف <code>config.php</code> أو من خلال لوحة إدارة المستخدمين (DB).</small>
      </div>
    </aside>
  </div>

  <!-- Add product modal (hidden by default) -->
  <div id="add-modal" class="modal" style="display:none;">
    <div class="modal-card">
      <h3>إضافة منتج جديد</h3>
      <form id="add-form" method="post" action="add_product.php" enctype="multipart/form-data">
        <div style="margin-top:10px">
          <input class="form-input" type="text" name="title" placeholder="اسم المنتج" required>
        </div>
        <div style="margin-top:10px" class="form-row">
          <input class="form-input" type="number" step="0.01" name="price" placeholder="السعر" required>
          <input class="form-input" type="text" name="tags" placeholder="الوسوم (فاصلة مفصولة)">
        </div>
        <div style="margin-top:10px">
          <textarea class="form-textarea" name="desc" placeholder="وصف المنتج"></textarea>
        </div>
        <div style="margin-top:10px">
          <input type="file" name="image" id="image-input" accept="image/*">
          <img id="img-preview" class="file-preview" src="" style="display:none;">
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:12px">
          <button type="button" id="close-modal" class="store-btn secondary">إلغاء</button>
          <button type="submit" class="store-btn">نشر المنتج</button>
        </div>
      </form>
    </div>
  </div>

  <footer>
    © <?php echo date('Y'); ?> Southland Roleplay. كل الحقوق محفوظة. — من تنفيذ خليل
  </footer>

  <script>
    const canAdd = <?php echo $can_add ? 'true' : 'false'; ?>;
    const openBtn = document.getElementById('open-add');
    const modal = document.getElementById('add-modal');
    const closeModal = document.getElementById('close-modal');
    const imgInput = document.getElementById('image-input');
    const imgPreview = document.getElementById('img-preview');

    if (openBtn) {
      openBtn.addEventListener('click', () => {
        if (!canAdd) return alert('ليس لديك صلاحية لإضافة منتجات.');
        modal.style.display = 'flex';
      });
    }

    if (closeModal) closeModal.addEventListener('click', () => modal.style.display = 'none');

    imgInput && imgInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (!file) return;
      const url = URL.createObjectURL(file);
      imgPreview.src = url; imgPreview.style.display = 'block';
    });

    // منع الذين ليس لديهم صلاحية من إرسال الفورم عبر JS
    document.getElementById('add-form')?.addEventListener('submit', function(e) {
      if (!canAdd) { e.preventDefault(); alert('مش مسموح'); }
    });
  </script>
</body>
</html>
