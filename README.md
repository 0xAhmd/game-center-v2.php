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
│   ├── index.html                  # Store / game listing page
│   ├── game.html                   # Individual game detail page
│   ├── cart.html                   # Shopping cart
│   ├── orders.html                 # Order history (user & admin)
│   ├── admin.html                  # Admin dashboard
│   ├── admin_user_profile.html     # Admin: view any user's profile
│   ├── profile.html                # User profile
│   ├── add.html                    # Add new game (admin)
│   ├── login.html                  # Login page
│   └── register.html               # Registration page
│
├── JS/
│   ├── auth_ui.js                  # Navbar injection based on session state
│   ├── fetch_games.js              # Fetches and renders game cards
│   ├── scripts.js                  # Search, modal logic, toast notifications
│   ├── cart.js                     # Cart page logic
│   ├── orders.js                   # Orders page logic
│   ├── library.js                  # Library page logic
│   └── admin.js                    # Admin dashboard logic
│
├── features/                       # ⭐ Feature-based backend (see below)
│   ├── shared/
│   │   ├── db.php                  # PDO + MySQLi database connection
│   │   └── auth_helpers.php        # Session, auth guards, helper functions
│   │
│   ├── auth/
│   │   ├── login.php               # Login handler
│   │   ├── logout.php              # Logout + cookie clear
│   │   ├── register.php            # Registration handler
│   │   └── session_status.php      # Returns current session as JSON
│   │
│   ├── games/
│   │   ├── get_games.php           # Returns all games as JSON (public)
│   │   ├── add_game.php            # Add game with image upload (admin)
│   │   ├── update_game.php         # Update game details (admin)
│   │   └── delete_game.php         # Delete game by ID (admin)
│   │
│   ├── cart/
│   │   ├── cart.php                # Cart CRUD API (get/count/add/update/remove/clear)
│   │   └── get_cart_ids.php        # Returns game IDs in user's cart
│   │
│   ├── orders/
│   │   └── orders.php              # Orders API (my_orders/all/detail/checkout/update_status)
│   │
│   ├── profile/
│   │   ├── profile.php             # Profile view/update/change_password API
│   │   ├── upload_avatar.php       # Avatar upload handler
│   │   ├── get_library_ids.php     # Returns owned game IDs (for store badges)
│   │   ├── get_user_library.php    # Returns full library for library page
│   │   └── remove_from_library.php # Remove a game from library
│   │
│   └── admin/
│       ├── users.php               # User management API (list/update_role/delete)
│       └── user_profile.php        # Admin: fetch any user's profile data
│
├── css/
│   ├── styles.css                  # Global styles, navbar, cards, buttons
│   ├── store.css                   # Cart, orders, admin panel styles
│   ├── auth.css                    # Login & register page styles
│   ├── profile.css                 # Profile page styles
│   └── avatar-additions.css        # Avatar/navbar additions
│
├── uploads/                        # Uploaded game images and avatars
│   └── avatars/
│
├── assets/
│   ├── logo.png
│   └── profile.png
│
└── game_center.sql                 # Full database schema + seed data
```

---

## 🧩 Feature-Based Backend

The backend is organized by **feature**, not by type. Each folder under `features/` is self-contained and owned by one teammate.

| Folder | Responsibility | Who owns it |
|--------|---------------|-------------|
| `shared/` | DB connection & auth helpers — included by everyone | All |
| `auth/` | Login, logout, register, session | Teammate A |
| `games/` | Browse, add, edit, delete games | Teammate B |
| `cart/` | Shopping cart CRUD | Teammate C |
| `orders/` | Checkout & order history | Teammate D |
| `profile/` | User profile, avatar, library | Teammate E |
| `admin/` | User management & admin views | Teammate F |

### How shared/ works

Every PHP file in `features/` starts with the same two lines:

```php
require_once '../shared/db.php';         // gives you $pdo and $conn
require_once '../shared/auth_helpers.php'; // gives you is_logged_in(), is_admin(), etc.
```

### Available helper functions (`auth_helpers.php`)

| Function | What it does |
|----------|-------------|
| `auto_login_from_cookie($pdo)` | Restores session from "Remember Me" cookie |
| `is_logged_in()` | Returns `true` if user has an active session |
| `is_admin()` | Returns `true` if logged-in user is an admin |
| `require_login()` | Redirects to login page if not authenticated |
| `require_admin()` | Redirects to login page if not admin |
| `json_ok($data)` | Outputs `{"success":true, ...}` and exits |
| `json_error($msg, $code)` | Outputs `{"error":"..."}` with HTTP status and exits |

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

3. **Configure the database connection** in `features/shared/db.php`:
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

> ⚠️ **Change default credentials before any public deployment.**

---

## 📡 API Reference

All backend endpoints live under `features/` and return `application/json`.

### Auth (`features/auth/`)

| Endpoint | Method | Description |
|----------|--------|-------------|
| `session_status.php` | GET | Returns current session state |
| `login.php` | POST | Authenticates user, sets session + optional cookie |
| `logout.php` | GET | Destroys session and clears cookie |
| `register.php` | POST | Creates a new user account |

### Games (`features/games/`)

| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| `get_games.php` | GET | Public | Returns all games as JSON |
| `add_game.php` | POST | Admin | Adds a new game (supports image upload) |
| `update_game.php` | POST | Admin | Updates a game |
| `delete_game.php` | GET | Admin | Deletes a game by `?id=` |

### Cart (`features/cart/`)

| Endpoint | Action | Method | Description |
|----------|--------|--------|-------------|
| `cart.php` | `get` | GET | Fetch user's cart items |
| `cart.php` | `count` | GET | Get total quantity count |
| `cart.php` | `add` | POST | Add item to cart |
| `cart.php` | `update` | POST | Update item quantity |
| `cart.php` | `remove` | POST | Remove single item |
| `cart.php` | `clear` | POST | Clear entire cart |
| `get_cart_ids.php` | — | GET | Returns array of game IDs in cart |

### Orders (`features/orders/`)

| Endpoint | Action | Auth | Description |
|----------|--------|------|-------------|
| `orders.php` | `my_orders` | User | Fetch current user's orders |
| `orders.php` | `all` | Admin | Fetch all orders |
| `orders.php` | `detail` | Both | Fetch items for a single order |
| `orders.php` | `checkout` | User | Place order from cart |
| `orders.php` | `update_status` | Admin | Change an order's status |

> When an order is marked **completed**, games are automatically added to the user's library.

### Profile (`features/profile/`)

| Endpoint | Action | Description |
|----------|--------|-------------|
| `profile.php` | `get` | Fetch profile, stats, recent orders |
| `profile.php` | `update` | Update username / email |
| `profile.php` | `change_password` | Change password |
| `upload_avatar.php` | POST | Upload profile picture |
| `get_library_ids.php` | GET | Returns owned game IDs |
| `get_user_library.php` | GET | Returns full library list |
| `remove_from_library.php` | POST | Remove a game from library |

### Admin (`features/admin/`)

| Endpoint | Action | Description |
|----------|--------|-------------|
| `users.php` | `list` | List all users |
| `users.php` | `update_role` | Change a user's role |
| `users.php` | `delete` | Delete a user |
| `user_profile.php` | GET `?user_id=` | Fetch any user's profile data |

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

user_library
├── id (PK)
├── user_id (FK → users)
├── game_id (FK → games)
└── purchase_date
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
