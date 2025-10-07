@extends('layouts.user-dashboard-app')

@section('title', 'Packages')

@section('content')

<style>
  /* Section Title */
  .package-title {
    text-align: center;
    font-weight: bold;
    font-size: 24px;
    margin-bottom: 20px;
    position: relative;
  }
  .package-title::after {
    content: "";
    display: block;
    width: 100px;
    height: 3px;
    background: #e63946;
    margin: 8px auto 0;
    border-radius: 50px;
  }

  /* Duration Buttons */
  .duration-btns {
    text-align: center;
    margin-bottom: 25px;
  }
  .duration-btns button {
    border: 1px solid #e63946;
    background: transparent;
    color: #e63946;
    padding: 8px 18px;
    margin: 5px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
  }
  .duration-btns button:hover,
  .duration-btns button.active {
    background: #e63946;
    color: #fff;
  }

  /* Package Card */
  .package-card {
    border: 1px solid #eee;
    border-radius: 12px;
    background: #fff;
    padding: 20px;
    transition: all 0.3s ease;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
  }
  .package-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    border: 1px solid #e63946;
  }
  .package-card h5 {
    font-weight: 600;
  }
  .package-card .price {
    font-size: 20px;
    font-weight: bold;
    color: #000;
  }
  .package-card ul {
    list-style: none;
    padding-left: 0;
    margin-top: 15px;
    margin-bottom: 20px;
  }
  .package-card ul li {
    margin-bottom: 8px;
    font-size: 14px;
    color: #333;
  }
  .package-card ul li i {
    color: #28a745;
    margin-right: 8px;
  }
  .pay-btn {
    display: block;
    width: 100%;
    padding: 10px;
    background: linear-gradient(90deg, #007bff, #0099ff);
    color: #fff;
    border: none;
    border-radius: 25px;
    font-weight: bold;
    transition: all 0.3s ease;
  }
  .pay-btn:hover {
    background: linear-gradient(90deg, #0056b3, #007bff);
    transform: scale(1.05);
  }
</style>

<div class="container my-5">

@if(session('success'))
<div id="successAlert" class="success-popup">
  <div class="success-icon">
    <svg viewBox="0 0 24 24" width="60" height="60">
      <circle cx="12" cy="12" r="10" fill="none" stroke="#4CAF50" stroke-width="2"/>
      <path d="M8 12l3 3 5-6" fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </div>
  <div class="success-text">
    🎉 {{ session('success') }}
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const alert = document.getElementById('successAlert');
    alert.classList.add('show');
    setTimeout(() => {
        alert.classList.remove('show');
    }, 4000); // hides after 4 seconds
});
</script>

<style>
.success-popup {
  position: fixed;
  top: 30px;
  right: 30px;
  background: #ffffff;
  border-left: 6px solid #4CAF50;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  opacity: 0;
  transform: translateY(-20px);
  transition: all 0.5s ease;
  z-index: 9999;
}
.success-popup.show {
  opacity: 1;
  transform: translateY(0);
}
.success-icon {
  animation: pop 0.4s ease;
}
.success-text {
  font-size: 16px;
  font-weight: 600;
  color: #2e7d32;
}
@keyframes pop {
  0% { transform: scale(0); }
  80% { transform: scale(1.2); }
  100% { transform: scale(1); }
}
</style>
@endif


  <!-- Title -->
  <h2 class="package-title">Regular Packages</h2>

  <!-- Duration Buttons -->
  <div class="duration-btns">
    <button class="active">3 Months</button>
    <button>6 Months</button>
    <button>1 Year</button>
  </div>

  <!-- Package Cards -->
  <div class="row g-4 justify-content-center">
    <!-- Gold -->
    <div class="col-md-4">
      <div class="package-card">
        <div class="d-flex justify-content-between">
          <h5>Gold</h5>
          <span class="price">BDT 3,900</span>
        </div>
        <ul>
          <li><i class="bi bi-envelope"></i> Send unlimited messages & chat online*</li>
          <li><i class="bi bi-phone"></i> View 40 verified mobile numbers*</li>
        </ul>
        <hr>
        <p class="mb-1"><strong>Total:</strong> BDT 3,900</p>
        <p class="mb-2">You have to pay <strong>BDT 3,900</strong></p>
        <button class="pay-btn">Pay Now</button>
      </div>
    </div>

    <!-- Diamond -->
    <div class="col-md-4">
      <div class="package-card">
        <div class="d-flex justify-content-between">
          <h5>Diamond</h5>
          <span class="price">BDT 4,600</span>
        </div>
        <ul>
          <li><i class="bi bi-envelope"></i> Send unlimited messages & chat online*</li>
          <li><i class="bi bi-phone"></i> View 55 verified mobile numbers*</li>
        </ul>
        <hr>
        <p class="mb-1"><strong>Total:</strong> BDT 4,600</p>
        <p class="mb-2">You have to pay <strong>BDT 4,600</strong></p>
        <button class="pay-btn">Pay Now</button>
      </div>
    </div>

    <!-- Platinum -->
    <div class="col-md-4">
      <div class="package-card">
        <div class="d-flex justify-content-between">
          <h5>Platinum</h5>
          <span class="price">BDT 5,400</span>
        </div>
        <ul>
          <li><i class="bi bi-envelope"></i> Send unlimited messages & chat online*</li>
          <li><i class="bi bi-phone"></i> View 70 verified mobile numbers*</li>
          <li><i class="bi bi-star"></i> 3 Months FREE Profile Highlighter</li>
        </ul>
        <hr>
        <p class="mb-1"><strong>Total:</strong> BDT 5,400</p>
        <p class="mb-2">You have to pay <strong>BDT 5,400</strong></p>
        <button class="pay-btn">Pay Now</button>
      </div>
    </div>
  </div>
</div>

@endsection
