<?php
// Function to determine active state
if (!function_exists('isActive')) {
    function isActive($page) {
        $current_page = basename($_SERVER['PHP_SELF']);
        return ($current_page === $page) ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200';
    }
}
?>
<div class="bg-white shadow-professional-lg rounded-2xl p-6 mb-8 relative">
    <!-- Decorative Background Element -->
    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-blue-500 to-green-500 opacity-10 rounded-full blur-xl"></div>
    
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
        <!-- Logo & Branding -->
        <a href="index.php" class="flex flex-col md:flex-row items-center gap-3 md:gap-4 text-center md:text-left group cursor-pointer hover:opacity-90 transition">
            <div class="bg-white p-2 rounded-lg shadow-sm border border-gray-100 group-hover:shadow-md transition">
                <img src="https://res.cloudinary.com/drnnwysol/image/upload/v1771404511/logo-pepc_vlsrf0.png" 
                     alt="Pertamina EP Cepu Logo" 
                     class="h-12 w-auto object-contain">
            </div>
            <div class="flex flex-col items-center md:items-start">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 leading-tight group-hover:text-blue-700 transition">Pertamina EP Cepu</h1>
                <p class="text-xs md:text-sm text-gray-600 font-medium">Visitor Management System</p>
                <div class="flex items-center gap-2 mt-1.5">
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-[10px] md:text-xs rounded-full font-semibold border border-green-200">
                        Security Post
                    </span>
                    <span class="text-gray-300">•</span>
                    <span class="text-[10px] md:text-xs text-gray-500"><?php echo date('d M Y'); ?></span>
                </div>
            </div>
        </a>

        <!-- Navigation Actions -->
        <div class="flex flex-nowrap gap-2 md:gap-3 items-center justify-center md:justify-end">
            <a href="index.php" 
               class="px-2.5 md:px-4 py-2 md:py-2.5 rounded-lg font-medium transition duration-200 flex items-center justify-center text-gray-600 hover:bg-gray-100 hover:text-gray-900 border border-gray-200"
               title="Kembali ke Halaman Utama">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </a>
            <a href="guest_entry.php" 
               class="px-3 md:px-6 py-2 md:py-2.5 rounded-lg font-medium transition duration-200 flex items-center justify-center gap-1.5 md:gap-2 text-sm md:text-base whitespace-nowrap <?php echo isActive('guest_entry.php'); ?>">
                <svg class="w-4 h-4 md:w-5 md:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                Masuk
            </a>
            <a href="exit.php" 
               class="px-3 md:px-6 py-2 md:py-2.5 rounded-lg font-medium transition duration-200 flex items-center justify-center gap-1.5 md:gap-2 text-sm md:text-base whitespace-nowrap <?php echo (basename($_SERVER['PHP_SELF']) === 'exit.php') ? 'bg-red-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200'; ?>">
                <svg class="w-4 h-4 md:w-5 md:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Keluar
            </a>
        </div>
    </div>
</div>

