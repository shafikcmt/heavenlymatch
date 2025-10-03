<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Packages</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
  /* Navbar */
  .navbar-custom {
    background-color: #007bff;
    color: #fff;
    padding: 10px 20px;
  }
  .navbar-custom .navbar-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .navbar-custom .navbar-brand img {
    height: 40px;
  }
  .navbar-custom .user-info {
    font-weight: bold;
  }

  /* Section Title */
  .package-title {
    text-align: center;
    font-weight: bold;
    font-size: 28px;
    margin-bottom: 30px;
    position: relative;
  }
  .package-title::after {
    content: "";
    display: block;
    width: 120px;
    height: 4px;
    background: #e63946;
    margin: 10px auto 0;
    border-radius: 50px;
  }

  /* Duration Buttons */
  .duration-btns {
    text-align: center;
    margin-bottom: 35px;
  }
  .duration-btns button {
    border: 1px solid #e63946;
    background: transparent;
    color: #e63946;
    padding: 10px 22px;
    margin: 5px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
  }
  .duration-btns button:hover,
  .duration-btns button.active {
    background: #e63946;
    color: #fff;
    transform: scale(1.05);
  }

  /* Package Card */
  .package-card {
    border: 1px solid #eee;
    border-radius: 16px;
    background: #fff;
    padding: 30px 25px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    height: 100%; /* equal height */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .package-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    border: 1px solid #e63946;
  }
  .package-card h5 {
    font-weight: 700;
    font-size: 20px;
  }
  .package-card .price {
    font-size: 24px;
    font-weight: bold;
    color: #000;
  }
  .package-card ul {
    list-style: none;
    padding-left: 0;
    margin: 20px 0;
  }
  .package-card ul li {
    margin-bottom: 10px;
    font-size: 15px;
    color: #333;
  }
  .package-card ul li i {
    color: #28a745;
    margin-right: 8px;
  }
  .pay-btn {
    display: block;
    width: 100%;
    padding: 12px;
    background: linear-gradient(90deg, #007bff, #0099ff);
    color: #fff;
    border: none;
    border-radius: 30px;
    font-weight: bold;
    font-size: 16px;
    transition: all 0.3s ease;
  }
  .pay-btn:hover {
    background: linear-gradient(90deg, #0056b3, #007bff);
    transform: scale(1.08);
  }


  .premium-section {
  background-color: #fff;
}

.premium-title {
  font-weight: 600;
  font-size: 28px;
}

.feature-box {
  text-align: center;
}

.feature-icon {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  background-color: #f16565;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
  font-size: 28px;
  color: #fff;
  transition: all 0.3s ease;
}

.feature-icon:hover {
  transform: scale(1.1);
  background-color: #e63946;
}

.feature-text {
  font-size: 14px;
  margin-top: 8px;
  color: #333;
}

.btn-primary {
  background-color: #0099ff;
  border-color: #0099ff;
  border-radius: 30px;
  padding: 10px 25px;
  font-size: 16px;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background-color: #007bff;
  border-color: #007bff;
  transform: scale(1.05);
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-custom">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{route('myhome')}}">
      <img src="https://i.pinimg.com/564x/02/e7/f5/02e7f5464591ce514fffbdf03b287eed.jpg" alt="Logo">
      <span class="ms-2">MyCompany</span>
    </a>
    <div class="user-info">Shafiqul (BGD3791925)</div>
  </div>
</nav>

<div class="container my-5">
  <!-- Title -->
  <h2 class="package-title">Regular Packages</h2>

  <!-- Duration Buttons -->
  <div class="duration-btns">
    <button class="active" data-duration="3">3 Months</button>
    <button data-duration="6">6 Months</button>
    <button data-duration="12">1 Year</button>
  </div>

  <!-- Package Cards -->
  <div class="row g-4 justify-content-center">
    <!-- Gold -->
    <div class="col-md-4">
      <div class="package-card" data-base="3900">
        <div>
          <div class="d-flex justify-content-between">
            <h5>Gold</h5>
            <span class="price">BDT 3,900</span>
          </div>
          <ul>
            <li><i class="bi bi-envelope"></i> Send unlimited messages & chat online*</li>
            <li><i class="bi bi-phone"></i> View 40 verified mobile numbers*</li>
          </ul>
        </div>
        <div>
          <hr>
          <p class="mb-1"><strong>Total:</strong> <span class="total-price">BDT 3,900</span></p>
          <p class="mb-3">You have to pay <strong><span class="total-price">BDT 3,900</span></strong></p>
          <button class="pay-btn">Pay Now</button>
        </div>
      </div>
    </div>

    <!-- Diamond -->
    <div class="col-md-4">
      <div class="package-card" data-base="4600">
        <div>
          <div class="d-flex justify-content-between">
            <h5>Diamond</h5>
            <span class="price">BDT 4,600</span>
          </div>
          <ul>
            <li><i class="bi bi-envelope"></i> Send unlimited messages & chat online*</li>
            <li><i class="bi bi-phone"></i> View 55 verified mobile numbers*</li>
          </ul>
        </div>
        <div>
          <hr>
          <p class="mb-1"><strong>Total:</strong> <span class="total-price">BDT 4,600</span></p>
          <p class="mb-3">You have to pay <strong><span class="total-price">BDT 4,600</span></strong></p>
          <button class="pay-btn">Pay Now</button>
        </div>
      </div>
    </div>

    <!-- Platinum -->
    <div class="col-md-4">
      <div class="package-card" data-base="5400">
        <div>
          <div class="d-flex justify-content-between">
            <h5>Platinum</h5>
            <span class="price">BDT 5,400</span>
          </div>
          <ul>
            <li><i class="bi bi-envelope"></i> Send unlimited messages & chat online*</li>
            <li><i class="bi bi-phone"></i> View 70 verified mobile numbers*</li>
            <li><i class="bi bi-star"></i> 3 Months FREE Profile Highlighter</li>
          </ul>
        </div>
        <div>
          <hr>
          <p class="mb-1"><strong>Total:</strong> <span class="total-price">BDT 5,400</span></p>
          <p class="mb-3">You have to pay <strong><span class="total-price">BDT 5,400</span></strong></p>
          <button class="pay-btn">Pay Now</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const buttons = document.querySelectorAll('.duration-btns button');
  const packageCards = document.querySelectorAll('.package-card');

  const priceMultipliers = {
    3: 1,
    6: 1.8,
    12: 3.6
  };

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const months = btn.dataset.duration;

      packageCards.forEach(card => {
        const basePrice = parseInt(card.dataset.base);
        const newPrice = basePrice * priceMultipliers[months];
        card.querySelectorAll('.total-price').forEach(span => span.innerText = 'BDT ' + newPrice.toLocaleString());
        card.querySelector('.price').innerText = 'BDT ' + newPrice.toLocaleString();
      });
    });
  });
</script>



<section class="premium-section py-5">
  <div class="container text-center">
    <!-- Top Icon -->
    <div class="premium-icon mb-3">
      <img src="https://i.ibb.co/3r7gk6k/crown.png" alt="Premium Crown" width="80">
    </div>

    <!-- Section Title -->
    <h3 class="premium-title mb-4">Why premium membership?</h3>

    <!-- Features -->
    <div class="row justify-content-center mb-4">
      <div class="col-6 col-md-3 mb-4">
        <div class="feature-box mx-auto">
          <div class="feature-icon">
            <i class="bi bi-telephone-fill"></i>
          </div>
          <p class="feature-text mt-2">Talk to matches directly</p>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-4">
        <div class="feature-box mx-auto">
          <div class="feature-icon">
            <i class="bi bi-file-text-fill"></i>
          </div>
          <p class="feature-text mt-2">Get complete profile details</p>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-4">
        <div class="feature-box mx-auto">
          <div class="feature-icon">
            <i class="bi bi-person-badge-fill"></i>
          </div>
          <p class="feature-text mt-2">Enhanced profile visibility</p>
        </div>
      </div>
      <div class="col-6 col-md-3 mb-4">
        <div class="feature-box mx-auto">
          <div class="feature-icon">
            <i class="bi bi-envelope-fill"></i>
          </div>
          <p class="feature-text mt-2">Get more responses</p>
        </div>
      </div>
    </div>

    <!-- Call to Action Button -->
    <a href="#packages" class="btn btn-primary btn-lg">Choose our best selling package</a>
  </div>
</section>


<!-- Pre-Footer Section -->
<section class="pre-footer">
  <div class="container">
    <div class="pre-footer-content">
      <h2>Have any queries or need help in making payment?</h2>
      <a href="mailto:support@heavenlymatch.com" class="mail-btn">Mail us</a>
    </div>
  </div>
</section>

<!-- Main Footer Section -->
<footer class="main-footer">
  <div class="container">
    <!-- Payment Gateway Image -->
    <div class="payment-image">
      <img src="https://imgs.bangladeshimatrimony.com/cbsimages/hp_new/payment-gateway-footer.jpg" alt="Payment Gateway">
    </div>

    <!-- Footer Text -->
    <p class="footer-text">
      HeavenlyMatch is part of GlobalMatrimony.com | Copyright © 2025. All rights reserved.<br>
      This website is strictly for matrimonial purpose only and not a dating website
    </p>
  </div>
</footer>

<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
  }

  /* Pre-Footer Section */
  .pre-footer {
    background-color: #f8f8f8;
    text-align: center;
    padding: 20px 20px; /* decreased height */
  }

  .pre-footer-content {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px; /* space between text and button */
  }

  .pre-footer h2 {
    font-size: 1.4rem;
    margin: 0; /* removed extra margin */
    color: #333;
  }

  .pre-footer .mail-btn {
    display: inline-block;
    background-color: #ff6b6b;
    color: #fff;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    transition: background 0.3s;
    white-space: nowrap; /* keep button text in single line */
  }

  .pre-footer .mail-btn:hover {
    background-color: #ff3b3b;
  }

  /* Main Footer Section */
  .main-footer {
    background-color: #222;
    color: #fff;
    text-align: center;
    padding: 40px 20px;
  }

  .main-footer .payment-image img {
    max-width: 490px; /* bigger image */
    margin-bottom: 20px;
  }

  .main-footer .footer-text {
    font-size: 0.9rem;
    line-height: 1.6;
    color: #ccc;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .pre-footer-content {
      flex-direction: column;
      gap: 10px;
    }
    .main-footer .payment-image img {
      max-width: 300px;
    }
  }

  @media (max-width: 480px) {
    .pre-footer h2 {
      font-size: 1.2rem;
    }
    .main-footer .payment-image img {
      max-width: 200px;
    }
  }
</style>







</body>
</html>
