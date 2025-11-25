# 📦 Statik Export - Doğu AŞ Envanter Sistemi

Bu klasör, projenin **sadece frontend (HTML/CSS/JS)** kaynaklarını içerir.
PHP backend kodları buraya dahil edilmemiştir.

---

## 📁 Klasör Yapısı

```
static-export/
├── login/                    # 🔐 Login Sayfası
│   ├── index.html
│   ├── style.css
│   └── script.js
│
├── personel/                 # 👤 PERSONEL PANELİ (Mavi Tema)
│   ├── dashboard/
│   │   ├── index.html
│   │   ├── style.css
│   │   └── script.js
│   ├── stok/
│   ├── aktif-isler/
│   └── gecmis/
│
├── yonetici/                 # 👨‍💼 YÖNETİCİ PANELİ (Koyu Tema)
│   ├── dashboard/
│   │   ├── index.html
│   │   ├── style.css
│   │   └── script.js
│   ├── stok/
│   ├── aktif-isler/
│   ├── gecmis/
│   └── kullanici-yonetimi/
│
├── assets/                   # 🎨 Ortak Kaynaklar
│   ├── css/
│   │   └── common.css        # Ortak stil tanımlamaları
│   └── js/
│       └── common.js         # Ortak JavaScript fonksiyonlar
│
└── README.md                 # 📖 Bu dosya
```

---

## 🎯 Özellikler

### ✅ Tamamlanmış Sayfalar

#### 1. Login Sayfası (`login/`)
- ✅ Modern gradient animasyonlu tasarım
- ✅ Password göster/gizle toggle
- ✅ Flash message sistemi
- ✅ Responsive design
- ✅ Form validation

#### 2. Personel Dashboard (`personel/dashboard/`)
- ✅ Mavi tema sidebar
- ✅ KPI kartları (Toplam ürün, kritik stok, bugünkü işlemler, bu ay satış)
- ✅ Son işlemler listesi
- ✅ Dinamik veri yükleme (demo data)
- ✅ Saat ve tarih gösterimi

#### 3. Yönetici Dashboard (`yonetici/dashboard/`)
- ✅ Koyu tema sidebar
- ✅ Sistem özeti kartları
- ✅ Genişletilmiş KPI'lar
- ✅ Kullanıcı yönetimi linki

---

## 🚀 Kullanım

### Local Olarak Çalıştırma

1. **Basit HTTP Server ile:**
   ```bash
   cd static-export
   python3 -m http.server 8000
   ```
   Tarayıcıda: `http://localhost:8000/login/`

2. **Node.js http-server ile:**
   ```bash
   npx http-server static-export -p 8000
   ```

3. **VS Code Live Server Extension:**
   - `login/index.html` dosyasına sağ tıklayın
   - "Open with Live Server" seçin

### Doğrudan HTML Açma
Tarayıcınızda `login/index.html` dosyasını doğrudan açabilirsiniz.
⚠️ Not: API çağrıları CORS hatası verebilir.

---

## 🔧 Özelleştirme

### Renk Teması Değiştirme

**Personel Paneli (Mavi → Yeşil):**
```css
/* assets/css/common.css içinde */
.sidebar-gradient-personel {
    background: linear-gradient(180deg, #10b981 0%, #059669 100%);
}
```

**Yönetici Paneli (Koyu → Mor):**
```css
.sidebar-gradient-yonetici {
    background: linear-gradient(180deg, #7c3aed 0%, #6d28d9 100%);
}
```

### Logo Değiştirme
Her sayfa header'ındaki SVG icon'u değiştirin:
```html
<div class="w-10 h-10 bg-orange-500 rounded-lg">
    <!-- Buraya kendi logo SVG'nizi yerleştirin -->
</div>
```

### API Endpoint'leri
`assets/js/common.js` dosyasında:
```javascript
const API_BASE = '/api/';  // Kendi API URL'nizi yazın
```

---

## 🎨 Tasarım Sistemi

### Renkler

**Personel Paneli:**
- Primary: `#2563eb` (Mavi)
- Accent: `#1d4ed8` (Koyu Mavi)
- Success: `#10b981` (Yeşil)
- Warning: `#f59e0b` (Turuncu)

**Yönetici Paneli:**
- Primary: `#1e293b` (Koyu Gri)
- Accent: `#f97316` (Turuncu)
- Background: `#0f172a` (Çok Koyu)

### Typography
- Font: System UI Stack (Arial, Helvetica fallback)
- Başlıklar: Bold, 2xl-3xl
- Normal metin: Regular, sm-base

### Spacing
- Card padding: `p-6` (24px)
- Section margin: `mb-6` (24px)
- Grid gap: `gap-6` (24px)

---

## 📊 Demo Data

Statik versiyonda, tüm API çağrıları **demo data** döndürür.

### Demo Kullanıcılar (Console'da görülebilir)
```
Yönetici: yonetici@dogu.com / admin123
Personel: personel@dogu.com / personel123
```

### Demo İstatistikler
```javascript
{
    toplam_urun: 145,
    kritik_stok: 12,
    bugunun_islemleri: 8,
    bu_ay_satis: 156780.50
}
```

---

## 🔗 Backend Entegrasyonu

Gerçek backend ile çalıştırmak için:

1. **API Base URL'i Güncelleyin:**
   ```javascript
   // assets/js/common.js
   const API_BASE = 'https://api.your-domain.com/';
   ```

2. **CSRF Token Meta Tag'i Ekleyin:**
   ```html
   <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
   ```

3. **Authentication Check:**
   Her sayfanın başına PHP auth kontrolü ekleyin.

---

## ⚠️ Önemli Notlar

1. **Bu Statik Versiyondur:**
   - API çağrıları demo data döndürür
   - Gerçek veritabanı bağlantısı yok
   - Authentication simüle edilmiştir

2. **Production Kullanımı:**
   - Bu dosyalar doğrudan production'da kullanılmamalı
   - Sadece tasarım/UI referansı içindir
   - Backend entegrasyonu gereklidir

3. **Güvenlik:**
   - CSRF koruması PHP backend'de implement edilmeli
   - XSS koruması için tüm user input'lar escape edilmeli
   - SQL Injection için prepared statements kullanılmalı

---

## 🛠️ Geliştirme

### Yeni Sayfa Eklemek

1. Yeni klasör oluşturun:
   ```bash
   mkdir -p yonetici/yeni-sayfa
   ```

2. Template dosyaları kopyalayın:
   ```bash
   cp personel/dashboard/* yonetici/yeni-sayfa/
   ```

3. İçeriği özelleştirin ve sidebar linklerini güncelleyin.

### Tailwind CSS Customization

CDN kullanıldığı için, custom Tailwind config gerekiyorsa:
```html
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'custom-blue': '#1e40af'
                }
            }
        }
    }
</script>
```

---

## 📞 Destek

Sorularınız için:
- 📧 Email: destek@dogu.com
- 📄 Dokümantasyon: [GitHub Wiki](#)
- 🐛 Issue: [GitHub Issues](#)

---

## 📝 Lisans

© 2025 Doğu AŞ. Tüm hakları saklıdır.

---

## 🎉 Katkıda Bulunanlar

- UI/UX Design: Doğu AŞ Development Team
- Frontend Development: Claude AI
- Backend Integration: PHP Team

---

**Son Güncelleme:** 25 Ocak 2025
**Versiyon:** 1.0.0
**Durum:** ✅ Stable
