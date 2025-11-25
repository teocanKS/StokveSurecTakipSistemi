/**
 * Personel Dashboard JavaScript
 */

// KPI Kartlarını yükle
async function loadDashboardStats() {
    try {
        // Demo data (gerçek ortamda API'den gelecek)
        const stats = {
            stok: {
                toplam_urun: 145,
                toplam_stok_adedi: 8734
            },
            kritik_stok: 12,
            bugunun_islemleri: 8,
            bu_ay_satis: 156780.50
        };

        const kpiCardsHTML = `
            <!-- Toplam Ürün -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-slate-600 mb-1">Toplam Ürün</p>
                <p class="text-3xl font-bold text-slate-900">${formatSayi(stats.stok.toplam_urun)}</p>
            </div>

            <!-- Kritik Stok -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-slate-600 mb-1">Kritik Stok</p>
                <p class="text-3xl font-bold text-orange-600">${formatSayi(stats.kritik_stok)}</p>
            </div>

            <!-- Bugünkü İşlemler -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-slate-600 mb-1">Bugünkü İşlemler</p>
                <p class="text-3xl font-bold text-slate-900">${formatSayi(stats.bugunun_islemleri)}</p>
            </div>

            <!-- Bu Ay Satış -->
            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition card-hover">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-slate-600 mb-1">Bu Ay Satış</p>
                <p class="text-2xl font-bold text-slate-900">${formatPara(stats.bu_ay_satis)}</p>
            </div>
        `;

        document.getElementById('kpiCards').innerHTML = kpiCardsHTML;

    } catch (error) {
        console.error('KPI yükleme hatası:', error);
    }
}

// Son işlemleri yükle
async function loadRecentOperations() {
    try {
        // Demo data
        const operations = [
            { tip: 'satis', firma: 'ABC Ltd.', urun: 'Ürün A', adet: 50, tutar: 12500, tarih: '2025-01-20' },
            { tip: 'alis', firma: 'XYZ A.Ş.', urun: 'Ürün B', adet: 100, tutar: 25000, tarih: '2025-01-19' },
            { tip: 'satis', firma: 'DEF Corp.', urun: 'Ürün C', adet: 30, tutar: 8900, tarih: '2025-01-18' }
        ];

        if (operations.length === 0) {
            document.getElementById('recentOperations').innerHTML = `
                <div class="text-center py-12 text-slate-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p>Henüz aktif işlem bulunmuyor</p>
                </div>
            `;
            return;
        }

        const operationsHTML = operations.map(op => {
            const tipIcon = op.tip === 'satis' ?
                '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>' :
                '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>';

            return `
                <div class="flex items-center space-x-4 p-4 hover:bg-slate-50 rounded-lg transition">
                    <div class="w-12 h-12 ${op.tip === 'satis' ? 'bg-green-100' : 'bg-blue-100'} rounded-lg flex items-center justify-center">
                        ${tipIcon}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-800">${op.firma || '-'}</p>
                        <p class="text-sm text-slate-600">${op.urun || '-'} · ${formatSayi(op.adet)} adet</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-slate-900">${formatPara(op.tutar)}</p>
                        <p class="text-xs text-slate-500">${formatTarih(op.tarih)}</p>
                    </div>
                </div>
            `;
        }).join('');

        document.getElementById('recentOperations').innerHTML = operationsHTML;

    } catch (error) {
        console.error('Son işlemler yükleme hatası:', error);
    }
}

// Saat güncelle
function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('currentTime').textContent = `${hours}:${minutes}`;
}

// Tarih güncelle
function updateDate() {
    const now = new Date();
    const options = { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' };
    const dateStr = now.toLocaleDateString('tr-TR', options);
    document.getElementById('currentDate').textContent = `Bugün ${dateStr}`;
}

// Sayfa yüklendiğinde
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardStats();
    loadRecentOperations();
    updateClock();
    updateDate();

    // Saati her dakika güncelle
    setInterval(updateClock, 60000);
});
