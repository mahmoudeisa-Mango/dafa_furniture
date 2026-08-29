<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dafa — Reports</title>
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
      padding: 3rem 2rem 5rem;
    }

    /* ── Brand header ── */
    .page-header {
      text-align: center;
      margin-bottom: 3rem;
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
      margin: 1rem auto;
    }

    /* ── Report block ── */
    .report-block {
      max-width: 820px;
      margin: 0 auto 4rem;
    }

    .report-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 24px;
      font-weight: 400;
      color: var(--ink);
      margin-bottom: 4px;
    }

    .report-eyebrow {
      font-size: 9.5px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--sage);
      margin-bottom: 6px;
    }

    .report-sub {
      font-size: 12px;
      color: var(--ink);
      opacity: 0.45;
      font-weight: 300;
      margin-bottom: 1.6rem;
    }

    .report-divider {
      height: 1px;
      background: var(--linen);
      margin: 4rem auto;
      max-width: 820px;
    }

    /* ── Table ── */
    .table-wrap {
      border: 1px solid var(--linen);
      border-radius: 4px;
      overflow: hidden;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
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

    tbody td { padding: 11px 16px; }

    tbody td:last-child {
      font-weight: 500;
      color: var(--sage);
    }

    /* ── Footer ── */
    .report-footer {
      margin-top: 1.2rem;
      font-size: 10.5px;
      color: var(--ink);
      opacity: 0.35;
      text-align: right;
      letter-spacing: 0.5px;
      max-width: 820px;
      margin-left: auto;
      margin-right: auto;
    }
  </style>
</head>
<body>

  <!-- Brand -->
  <div class="page-header">
    <span class="brand-name">Dafa</span>
    <span class="brand-sub">دفا · Warmth in every piece</span>
    <div class="brand-rule"></div>
  </div>

  <!-- ══ REPORT 1: PRICE ══ -->
  <div class="report-block">
    <p class="report-eyebrow">Report 1</p>
    <h2 class="report-title">Price Report</h2>
    <p class="report-sub">Products priced at EGP 10,000 and above</p>

    <div class="table-wrap">
      <table border="1">
        <tr><th>Product_id</th><th>Product_name</th><th>Price</th></tr>
        <?php
        $price =10000;
        $conn = mysqli_connect("localhost", "root", "", "furniture");
        $stmt = "select * from `product` where `price` >= '$price'";
        $result = mysqli_query($conn, $stmt);
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[7]</td></tr>";
        }
        ?>
      </table>
    </div>

    <p class="report-footer">Dafa · Price Report · Generated <?= date("d M Y, H:i") ?></p>
  </div>

  <div class="report-divider"></div>

  <!-- ══ REPORT 2: STOCK ══ -->
  <div class="report-block">
    <p class="report-eyebrow">Report 2</p>
    <h2 class="report-title">Stock Report</h2>
    <p class="report-sub">Products with 5 units or fewer remaining in stock</p>

    <div class="table-wrap">
      <table border="1">
        <tr><th>Product_Id</th><th>Product_name</th><th>Stock_Quantity</th></tr>
        <?php
        $stock =5;
        $conn = mysqli_connect("localhost", "root", "", "furniture");
        $stmt = "select * from `product` where `stock_quantity` <= '$stock'";
        $result = mysqli_query($conn, $stmt);
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr><td>$row[0]</td><td>$row[1]</td><td>$row[4]</td></tr>";
        }
        ?>
      </table>
    </div>

    <p class="report-footer">Dafa · Stock Report · Generated <?= date("d M Y, H:i") ?></p>
  </div>

</body>
</html>