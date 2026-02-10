<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin authentication
requireAdmin();

$adminName = $_SESSION['admin_nama'] ?? 'Admin';
$today = date('Y-m-d');

// Get today's statistics
$stats = getVisitStats($today);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - <?php echo APP_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section (Replaces Nav) -->
        <div class="bg-white shadow-professional-lg rounded-2xl p-6 mb-8 relative overflow-hidden">
            <!-- Decorative Background Element -->
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-blue-500 to-green-500 opacity-10 rounded-full blur-xl"></div>
            
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
                <!-- Branding -->
                <div class="flex items-center gap-4 text-center md:text-left">
                    <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100">
                        <img src="https://res.cloudinary.com/drnnwysol/image/upload/v1770018633/logo_j5pwjf.png" 
                             alt="Pertamina EP Cepu Logo" 
                             class="h-12 w-auto object-contain">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 leading-tight">Dashboard Admin</h1>
                        <p class="text-sm text-gray-600 font-medium">Sistem Buku Tamu Digital</p>
                    </div>
                </div>

                <!-- Admin Actions -->
                <div class="flex items-center gap-4">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-semibold text-gray-900"><?php echo e($adminName); ?></p>
                        <p class="text-xs text-gray-500"><?php echo formatDate($today); ?></p>
                    </div>
                    <div class="h-8 w-px bg-gray-200 hidden md:block"></div>
                    <div class="flex gap-2">
                        <a href="data.php" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition font-medium text-sm flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Data Lengkap
                        </a>
                        <a href="logout.php" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition font-medium text-sm flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Pengunjung Hari Ini -->
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-professional p-6 text-white card-hover relative overflow-hidden">
                <div class="absolute top-0 right-0 -m-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">Total Pengunjung Hari Ini</p>
                        <p class="text-4xl font-bold"><?php echo $stats['total']; ?></p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-xl p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Masih di Dalam -->
            <div class="bg-white rounded-2xl shadow-professional p-6 border-l-4 border-green-500 card-hover relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Sedang Bertamu</p>
                        <p class="text-4xl font-bold text-gray-800"><?php echo $stats['inside']; ?></p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Sudah Keluar -->
            <div class="bg-white rounded-2xl shadow-professional p-6 border-l-4 border-gray-500 card-hover relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Sudah Checkout</p>
                        <p class="text-4xl font-bold text-gray-800"><?php echo $stats['exited']; ?></p>
                    </div>
                    <div class="bg-gray-100 rounded-xl p-3">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Sidebar / Quick Actions -->
            <div class="space-y-6">
                <!-- Auto Flag Card -->
                <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-lg p-6 text-white text-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Auto Checkout</h3>
                    <p class="text-orange-100 text-sm mb-6">Flag manual untuk tamu yang lupa checkout hari ini.</p>
                    <button onclick="runAutoFlag()" class="w-full py-2.5 bg-white text-orange-600 rounded-lg font-semibold hover:bg-orange-50 transition shadow-sm">
                        Jalankan Sekarang
                    </button>
                </div>

                <!-- Info Card -->
                 <div class="bg-white rounded-2xl shadow-professional p-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Informasi
                    </h3>
                    <p class="text-sm text-gray-600">
                        Data kunjungan ditampilkan khusus untuk tanggal hari ini <b>(<?php echo formatDate($today); ?>)</b>.
                        <br><br>
                        Untuk melihat histori lengkap atau mengekspor laporan, silakan menu <b>Data Lengkap</b>.
                    </p>
                </div>
            </div>

            <!-- Recent Visitors Table -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-professional overflow-hidden">
                    <div class="p-4 md:p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Pengunjung Terbaru</h3>
                        <a href="data.php" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Semua →</a>
                    </div>
                    
                    <?php
                    $recentVisits = getAll(
                        "SELECT * FROM visits WHERE visit_date = :date ORDER BY jam_masuk DESC LIMIT 8",
                        ['date' => $today]
                    );
                    
                    if (count($recentVisits) > 0):
                    ?>
                    
                    <!-- Table View - All Devices with horizontal scroll -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama / Asal</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fungsi</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keperluan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Masuk</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keluar</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <?php foreach ($recentVisits as $visit): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                                <?php echo strtoupper(substr($visit['nama'], 0, 1)); ?>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900"><?php echo e($visit['nama']); ?></div>
                                                <div class="text-xs text-gray-500"><?php echo e($visit['asal']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo e($visit['fungsi']); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 max-w-xs">
                                        <div class="truncate" title="<?php echo e($visit['keperluan']); ?>"><?php echo e($visit['keperluan']); ?></div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <span class="font-medium text-green-600"><?php echo formatTime($visit['jam_masuk']); ?></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <span class="font-medium text-red-600"><?php echo $visit['jam_keluar'] ? formatTime($visit['jam_keluar']) : '-'; ?></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <?php if ($visit['status'] === 'MASUK'): ?>
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                Active
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                Done
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p>Belum ada pengunjung hari ini.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <script>
        async function runAutoFlag() {
            if (!confirm('Jalankan auto-flag untuk semua kunjungan yang belum checkout hari ini?')) {
                return;
            }
            
            try {
                const response = await fetch('../cron/auto_flag.php', {
                    method: 'POST'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan saat menjalankan auto-flag.');
                console.error('Error:', error);
            }
        }
    </script>
</body>
</html>
