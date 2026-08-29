<?php
session_start();

// Initialize cart session variables if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['cartItemCount'])) {
    $_SESSION['cartItemCount'] = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dining Collection — DAFA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --cream:   #F5F4EC;
            --sage:    #7D9B76;
            --sage-dk: #5e7a58;
            --beige:   #C9B8A8;
            --beige-lt:#E8DDD4;
            --ink:     #2C2C2A;
            --muted:   #7A7872;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: var(--cream);
            font-family: 'Jost', sans-serif;
            font-weight: 300;
            color: var(--ink);
            letter-spacing: 0.01em;
        }


        /* ── PAGE HERO ── */
        .collection-hero {
            padding: 5rem 3rem 3rem;
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            border-bottom: 1px solid var(--beige-lt);
            margin-bottom: 3rem;
        }
        .collection-hero h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 300;
            line-height: 1.1;
            color: var(--ink);
        }
        .collection-hero h2 em {
            font-style: italic;
            color: var(--sage);
        }
        .collection-hero p {
            font-size: 0.82rem;
            color: var(--muted);
            letter-spacing: 0.05em;
            max-width: 280px;
            text-align: right;
            line-height: 1.8;
        }

        /* ── PRODUCT GRID ── */
        .products-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 3rem 6rem;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2px;
        }

        /* ── PRODUCT CARD ── */
        .product-card {
            background: var(--cream);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: background 0.3s;
        }
        .product-card:hover { background: #EFEDE4; }

        .product-card form { display: flex; flex-direction: column; height: 100%; }

        .card-image {
            position: relative;
            overflow: hidden;
            aspect-ratio: 4/3;
            background: var(--beige-lt);
        }
        .card-image img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            display: block;
        }
        .product-card:hover .card-image img { transform: scale(1.04); }

        .card-overlay {
            position: absolute;
            inset: 0;
            background: rgba(44,44,42,0);
            transition: background 0.4s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-card:hover .card-overlay { background: rgba(44,44,42,0.08); }

        .card-body-custom {
            padding: 1.4rem 1.6rem 1.6rem;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            border-top: 1px solid var(--beige-lt);
        }

        .card-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem;
            font-weight: 400;
            color: var(--ink);
            letter-spacing: 0.02em;
        }

        .card-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-price {
            font-size: 0.85rem;
            color: var(--ink);
            font-weight: 400;
            letter-spacing: 0.05em;
        }
        .card-price span {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.1rem;
            color: var(--sage-dk);
        }

        .card-stock {
            font-size: 0.68rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
        }
        .card-stock.low { color: #B85C38; }

        .card-footer-custom {
            padding: 0 1.6rem 1.6rem;
            margin-top: auto;
        }

        .btn-add-cart {
            width: 100%;
            background: transparent;
            border: 1px solid var(--ink);
            color: var(--ink);
            font-family: 'Jost', sans-serif;
            font-size: 0.68rem;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: background 0.25s, color 0.25s, border-color 0.25s;
        }
        .btn-add-cart:hover {
            background: var(--sage);
            border-color: var(--sage);
            color: #fff;
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 6rem 2rem;
            color: var(--muted);
        }
        .empty-state h3 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 300;
            margin-bottom: 0.75rem;
            color: var(--ink);
        }
        .empty-state p { font-size: 0.82rem; letter-spacing: 0.05em; }

        /* ── FOOTER ── */
        footer {
            background: var(--ink);
            color: var(--beige-lt);
            text-align: center;
            padding: 2.5rem;
            font-size: 0.7rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            nav.dafa-nav { padding: 0 1.25rem; }
            .dafa-nav-links { display: none; }
            .collection-hero { padding: 3rem 1.25rem 2rem; flex-direction: column; gap: 1rem; }
            .collection-hero p { text-align: left; }
            .products-wrapper { padding: 0 1.25rem 4rem; }
            .products-grid { grid-template-columns: 1fr 1fr; gap: 2px; }
        }
        @media (max-width: 480px) {
            .products-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<?php include 'nav2.php'; ?>


<!-- HERO HEADER -->
<div class="collection-hero">
    <div>
        <h2>The <em>Living</em><br>Collection</h2>
    </div>
    <p>Sofas, tables & shelving, and much more.</p>
</div>

<!-- PRODUCTS -->
<div class="products-wrapper">
    <?php
    function getConnection() {
        $conn = mysqli_connect("localhost", "root", "", "furniture");
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        return $conn;
    }

    $conn = getConnection();
    $query = "SELECT * FROM product WHERE cateogry = 'Living Room'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 0): ?>
        <div class="empty-state">
            <h3>No pieces found</h3>
            <p>The dining collection is being curated. Check back soon.</p>
        </div>
    <?php else: ?>
        <div class="products-grid">
        <?php while ($prodset = mysqli_fetch_array($result)): ?>
            <div class="product-card">
                <form action="" method="post">
                    <input type="hidden" name="prodcode" value="<?= htmlspecialchars($prodset[0]) ?>">

                    <div class="card-image">
                        <img src="<?= htmlspecialchars($prodset[8]) ?>"
                             alt="<?= htmlspecialchars($prodset[1] ?? 'Product') ?>">
                        <div class="card-overlay"></div>
                    </div>

                    <div class="card-body-custom">
                        <div class="card-name"><?= htmlspecialchars($prodset[1] ?? 'Dining Piece') ?></div>
                        <div class="card-meta">
                            <div class="card-price">
                                <span>EGP <?= number_format($prodset[7], 0) ?></span>
                            </div>
                            <div class="card-stock <?= ($prodset[4] <= 5) ? 'low' : '' ?>">
                                <?= htmlspecialchars($prodset[4]) ?> in stock
                            </div>
                        </div>
                    </div>

                    <div class="card-footer-custom">
                        <button type="submit" name="Buy" value="Buy" class="btn-add-cart">
                            Add to Cart
                        </button>
                    </div>
                </form>
            </div>
        <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<footer>
    &copy; <?= date('Y') ?> DAFA — Warmth in Every Piece
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>