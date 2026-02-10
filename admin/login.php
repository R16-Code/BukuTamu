<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (checkAuthAdmin()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - <?php echo APP_NAME; ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    
    <!-- Background Decor -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-5 animate-blob"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-red-500 rounded-full mix-blend-multiply filter blur-3xl opacity-5 animate-blob animation-delay-2000"></div>
    </div>

    <div class="max-w-md w-full space-y-8">
        <!-- Logo & Branding -->
        <div class="text-center">
            <a href="../index.php" class="inline-block group">
                <div class="bg-white p-3 rounded-xl shadow-md border border-gray-100 inline-block mb-4 group-hover:scale-105 transition duration-300">
                    <img src="https://res.cloudinary.com/drnnwysol/image/upload/v1770018633/logo_j5pwjf.png" 
                         alt="Pertamina EP Cepu Logo" 
                         class="h-16 w-auto object-contain">
                </div>
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Login Admin</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Sistem Buku Tamu & Manajemen Pengunjung
                </p>
                <div class="w-16 h-1 bg-gradient-to-r from-blue-500 to-red-500 mx-auto mt-4 rounded-full"></div>
            </a>
        </div>

        <div id="alertContainer"></div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-yellow-500 to-red-500"></div>
            
            <form id="loginForm" class="space-y-6 mt-2">
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input id="username" name="username" type="text" required class="appearance-none rounded-lg relative block w-full px-4 py-3 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition" placeholder="Username Admin">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required class="appearance-none rounded-lg relative block w-full px-4 py-3 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm transition" placeholder="Password Admin">
                    </div>
                </div>

                <div>
                    <button type="submit" id="loginBtn" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                        </span>
                        Masuk Dashboard
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <a href="../index.php" class="font-medium text-sm text-blue-600 hover:text-blue-500 hover:underline transition">
                    &larr; Kembali ke Halaman Utama
                </a>
            </div>
        </div>
        
        <!-- Credits -->
        <p class="text-center text-xs text-gray-400 mt-4">
            &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
        </p>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const loginBtn = document.getElementById('loginBtn');
            const originalText = loginBtn.innerHTML;
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<span class="loading-spinner mr-2"></span> Memproses...';
            
            try {
                const response = await fetch('process_login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1000);
                } else {
                    showAlert('error', result.message);
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = originalText;
                }
            } catch (error) {
                showAlert('error', 'Terjadi kesalahan. Silakan coba lagi.');
                console.error('Error:', error);
                loginBtn.disabled = false;
                loginBtn.innerHTML = originalText;
            }
        });
        
        function showAlert(type, message) {
            const container = document.getElementById('alertContainer');
            const colors = {
                success: 'bg-green-50 border-green-500 text-green-800',
                error: 'bg-red-50 border-red-500 text-red-800'
            };
            
            const alert = document.createElement('div');
            alert.className = `${colors[type]} border-l-4 p-4 rounded-lg alert-slide-in shadow-professional`;
            alert.innerHTML = `
                <div class="flex items-center justify-between">
                    <p class="font-medium">${message}</p>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-2xl font-bold ml-4 hover:opacity-70">&times;</button>
                </div>
            `;
            
            container.appendChild(alert);
            setTimeout(() => alert.remove(), 5000);
        }
    </script>
</body>
</html>
