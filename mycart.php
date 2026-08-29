<!-- <span></span> -->
<?php
// ================================
// Session & cart initialization
// ================================
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Calculate total
$total = 0;
$ids   = [];
$q     = [];

foreach ($_SESSION['cart'] as $stortd_product) {
    $total += $stortd_product['qunt'] * $stortd_product['price'];
}
$_SESSION['total'] = $total;

foreach ($_SESSION['cart'] as $stortd_product) {
    $ids[] = $stortd_product['id'];
    $q[]   = $stortd_product['qunt'];
}

// Promo code logic
$discount         = 0;
$promo_code       = '';
$promo_message    = '';
$discount_applied = 0;

if (isset($_POST['apply_promo'])) {
    $promo_code = trim($_POST['promo_code']);
    $valid_promos = ['DAFA10' => 10, 'WARMTH20' => 20];
    if (array_key_exists($promo_code, $valid_promos)) {
        $discount         = $valid_promos[$promo_code];
        $discount_applied = $total * ($discount / 100);
        $promo_message    = 'success';
    } else {
        $promo_message = 'invalid';
    }
}

$final_total = $total - $discount_applied;

// DB connection
$conn = mysqli_connect("localhost", "root", "", "furniture");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DAFA — Your Cart</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        /* ── Palette ── */
        :root {
            --cream:   #FAFAF3;
            --sage:    #8FA68A;
            --sage-dk: #6B8A65;
            --sand:    #D4C4B0;
            --grey:    #EEEEEE;
            --dark:    #2C2C2C;
            --mid:     #6B6B6B;
        }

        /* ── Base ── */
        body { background-color: var(--cream); color: var(--dark); font-family: 'Jost', sans-serif; font-weight: 300; }

        

        /* ── Page header ── */
        .page-header { border-bottom: 1px solid var(--grey); }
        .page-title { font-family: 'Cormorant Garamond', serif; font-size: 2.4rem; font-weight: 300; letter-spacing: .04em; }
        .breadcrumb-item a { color: var(--mid); text-decoration: none; font-size: .7rem; letter-spacing: .2em; text-transform: uppercase; }
        .breadcrumb-item.active { color: var(--mid); font-size: .7rem; letter-spacing: .2em; text-transform: uppercase; }
        .breadcrumb-item + .breadcrumb-item::before { color: var(--mid); }

        /* ── Cart table ── */
        .cart-table thead th { font-size: .68rem; letter-spacing: .22em; text-transform: uppercase; color: var(--mid); font-weight: 400; background: transparent; border-bottom: 1px solid var(--grey); }
        .cart-table tbody tr { border-bottom: 1px solid var(--grey); }
        .cart-table tbody td { vertical-align: middle; padding: 22px 12px; border: none; background: transparent; }
        .item-name { font-family: 'Cormorant Garamond', serif; font-size: 1rem; font-weight: 400; }
        .item-code { font-size: .68rem; color: var(--mid); letter-spacing: .1em; }
        .item-thumb { width: 68px; height: 68px; object-fit: cover; background: var(--grey); }
        .subtotal-cell { color: var(--sage-dk); font-weight: 400; font-size: .88rem; }

        /* Qty stepper */
        .qty-group { max-width: 112px; }
        .qty-group .form-control { text-align: center; border-color: var(--grey); border-radius: 0; background: transparent; font-size: .82rem; color: var(--dark); pointer-events: none; }
        .qty-group .btn { border-color: var(--grey); background: transparent; color: var(--mid); border-radius: 0; padding: 4px 10px; font-size: .85rem; }
        .qty-group .btn:hover { background: var(--grey); color: var(--dark); }

        /* Remove */
        .btn-remove { color: var(--sand); background: none; border: none; padding: 4px 6px; }
        .btn-remove:hover { color: #b06060; }

        /* ── Empty cart ── */
        .empty-icon { font-size: 4rem; color: var(--sand); opacity: .55; }
        .empty-title { font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; font-weight: 300; }

        /* ── Summary card ── */
        .summary-card { background: #fff; border: 1px solid var(--grey) !important; border-radius: 0 !important; }
        .summary-card-title { font-family: 'Cormorant Garamond', serif; font-size: 1.25rem; font-weight: 400; letter-spacing: .06em; border-bottom: 1px solid var(--grey); padding-bottom: 14px; }
        .summary-row { font-size: .83rem; color: var(--mid); }
        .summary-row.total-row { color: var(--dark); font-size: .93rem; font-weight: 500; }
        .shipping-free { color: var(--sage); }
        .discount-row { color: var(--sage); font-size: .83rem; }

        /* Promo */
        .promo-label  { font-size: .7rem; letter-spacing: .2em; text-transform: uppercase; color: var(--mid); }
        .promo-input  { border-radius: 0; border-color: var(--grey); font-size: .82rem; background: transparent; }
        .promo-input:focus { border-color: var(--sand); box-shadow: none; }
        .btn-promo    { background: var(--sand); border-color: var(--sand); color: var(--dark); border-radius: 0; font-size: .7rem; letter-spacing: .15em; text-transform: uppercase; }
        .btn-promo:hover { background: #c4b09e; border-color: #c4b09e; color: var(--dark); }

        /* Payment */
        .payment-label  { font-size: .7rem; letter-spacing: .2em; text-transform: uppercase; color: var(--mid); }
        .payment-option { border: 1px solid var(--grey); padding: 11px 14px; font-size: .82rem; }
        .payment-option:hover { border-color: var(--sand); background: rgba(212,196,176,.08); }
        .form-check-input:checked { background-color: var(--sage); border-color: var(--sage); }

        /* Checkout */
        .btn-checkout { background-color: var(--sage) !important; border-color: var(--sage) !important; color: #fff !important; border-radius: 0; font-size: .75rem; letter-spacing: .22em; text-transform: uppercase; padding: 14px; }
        .btn-checkout:hover { background-color: var(--sage-dk) !important; border-color: var(--sage-dk) !important; }
        .btn-checkout:disabled { opacity: .4; }

        /* Continue link */
        .continue-link { font-size: .72rem; letter-spacing: .15em; text-transform: uppercase; color: var(--mid); text-decoration: none; }
        .continue-link:hover { color: var(--sage); }

        /* Divider on desktop */
        @media (min-width: 992px) { .cart-col { border-right: 1px solid var(--grey); } }
    </style>
</head>
<body>

<!-- ══════════ NAVBAR ══════════ -->
<?php include 'nav2.php'; ?>



<!-- ══════════ PAGE HEADER ══════════ -->
<div class="page-header py-4 px-4 px-lg-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
        </ol>
    </nav>
    <h2 class="page-title mb-0">Your Cart</h2>
</div>

<!-- ══════════ MAIN CONTENT ══════════ -->
<div class="container-fluid px-4 px-lg-5 py-5">
    <div class="row g-0">

        <!-- ── Left: Items ── -->
        <div class="col-lg-8 cart-col pe-lg-5">

            <?php if (empty($ids)): ?>

            <!-- Empty state -->
            <div class="text-center py-5">
                <div class="empty-icon mb-3"><i class="bi bi-bag"></i></div>
                <h3 class="empty-title mb-2">Your cart is empty</h3>
                <p class="mb-4" style="color:var(--mid);font-size:.85rem;">
                    Discover pieces that invite you to slow down and stay a while.
                </p>
                <a href="nothome.php" class="btn btn-checkout px-5">Back to Collections</a>
            </div>

            <?php else: ?>

            <table class="table cart-table">
                <thead>
                    <tr>
                        <th style="width:42%">Product</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th class="d-none d-md-table-cell">Sub Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $stmt1   = "SELECT * FROM `product`";
                $result2 = mysqli_query($conn, $stmt1);

                while ($row = mysqli_fetch_array($result2)):
                    for ($i = 0; $i < count($ids); $i++):
                        if ($row['product_id'] == $ids[$i]):
                            $sub_total = $row['price'] * $q[$i];
                ?>
                <tr>
                    <!-- Product info -->
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <?php if (!empty($row['picture'])): ?>
                                <img src="<?= htmlspecialchars($row['picture']) ?>"
                                     class="item-thumb d-none d-sm-block flex-shrink-0"
                                     alt="<?= htmlspecialchars($row['product_name'] ?? '') ?>">
                            <?php else: ?>
                                <div class="item-thumb d-none d-sm-flex align-items-center justify-content-center flex-shrink-0">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="item-name"><?= htmlspecialchars($row['product_name'] ?? 'Product') ?></div>
                                <div class="item-code">#<?= htmlspecialchars($row['product_id']) ?></div>
                            </div>
                        </div>
                    </td>

                    <!-- Unit price -->
                    <td style="font-size:.88rem;">EGP <?= number_format($row['price'], 2) ?></td>

                    <!-- Quantity — two separate forms, one per button, no JS -->
                    <td>
                        <div class="input-group qty-group">
                            <form method="post" action="update_cart.php" class="m-0">
                                <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                                <input type="hidden" name="action" value="decrease">
                                <button type="submit" class="btn btn-outline-secondary">−</button>
                            </form>
                            <span class="form-control"><?= $q[$i] ?></span>
                            <form method="post" action="update_cart.php" class="m-0">
                                <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                                <input type="hidden" name="action" value="increase">
                                <button type="submit" class="btn btn-outline-secondary">+</button>
                            </form>
                        </div>
                    </td>

                    <!-- Sub total -->
                    <td class="subtotal-cell d-none d-md-table-cell">
                        EGP <?= number_format($sub_total, 2) ?>
                    </td>

                    <!-- Remove -->
                    <td>
                        <form method="post" action="remove_from_cart.php">
                            <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                            <button type="submit" class="btn-remove" title="Remove item">
                                <i class="bi bi-trash" style="font-size:1rem;"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php
                        endif;
                    endfor;
                endwhile;
                ?>
                </tbody>
            </table>

            <?php endif; ?>

            <div class="mt-3">
                <a href="nothome.php" class="continue-link">
                    <i class="bi bi-arrow-left me-1"></i>Continue Shopping
                </a>
            </div>
        </div><!-- /col-lg-8 -->

        <!-- ── Right: Order Summary ── -->
        <div class="col-lg-4 ps-lg-5 mt-5 mt-lg-0">
            <div class="card summary-card border p-4">

                <div class="summary-card-title mb-4">Order Summary</div>

                <div class="d-flex justify-content-between summary-row py-3 border-bottom" style="border-color:var(--grey)!important;">
                    <span>Subtotal</span>
                    <span>EGP <?= number_format($total, 2) ?></span>
                </div>

                <div class="d-flex justify-content-between summary-row py-3 border-bottom" style="border-color:var(--grey)!important;">
                    <span>Shipping</span>
                    <span class="shipping-free">Free</span>
                </div>

                <?php if ($discount_applied > 0): ?>
                <div class="d-flex justify-content-between discount-row py-3 border-bottom" style="border-color:var(--grey)!important;">
                    <span>Promo (<?= htmlspecialchars($promo_code) ?>)</span>
                    <span>− EGP <?= number_format($discount_applied, 2) ?></span>
                </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between summary-row total-row py-3 border-bottom" style="border-color:var(--grey)!important;">
                    <span>Total</span>
                    <span>EGP <?= number_format($final_total, 2) ?></span>
                </div>

                <!-- Promo Code -->
                <div class="mt-4">
                    <label class="promo-label d-block mb-2">Promo Code</label>
                    <form method="post" action="">
                        <div class="input-group">
                            <input type="text" name="promo_code" class="form-control promo-input"
                                   placeholder="Enter code"
                                   value="<?= htmlspecialchars($promo_code) ?>">
                            <button type="submit" name="apply_promo" class="btn btn-promo">Apply</button>
                        </div>
                    </form>
                    <?php if ($promo_message === 'success'): ?>
                        <div class="mt-2 small" style="color:var(--sage);">
                            <i class="bi bi-check-circle me-1"></i>Code applied — <?= $discount ?>% off
                        </div>
                    <?php elseif ($promo_message === 'invalid'): ?>
                        <div class="mt-2 small" style="color:#b06060;">
                            <i class="bi bi-x-circle me-1"></i>Invalid promo code
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Payment Method -->
                <div class="mt-4">
                    <label class="payment-label d-block mb-2">Payment Method</label>
                    <div class="payment-option mb-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="radio" name="payment" id="dbt" value="dbt" checked>
                            <label class="form-check-label" for="dbt">Direct Bank Transfer</label>
                        </div>
                    </div>
                    <div class="payment-option">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="radio" name="payment" id="cod" value="cod">
                            <label class="form-check-label" for="cod">Cash on Delivery</label>
                        </div>
                    </div>
                </div>

                <!-- Checkout -->
                <?php if (!empty($ids)): ?>
                    <a href="confirm_order3.php" class="btn btn-checkout w-100 mt-4">
                        Proceed to Checkout
                    </a>
                <?php else: ?>
                    <button class="btn btn-checkout w-100 mt-4" disabled>
                        Proceed to Checkout
                    </button>
                <?php endif; ?>

            </div>
        </div><!-- /col-lg-4 -->

    </div><!-- /row -->
</div><!-- /container -->

<!-- Bootstrap 5 JS bundle (includes Popper — needed for navbar collapse) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>