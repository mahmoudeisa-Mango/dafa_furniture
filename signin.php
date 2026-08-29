<?php
session_start();
?>
<?php
if(isset($_POST['sigin'])){
    $email = $_POST["email"];
    $pass = $_POST["password"];
    $conn = mysqli_connect("localhost","root","","furniture");
    $stmt = "SELECT * from `customer` where `email`='$email'";
    $result = mysqli_query($conn,$stmt);
    if( $row = mysqli_fetch_array($result)){
        // Verify the hashed password
        if (password_verify($pass, $row['password'])){
            // Storing user data in the session
            $_SESSION['customerid'] = $row[0];
            $_SESSION['email'] = $row[3];

            // Accessing the session variable
            header("Location: nothome.php");
            exit();
        }
        else{
            echo "Login failed. Invalid Email/Password!";
        }
    }
    else{
        echo "Login failed. Invalid Email/Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dafa — Sign In</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
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
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }

    /* ── Card ── */
    .card {
      width: 100%;
      max-width: 400px;
      background: var(--ivory);
      border: 1px solid var(--linen);
      border-radius: 6px;
      padding: 2.8rem 2.4rem 2.4rem;
      margin-top: -3rem;
    }

    /* ── Brand ── */
    .brand {
      text-align: center;
      margin-bottom: 2rem;
    }

    .brand-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 36px;
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
      font-weight: 300;
    }

    .brand-rule {
      width: 28px;
      height: 1px;
      background: var(--sage);
      margin: 1rem auto 0;
    }

    /* ── Headings ── */
    .form-eyebrow {
      font-size: 9.5px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--sage);
      margin-bottom: 4px;
    }

    .form-heading {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      font-weight: 400;
      color: var(--ink);
      margin-bottom: 4px;
    }

    .form-sub {
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

    /* ── Fields ── */
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

    .field input {
      width: 100%;
      height: 42px;
      border: 1px solid var(--linen);
      border-radius: 4px;
      padding: 0 13px;
      font-family: 'DM Sans', sans-serif;
      font-size: 13.5px;
      color: var(--ink);
      background: var(--ivory);
      outline: none;
      transition: border-color 0.2s, background 0.2s;
    }

    .field input::placeholder { color: var(--linen); }

    .field input:focus {
      border-color: var(--sage);
      background: var(--ivory);
    }


    /* ── Button ── */
    .btn-primary {
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
      border-radius: 22px;       /* fully rounded pill */
      transition: opacity 0.2s;
    }

    .btn-primary:hover { opacity: 0.88; }

    /* ── Alt link ── */
    .alt-link {
      text-align: center;
      margin-top: 1.2rem;
      font-size: 12px;
      color: var(--ink);
      opacity: 0.5;
      font-weight: 300;
    }

    .alt-link a {
      color: var(--sage);
      text-decoration: none;
      opacity: 1;
      border-bottom: 1px solid var(--linen);
      padding-bottom: 1px;
      transition: border-color 0.2s;
    }

    .alt-link a:hover { border-color: var(--sage); }
  </style>
</head>
<body>

<div class="card">

  <!-- Brand -->
  <div class="brand">
    <span class="brand-name">Dafa</span>
    <span class="brand-sub">دفا · Warmth in every piece</span>
    <div class="brand-rule"></div>
  </div>

  <!-- Form headings -->
  <p class="form-eyebrow">Welcome back</p>
  <h1 class="form-heading">Sign in</h1>
  <p class="form-sub">Access your orders and bespoke requests.</p>
  <div class="divider"></div>

  <form action="" method="post" novalidate>

    <div class="field">
      <label for="email">Email address</label>
      <input type="email" id="email" name="email"
             placeholder="your@email.com" autocomplete="email" required>
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password"
             placeholder="••••••••" autocomplete="current-password" required>
    </div>


    <input type="submit" class="btn-primary" name="sigin" value="SIGN IN">

    <p class="alt-link" style="margin-top: 1.2rem;">
      New here? <a href="dafa_signup_form new.html">Create an account</a>
    </p>

  </form>

</div>

</body>
</html>


 

        

