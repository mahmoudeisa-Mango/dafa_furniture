<?php
session_start();

$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "furniture";

// ── Hard-coded admin credentials ──────────────────────
$admin_email    = "admin@dafa.com";
$admin_password = "admin123";

$message = "";

// ── Handle admin login ─────────────────────────────────
if (isset($_POST['admin_login'])) {
    $email = $_POST['email'];
    $pass  = $_POST['password'];

    if ($email === $admin_email && $pass === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $message = "Invalid email or password.";
    }
}

// ── Handle logout ──────────────────────────────────────
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// ── Handle add product ────────────────────────────────
if (isset($_POST['add_product']) && isset($_SESSION['admin_logged_in'])) {
    $product_name   = $_POST['product_name'];
    $category       = $_POST['category'];
    $description    = $_POST['description'];
    $stock_quantity = $_POST['stock_quantity'];
    $color          = $_POST['color'];
    $material       = $_POST['material'];
    $price          = $_POST['price'];

    // Handle image upload
    $picture = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = "furnimages/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $picture = $upload_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $picture);
    }

    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$conn) {
        $message = "Database connection failed.";
    } else {
        $stmt = "INSERT INTO `product` 
                 (`product_name`, `cateogry`, `description`, `stock_quantity`, `color`, `material`, `price`, `picture`)
                 VALUES ('$product_name', '$category', '$description', '$stock_quantity', '$color', '$material', '$price', '$picture')";
        $result = mysqli_query($conn, $stmt);

        if ($result === FALSE) {
            $message = "Error adding product. Please try again.";
        } else {
            $message = "success: Product <strong>$product_name</strong> was added successfully.";
        }
        mysqli_close($conn);
    }
}

// ── Handle delete product ─────────────────────────────
if (isset($_GET['delete']) && isset($_SESSION['admin_logged_in'])) {
    $id   = (int)$_GET['delete'];
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if ($conn) {
        mysqli_query($conn, "DELETE FROM `product` WHERE `product_id`=$id");
        mysqli_close($conn);
        header("Location: admin.php?tab=products");
        exit();
    }
}

// ── Fetch all products ────────────────────────────────
$products = [];
if (isset($_SESSION['admin_logged_in'])) {
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if ($conn) {
        $res = mysqli_query($conn, "SELECT * FROM `product`");
        while ($row = mysqli_fetch_assoc($res)) {
            $products[] = $row;
        }
        mysqli_close($conn);
    }
}

$active_tab = $_GET['tab'] ?? 'add';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dafa — Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    :root {
      --ivory: #f2f0e6;
      --sage:  #7a9478;
      --linen: #cdbfae;
      --ink:   #2c2c2c;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--ivory);
      color: var(--ink);
      min-height: 100vh;
    }

    /* ── LOGIN PAGE ── */
    .login-wrap {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .login-card {
      width: 100%;
      max-width: 380px;
      background: var(--ivory);
      border: 1px solid var(--linen);
      border-radius: 6px;
      padding: 2.8rem 2.4rem;
    }

    .brand {
      text-align: center;
      margin-bottom: 2rem;
    }

    .brand-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 34px;
      font-weight: 300;
      letter-spacing: 8px;
      text-transform: uppercase;
      color: var(--sage);
      display: block;
    }

    .brand-sub {
      font-size: 10px;
      letter-spacing: 3px;
      color: var(--linen);
      text-transform: uppercase;
      margin-top: 3px;
      display: block;
    }

    .brand-rule {
      width: 28px;
      height: 1px;
      background: var(--sage);
      margin: 0.9rem auto 0;
    }

    .eyebrow {
      font-size: 9.5px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--sage);
      margin-bottom: 4px;
    }

    .heading {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      font-weight: 400;
      color: var(--sage);
      margin-bottom: 4px;
    }

    .subtext {
      font-size: 12px;
      color: var(--ink);
      opacity: 0.45;
      font-weight: 300;
      margin-bottom: 1.6rem;
    }

    .divider {
      height: 1px;
      background: var(--linen);
      margin-bottom: 1.5rem;
    }

    /* ── FIELDS ── */
    .field { margin-bottom: 14px; }

    .field label {
      display: block;
      font-size: 9.5px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--ink);
      opacity: 0.5;
      margin-bottom: 5px;
      font-weight: 500;
    }

    .field input,
    .field select,
    .field textarea {
      width: 100%;
      border: 1px solid var(--linen);
      border-radius: 4px;
      padding: 0 13px;
      font-family: 'DM Sans', sans-serif;
      font-size: 13px;
      color: var(--ink);
      background: var(--ivory);
      outline: none;
      transition: border-color 0.2s;
    }

    .field input,
    .field select { height: 42px; }

    .field textarea {
      height: 90px;
      padding: 10px 13px;
      resize: vertical;
    }

    .field input::placeholder { color: var(--linen); }
    .field input:focus,
    .field select:focus,
    .field textarea:focus { border-color: var(--sage); }

    .field select { appearance: none; cursor: pointer; }

    .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }

    /* ── BUTTONS ── */
    .btn {
      width: 100%;
      height: 44px;
      border: none;
      background: var(--sage);
      color: var(--ivory);
      font-family: 'DM Sans', sans-serif;
      font-size: 10.5px;
      font-weight: 500;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      cursor: pointer;
      border-radius: 22px;
      margin-top: 1rem;
      transition: opacity 0.2s;
    }

    .btn:hover { opacity: 0.85; }

    .btn-sm {
      display: inline-block;
      padding: 5px 14px;
      border: none;
      background: var(--linen);
      color: var(--ink);
      font-family: 'DM Sans', sans-serif;
      font-size: 10px;
      letter-spacing: 1px;
      text-transform: uppercase;
      cursor: pointer;
      border-radius: 12px;
      text-decoration: none;
      transition: background 0.2s;
    }

    .btn-sm:hover { background: var(--sage); color: var(--ivory); }

    .btn-danger {
      background: var(--linen);
      color: var(--ink);
    }

    .btn-danger:hover { background: #b07a6e; color: var(--ivory); }

    /* ── MESSAGE ── */
    .msg {
      padding: 10px 14px;
      border-radius: 4px;
      font-size: 12px;
      margin-bottom: 1.2rem;
      border: 1px solid var(--linen);
      background: var(--ivory);
      color: var(--ink);
    }

    .msg.success {
      border-color: var(--sage);
      color: var(--sage);
    }

    .msg.error {
      border-color: var(--linen);
      color: var(--ink);
      opacity: 0.7;
    }

    /* ── ADMIN LAYOUT ── */
    .admin-layout {
      display: grid;
      grid-template-columns: 220px 1fr;
      min-height: 100vh;
    }

    /* ── SIDEBAR ── */
    .sidebar {
      background: var(--ink);
      padding: 2.2rem 1.6rem;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .sidebar .brand-name { font-size: 24px; letter-spacing: 6px; }
    .sidebar .brand-sub  { color: var(--linen); opacity: 0.6; }

    .sidebar-rule { width: 28px; height: 1px; background: var(--sage); margin: 1.2rem 0; }

    .sidebar-label {
      font-size: 9px;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      color: var(--linen);
      opacity: 0.5;
      margin-bottom: 0.8rem;
      font-weight: 500;
    }

    .sidebar nav { display: flex; flex-direction: column; gap: 4px; }

    .sidebar nav a {
      font-size: 12px;
      color: var(--linen);
      text-decoration: none;
      letter-spacing: 0.5px;
      padding: 8px 12px;
      border-radius: 4px;
      transition: background 0.2s, color 0.2s;
      opacity: 0.7;
    }

    .sidebar nav a:hover,
    .sidebar nav a.active {
      background: rgba(255,255,255,0.08);
      color: var(--ivory);
      opacity: 1;
    }

    .sidebar nav a.active { border-left: 2px solid var(--sage); padding-left: 10px; }

    .sidebar-logout a {
      font-size: 10px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--linen);
      text-decoration: none;
      opacity: 0.4;
      transition: opacity 0.2s;
    }

    .sidebar-logout a:hover { opacity: 0.8; }

    /* ── MAIN CONTENT ── */
    .main { padding: 2.5rem 2.8rem; }

    .page-eyebrow {
      font-size: 9.5px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--sage);
      margin-bottom: 4px;
    }

    .page-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 28px;
      font-weight: 400;
      color: var(--sage);
      margin-bottom: 0.3rem;
    }

    .page-sub {
      font-size: 12px;
      color: var(--ink);
      opacity: 0.4;
      font-weight: 300;
      margin-bottom: 2rem;
    }

    /* ── FORM CARD ── */
    .form-card {
      background: var(--ivory);
      border: 1px solid var(--linen);
      border-radius: 4px;
      padding: 2rem 2.2rem;
      max-width: 700px;
    }

    .section-label {
      font-size: 9px;
      letter-spacing: 2.5px;
      text-transform: uppercase;
      color: var(--sage);
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 1.4rem 0 1rem;
      font-weight: 500;
    }

    .section-label::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--linen);
    }

    /* ── PRODUCTS TABLE ── */
    .table-wrap {
      overflow-x: auto;
      border: 1px solid var(--linen);
      border-radius: 4px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 12.5px;
    }

    thead {
      background: var(--ink);
      color: var(--ivory);
    }

    thead th {
      padding: 12px 16px;
      font-size: 9px;
      letter-spacing: 2px;
      text-transform: uppercase;
      font-weight: 500;
      text-align: left;
    }

    tbody tr { border-bottom: 1px solid var(--linen); }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: rgba(205,191,174,0.2); }

    tbody td {
      padding: 11px 16px;
      color: var(--ink);
      vertical-align: middle;
    }

    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 10px;
      font-size: 10px;
      background: var(--linen);
      color: var(--ink);
      letter-spacing: 0.5px;
    }

    .empty-state {
      text-align: center;
      padding: 3rem;
      color: var(--ink);
      opacity: 0.35;
      font-size: 13px;
    }
  </style>
</head>
<body>

<?php if (!isset($_SESSION['admin_logged_in'])): ?>
<!-- ══════════════════════════════════════════
     ADMIN LOGIN
══════════════════════════════════════════ -->
<div class="login-wrap">
  <div class="login-card">

    <div class="brand">
      <span class="brand-name">Dafa</span>
      <span class="brand-sub">Admin Panel</span>
      <div class="brand-rule"></div>
    </div>

    <p class="eyebrow">Restricted access</p>
    <h1 class="heading">Sign in</h1>
    <p class="subtext">Admins only. Enter your credentials to continue.</p>
    <div class="divider"></div>

    <?php if ($message): ?>
      <div class="msg error"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" action="admin.php">

      <div class="field">
        <label>Email address</label>
        <input type="email" name="email" placeholder="admin@dafa.com" required>
      </div>

      <div class="field">
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>

      <button type="submit" name="admin_login" class="btn">Sign in</button>

    </form>
  </div>
</div>

<?php else: ?>
<!-- ══════════════════════════════════════════
     ADMIN DASHBOARD
══════════════════════════════════════════ -->
<div class="admin-layout">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div>
      <div class="brand">
        <span class="brand-name">Dafa</span>
        <span class="brand-sub">Admin Panel</span>
      </div>
      <div class="sidebar-rule"></div>
      <p class="sidebar-label">Navigation</p>
      <nav>
        <a href="admin.php?tab=add"      class="<?= $active_tab === 'add'      ? 'active' : '' ?>">+ Add Product</a>
        <a href="admin.php?tab=products" class="<?= $active_tab === 'products' ? 'active' : '' ?>">All Products</a>
      </nav>
    </div>
    <div class="sidebar-logout">
      <a href="admin.php?logout=1">← Sign out</a>
    </div>
  </aside>

  <!-- Main -->
  <main class="main">

    <?php if ($message): ?>
      <div class="msg <?= strpos($message, 'success') === 0 ? 'success' : 'error' ?>">
        <?= ltrim($message, 'success: ') ?>
      </div>
    <?php endif; ?>

    <?php if ($active_tab === 'add'): ?>
    <!-- ── ADD PRODUCT ── -->
    <p class="page-eyebrow">Inventory</p>
    <h1 class="page-title">Add New Product</h1>
    <p class="page-sub">Fill in the details below to add a product to the catalogue.</p>

    <div class="form-card">
      <form method="post" action="admin.php?tab=add" enctype="multipart/form-data">

        <div class="section-label">Basic Info</div>

        <div class="field">
          <label>Product Name</label>
          <input type="text" name="product_name" placeholder="e.g. Oslo Lounge Chair" required>
        </div>

        <div class="row-2">
          <div class="field">
            <label>Category</label>
            <select name="category" required>
              <option value="" disabled selected>Select category</option>
              <option>Living Room</option>
              <option>Bedroom</option>
              <option>Dining</option>
              <option>Bath</option>
              <option>Storage Unit</option>
              <option>Carpets</option>
            </select>
          </div>
          <div class="field">
            <label>Price (EGP)</label>
            <input type="number" name="price" placeholder="0.00" step="0.01" min="0" required>
          </div>
        </div>

        <div class="field">
          <label>Description</label>
          <textarea name="description" placeholder="Brief product description..."></textarea>
        </div>

        <div class="section-label">Specifications</div>

        <div class="row-3">
          <div class="field">
            <label>Color</label>
            <input type="text" name="color" placeholder="e.g. Walnut">
          </div>
          <div class="field">
            <label>Material</label>
            <input type="text" name="material" placeholder="e.g. Solid Oak">
          </div>
          <div class="field">
            <label>Stock Quantity</label>
            <input type="number" name="stock_quantity" placeholder="0" min="0" required>
          </div>
        </div>

        <div class="section-label">Product Image</div>

        <div class="field">
          <label>Upload Image</label>
          <input type="file" name="image" accept="image/*">
        </div>

        <button type="submit" name="add_product" class="btn">Add Product</button>

      </form>
    </div>

    <?php elseif ($active_tab === 'products'): ?>
    <!-- ── ALL PRODUCTS ── -->
    <p class="page-eyebrow">Inventory</p>
    <h1 class="page-title">All Products</h1>
    <p class="page-sub"><?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?> in the catalogue.</p>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Color</th>
            <th>Material</th>
            <th>Stock</th>
            <th>Price</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($products)): ?>
            <tr><td colspan="8" class="empty-state">No products added yet.</td></tr>
          <?php else: ?>
            <?php foreach ($products as $p): ?>
            <tr>
              <td><?= $p['product_id'] ?></td>
              <td><?= htmlspecialchars($p['product_name']) ?></td>
              <td><span class="badge"><?= htmlspecialchars($p['cateogry']) ?></span></td>
              <td><?= htmlspecialchars($p['color'] ?? '—') ?></td>
              <td><?= htmlspecialchars($p['material'] ?? '—') ?></td>
              <td><?= $p['stock_quantity'] ?></td>
              <td>EGP <?= number_format($p['price'], 2) ?></td>
              <td>
                <a href="admin.php?delete=<?= $p['product_id'] ?>&tab=products"
                   class="btn-sm btn-danger"
                   onclick="return confirm('Delete this product?')">Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php endif; ?>
  </main>

</div>
<?php endif; ?>

</body>
</html>