# 🚀 Shiprocket Integration for Bagisto

**Shiprocket Integration** by [Wontonee](https://wontonetech.com) allows Bagisto store owners to easily manage shipping logistics, order syncing, and tracking using Shiprocket — one of India's leading shipping aggregators.

---

## 📦 Features

- Sync orders with Shiprocket in real-time
- Track shipment statuses within Bagisto
- Assign pickup addresses and channel IDs
- Secure with license-based access

---

## 🛠️ Installation Instructions

### Step 1: Install via Composer

```bash
composer require wontonee/shiprocket
```

---

### Step 2: Publish the Package

```bash
php artisan vendor:publish --provider="Wontonee\\Shiprocket\\Providers\\ShiprocketServiceProvider"
```

---

### Step 3: Optimize Application

```bash
php artisan optimize
```

---

### Step 4: Get Your License Key

Visit [https://myapps.wontonee.com](https://myapps.wontonee.com) and create a free or paid license key for Shiprocket integration.

---

### Step 5: Configure in Admin Panel

1. Log in to your Bagisto admin panel.
2. Go to:  
   **Shiprocket → Settings**
3. Enter the following:
   - Shiprocket API Username
   - Shiprocket API Password
   - Your **Wontonee License Key**

---

### Step 6: Set Up Channel & Pickup Details

After entering your credentials:

- Go to **Shiprocket → Channel Menu**  
  Enter your **Channel ID**
- Go to **Shiprocket → Pickup Menu**  
  Add your **Pickup Location**

---

## 📋 Requirements

- Bagisto version: `^2.2`
- PHP version: `^8.1`
- Valid License Key from [Wontonee](https://myapps.wontonee.com)

---

## 🧑‍💻 Developed By

**Saju Gopal**  
[Wontonee DigitalCraft LLP](https://wontonee.com)

---

## 📬 Support

- Email: [dev@wontonee.com](mailto:dev@wontonee.com)
- Issues: [GitHub Issues](https://github.com/wontonee/shiprocket/issues)

---

## 📄 License

This package is open-sourced under the [MIT license](LICENSE).
