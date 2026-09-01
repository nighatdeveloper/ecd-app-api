# ECD API — Ready-to-Run Package

Ye poora Laravel project hai — official Laravel skeleton + humara custom code (Google Login,
Profile Update, Add Children) already merged. Pichli zip ki tarah manually copy-paste
nahi karna — bas neeche diye steps follow karein.

---

## Step 1 — Extract karein

Is zip ko extract karein kahin bhi, jaise `C:\xampp\htdocs\ecd-app`.

## Step 2 — Dependencies install karein

Project folder ke andar terminal/PowerShell kholein:

```bash
cd ecd-app
composer install
```

Ye Laravel framework + Sanctum (already `composer.json` mein add kiya hua hai) dono install kar dega.

## Step 3 — `.env` banayein

```bash
copy .env.example .env
php artisan key:generate
```

`.env` file already MySQL ke liye configured hai:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecd_app
DB_USERNAME=root
DB_PASSWORD=
```

## Step 4 — Database banayein

XAMPP ka phpMyAdmin (`http://localhost/phpmyadmin`) kholein → naya database banayein: **`ecd_app`**

## Step 5 — Migrate aur serve

```bash
php artisan migrate
php artisan serve
```

Ab API live hai: `http://127.0.0.1:8000/api/v1/...`

---

## Endpoints

### 1. Google Login — `POST /api/v1/auth/google-login`
```json
{
  "email": "user@gmail.com",
  "name": "Muhammad Zohaib",
  "profile_image": "https://example.com/profile.jpg",
  "google_access_token": "google_access_token"
}
```
Backend token ko Google ke `oauth2/v3/userinfo` endpoint se verify karta hai. Naya user → `201`, existing → `200`.

### 2. Update Profile — `POST /api/v1/profile/update` 🔒
Header: `Authorization: Bearer {token}`
```json
{ "gender": "Male", "age": 30 }
```

### 3. Add Children — `POST /api/v1/children` 🔒
Header: `Authorization: Bearer {token}`

`total_children` = `total_daughters + total_sons + total_transgender` warna `422` error.

---

## Quick test (curl)

```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/google-login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"user@gmail.com\",\"name\":\"Muhammad Zohaib\",\"profile_image\":\"https://example.com/p.jpg\",\"google_access_token\":\"REAL_GOOGLE_TOKEN\"}"
```

> Google Login test karne ke liye real Google access token chahiye (Flutter app se ya
> Google OAuth Playground se) — backend usse Google ke server par verify karta hai.

## Agar koi error aaye

Jo bhi command chalayein aur error aaye, poora output copy karke bhej dein — us hisaab se fix karte hain.
