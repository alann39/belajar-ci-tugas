  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link <?php echo (uri_string() == '') ? "" : "collapsed" ?>" href="/">
          <i class="bi bi-grid"></i>
          <span>Catalogue</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo (uri_string() == 'cart') ? "" : "collapsed" ?>" href="/cart">
          <i class="bi bi-cart"></i>
          <span>Cart</span>
        </a>
      </li><!-- End Cart Nav -->
      
      <?php 
      if (session()->get('role') == 'admin') {
      ?>
        <li class="nav-item">
          <a class="nav-link <?php echo (uri_string() == 'product') ? "" : "collapsed" ?>" href="/product">
            <i class="bi bi-box"></i>
            <span>Product</span>
          </a>
        </li><!-- End Product Nav -->
      <?php
      }
      ?>

      <li class="nav-item">
          <a class="nav-link <?php echo (uri_string() == 'profile') ? "" : "collapsed" ?>" href="profile">
              <i class="bi bi-clock"></i>
              <span>History</span>
          </a>
      </li><!-- End Profile Nav -->

      <li class="nav-item">
        <a class="nav-link <?php echo (uri_string() == 'sales') ? "" : "collapsed" ?>" href="transaction">
          <i class="bi bi-cash-stack"></i>
          <span>Sales</span>
        </a>
      </li><!-- End Sales Nav -->
    </ul>

  </aside><!-- End Sidebar-->