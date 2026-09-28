# Hostinger Deployment Guide (School Management System)

Yeh guide aapko bataye gi ke **Laravel + Vite** project ko Hostinger Shared Hosting / VPS par live kaise karna hai, kon kon se folders upload karne hain aur bad mei kon si commands run karni hain.

---

## 📁 1. Kon Kon Se Folders Upload Karne Hain?

Project ko Hostinger par upload karne se pehle zaroori folders aur files ki detail:

### ✅ In Folders & Files Ko Zip Karke Upload Karein:
- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `public/` *(Zaroori: `npm run build` chalane ke baad ka `public/build` folder isme shamil hona chahiye)*
- `resources/`
- `routes/`
- `storage/`
- `artisan`
- `composer.json` & `composer.lock`
- `package.json`
- `.env.example` *(ya server par naya `.env` banayein)*

---

### ❌ In Folders Ko Exclude Karein (Upload NA Karein):
1. **`node_modules/`** - Yeh heavy folder hota hai. Assets ki building local machine par `npm run build` se `public/build/` mein already ban chuki hoti hai.
2. **`.git/`** - Git history ki hosting par zaroorat nahi hoti.
3. **`vendor/`** *(Optional)* - Agar aap Hostinger SSH se `composer install` chalayein ge to vendor upload karne ki zaroorat nahi hai. Agar SSH access **na** ho to local `vendor/` folder zip karke upload karna parega.
4. **`storage/logs/*`** aur **`storage/framework/cache/data/*`** - Local system ki logs aur cache files exclude kar dein.

---

## ⚡ 2. Upload Karne Se Pehle Local Machine Par Steps

Apni local machine par terminal open karke yeh commands chalayein:

```bash
# 1. Assets compile build karein (Vite build)
npm run build

# 2. Local cache clear karein
php artisan config:clear
php artisan cache:clear
```

Iske baad poore project directory ko `.zip` file mein compress karein (`node_modules` aur `.git` ko chhor kar).

---

## 🌐 3. Hostinger Server Setup & File Upload

### Method A: Hostinger Document Root Change Karna (Recommended / Easy Method)
1. Hostinger **hPanel** mein login karein.
2. **Websites** -> **Manage** -> **Domain Settings** / **General Information** par jayein.
3. Domain ka **Document Root** change karke `public_html/public` kar dein.
4. Project ZIP file ko `public_html` mein upload karke Extract kar dein.

---

### Method B: Document Root Change Ki Option Na Ho (Alternative Method)
1. `public_html` folder se bahar (Root Directory mein) ek naya folder banayein, e.g., `laravel-app/`.
2. Zip file ko `laravel-app/` folder mein extract karein.
3. `laravel-app/public/` ke andar ki tamaam files (`index.php`, `.htaccess`, `build/`, `favicon.ico`, etc.) ko copy/move karke `public_html/` mein paste kar dein.
4. `public_html/index.php` file ko edit karke paths update karein:
   ```php
   // Vendor path update
   require __DIR__.'/../laravel-app/vendor/autoload.php';

   // Bootstrap path update
   $app = require_once __DIR__.'/../laravel-app/bootstrap/app.php';
   ```

---

## 🗄️ 4. Database Setup

1. Hostinger **hPanel** -> **Databases** -> **MySQL Databases** mein jayein.
2. Naya **Database Name**, **Database Username**, aur strong **Password** create karein.
3. **phpMyAdmin** open karke local database se export ki hui `.sql` file import karein.

---

## ⚙️ 5. `.env` File Configuration

Hostinger Server par `.env` file create / edit karein:

```env
APP_NAME="School Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_hostinger_db_name
DB_USERNAME=your_hostinger_db_user
DB_PASSWORD=your_hostinger_db_password
```

---

## 💻 6. Server Par Kon Kon Si Commands Run Karna Hogi?

Hostinger **SSH Terminal** (hPanel -> Advanced -> SSH Access) se apne project root directory mein jayein:

```bash
# Domain folder mein navigate karein
cd domains/yourdomain.com/public_html
```

Phir yeh commands ek ek kar ke run karein:

```bash
# 1. Composer packages install karein (Agar vendor upload nahi kiya tha)
composer install --optimize-autoloader --no-dev

# 2. Application key generate karein (Agar missing ho)
php artisan key:generate

# 3. Database migrations run karein (Agar phpMyAdmin se SQL import nahi kiya)
php artisan migrate --force

# 4. Storage Link banayein (Upload ki hui images aur files show karne ke liye)
php artisan storage:link

# 5. Production Performance & Cache Commands (Site fast chalane ke liye)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Storage aur Cache folder permissions fix karein
chmod -R 775 storage bootstrap/cache
```

---

## 🛠️ 7. Agar SSH Terminal Access Na Ho (Without SSH Alternative)

Agar aapke paas Hostinger SSH access nahi hai:
1. Local machine se `vendor/` folder ko zip karke upload karein.
2. Storage link banane ke liye `routes/web.php` mein temporary route add karein:
   ```php
   Route::get('/create-storage-link', function () {
       Illuminate\Support\Facades\Artisan::call('storage:link');
       return 'Storage link successfully created!';
   });
   ```
3. Browser mein `https://yourdomain.com/create-storage-link` open karein. Link ban jaane ke baad yeh route delete kar dein.

---

## ✅ Final Check
Aapki site **https://yourdomain.com** par successfully live aur functioning honi chahiye!
