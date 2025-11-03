<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Bubur Ayam Bandung Bang Jay</title>
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
                <a href="index.php">Home</a> / <span>pages</span> / <span class="current-page">Register</span>
            </div>

            <div class="form-container">
                <div class="form-illustration">
                    <img src="../../assets/images/ilustrasi-register.png" alt="Ilustrasi Daftar">
                </div>

                <div class="form-content">
                    <h2 class="form-title">Daftar</h2>
                    <p class="form-subtitle">JOIN TO US</p>

                    <form method="POST" action="register.php"> 
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Anomali" required>
                        
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" name="email" placeholder="Example@gmail.com" required>
                        
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="---" required>
                        
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="---" required>
                        
                        <button type="submit" class="tombol-form-cta">DAFTAR</button>
                        
                        <p class="switch-page">
                            Sudah Punya Akun? <a href="login.php">LOGIN</a>
                        </p>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>