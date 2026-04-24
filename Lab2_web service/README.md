# Posts Management API — Pure PHP (No Framework)

## Project Structure
```
posts-api/
├── public/
│   └── index.php          # Single entry point (Front Controller)
├── src/
│   ├── Helpers/
│   │   ├── JWT.php        # JWT encode/decode logic
│   │   └── Response.php   # Standardized JSON responses
│   ├── Middleware/
│   │   └── AuthMiddleware.php  # Protects private routes
│   ├── Models/
│   │   ├── Database.php   # PDO connection (singleton)
│   │   └── Post.php       # Post CRUD operations
│   └── Controllers/
│       ├── AuthController.php  # Login → returns JWT
│       └── PostController.php  # CRUD for posts
├── config/
│   └── config.php         # DB creds, JWT secret
└── database.sql           # Schema + seed data
```

## Setup
1. Import `database.sql` into MySQL
2. Update `config/config.php` with your DB credentials
3. Point your web server root to `public/`
4. Use `http://localhost/api/...`
