<?php
// top of nav2.php

if (isset($_POST['Buy'])) {
    $prodcode = $_POST['prodcode'];

    // Fetch the selected product from the DB
    $conn_cart = mysqli_connect("localhost", "root", "", "furniture");
    $safe_id   = mysqli_real_escape_string($conn_cart, $prodcode);
    $res       = mysqli_query($conn_cart, "SELECT * FROM product WHERE product_id = '$safe_id'");
    $row       = mysqli_fetch_array($res);

    // Build a product array matching the cart structure
    $product = [
        'id'    => $row['product_id'],
        'product_name'  => $row['product_name'],
        'price' => $row['price'],
        'picture' => $row['picture'],
        'qunt'  => 1,
    ];

    // Extract IDs already in the cart
    $ids = array_column($_SESSION['cart'], 'id');

    if (!in_array($product['id'], $ids)) {
        // Product not in cart yet — add it
        $_SESSION['cart'][] = $product;
        $_SESSION['cartItemCount'] += 1;
    } else {
        // Product already in cart — increment its quantity
        foreach ($_SESSION['cart'] as $key => $stortd_product) {
            if ($product['id'] == $stortd_product['id']) {
                $_SESSION['cart'][$key]['qunt'] += 1;
            }
        }
        $_SESSION['cartItemCount'] += 1;
    }

    mysqli_close($conn_cart);
  
}
?>
<!-- rest of nav HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>Document</title>
         
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

        /* ── NAV ── */
        nav.dafa-nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--cream);
            border-bottom: 1px solid var(--beige-lt);
            padding: 0 3rem;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dafa-brand h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            font-weight: 500;
            letter-spacing: 0.15em;
            color: var(--sage);
        }
        .brand-link {
            text-decoration: none;
            
        }
      
        .dafa-brand p {
            font-size: 0.6rem;
            letter-spacing: 0.25em;
            color: var(--muted);
            text-transform: uppercase;
            margin-top: -2px;
        }
        .dafa-nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
        }
        
        .dafa-nav-links a {
            font-size: 0.72rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            text-decoration: none;
            color: var(--ink);
            transition: color 0.2s;
        }
        .dafa-nav-links a:hover { color: var(--sage); }
        .nav-right { display: flex; align-items: center; gap: 1.5rem; }
        .cart-icon {
            position: relative;
            text-decoration: none;
            color: var(--ink);
        }
        .cart-count {
            position: absolute;
            top: -6px; right: -8px;
            background: var(--sage);
            color: #fff;
            border-radius: 50%;
            width: 16px; height: 16px;
            font-size: 0.6rem;
            display: flex; align-items: center; justify-content: center;
        }
        .btn-logout {
            background: var(--sage);
            color: #fff;
            border: none;
            padding: 0.45rem 1.2rem;
            font-family: 'Jost', sans-serif;
            font-size: 0.7rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            text-decoration: none;
            transition: background 0.25s;
        }
        .btn-logout:hover { background: var(--sage-dk); color: #fff; }

        /* Dropdown wrapper */
.nav-dropdown {
    position: relative;
}

/* The trigger link — inherits your existing nav link style automatically */
/* This creates an invisible bridge so the menu doesn't close */
.nav-dropdown-menu::before {
    content: "";
    position: absolute;
    top: -16px; 
    left: 0;
    right: 0;
    height: 16px;
}

/* Arrow icon */
.nav-dropdown > a span {
    font-size: 10px;
    color: var(--sage);
}

/* Hidden dropdown list */
.nav-dropdown-menu {
    display: none;
    position: absolute;
    top: calc(100% + 16px);
    left: 0;
    background: var(--cream);
    border: 1px solid var(--beige-lt);
    border-radius: 4px;
    list-style: none;
    min-width: 160px;
    padding: 6px 0;
    z-index: 999;
    box-shadow: 0 8px 20px rgba(0,0,0,0.07);
}

/* Show on hover */
.nav-dropdown:hover .nav-dropdown-menu {
    display: block;
}

/* Dropdown items */
.nav-dropdown-menu li a {
    display: block;
    padding: 9px 18px;
    font-size: 0.68rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--ink);
    text-decoration: none;
    transition: background 0.15s, color 0.15s;
}

.nav-dropdown-menu li a:hover {
    background: var(--beige-lt);
    color: var(--sage);
}

.dafa-nav-links li {
    display: flex;
    align-items: center;
}

.dafa-nav-links a {
    font-size: 0.72rem;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    text-decoration: none;
    color: var(--ink);
    transition: color 0.2s;
    
    /* Add these lines */
    display: flex;
    align-items: center;
    height: 100%; 
}
    </style>

</head>
<body>

    <nav class="dafa-nav">
    <div class="dafa-brand">
        <a href="nothome.php?" class="brand-link">
           <h1> DAFA</h1></a>
        <p>Warmth in every piece</p>
    </div>
    <ul class="dafa-nav-links">
        <li><a href="#">Collections</a></li>
         <li class="nav-dropdown">
    <a href="#">Select Room <span>▾</span></a>
    <ul class="nav-dropdown-menu">
        <li><a href="living.php?room=Living Room">Living Room</a></li>
        <li><a href="bedroom.php">Bedroom</a></li>
        <li><a href="dining2.php">Dining</a></li>
        <li><a href="nothome.php?room=Bath">Bath</a></li>
        <li><a href="nothome.php?room=Storage Unit">Storage Unit</a></li>
        <li><a href="nothome.php?room=Carpets">Carpets</a></li>
    </ul>
</li>
        <li><a href="#">Bespoke</a></li>
        <li><a href="#">Our Story</a></li>
    </ul>
    <div class="nav-right">
        <a href="mycart.php" class="cart-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
            </svg>
            <span class="cart-count"><?php echo isset($_SESSION['cartItemCount']) ? $_SESSION['cartItemCount'] : 0; ?></span>
        </a>
        <a href="Logout.php" class="btn-logout">Log Out</a>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>