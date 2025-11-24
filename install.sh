#!/bin/bash

################################################################################
# Stokve Surec Takip Sistemi - Raspberry Pi Kurulum Script'i
# Nginx + PHP-FPM + PostgreSQL Client
################################################################################

echo "╔══════════════════════════════════════════════════════════╗"
echo "║   Doğu AŞ - Stok ve Süreç Takip Sistemi Kurulumu        ║"
echo "║   Raspberry Pi + Nginx + PHP + Supabase                  ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""

# Root kontrolü
if [ "$EUID" -ne 0 ]; then
    echo "❌ Bu script'i root olarak çalıştırın: sudo bash install.sh"
    exit 1
fi

echo "📦 Sistem güncelleniyor..."
apt update && apt upgrade -y

echo ""
echo "📦 Gerekli paketler kuruluyor..."
apt install -y nginx php8.2-fpm php8.2-pgsql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath git

# PHP 8.2 yoksa 7.4 dene
if ! command -v php8.2 &> /dev/null; then
    echo "⚠️  PHP 8.2 bulunamadı, PHP 7.4 kuruluyor..."
    apt install -y php7.4-fpm php7.4-pgsql php7.4-mbstring php7.4-xml php7.4-curl php7.4-zip php7.4-bcmath
    PHP_VERSION="7.4"
else
    PHP_VERSION="8.2"
fi

echo ""
echo "📁 Proje dizini oluşturuluyor..."
mkdir -p /var/www/stokve
chown -R www-data:www-data /var/www/stokve

echo ""
echo "📋 Dosyalar kopyalanıyor..."
CURRENT_DIR=$(pwd)
cp -r $CURRENT_DIR/* /var/www/stokve/
chown -R www-data:www-data /var/www/stokve

echo ""
echo "🔧 Nginx yapılandırması..."
cp /var/www/stokve/nginx.conf /etc/nginx/sites-available/stokve

# PHP version'ı nginx config'de güncelle
sed -i "s/php8.2-fpm.sock/php${PHP_VERSION}-fpm.sock/g" /etc/nginx/sites-available/stokve

# Eski default config'i kaldır
rm -f /etc/nginx/sites-enabled/default

# Yeni config'i aktif et
ln -sf /etc/nginx/sites-available/stokve /etc/nginx/sites-enabled/stokve

echo ""
echo "🔧 PHP-FPM yapılandırması..."
# PHP memory limit artır
sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/${PHP_VERSION}/fpm/php.ini
sed -i 's/upload_max_filesize = .*/upload_max_filesize = 20M/' /etc/php/${PHP_VERSION}/fpm/php.ini
sed -i 's/post_max_size = .*/post_max_size = 20M/' /etc/php/${PHP_VERSION}/fpm/php.ini

echo ""
echo "🔒 Dosya izinleri ayarlanıyor..."
chmod -R 755 /var/www/stokve
chmod -R 775 /var/www/stokve/public
chown -R www-data:www-data /var/www/stokve

echo ""
echo "🔐 Environment variables ayarlanıyor..."
if [ ! -f /var/www/stokve/.env ]; then
    cp /var/www/stokve/.env.example /var/www/stokve/.env
    echo "⚠️  .env dosyası oluşturuldu. Supabase bilgilerinizi girin:"
    echo "   nano /var/www/stokve/.env"
fi

echo ""
echo "🔄 Servisler yeniden başlatılıyor..."
systemctl restart php${PHP_VERSION}-fpm
systemctl restart nginx

echo ""
echo "✅ Nginx ve PHP-FPM otomatik başlatma..."
systemctl enable nginx
systemctl enable php${PHP_VERSION}-fpm

echo ""
echo "🧪 Konfigürasyon test ediliyor..."
nginx -t

if [ $? -eq 0 ]; then
    echo ""
    echo "╔══════════════════════════════════════════════════════════╗"
    echo "║              🎉 KURULUM TAMAMLANDI! 🎉                   ║"
    echo "╚══════════════════════════════════════════════════════════╝"
    echo ""
    echo "📌 Yapılması Gerekenler:"
    echo ""
    echo "1️⃣  Environment variables'ı düzenle:"
    echo "   sudo nano /var/www/stokve/.env"
    echo ""
    echo "2️⃣  Supabase bilgilerini gir (.env dosyasına)"
    echo ""
    echo "3️⃣  Test kullanıcısı oluştur:"
    echo "   php /var/www/stokve/generate_test_hash.php"
    echo ""
    echo "4️⃣  Tarayıcıdan test et:"
    echo "   http://$(hostname -I | awk '{print $1}')"
    echo ""
    echo "📊 Servis Durumları:"
    systemctl status nginx --no-pager | grep "Active:"
    systemctl status php${PHP_VERSION}-fpm --no-pager | grep "Active:"
    echo ""
    echo "📝 Log dosyaları:"
    echo "   Nginx: /var/log/nginx/stokve-*.log"
    echo "   PHP: /var/log/php${PHP_VERSION}-fpm.log"
    echo ""
else
    echo ""
    echo "❌ Nginx konfigürasyon hatası! Lütfen kontrol edin."
    exit 1
fi
