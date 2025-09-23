---

# Dev Monitoring Guard

A Laravel package that provides a **secure developer dashboard** for **Telescope**, **Log Viewer**, and **Scramble**, protected by a **custom DevUser auth guard**.

> Before accessing developer tools, users must log in via the DevUser guard.

---

## **Features**

* Custom **DevUser authentication guard**.
* Secure **developer dashboard**.
* Quick links to:

  * [Laravel Telescope](https://laravel.com/docs/telescope)
  * [Log Viewer](https://github.com/rap2hpoutre/laravel-log-viewer)
  * [Scramble](https://github.com/beyondcode/laravel-scramble)
* Seeder for immediate DevUser login.
* Easy integration with Laravel 10+ / 11+.

---

## **Installation**

### **1. Require the Package**

```bash
composer require emmanuelikeogu/devguard
```

> If using locally, add a `path` repository in `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/emmanuelikeogu/devguard"
    }
]
```

Then run:

```bash
composer require emmanuelikeogu/devguard:@dev
```

---

### **2. Publish Views (Optional)**

```bash
php artisan vendor:publish --provider="DevMonitoringGuard\DevMonitoringGuardServiceProvider" --tag=views
```

---

### **3. Run Migrations**

```bash
php artisan migrate
```

This creates the `dev_users` table.

---

### **4. Seed a DevUser**

```bash
php artisan db:seed --class=DevUserSeeder
```

**Default credentials:**

```
Email: dev@example.com
Password: secret123
```

> You can change them by editing the seeder.

---

## **Configuration**

In `config/auth.php`, ensure the guard and provider exist:

```php
'guards' => [
    'dev' => [
        'driver' => 'session',
        'provider' => 'dev_users',
    ],
],

'providers' => [
    'dev_users' => [
        'driver' => 'eloquent',
        'model' => App\Models\DevUser::class,
    ],
],
```

---

## **Usage**

1. Access the **Dev Dashboard**:

```
/dev-dashboard
```

2. Links on the dashboard give access to:

* Telescope: `/telescope`
* Log Viewer: `/logs`
* Scramble: `/scramble`

3. Routes are **protected by the DevUser guard**; only logged-in DevUsers can access.

---




---

## **Screenshots**

*(Optional: add screenshots of dashboard, Telescope, Log Viewer, Scramble)*

---

## **License**

MIT License. See [LICENSE](LICENSE).

---
