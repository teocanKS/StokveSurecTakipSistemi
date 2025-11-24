# 🏢 Envanter ve Süreç Takip Sistemi — Doğu AŞ

Modern, güvenli ve profesyonel **envanter ve süreç takip sistemi**.
Tamamen **PHP, Vanilla JavaScript ve Tailwind CSS** ile geliştirilmiştir.

---

## 🚀 Özellikler

- 👥 **Rol tabanlı erişim:** Yönetici ve Personel rolleri
- 🔐 **Güvenli kimlik doğrulama:** PHP session + password hashing
- 📦 **Stok yönetimi:** Kritik stok uyarıları, yeniden sipariş kuyruğu
- 📊 **Dashboard:** KPI kartları, gerçek zamanlı veriler
- 🗓️ **Aktif işler:** Alış/satış operasyonları takibi
- 🕒 **Geçmiş:** Gelişmiş filtreleme ve sıralama
- 🎨 **Modern UI:** Tailwind CSS ile profesyonel tasarım
- 🔒 **Güvenlik:** SQL injection, XSS, CSRF koruması

---

## 🧠 Teknoloji Stack

| Katman     | Teknoloji               |
|------------|-------------------------|
| Frontend   | HTML5, Tailwind CSS, Vanilla JS |
| Backend    | PHP 8.0+                |
| Veritabanı | Supabase PostgreSQL     |
| Icons      | Heroicons               |

---

## 📁 Proje Yapısı

```
/StokveSurecTakipSistemi/
├── public/                 # Web root
│   ├── index.php           # Login sayfası
│   ├── /personel/          # Personel paneli
│   └── /yonetici/          # Yönetici paneli
├── src/                    # Backend PHP
│   ├── /config/            # Veritabanı ve config
│   ├── /auth/              # Kimlik doğrulama
│   ├── /api/               # REST API endpoints
│   └── /utils/             # Yardımcı fonksiyonlar
└── logs/                   # Log dosyaları
```

---

## 🔧 Kurulum

### 1. Gereksinimler

- PHP 8.0 veya üstü
- PostgreSQL (Supabase)
- Apache/Nginx web server

### 2. Konfigürasyon

1. `src/config/database.php.example` dosyasını kopyalayın:
   ```bash
   cp src/config/database.php.example src/config/database.php
   ```

2. Supabase bağlantı bilgilerinizi girin:
   ```php
   define('SUPABASE_URL', 'https://your-project.supabase.co');
   define('SUPABASE_SERVICE_KEY', 'your-service-role-key');
   define('DB_CONNECTION_STRING', 'postgresql://...');
   ```

3. Web server'ınızı `public/` klasörüne point edin.

### 3. Veritabanı

Supabase'de gerekli tablolar zaten mevcut. Schema için `database_schema.sql` dosyasına bakın.

---

## 🔐 Güvenlik Özellikleri

- ✅ **SQL Injection Koruması:** PDO Prepared Statements
- ✅ **XSS Koruması:** HTML escaping ve sanitization
- ✅ **CSRF Token:** Form güvenliği
- ✅ **Password Hashing:** bcrypt ile şifreleme
- ✅ **Session Güvenliği:** httponly, secure, samesite cookies
- ✅ **RLS Bypass:** Service role key (sadece backend)

---

## 👥 Kullanıcı Rolleri

### Personel (viewer)
- Stok durumunu görüntüleme
- Kendi aktif işlerini görüntüleme
- Geçmiş işlemleri görüntüleme

### Yönetici (yonetici)
- Tüm personel yetkilerine ek olarak:
- Kullanıcı yönetimi
- Tüm operasyonları görüntüleme ve düzenleme
- Sistem raporları ve analizler

---

## 🎨 UI/UX Tasarımı

### Personel Paneli
- **Renk Teması:** Mavi tonları (profesyonel, yumuşak)
- **Vurgu Rengi:** `blue-600`
- **Sidebar:** Açık mavi gradient

### Yönetici Paneli
- **Renk Teması:** Koyu tonlar (kurumsal, ciddi)
- **Vurgu Rengi:** `orange-500`
- **Sidebar:** Koyu slate gradient

---

## 📝 Lisans

Bu proje Doğu AŞ için özel olarak geliştirilmiştir.

---

## 📞 İletişim

Sorularınız için: [GitHub Issues](https://github.com/teocanKS/StokveSurecTakipSistemi/issues)

---

**Geliştirici:** AI-Assisted Development
**Versiyon:** 1.0.0
**Son Güncelleme:** 2025-11-24
