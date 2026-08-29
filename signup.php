<?php
if (isset($_POST['sub'])) {

    $fname   = $_POST['fname'];
    $lname   = $_POST['lname'];
    $email   = $_POST['email'];
    $phone   = $_POST['phone'];
    $pass1   = $_POST['password'];
    $pass2   = $_POST['confirm'];
    $street  = $_POST['street'];
    $city    = $_POST['city'];
    $country = $_POST['country'];

    if ($pass1 !== $pass2) {
        die("Error: Passwords do not match.");
    }

    $conn = mysqli_connect("localhost", "root", "", "furniture");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $stmt1 = mysqli_prepare($conn, "SELECT COUNT(*) AS count FROM customer WHERE email = ?");
    mysqli_stmt_bind_param($stmt1, "s", $email);
    mysqli_stmt_execute($stmt1);
    $result1 = mysqli_stmt_get_result($stmt1);
    $row = mysqli_fetch_assoc($result1);

    if ($row['count'] > 0) {
        die("Error: Email already exists.");
    }

    $hashed_password = password_hash($pass1, PASSWORD_DEFAULT);

    $stmt2 = mysqli_prepare($conn, "INSERT INTO customer 
        (first_name, last_name, email, phone, password, street_adress, city, country)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt2, "ssssssss",
        $fname, $lname, $email, $phone,
        $hashed_password, $street, $city, $country);

    if (mysqli_stmt_execute($stmt2)) {
        header("Location: signin.php");
        exit();
    } else {
        die("Error: " . mysqli_stmt_error($stmt2));
    }

    mysqli_close($conn);
}
if(isset($_POST['sub'])){
    header("Location:login new.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dafa — Create Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    /* ── Palette — matches homepage exactly ── */
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
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding-bottom: 4rem;
    }

    .form-container {
      width: 100%;
      max-width: 520px;
      padding: 0.1rem 1.5rem 0;
      animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── Brand ── */
    .brand {
      text-align: center;
      margin-bottom: 1rem;
    }

    .brand-name {
      font-family: 'Cormorant Garamond', serif;
      font-size: 38px;
      font-weight: 300;
      letter-spacing: 8px;
      color: var(--sage);
      text-transform: uppercase;
      display: block;
    }

    .brand-sub {
      font-size: 10px;
      letter-spacing: 4px;
      color: var(--linen);
      text-transform: uppercase;
      margin-top: 4px;
      display: block;
      font-weight: 300;
    }

    .ornament {
      text-align: center;
      color: var(--linen);
      font-size: 14px;
      letter-spacing: 6px;
      margin-bottom: 1rem;
    }

    /* ── Card ── */
    .form-card {
      background: var(--ivory);
      border: 1px solid var(--linen);
      border-radius: 4px;
      padding: 2.5rem 2.5rem 2rem;
    }

    .form-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 22px;
      font-weight: 400;
      color: var(--sage);
      letter-spacing: 1px;
      margin-bottom: 0.3rem;
    }

    .form-subtitle {
      font-size: 12px;
      color: var(--ink);
      opacity: 0.45;
      letter-spacing: 0.3px;
      margin-bottom: 2rem;
      font-weight: 300;
    }

    .divider-line {
      height: 1px;
      background: var(--linen);
      margin-bottom: 2rem;
    }

    /* ── Section labels ── */
    .section-label {
      font-size: 9px;
      letter-spacing: 3px;
      text-transform: uppercase;
      color: var(--sage);
      margin: 1.4rem 0 1.1rem;
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 500;
    }

    .section-label::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--linen);
    }

    /* ── Fields ── */
    .field-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }

    .field { margin-bottom: 16px; }

    .field label {
      display: block;
      font-size: 10px;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--ink);
      opacity: 0.5;
      margin-bottom: 6px;
      font-weight: 500;
    }

    .field input {
      width: 100%;
      height: 43px;
      border: 1px solid var(--linen);
      border-radius: 4px;
      padding: 0 14px;
      font-family: 'DM Sans', sans-serif;
      font-size: 13.5px;
      color: var(--ink);
      background: var(--ivory);
      outline: none;
      transition: border-color 0.2s, background 0.2s;
    }

    .field input::placeholder { color: var(--linen); font-weight: 300; }

    .field input:focus {
      border-color: var(--sage);
      background: var(--ivory);
    }

    .field select {
      width: 100%;
      height: 43px;
      border: 1px solid var(--linen);
      border-radius: 4px;
      padding: 0 14px;
      font-family: 'DM Sans', sans-serif;
      font-size: 13.5px;
      color: var(--ink);
      background: var(--ivory);
      outline: none;
      appearance: none;
      cursor: pointer;
      transition: border-color 0.2s;
    }

    .field select:focus { border-color: var(--sage); }

    .field input[readonly] {
      color: var(--ink);
      opacity: 0.6;
      cursor: not-allowed;
    }

    /* ── Button ── */
    .submit-btn {
      width: 100%;
      height: 46px;
      border: none;
      background: var(--sage);
      color: var(--ivory);
      font-family: 'DM Sans', sans-serif;
      font-size: 10.5px;
      font-weight: 500;
      letter-spacing: 3px;
      text-transform: uppercase;
      cursor: pointer;
      border-radius: 23px;     /* pill shape — matches login */
      margin-top: 1.6rem;
      transition: opacity 0.2s, transform 0.15s;
    }

    .submit-btn:hover  { opacity: 0.88; }
    .submit-btn:active { transform: scale(0.99); }

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

<form action="" method="post">
<div class="form-container">

  <!-- Brand -->
  <div class="brand">
    <span class="brand-name">Dafa</span>
    <span class="brand-sub">دفا · Warmth in every piece</span>
  </div>

  <div class="ornament">· · ·</div>

  <div class="form-card">
    <div class="form-title">Create your account</div>
    <div class="form-subtitle">Join Dafa and bring warmth into your space</div>
    <div class="divider-line"></div>

    <!-- Personal Details -->
    <div class="section-label">Personal Details</div>

    <div class="field-row">
      <div class="field">
        <label>First Name</label>
        <input type="text" placeholder="Ahmed" name="fname" required>
      </div>
      <div class="field">
        <label>Last Name</label>
        <input type="text" placeholder="Karim" name="lname" required>
      </div>
    </div>

    <div class="field">
      <label>Email Address</label>
      <input type="email" placeholder="your@email.com" name="email" required>
    </div>

    <div class="field">
      <label>Phone Number</label>
      <input type="tel" placeholder="+20 10 0000 0000" name="phone" required>
    </div>

    <!-- Security -->
    <div class="section-label">Security</div>

    <div class="field">
      <label>Password</label>
      <input type="password" placeholder="••••••••" name="password" required>
    </div>

    <div class="field">
      <label>Confirm Password</label>
      <input type="password" placeholder="••••••••" name="confirm" required>
    </div>

    <!-- Delivery Address -->
    <div class="section-label">Delivery Address</div>

    <div class="field">
      <label>Street Address</label>
      <input type="text" placeholder="123 Al Nile Street, Apt 4" name="street" required>
    </div>

    <div class="field-row">
      <div class="field">
        <label>City</label>
        <select name="city" required>
          <option value="" disabled selected>Select city</option>
          <option>Cairo</option>
          <option>Giza</option>
          <option>Alexandria</option>
          <option>Shubra El Kheima</option>
          <option>Port Said</option>
          <option>Suez</option>
          <option>Luxor</option>
          <option>Aswan</option>
          <option>Asyut</option>
          <option>Ismailia</option>
          <option>Fayyum</option>
          <option>Zagazig</option>
          <option>Damietta</option>
          <option>Mansoura</option>
          <option>Tanta</option>
          <option>Beni Suef</option>
          <option>Sohag</option>
          <option>Minya</option>
          <option>Hurghada</option>
          <option>Sharm El Sheikh</option>
          <option>Marsa Matrouh</option>
          <option>Qena</option>
          <option>New Cairo</option>
          <option>6th of October</option>
          <option>Obour</option>
          <option>Badr City</option>
          <option>Shibin El Kom</option>
          <option>Banha</option>
          <option>Kafr El Sheikh</option>
          <option>Damanhour</option>
        </select>
      </div>
      <div class="field">
        <label>Country</label>
        <input type="text" name="country" value="Egypt" readonly>
      </div>
    </div>

    <input class="submit-btn" type="submit" name="sub" value="Create Account">

    <div class="alt-link">
      Already have an account? <a href="login new.html">Sign in</a>
    </div>
  </div>

</div>
</form>

</body>
</html>
