<!-- resources/views/components/user-footer.blade.php -->

<style>
  .three-part-footer {
    background-color: #222;
    color: #fff;
    padding: 40px 20px 20px 20px;
  }

  .footer-section h3 {
    font-size: 1.2rem;
    margin-bottom: 15px;
    color: #ff6b6b;
  }

  .footer-section p {
    font-size: 0.9rem;
    line-height: 1.6;
    color: #ccc;
    text-align:left;
  }

  .footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .footer-section ul li {
    margin-bottom: 10px;
  }

  .footer-section ul li a {
    color: #ccc;
    text-decoration: none;
    transition: color 0.3s;
  }

  .footer-section ul li a:hover {
    color: #ff6b6b;
  }

  .footer-section a {
    color: #ff6b6b;
    text-decoration: none;
  }

  .footer-section a:hover {
    text-decoration: underline;
  }

  .bottom-footer {
    border-top: 1px solid #444;
    margin-top: 30px;
    padding-top: 20px;
    text-align: center;
    font-size: 0.85rem;
    color: #ccc;
  }
</style>

<footer class="three-part-footer">
  <div class="container">
    <div class="row g-4">
      <!-- About Us -->
      <div class="col-lg-4 col-md-6 footer-section">
        <h3>About Us</h3>
        <p>
          HeavenlyMatch.com is the No.1 most trusted matrimony site for Bangladeshi brides and grooms. 
          Lakhs of members have successfully found their life partners here! Browse through our vast 
          selection of profiles from all Religions... <a href="#">more »</a>
        </p>
      </div>

      <!-- Help & Support -->
      <div class="col-lg-4 col-md-6 footer-section">
        <h3>Help & Support</h3>
        <ul>
          <li><a href="#">Live help</a></li>
          <li><a href="#">Contact us</a></li>
          <li><a href="#">Feedback</a></li>
          <li><a href="#">FAQs</a></li>
        </ul>
      </div>

      <!-- Quick Links -->
      <div class="col-lg-4 col-md-12 footer-section">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="#">Upgrade</a></li>
          <li><a href="#">Safe Matrimony</a></li>
          <li><a href="#">Popular Matrimony Searches</a></li>
          <li><a href="#">Terms, Conditions and Refund Policy</a></li>
          <li><a href="#">Privacy Policy</a></li>
        </ul>
      </div>
    </div>

    <!-- Bottom Footer Text -->
    <div class="bottom-footer">
      <p>
        HeavenlyMatch is part of GlobalMatrimony.com | Copyright © 2025. All rights reserved.<br>
        This website is strictly for matrimonial purpose only and not a dating website
      </p>
    </div>
  </div>
</footer>
