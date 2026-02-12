<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absen Masuk - <?php echo APP_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#dc2626',
                        accent: '#f59e0b',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen py-8 px-4">
    
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <?php include 'includes/navbar.php'; ?>

        <!-- Alert Container -->
        <div id="alertContainer" class="mb-6"></div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-professional-lg p-8">
            <div class="border-l-4 border-blue-600 pl-4 mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Form Absensi Masuk</h2>
                <p class="text-gray-600 mt-1">Lengkapi informasi di bawah ini</p>
            </div>

            <form id="entryForm" class="space-y-6">
                <!-- Tanggal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tanggal Kunjungan
                        </label>
                        <input type="text" id="visitDate" readonly 
                               value="<?php echo formatDate(date('Y-m-d')); ?>"
                               class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 cursor-not-allowed">
                        <input type="hidden" name="tanggal" value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan nama lengkap">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Asal -->
                    <div>
                        <label for="asal" class="block text-sm font-semibold text-gray-700 mb-2">
                            Asal<span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="asal" name="asal" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan asal tempat tinggal">
                    </div>

                    <!-- Fungsi -->
                    <!-- Fungsi -->
                    <div>
                        <label for="fungsi" class="block text-sm font-semibold text-gray-700 mb-2">
                            Instansi / Fungsi <span class="text-red-600">*</span>
                        </label>
                        <input type="text" id="fungsi" name="fungsi" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: Universitas / Perusahaan / dll">
                    </div>
                </div>

                <!-- Jenis Identitas -->
                <div>
                    <label for="jenisIdentitas" class="block text-sm font-semibold text-gray-700 mb-2">
                        Jenis Identitas <span class="text-red-600">*</span>
                    </label>
                    <select id="jenisIdentitas" name="jenis_identitas" required
                            class="custom-select w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">-- Pilih Jenis Identitas --</option>
                        <option value="KTP">KTP</option>
                        <option value="KTM">KTM (Kartu Tanda Mahasiswa)</option>
                        <option value="ID_CARD">ID Card</option>
                        <option value="LAINNYA">Lainnya...</option>
                    </select>
                    
                    <!-- Input untuk Jenis Identitas Lainnya -->
                    <div id="identitasLainnyaContainer" class="mt-3" style="display: none;">
                        <input type="text" id="jenisIdentitasLainnya" name="jenis_identitas_lainnya"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Masukkan jenis identitas yang digunakan">
                    </div>
                </div>

                <!-- Nomor Identitas -->
                <div>
                    <label for="nomorIdentitas" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nomor Identitas <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="nomorIdentitas" name="nomor_identitas" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Masukkan nomor identitas sesuai jenis identitas yang dipilih">
                    <p class="text-xs text-gray-500 mt-1">Data ini digunakan untuk identifikasi saat absen keluar</p>
                </div>

                <!-- Keperluan -->
                <div>
                    <label for="keperluan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Keperluan Kunjungan <span class="text-red-600">*</span>
                    </label>
                    <textarea id="keperluan" name="keperluan" required rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Jelaskan keperluan kunjungan"></textarea>
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Keterangan (Opsional)
                    </label>
                    <textarea id="keterangan" name="keterangan" rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Catatan tambahan"></textarea>
                </div>

                <!-- Tanda Tangan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanda Tangan <span class="text-red-600">*</span>
                    </label>
                    <div class="border-2 border-gray-300 rounded-lg p-4 bg-gray-50">
                        <canvas id="signatureCanvas" class="signature-canvas w-full h-48 bg-white rounded"></canvas>
                        <button type="button" id="clearSignature" 
                                class="mt-3 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm font-medium">
                            Hapus Tanda Tangan
                        </button>
                    </div>
                    <input type="hidden" id="tandaTanganData" name="tanda_tangan">
                </div>

                <!-- Submit Button -->
                <div class="pt-4 flex gap-3">
                    <button type="submit" id="submitBtn"
                            class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">
                        Kirim Absen Masuk
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600 text-sm space-y-2">
            <p>&copy; 2026 <?php echo APP_NAME; ?> - Version <?php echo APP_VERSION; ?></p>
            <a href="admin/login.php" class="inline-block text-xs text-gray-400 hover:text-blue-600 border border-gray-200 hover:border-blue-400 hover:bg-blue-50 px-3 py-1 rounded-full transition duration-200">
                Login Admin
            </a>
        </div>
    </div>

    <!-- Modal Popup -->
    <div id="resultModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <div class="p-6 text-center">
                <!-- Icon Container -->
                <div id="modalIcon" class="mx-auto w-20 h-20 rounded-full flex items-center justify-center mb-4">
                    <!-- Icon will be inserted here -->
                </div>
                
                <!-- Title -->
                <h3 id="modalTitle" class="text-2xl font-bold mb-2"></h3>
                
                <!-- Message -->
                <p id="modalMessage" class="text-gray-600 mb-6"></p>
                
                <!-- Button -->
                <button id="modalButton" class="w-full py-3 rounded-lg font-semibold transition shadow-md">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/signature.js"></script>
    <script>
        let signatureCanvas;
        let isSuccess = false;
        
        document.addEventListener('DOMContentLoaded', function() {
            signatureCanvas = new SignatureCanvas('signatureCanvas', 'clearSignature');
            
            // Identity Type Dropdown - Show/Hide "Lainnya" input
            const identitySelect = document.getElementById('jenisIdentitas');
            const lainnyaContainer = document.getElementById('identitasLainnyaContainer');
            const lainnyaInput = document.getElementById('jenisIdentitasLainnya');
            
            identitySelect.addEventListener('change', function() {
                if (this.value === 'LAINNYA') {
                    lainnyaContainer.style.display = 'block';
                    lainnyaInput.required = true;
                } else {
                    lainnyaContainer.style.display = 'none';
                    lainnyaInput.required = false;
                    lainnyaInput.value = '';
                }
            });
            
            // Modal button handler
            document.getElementById('modalButton').addEventListener('click', function() {
                closeModal();
                if (isSuccess) {
                    window.location.href = 'index.php';
                }
            });
            
            // Close modal when clicking outside
            document.getElementById('resultModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                    if (isSuccess) {
                        window.location.href = 'index.php';
                    }
                }
            });
            
            document.getElementById('entryForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Validate "Lainnya" input if selected
                if (identitySelect.value === 'LAINNYA' && !lainnyaInput.value.trim()) {
                    showModal('error', 'Validasi Gagal', 'Silakan isi jenis identitas yang digunakan!');
                    return;
                }
                
                if (signatureCanvas.isEmpty()) {
                    showModal('error', 'Validasi Gagal', 'Tanda tangan wajib diisi!');
                    return;
                }
                
                document.getElementById('tandaTanganData').value = signatureCanvas.getDataURL();
                const formData = new FormData(this);
                
                const submitBtn = document.getElementById('submitBtn');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="loading-spinner mr-2"></span> Menyimpan...';
                
                try {
                    const response = await fetch('process_entry.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        isSuccess = true;
                        showModal('success', 'Berhasil!', result.message);
                        // Reset form
                        this.reset();
                        signatureCanvas.clear();
                        lainnyaContainer.style.display = 'none';
                        lainnyaInput.required = false;
                    } else {
                        isSuccess = false;
                        showModal('error', 'Gagal!', result.message);
                    }
                } catch (error) {
                    isSuccess = false;
                    showModal('error', 'Error!', 'Terjadi kesalahan sistem. Silakan coba lagi.');
                    console.error('Error:', error);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        });
        
        function showModal(type, title, message) {
            const modal = document.getElementById('resultModal');
            const modalContent = document.getElementById('modalContent');
            const modalIcon = document.getElementById('modalIcon');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalButton = document.getElementById('modalButton');
            
            if (type === 'success') {
                modalIcon.className = 'mx-auto w-20 h-20 rounded-full flex items-center justify-center mb-4 bg-green-100';
                modalIcon.innerHTML = `
                    <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                `;
                modalTitle.className = 'text-2xl font-bold mb-2 text-green-600';
                modalButton.className = 'w-full py-3 rounded-lg font-semibold transition shadow-md bg-green-500 hover:bg-green-600 text-white';
            } else {
                modalIcon.className = 'mx-auto w-20 h-20 rounded-full flex items-center justify-center mb-4 bg-red-100';
                modalIcon.innerHTML = `
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                `;
                modalTitle.className = 'text-2xl font-bold mb-2 text-red-600';
                modalButton.className = 'w-full py-3 rounded-lg font-semibold transition shadow-md bg-red-500 hover:bg-red-600 text-white';
            }
            
            modalTitle.textContent = title;
            modalMessage.textContent = message;
            
            // Show modal with animation
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        
        function closeModal() {
            const modal = document.getElementById('resultModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 200);
        }
    </script>
</body>
</html>
