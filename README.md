# 🏠 PG Life — Complete Setup Guide

## 📁 Project Structure
```
PGLife/
├── index.php               ← Homepage
├── property_list.php       ← Browse PGs by city
├── property_detail.php     ← Single PG page
├── booking.php             ← Booking form (NEW ✅)
├── payment.php             ← Payment page  (NEW ✅)
├── dashboard.php           ← User dashboard
├── logout.php              ← Logout
├── pglife.sql              ← Database schema + seed data
│
├── includes/
│   ├── database_connect.php  ← DB credentials (edit this)
│   ├── head_links.php
│   ├── header.php
│   ├── footer.php
│   ├── signup_modal.php
│   └── login_modal.php
│
├── api/
│   ├── signup.php
│   ├── login.php
│   └── mark_interested.php
│
├── css/
│   ├── home.css
│   ├── property_list.css
│   ├── property_detail.css
│   └── dashboard.css
│
├── js/
│   ├── common.js           ← Signup/Login AJAX
│   ├── property_list.js    ← Heart toggle
│   ├── property_detail.js  ← Heart toggle
│   └── dashboard.js        ← Remove interest
│
└── img/
    ├── delhi.png
    ├── mumbai.png
    ├── bangalore.png
    ├── hyderabad.png
    ├── male.png
    ├── female.png
    ├── unisex.png
    ├── filter.png
    ├── asc.png
    ├── desc.png
    ├── man.png
    ├── amenities/          ← SVG icons for amenities
    └── properties/         ← Property photos (subfolders by ID)
```

---

## 🚀 Installation (XAMPP)

### Step 1 — Copy files
Place the entire `PGLife/` folder inside:
```
C:/xampp/htdocs/PGLife/
```

### Step 2 — Import database
1. Start Apache & MySQL in XAMPP Control Panel
2. Open http://localhost/phpmyadmin/
3. Create a new database named `pglife`
4. Click **Import** → choose `pglife.sql` → click **Go**

### Step 3 — Configure DB connection
Open `includes/database_connect.php` and verify:
```php
$db_host     = "localhost";
$db_user     = "root";
$db_password = "";    // add password if your MySQL has one
$db_name     = "pglife";
```

### Step 4 — Open the site
Visit: http://localhost/PGLife/

---

## 🔗 User Flow

```
Homepage → Search city → Property List → Property Detail
                                              ↓
                                         [Book Now]
                                              ↓
                                        booking.php   (Step 1)
                                              ↓
                                  [Proceed to Payment]
                                              ↓
                                        payment.php   (Step 2)
                                              ↓
                                  [Pay Securely] → Success ✅
                                              ↓
                                         dashboard.php
```

> **Note:** The "Book Now" button requires the user to be **logged in**.
> If not logged in, they will be redirected to the homepage.

---

## 📸 Adding Property Images
Place images in: `img/properties/{property_id}/`

For example:
```
img/properties/1/room1.jpg
img/properties/1/room2.jpg
img/properties/2/room1.jpg
```

---

## ✅ Features
- User Signup / Login (with hashed passwords)
- Browse PGs by city
- Filter by gender (Male / Female / Unisex)
- Sort by rent (high to low / low to high)
- Heart/interest toggle (AJAX)
- Full property detail with amenities, ratings, testimonials
- Multi-step booking form (pre-filled with user data)
- Payment page with Card / UPI / Net Banking / Wallet tabs
- Booking confirmation popup
- User dashboard showing saved properties
