<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bubur Ayam Bandung Bang Jay</title>
    <link rel="stylesheet" href="../../css/styles.css">
</head>
<body>
    <div class="main-wrapper">
        <header class="header-toko">
            <div class="container-header">
                <div class="logo-text"><h3>BUBUR AYAM BANDUNG</h3><h1>BANG JAY</h1></div>
                <div class="welcome-section"><span>WELCOME</span><a href="login.php" class="user-name">LOG IN / REGISTER</a></div>
            </div>
            <div class="search-bar-container"><div class="container-search"></div></div>
        </header>

        <main class="main-content-form">
            <div class="breadcrumb">
                <a href="index.php">Home</a> / <span>pages</span> / <span class="current-page">Login</span>
            </div>

            <div class="form-container">
                <div class="form-illustration">
                    <img src="../../assets/images/ilustrasi-login.png" alt="Ilustrasi Login">
                </div>

                <div class="form-content">
                    <h2 class="form-title">SELAMAT DATANG</h2>
                    <p class="form-subtitle">LOGIN UNTUK MELANJUTKAN</p>

                    <form method="POST" action="index.php"> 
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" name="email" placeholder="Example@gmail.com" required>
                        
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="---" required>
                        
                        <a href="#" class="forgot-password">Lupa Password?</a>

                        <button type="submit" class="tombol-form-cta">LOGIN</button>
                        
                        <p class="switch-page">
                            Pengguna Baru? <a href="register.php">SIGN UP</a>
                        </p>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>