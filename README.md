# TeamHub - Collaborative Project Tracker
## Quick Setup & Installation Guide

## 🚀 One-Minute Quick Start

### Prerequisites
- PHP 7.4+ (`php --version`)
- MySQL 5.7+ (`mysql --version`)
- Composer (`composer --version`)

### Installation (Windows/Linux/Mac)

```bash
# 1. Download and extract project
# If using git:
git clone https://github.com/yourusername/teamhub.git
cd teamhub

# OR if you have the ZIP file:
# Extract to your web server directory

# 2. Install dependencies
composer install

# 3. Copy environment file
cp env .env

# 4. Edit .env with your database credentials
# Open .env in a text editor and update:
# database.default.database = teamhub
# database.default.username = root
# database.default.password = yourpassword

# 5. Create database
mysql -u root -p -e "CREATE DATABASE teamhub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Run migrations
php spark migrate

# 7. Start development server
php -S localhost:8080 -t public/

# 8. Open browser and go to:
# http://localhost:8080
```
## 🐳 Docker Installation (Even Easier)

```bash
# 1. Clone repository
git clone https://github.com/yourusername/teamhub.git
cd teamhub

# 2. Start with Docker Compose
docker-compose up -d

# 3. Access at:
# http://localhost:8080
```

## 🌐 Web Server Setup

### For Apache Users:
1. Copy project to `htdocs` or web directory
2. Enable mod_rewrite:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### For Nginx Users:
Add this to your site configuration:
```nginx
location / {
    try_files $uri $uri/ /index.php$is_args$args;
}
```

## ✅ Verify Installation

After installation, test these URLs:
- **Home/Login**: http://localhost:8080/
- **Register**: http://localhost:8080/register
- **Dashboard**: http://localhost:8080/dashboard (after login)

## 🔧 Troubleshooting Common Issues

### 1. "404 Not Found"
```bash
# Use PHP built-in server instead
php -S localhost:8080 -t public/
```

### 2. Database Connection Error
```bash
# Check if MySQL is running
sudo systemctl status mysql

# Or start it
sudo systemctl start mysql
```

### 3. Permission Issues
```bash
# Set write permissions
chmod -R 755 writable/
```

### 4. Composer Issues
```bash
# Update composer
composer self-update
composer install --no-dev
```

## 🎯 Quick Test

```bash
# Test API with curl
curl -X POST http://localhost:8080/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"test123"}'
```

## 📞 Need Help?

1. Check the `writable/logs/` directory for error logs
2. Ensure all prerequisites are installed
3. Verify database credentials in `.env` file

## 🏁 Getting Started

1. **Register** a new account at http://localhost:8080/register
2. **Login** with your credentials
3. **Create** your first team
4. **Invite** team members
5. **Start** adding projects and tasks!
