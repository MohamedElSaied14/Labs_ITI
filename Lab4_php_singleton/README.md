# Tesst – Registration System (Fixed)

## What changed from the original

| Original                        | Fixed                                      |
|---------------------------------|--------------------------------------------|
| Flat `data.txt` storage         | MySQL database via PDO                     |
| Plain-text passwords            | `password_hash()` bcrypt                   |
| No input validation             | Full server-side validation on every field |
| SQL / injection via file paths  | PDO prepared statements everywhere         |
| XSS vulnerabilities             | `htmlspecialchars()` on all output         |
| No CAPTCHA validation           | CAPTCHA checked server-side                |
| Integer IDs as file line offsets| Auto-increment primary keys in DB          |
| Missing fields (address, etc.)  | All wireframe fields implemented           |

## Setup

### 1. Create the database
```bash
mysql -u root -p < database.sql
```

### 2. Edit credentials
Open `config.php` and set:
```php
define('DB_USER', 'your_mysql_user');
define('DB_PASS', 'your_mysql_password');
```

### 3. Place files on your server
Copy all `.php` files to your web root (e.g. `htdocs/Tesst/`).

### 4. Open in browser
```
http://localhost/Tesst/login.php   ← Registration form
http://localhost/Tesst/view.php    ← View all users
```

## Files
| File           | Purpose                        |
|----------------|--------------------------------|
| `config.php`   | PDO connection + constants     |
| `database.sql` | Run once to create the table   |
| `login.php`    | Registration form (main page)  |
| `view.php`     | List all registered users      |
| `details.php`  | View one user's full details   |
| `edit.php`     | Edit an existing user          |
| `remove.php`   | Delete a user                  |
| `done.php`     | Redirect shim (backward compat)|
| `update.php`   | Redirect shim (backward compat)|
