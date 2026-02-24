<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Require admin authentication
requireAdmin();

// Get filter parameters (same as data.php)
$filter = $_GET['filter'] ?? 'today';
$startDate = $_GET['start_date'] ?? date('Y-m-d');
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$search = $_GET['search'] ?? '';

// Build query based on filter
$whereClauses = [];
$params = [];

// Date/Range Filters
switch ($filter) {
    case 'today':
        $whereClauses[] = 'visit_date = :date';
        $params['date'] = date('Y-m-d');
        $filename = 'BukuTamu_' . date('Y-m-d');
        $titlePeriod = 'Hari Ini (' . date('d M Y') . ')';
        break;
    case 'week':
        $whereClauses[] = 'visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
        $filename = 'BukuTamu_Minggu_' . date('Y-m-d');
        $titlePeriod = '7 Hari Terakhir';
        break;
    case 'month':
        $whereClauses[] = 'MONTH(visit_date) = MONTH(CURDATE()) AND YEAR(visit_date) = YEAR(CURDATE())';
        $filename = 'BukuTamu_' . date('F_Y');
        $titlePeriod = 'Bulan ' . date('F Y');
        break;
    case 'year':
        $whereClauses[] = 'YEAR(visit_date) = YEAR(CURDATE())';
        $filename = 'BukuTamu_Tahun_' . date('Y');
        $titlePeriod = 'Tahun ' . date('Y');
        break;
    case 'custom':
        $whereClauses[] = 'visit_date BETWEEN :start_date AND :end_date';
        $params['start_date'] = $startDate;
        $params['end_date'] = $endDate;
        $filename = 'BukuTamu_' . $startDate . '_to_' . $endDate;
        $titlePeriod = "$startDate s.d $endDate";
        break;
    case 'all':
        $filename = 'BukuTamu_Semua_Data';
        $titlePeriod = 'Semua Data';
        break;
    default:
        $filename = 'BukuTamu_Export';
        $titlePeriod = 'Export Data';
}

// Search Filter (keep consistent with data.php logic)
if (!empty($search)) {
    $whereClauses[] = '(nama LIKE :search1 OR nomor_identitas LIKE :search2 OR fungsi LIKE :search3 OR keperluan LIKE :search4)';
    $params['search1'] = "%$search%";
    $params['search2'] = "%$search%";
    $params['search3'] = "%$search%";
    $params['search4'] = "%$search%";
    $filename .= '_Search';
}

// Combine clauses
$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Get visits
$query = "SELECT * FROM visits {$whereSql} ORDER BY visit_date ASC, jam_masuk ASC";
$visits = getAll($query, $params);

// Headers for Excel (XLS)
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename={$filename}.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; }
        .header { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 20px; }
        .sub-header { font-size: 14px; text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #00428d; color: #ffffff; padding: 10px; border: 1px solid #000000; font-weight: bold; vertical-align: middle; }
        td { padding: 8px; border: 1px solid #000000; vertical-align: top; }
        .center { text-align: center; }
        .bg-gray { background-color: #f2f2f2; }
        /* Force Excel to treat as text - prevents scientific notation & preserves leading zeros */
        .text-cell { mso-number-format:"\@"; }
    </style>
</head>
<body>
    <div class="header">LAPORAN VISITOR LOG - <?php echo strtoupper(APP_NAME); ?></div>
    <div class="sub-header">Periode: <?php echo $titlePeriod; ?></div>
    
    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="170">Tanggal</th>
                <th width="150">Nama</th>
                <th width="120">Asal Fungsi</th>
                <th width="150">Alamat</th>
                <th width="120">NO.PEK/NIK/SIM/PASSPORT</th>
                <th width="100">NO. ID CARD</th>
                <th width="200">Keperluan</th>
                <th width="80">Jam Masuk</th>
                <th width="80">Jam Keluar</th>
                <th width="80">Status</th>
                <th width="90">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($visits as $visit): 
                $bgClass = $no % 2 == 0 ? 'bg-gray' : '';
            ?>
            <tr class="<?php echo $bgClass; ?>">
                <td class="center"><?php echo $no++; ?></td>
                <td><?php echo formatDate($visit['visit_date']); ?></td>
                <td><?php echo e($visit['nama']); ?></td>
                <td><?php echo e($visit['fungsi']); ?></td>
                <td><?php echo e($visit['asal']); ?></td>
                <td class="text-cell"><?php echo !empty($visit['no_pek']) ? e($visit['no_pek']) : '-'; ?></td>
                <td class="text-cell"><?php echo e($visit['nomor_identitas']); ?></td>
                <td><?php echo e($visit['keperluan']); ?></td>
                <td class="center"><?php echo formatTime($visit['jam_masuk']); ?></td>
                <td class="center"><?php echo $visit['jam_keluar'] ? formatTime($visit['jam_keluar']) : '-'; ?></td>
                <td class="center">
                    <?php echo $visit['status'] === 'MASUK' ? 'Aktif' : 'Selesai'; ?>
                </td>
                <td>
                    <?php 
                    $ket = [];
                    if (!empty($visit['keterangan']) && $visit['keterangan'] !== '-') $ket[] = $visit['keterangan'];
                    echo empty($ket) ? '-' : implode(', ', $ket);
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <br>
    <table style="width: 100%; border: none;">
        <tr>
            <td colspan="9" style="border: none;"></td>
            <td colspan="2" style="border: none; text-align: center;">
                Dicetak pada: <?php echo date('d F Y H:i'); ?><br><br><br>
                ( <?php echo $_SESSION['admin_nama'] ?? 'Admin'; ?> )
            </td>
        </tr>
    </table>
</body>
</html>
