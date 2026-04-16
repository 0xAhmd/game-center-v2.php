# 🎮 Game Center

A full-stack game store web application built with PHP, MySQL, and vanilla JavaScript. Game Center supports user authentication, a shopping cart, order management, an admin dashboard, and profile customization — all wrapped in a dark, glassmorphism-inspired UI.

---

## 📸 Preview

| Store | Cart | Admin Dashboard |
|-------|------|-----------------|
| Browse and search a catalog of games | Add games to cart and checkout | Manage orders, users, and game listings |

---

## ✨ Features

### 🛍️ Store
- Browse a full game catalog with cover images, genres, and prices
- Live search filtering by game title
- Dedicated game detail page with purchase button
- Free and paid game support

### 🛒 Shopping Cart
- Add, update quantity, or remove items
- Clear entire cart
- Checkout with order confirmation modal
- Cart item count badge in the navbar

### 📦 Orders
- Users can view their full order history
- Per-order detail modal with itemized breakdown
- Order status tracking (Pending, Processing, Completed, Cancelled)

### 👤 User Profiles
- Edit username and email
- Change password with current password verification
- Upload a profile picture (avatar)
- View order stats and recent order history

### ⚙️ Admin Dashboard
- Stats panel: total games, users, orders, and revenue
- Manage all orders with status update controls
- Manage all users: view, change role, or delete
- Add new games with image URL or file upload
- Edit or delete existing games inline via modal

### 🔐 Authentication
- Register and login with secure password hashing (`password_hash`)
- "Remember Me" functionality via signed cookie (30-day session)
- Role-based access control: `admin` vs `user`
- Auto-login from cookie on every page

---

## 🗂️ Project Structure

```
game_center/
├── HTML/
│   ├── index.html          # Store / game listing page
│   ├── game.html           # Individual game detail page
│   ├── cart.html           # Shopping cart
│   ├── orders.html         # Order history (user & admin)
│   ├── admin.html          # Admin dashboard
│   ├── profile.html        # User profile
│   ├── add.html            # Add new game (admin)
│   ├── login.html          # Login page
│   └── register.html       # Registration page
│
├── JS/
│   ├── auth_ui.js          # Navbar injection based on session state
│   ├── fetch_games.js      # Fetches and renders game cards
│   ├── scripts.js          # Search, modal logic, toast notifications
│   ├── cart.js             # Cart page logic
│   ├── orders.js           # Orders page logic
│   └── admin.js            # Admin dashboard logic
│
├── css/
│   ├── styles.css          # Global styles, navbar, cards, buttons
│   ├── store.css           # Cart, orders, admin panel styles
│   ├── auth.css            # Login & register page styles
│   ├── profile.css         # Profile page styles
│   └── avatar-additions.css # Avatar/navbar additions
│
├── scripts/
│   ├── db_connect.php      # PDO + MySQLi database connection
│   ├── auth.php            # Auth helpers (login, admin guard, cookie)
│   ├── session_status.php  # Returns current session as JSON
│   ├── login.php           # Login handler
│   ├── logout.php          # Logout + cookie clear
│   ├── register.php        # Registration handler
│   ├── get_games.php       # Returns all games as JSON
│   ├── add_game.php        # Add game (with image upload)
│   ├── update_game.php     # Update game details
│   ├── delete_game.php     # Delete game by ID
│   ├── cart.php            # Cart CRUD API
│   ├── orders.php          # Orders + checkout API
│   ├── profile.php         # Profile view/update/password API
│   ├── upload_avatar.php   # Avatar upload handler
│   └── users.php           # Admin user management API
│
├── uploads/                # Uploaded game images and avatars
│   └── avatars/
│
├── assets/
│   ├── logo.png
│   └── profile.png
│
└── game_center.sql         # Full database schema + seed data
```

---

## 🛠️ Tech Stack

| Layer      | Technology                          |
|------------|-------------------------------------|
| Frontend   | HTML5, CSS3, Vanilla JavaScript     |
| UI Library | Bootstrap 5.3                       |
| Backend    | PHP 8.x                             |
| Database   | MySQL / MariaDB (via PDO + MySQLi)  |
| Server     | Apache (XAMPP / WAMP / LAMP)        |
| Fonts      | Google Fonts — Rajdhani + Inter     |

---

## 🚀 Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (or any LAMP/WAMP stack)
- PHP 8.0+
- MySQL / MariaDB

### Installation

1. **Clone the repository** into your web server's root directory:
   ```bash
   git clone https://github.com/your-username/game-center.git
   cd game-center
   ```
   Place the folder inside `htdocs/` (XAMPP) or `www/` (WAMP).

2. **Create the database:**
   - Open [phpMyAdmin](http://localhost/phpmyadmin)
   - Create a new database named `game_center`
   - Import `game_center.sql` via the **Import** tab

3. **Configure the database connection** in `scripts/db_connect.php`:
   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";          // your MySQL password
   $db   = "game_center";
   ```

4. **Start Apache and MySQL** from the XAMPP Control Panel.

5. **Open the app** in your browser:
   ```
   http://localhost/game-center/HTML/index.html
   ```

---

## 🔑 Default Credentials

| Role  | Email                     | Password   |
|-------|---------------------------|------------|
| Admin | admin@gamecenter.com      | `password` |
| User  | ultramale200987@gmail.com | (hashed)   |

> ⚠️ **Change default credentials before any public deployment.**

---

## 📡 API Reference

All backend endpoints return `application/json`.

### Auth & Session

| Endpoint                   | Method | Description                        |
|----------------------------|--------|------------------------------------|
| `scripts/session_status.php` | GET  | Returns current session state      |
| `scripts/login.php`         | POST  | Authenticates user                 |
| `scripts/logout.php`        | GET   | Destroys session and cookie        |
| `scripts/register.php`      | POST  | Creates a new user account         |

### Games

| Endpoint                    | Method | Auth    | Description              |
|-----------------------------|--------|---------|--------------------------|
| `scripts/get_games.php`     | GET    | Public  | Returns all games        |
| `scripts/add_game.php`      | POST   | Admin   | Adds a new game          |
| `scripts/update_game.php`   | POST   | Admin   | Updates a game           |
| `scripts/delete_game.php`   | GET    | Admin   | Deletes a game by `?id=` |

### Cart

| Action    | Method | Description              |
|-----------|--------|--------------------------|
| `get`     | GET    | Fetch user's cart items  |
| `count`   | GET    | Get total quantity count |
| `add`     | POST   | Add item to cart         |
| `update`  | POST   | Update item quantity     |
| `remove`  | POST   | Remove single item       |
| `clear`   | POST   | Clear entire cart        |

### Orders

| Action          | Method | Auth  | Description                    |
|-----------------|--------|-------|--------------------------------|
| `my_orders`     | GET    | User  | Fetch current user's orders    |
| `all`           | GET    | Admin | Fetch all orders               |
| `detail`        | GET    | Both  | Fetch items for a single order |
| `checkout`      | POST   | User  | Place order from cart          |
| `update_status` | POST   | Admin | Change an order's status       |

### Profile & Users

| Endpoint                     | Action           | Description                    |
|------------------------------|------------------|--------------------------------|
| `scripts/profile.php`        | `get`            | Fetch profile, stats, orders   |
| `scripts/profile.php`        | `update`         | Update username / email        |
| `scripts/profile.php`        | `change_password`| Change password                |
| `scripts/upload_avatar.php`  | POST             | Upload profile picture         |
| `scripts/users.php`          | `list`           | List all users (admin)         |
| `scripts/users.php`          | `update_role`    | Change a user's role (admin)   |
| `scripts/users.php`          | `delete`         | Delete a user (admin)          |

---

## 🗃️ Database Schema

```
users
├── id (PK)
├── username (UNIQUE)
├── email (UNIQUE)
├── password (bcrypt hash)
├── role (admin | user)
├── avatar_path
└── created_at

games
├── id (PK)
├── title
├── genre
├── price
├── image_url
├── image_path
├── description
└── created_at

cart
├── id (PK)
├── user_id (FK → users)
├── game_id (FK → games)
├── quantity
└── added_at

orders
├── id (PK)
├── user_id (FK → users)
├── total_price
├── status (pending | processing | completed | cancelled)
└── created_at

order_items
├── id (PK)
├── order_id (FK → orders)
├── game_id (FK → games)
├── quantity
└── price
```

---

## 🔒 Security Highlights

- Passwords hashed with PHP's `password_hash()` (bcrypt)
- Session regeneration on login to prevent fixation attacks
- Signed "Remember Me" cookies — token includes a hash of the user's password so it's invalidated on password change
- Prepared statements (PDO) used throughout — no raw SQL concatenation
- Role-based access control enforced server-side on every protected endpoint
- XSS protection via `htmlspecialchars()` on all rendered output
- File uploads validated by MIME type and size limit (5 MB for games, 2 MB for avatars)

---

## 📝 License

This project is for educational purposes. Feel free to fork and extend it.

---

## 🙌 Acknowledgements

- [Bootstrap 5](https://getbootstrap.com/) — UI components
- [Google Fonts](https://fonts.google.com/) — Rajdhani & Inter typefaces
- [Steam](https://store.steampowered.com/) — Game cover images used for demonstration only
