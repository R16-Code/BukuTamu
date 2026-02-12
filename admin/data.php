<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin authentication
requireAdmin();

$adminName = $_SESSION['admin_nama'] ?? 'Admin';
$today = date('Y-m-d');

// Get filter parameters
$filter = $_GET['filter'] ?? 'today';
$startDate = $_GET['start_date'] ?? date('Y-m-d');
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$search = $_GET['search'] ?? '';

// Build query based on filter
$whereClauses = [];
$params = [];

// When searching, automatically switch to "all" dates unless custom filter is explicitly set
// This ensures search results aren't limited by the default "today" filter
$effectiveFilter = $filter;
if (!empty($search) && $filter === 'today' && !isset($_GET['filter'])) {
    $effectiveFilter = 'all';
}

// Date/Range Filters
switch ($effectiveFilter) {
    case 'today':
        $whereClauses[] = 'visit_date = :date';
        $params['date'] = date('Y-m-d');
        break;
    case 'week':
        $whereClauses[] = 'visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
        break;
    case 'month':
        $whereClauses[] = 'MONTH(visit_date) = MONTH(CURDATE()) AND YEAR(visit_date) = YEAR(CURDATE())';
        break;
    case 'year':
        $whereClauses[] = 'YEAR(visit_date) = YEAR(CURDATE())';
        break;
    case 'custom':
        $whereClauses[] = 'visit_date BETWEEN :start_date AND :end_date';
        $params['start_date'] = $startDate;
        $params['end_date'] = $endDate;
        break;
    case 'all':
        // No date restriction
        break;
}

// Search Filter
if (!empty($search)) {
    // Fixed: unique parameters for PDO with emulate_prepares=false
    $whereClauses[] = '(nama LIKE :search1 OR nomor_identitas LIKE :search2 OR fungsi LIKE :search3 OR keperluan LIKE :search4)';
    $params['search1'] = "%$search%";
    $params['search2'] = "%$search%";
    $params['search3'] = "%$search%";
    $params['search4'] = "%$search%";
}

// Combine clauses
$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Get visits
$query = "SELECT * FROM visits {$whereSql} ORDER BY visit_date DESC, jam_masuk DESC";
$visits = getAll($query, $params);

// Count flagged (belum checkout)
$flaggedCount = 0;
foreach ($visits as $visit) {
    if ($visit['status'] === 'MASUK' || $visit['is_flagged']) {
        $flaggedCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Buku Tamu - <?php echo APP_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/custom.css">
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Section -->
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
                        <h1 class="text-2xl font-bold text-gray-900 leading-tight">Data Buku Tamu</h1>
                        <p class="text-sm text-gray-600 font-medium">Kelola dan monitor data kunjungan</p>
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
                         <a href="dashboard.php" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium text-sm flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Dashboard
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

        <!-- Alert for flagged visitors -->
        <?php if ($flaggedCount > 0): ?>
        <div class="bg-orange-50 border-l-4 border-orange-500 text-orange-700 p-4 mb-6 rounded-r-lg shadow-sm flex items-center">
            <svg class="w-6 h-6 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <p class="font-medium">Peringatan: Ada <?php echo $flaggedCount; ?> pengunjung yang belum checkout atau masa kunjungannya aktif!</p>
        </div>
        <?php endif; ?>

        <!-- Filters & Search -->
        <div class="bg-white rounded-2xl shadow-professional p-4 md:p-6 mb-6">
            <form method="GET" class="space-y-4">
                <!-- Header & Search -->
                <div class="flex flex-col gap-3">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter & Pencarian
                    </h3>
                    
                    <!-- Search Bar -->
                    <div class="flex items-center gap-2 w-full">
                        <input type="text" name="search" value="<?php echo e($search); ?>" 
                               class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                               placeholder="Cari Nama, ID, atau Fungsi...">
                        <button type="submit" name="filter" value="all" 
                                class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm flex items-center gap-2 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </div>
                </div>

                <hr class="border-gray-100">

                <!-- Quick Filters -->
                <div class="grid grid-cols-4 gap-2">
                    <button type="submit" name="filter" value="today" 
                            class="px-2 py-2 rounded-lg text-xs md:text-sm font-medium transition text-center <?php echo $filter === 'today' ? 'bg-blue-100 text-blue-700 ring-2 ring-blue-500' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                        Hari Ini
                    </button>
                    <button type="submit" name="filter" value="week" 
                            class="px-2 py-2 rounded-lg text-xs md:text-sm font-medium transition text-center <?php echo $filter === 'week' ? 'bg-blue-100 text-blue-700 ring-2 ring-blue-500' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                        Minggu
                    </button>
                    <button type="submit" name="filter" value="month" 
                            class="px-2 py-2 rounded-lg text-xs md:text-sm font-medium transition text-center <?php echo $filter === 'month' ? 'bg-blue-100 text-blue-700 ring-2 ring-blue-500' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                        Bulan
                    </button>
                    <button type="submit" name="filter" value="all" 
                            class="px-2 py-2 rounded-lg text-xs md:text-sm font-medium transition text-center <?php echo $filter === 'all' ? 'bg-blue-100 text-blue-700 ring-2 ring-blue-500' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'; ?>">
                        Semua
                    </button>
                </div>

                <!-- Date Range & Action -->
                <div class="flex flex-col md:flex-row gap-2 items-stretch md:items-end">
                    <div class="grid grid-cols-2 gap-2 flex-1">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Dari</label>
                            <input type="date" name="start_date" value="<?php echo e($startDate); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Sampai</label>
                            <input type="date" name="end_date" value="<?php echo e($endDate); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500">
                        </div>
                    </div>
                    <?php if ($filter === 'custom'): ?>
                        <input type="hidden" name="filter" value="custom">
                    <?php endif; ?>
                    
                    <div class="flex gap-2">
                        <button type="submit" name="filter" value="custom" class="flex-1 md:flex-none px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm shadow-sm">
                            Terapkan
                        </button>
                        
                        <?php if(!empty($search) || $filter !== 'today'): ?>
                        <a href="data.php" class="flex-1 md:flex-none px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium text-sm text-center">
                            Reset
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Export & Count -->
        <div class="mb-4 flex justify-between items-center">
             <div class="text-gray-600 text-sm">
                Menampilkan <strong><?php echo count($visits); ?></strong> data kunjungan
                <?php if (!empty($search)): ?>
                untuk pencarian "<strong><?php echo e($search); ?></strong>"
                <?php endif; ?>
            </div>
            
            <a href="export_excel.php?<?php echo http_build_query($_GET); ?>" 
               class="px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm inline-flex items-center shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </a>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-2xl shadow-professional overflow-hidden border border-gray-100">
            <!-- Table View - All Devices with horizontal scroll -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Identitas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Asal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Instansi / Fungsi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keperluan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jam Keluar</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">TTD</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php if (count($visits) > 0): ?>
                            <?php foreach ($visits as $visit): ?>
                            <tr class="hover:bg-gray-50 transition <?php echo $visit['is_flagged'] ? 'bg-orange-50' : ''; ?>">
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <div class="font-medium text-gray-900"><?php echo formatDate($visit['visit_date']); ?></div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs mr-2">
                                            <?php echo strtoupper(substr($visit['nama'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900"><?php echo e($visit['nama']); ?></div>
                                            <div class="text-xs text-gray-500"><?php echo e($visit['jenis_identitas']); ?> - <?php echo e($visit['nomor_identitas']); ?></div>
                                            <?php if ($visit['is_flagged']): ?>
                                                <div class="text-xs text-orange-600 font-medium">🚩 Flagged</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 max-w-xs">
                                    <div class="truncate" title="<?php echo e($visit['asal']); ?>"><?php echo e($visit['asal']); ?></div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
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
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Aktif</span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <!-- TTD Column - Signature Status -->
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <!-- Entry Signature -->
                                        <div class="relative group">
                                            <?php if (!empty($visit['tanda_tangan_masuk'])): ?>
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 cursor-pointer" title="TTD Masuk: Ada">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                                <!-- Tooltip with signature preview -->
                                                <div class="hidden group-hover:block absolute z-50 bottom-full left-1/2 transform -translate-x-1/2 mb-2 p-2 bg-white rounded-lg shadow-xl border border-gray-200">
                                                    <p class="text-xs text-gray-500 mb-1 whitespace-nowrap">TTD Masuk:</p>
                                                    <?php 
                                                    $ttdMasuk = $visit['tanda_tangan_masuk'];
                                                    $ttdMasukSrc = (strpos($ttdMasuk, 'data:') === 0) ? $ttdMasuk : '../' . $ttdMasuk;
                                                    ?>
                                                    <img src="<?php echo $ttdMasukSrc; ?>" alt="TTD Masuk" class="w-32 h-auto border rounded">
                                                </div>
                                            <?php else: ?>
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-600" title="TTD Masuk: Tidak ada">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-gray-300">/</span>
                                        <!-- Exit Signature -->
                                        <div class="relative group">
                                            <?php if (!empty($visit['tanda_tangan_keluar'])): ?>
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-600 cursor-pointer" title="TTD Keluar: Ada">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                                <!-- Tooltip with signature preview -->
                                                <div class="hidden group-hover:block absolute z-50 bottom-full left-1/2 transform -translate-x-1/2 mb-2 p-2 bg-white rounded-lg shadow-xl border border-gray-200">
                                                    <p class="text-xs text-gray-500 mb-1 whitespace-nowrap">TTD Keluar:</p>
                                                    <?php 
                                                    $ttdKeluar = $visit['tanda_tangan_keluar'];
                                                    $ttdKeluarSrc = (strpos($ttdKeluar, 'data:') === 0) ? $ttdKeluar : '../' . $ttdKeluar;
                                                    ?>
                                                    <img src="<?php echo $ttdKeluarSrc; ?>" alt="TTD Keluar" class="w-32 h-auto border rounded">
                                                </div>
                                            <?php else: ?>
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-400" title="TTD Keluar: Belum">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-1">
                                        <?php if ($visit['status'] === 'MASUK'): ?>
                                            <button onclick="manualCheckout(<?php echo $visit['id']; ?>, '<?php echo e($visit['nama']); ?>')" 
                                                    class="px-2 py-1 bg-blue-600 text-white rounded text-xs font-medium hover:bg-blue-700 transition">
                                                Checkout
                                            </button>
                                        <?php endif; ?>
                                        <button onclick="deleteVisit(<?php echo $visit['id']; ?>, '<?php echo e($visit['nama']); ?>')"
                                                class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                            <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        <p class="text-gray-500 font-medium">Tidak ada data kunjungan ditemukan.</p>
                                        <p class="text-gray-400 text-sm mt-1">Coba ubah filter atau kata kunci pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Signature Modal -->
        <div id="signatureModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-auto">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900" id="signatureModalTitle">Tanda Tangan</h3>
                    <button onclick="closeSignatureModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                <div class="p-4">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Tanda Tangan Masuk:</p>
                        <div class="border rounded-lg p-2 bg-gray-50">
                            <img id="signatureMasuk" src="" alt="Tanda Tangan Masuk" class="max-w-full h-auto mx-auto">
                        </div>
                    </div>
                    <div id="signatureKeluarContainer" style="display: none;">
                        <p class="text-sm text-gray-600 mb-2">Tanda Tangan Keluar:</p>
                        <div class="border rounded-lg p-2 bg-gray-50">
                            <img id="signatureKeluar" src="" alt="Tanda Tangan Keluar" class="max-w-full h-auto mx-auto">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        async function manualCheckout(visitId, namaVisitor) {
            if (!confirm(`Checkout manual untuk ${namaVisitor}?\n\nIni akan mengisi jam keluar otomatis.`)) {
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('visit_id', visitId);
                
                const response = await fetch('manual_checkout.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan. Silakan coba lagi.');
                console.error('Error:', error);
            }
        }

        async function deleteVisit(visitId, namaVisitor) {
            if (!confirm(`PERINGATAN: Apakah Anda yakin ingin menghapus data kunjungan ${namaVisitor}?\n\nData yang dihapus tidak dapat dikembalikan!`)) {
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('visit_id', visitId);
                
                const response = await fetch('delete_visit.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan sistem. Silakan coba lagi.');
                console.error('Error:', error);
            }
        }

        async function viewSignature(visitId, namaVisitor) {
            document.getElementById('signatureModalTitle').textContent = `Tanda Tangan - ${namaVisitor}`;
            document.getElementById('signatureMasuk').src = '';
            document.getElementById('signatureKeluar').src = '';
            document.getElementById('signatureKeluarContainer').style.display = 'none';
            
            try {
                const response = await fetch(`get_signature.php?id=${visitId}`);
                const result = await response.json();
                
                if (result.success) {
                    if (result.data.tanda_tangan_masuk) {
                        // Check if it's a file path or base64 data
                        const masukSrc = result.data.tanda_tangan_masuk.startsWith('data:') 
                            ? result.data.tanda_tangan_masuk 
                            : '../' + result.data.tanda_tangan_masuk;
                        document.getElementById('signatureMasuk').src = masukSrc;
                    }
                    if (result.data.tanda_tangan_keluar) {
                        // Check if it's a file path or base64 data
                        const keluarSrc = result.data.tanda_tangan_keluar.startsWith('data:') 
                            ? result.data.tanda_tangan_keluar 
                            : '../' + result.data.tanda_tangan_keluar;
                        document.getElementById('signatureKeluar').src = keluarSrc;
                        document.getElementById('signatureKeluarContainer').style.display = 'block';
                    }
                    
                    const modal = document.getElementById('signatureModal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Gagal memuat tanda tangan.');
                console.error('Error:', error);
            }
        }

        function closeSignatureModal() {
            const modal = document.getElementById('signatureModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>
