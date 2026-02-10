<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/custom.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb', // Blue
                        secondary: '#dc2626', // Red
                        accent: '#f59e0b', // Yellow
                    }
                }
            }
        }
    </script>
    <style>
        .pertamina-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #dc2626 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col overflow-y-auto">
    
    <!-- Background Decor -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-red-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
    </div>

    <div class="flex-1 flex items-center justify-center p-4">
        <div class="max-w-4xl w-full">
            
            <!-- Logo & Title -->
            <div class="text-center mb-12 animate-fade-in-up">
                <img src="https://res.cloudinary.com/drnnwysol/image/upload/v1770018633/logo_j5pwjf.png" 
                     alt="Pertamina EP Cepu Logo" 
                     class="h-24 mx-auto mb-6 object-contain">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-2">Selamat Datang</h1>
                <p class="text-xl text-gray-600">Sistem Buku Tamu & Manajemen Pengunjung</p>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-500 via-yellow-500 to-red-500 mx-auto mt-6 rounded-full"></div>
            </div>

            <!-- Selection Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto">
                
                <!-- Guest Card -->
                <a href="guest_entry.php" class="group bg-white rounded-2xl p-8 shadow-xl border border-gray-100 card-hover relative overflow-hidden text-center cursor-pointer">
                    <div class="absolute inset-0 bg-blue-50 opacity-0 group-hover:opacity-100 transition duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-600 transition duration-300">
                            <svg class="w-10 h-10 text-blue-600 group-hover:text-white transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Tamu / Pengunjung</h2>
                        <p class="text-gray-500 mb-6">Isi buku tamu untuk keperluan kunjungan dinas atau umum.</p>
                        <span class="inline-flex items-center text-blue-600 font-semibold group-hover:translate-x-1 transition duration-200">
                            Masuk sebagai Tamu
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    </div>
                </a>

                <!-- Admin Card -->
                <a href="admin/login.php" class="group bg-white rounded-2xl p-8 shadow-xl border border-gray-100 card-hover relative overflow-hidden text-center cursor-pointer">
                    <div class="absolute inset-0 bg-red-50 opacity-0 group-hover:opacity-100 transition duration-300"></div>
                    <div class="relative z-10">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-red-600 transition duration-300">
                            <svg class="w-10 h-10 text-red-600 group-hover:text-white transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Administrator</h2>
                        <p class="text-gray-500 mb-6">Login untuk mengelola data tamu dan laporan.</p>
                        <span class="inline-flex items-center text-red-600 font-semibold group-hover:translate-x-1 transition duration-200">
                            Masuk sebagai Admin
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    </div>
                </a>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center py-6 text-gray-500 text-sm relative z-10">
        <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
    </div>

</body>
</html>
