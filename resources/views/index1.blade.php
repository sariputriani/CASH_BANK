<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Bank</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet"  href="{{ asset('css/style.css') }}" class="css">
    <link rel="stylesheet"  href="{{ asset('images') }}" class="css">
    <!-- Bootstrap JS
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"> -->
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>

<body>
    <!-- HEADER -->
    <div class="header-container">
      <header>
        <span class="search">
            <i class="bi bi-search"></i>
            <input type="text" name="" id="seacrh" placeholder="Search...">
          </span>
        <span class="icon">
          <i class="bi bi-person"></i>
          <!-- <i class="bi bi-box-arrow-right"></i> -->
        </span>
        <span class="icon">
          <i class="bi bi-box-arrow-right"></i>
        </span>
      </header>
    </div>
    <!-- END HEADER -->


    <!-- SIDE BAR -->
    <div class="sidebar">
      <!-- fungsi flex column agar menu menjadi vertikal -->
      <nav class="nav flex-column">
           <!-- menu dashboard -->
        <a href="#" class="brand">
            <span class="icon">
              <img src="{{ asset('images/Logo PTPN IV.png') }}" alt="logo PTP" width="40" height="40" >
              <span class="text">Cash Bank</span>
            </span>
        </a>
          <hr style="color:white;" >
        <!-- <div class="menu-list">
          <div class="menu-title">
            <h3>Cash Bank</h3>
            <img src="{{ asset('images/logo-removebg-preview.png') }}" alt="logo PTP" width="100" height="100">
          </div>
        </div> -->

        <!-- menu dashboard -->
        <a href="#" class="nav-link">
            <span class="icon">
              <i class="bi bi-grid-1x2-fill"></i>
            </span>
            <span class="description">Dashboard</span>
        </a>
        <!-- Daftar SPP -->
        <a href="#" class="nav-link">
            <span class="icon">
             <i class="bi bi-file-earmark-medical"></i>
            </span>
            <span class="description">Daftar SPP</span>
        </a>
        <!-- Daftar Bank dropdown -->
        <a href="#" class="nav-link"
         data-bs-toggle="collapse" data-bs-target="#submenu-bank" aria-expanded="false" aria-controls="submenu-bank>
            <span class="icon">
                <i class="bi bi-coin"></i>
            </span>
            <span class="description">
                Daftar Bank <i class="bi bi-caret-down"></i>
            </span>
        </a>

        <!-- submenu dropdown -->
        <div class="sub-menu collapse" id="submenu-bank">
            <a href="#" class="nav-link">
                <span class="icon">
                    <i class="bi bi-wallet-fill"></i>
                </span>
                <span class="description">Bank Masuk</span>
            </a>

            <a href="#" class="nav-link">
                <span class="icon">
                    <i class="bi bi-wallet2"></i>
                </span>
                <span class="description">Bank Keluar</span>
            </a>
          </div>
        </div>
      </nav>
    </div>
    <!-- END SIDE BAR -->
   
     <div class="main-content">
      @yield('content')
     </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
