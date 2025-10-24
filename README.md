# Vending Machine System - Native PHP MVC Application

## Project Overview

This is a comprehensive PHP vending machine system. The application demonstrates PHP including MVC architecture, authentication, API development, testing and security best practices.

## Features

- **Product Management**: Complete CRUD operations for products
- **User Authentication**: Session-based authentication with role management
- **Role-based Access Control**: Admin and User roles with different permissions
- **Inventory Management**: Real-time stock tracking and updates
- **Purchase System**: Complete transaction processing with validation
- **RESTful API**: Full API with JWT authentication
- **Unit Testing**: Comprehensive test suite using PHPUnit with mocking capabilities
- **Form Validation**: Both server-side and client-side validation
- **Pagination & Sorting**: Advanced data presentation features

## Architecture

### MVC Pattern Implementation
```
├── controllers/          # Business logic controllers
├── models/              # Data models and database interactions
├── views/               # User interface templates
├── core/                # Framework components
├── routes/              # Route definitions
└── public/              # Web server entry point
```

### Key Components
- **Database**: Singleton pattern with PDO for secure database access
- **Router**: Custom routing system with role-based access control
- **Validation**: Comprehensive input validation and sanitization
- **JWT**: Custom JWT implementation for API authentication
- **Testing**: PHPUnit testing with mocking capabilities

## Database Schema

### Products Table
```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity_available INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Transactions Table
```sql
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

### Installation Steps

1. **Clone/Download the project**
   ```bash
   # Navigate to your web server directory
   cd /var/www/work/vending-machine-system
   ```

2. **Database Setup**
   ```bash
   # Create database and import schema
   mysql -u root -p < database_vending_machine.sql
   ```

3. **Configure Database Connection**
   Edit `core/Database.php` with your database credentials:
   ```php
   private $host = 'localhost';
   private $db   = 'vending_machine';
   private $user = 'your_username';
   private $pass = 'your_password';
   ```

4. **Run the Application**
   
   **Option A: PHP Built-in Server**
   ```bash
   php -S localhost:8000 -t public/
   ```
   
   **Option B: Web Server**
   - Point your web server document root to the `public/` directory
   - Configure virtual host if needed

5. **Access the Application**
   - Open http://localhost:8000 in your browser
   - Register a new account or use existing credentials

## User Roles & Permissions

### Admin Users
- Manage products (Create, Read, Update, Delete)
- View all products with pagination and sorting
- Access admin panel at `/products`
- Full system access

### Regular Users
-  View available products
-  Purchase products
-  Access vending machine at `/vending`
-  Cannot manage products

## Authentication System

### Web Authentication
- **Session-based**: PHP sessions with secure session handling
- **Password Security**: `password_hash()` and `password_verify()`
- **Role Management**: Admin and User roles with different access levels

### API Authentication
- **JWT Tokens**: Custom JWT implementation
- **Token Expiration**: 7-day token lifetime
- **Bearer Authentication**: `Authorization: Bearer <token>` header

## API Endpoints

### Authentication
- `POST /api/auth/login` - Get JWT token

### Products
- `GET /api/products` - List all products (public)
- `GET /api/products/{id}` - Get specific product (public)
- `POST /api/products` - Create product (admin only)
- `PUT /api/products/{id}` - Update product (admin only)
- `DELETE /api/products/{id}` - Delete product (admin only)

### Purchases
- `POST /api/purchase` - Purchase product (authenticated users)

### Example API Usage and Sample Data

#### User Authentication
```json
// Request: POST /api/auth/login
{
  "username": "testuser",
  "password": "password"
}

// Response:
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": 1,
    "username": "testuser",
    "role": "admin"
  }
}
```

#### List All Products (Public)
```json
// Request: GET /api/products
// Optional parameters: ?page=1&limit=10&sort=name&order=asc

// Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Coke",
      "price": "3.99",
      "quantity_available": 10,
      "created_at": "2025-10-23 18:47:25",
      "updated_at": "2025-10-24 08:22:36"
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 5,
    "pages": 1
  }
}
```

#### Get Single Product
```json
// Request: GET /api/products/1

// Response:
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Coke",
    "price": "3.99",
    "quantity_available": 10,
    "created_at": "2025-10-23 18:47:25",
    "updated_at": "2025-10-24 08:22:36"
  }
}
```

#### Create Product (Admin only)
```json
// Request: POST /api/products
// Headers: Authorization: Bearer YOUR_JWT_TOKEN

{
  "name": "New Product",
  "price": 12.99,
  "quantity_available": 50
}

// Response:
{
  "success": true,
  "message": "Product created successfully"
}
```

#### Update Product (Admin only)
```json
// Request: PUT /api/products/1
// Headers: Authorization: Bearer YOUR_JWT_TOKEN

{
  "name": "Updated Product Name",
  "price": 15.99,
  "quantity_available": 25
}

// Response:
{
  "success": true,
  "message": "Product updated successfully"
}
```

#### Delete Product (Admin only)
```json
// Request: DELETE /api/products/1
// Headers: Authorization: Bearer YOUR_JWT_TOKEN

// Response:
{
  "success": true,
  "message": "Product deleted successfully"
}
```

#### Purchase Product (Authenticated users)
```json
// Request: POST /api/purchase
// Headers: Authorization: Bearer YOUR_JWT_TOKEN

{
  "product_id": 1,
  "quantity": 2
}

// Response:
{
  "success": true,
  "message": "Purchase completed successfully",
  "data": {
    "product": {
      "id": 1,
      "name": "Coke",
      "price": "3.99",
      "quantity_available": 8,
      "created_at": "2025-10-23 18:47:25",
      "updated_at": "2025-10-24 10:08:00"
    },
    "quantity": 2,
    "total_price": 7.98
  }
}
```


## Testing

### Running Tests
```bash
# Install dependencies first
composer install

# Run the complete test suite with PHPUnit
./vendor/bin/phpunit tests/ProductsControllerTest.php
```

### Test Coverage
- **Dependency Injection**: Controller testing with mocked dependencies
- **Validation**: Input validation testing
- **Authentication**: Role-based access testing
- **Mock Objects**: PHPUnit mock framework for isolated testing
- **Edge Cases**: Error handling and boundary condition testing

## Project Structure

```
VENDING-MACHINE-SYSTEM/
 
├── controllers/
│   ├── api/
│   │   └── ProductController.php      # RESTful API controller
│   ├── AuthController.php             # Authentication controller
│   └── ProductsController.php         # Main products controller
├── core/
│   ├── auth/
│   │   └── JWT.php                    # JWT authentication
│   ├── Database.php                   # Database connection
│   ├── Router.php                     # Custom router
│   └── utils/
│       └── Validation.php             # Input validation
├── models/
│   ├── Product.php                     # Product model
│   ├── Transaction.php                 # Transaction model
│   └── VendingUser.php                # User model
├── public/
│   └── index.php                      # Application entry point
├── routes/
│   ├── api.php                        # API routes
│   └── web.php                        # Web routes
├── tests/
│   └── ProductsControllerTest.php      # PHPUnit test suite
├── views/
│   ├── auth/
│   │   ├── login.php                  # Login page
│   │   └── register.php               # Registration page
│   ├── products/
│   │   ├── create.php                 # Create product form
│   │   ├── edit.php                   # Edit product form
│   │   └── index.php                  # Products listing
│   └── vending/
│       └── index.php                  # Vending machine interface
├── database_vending_machine.sql       # Database schema
├── composer.json                      # Project dependencies including PHPUnit
├── phpunit.xml                        # PHPUnit configuration
├── run_tests.php                      # Test runner
└── README.md                          # This documentation
```

## Configuration

### Database Configuration
Update `core/Database.php` with your database settings:
```php
private $host = 'localhost';
private $db   = 'vending_machine';
private $user = 'your_username';
private $pass = 'your_password';
```

### JWT Configuration
Update `core/auth/JWT.php` with your secret key:
```php
private static $secretKey = 'your-secret-key-here';
```

## Security Features

- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: Input sanitization and output escaping
- **Password Security**: PHP's built-in password hashing
- **Session Security**: Secure session handling
- **Input Validation**: Server-side and client-side validation
- **JWT Security**: Secure token generation and validation

## Performance Features

- **Database Optimization**: Singleton pattern for connection reuse
- **Pagination**: Efficient data loading for large datasets
- **Caching**: Session-based caching for user data

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify database credentials in `core/Database.php`
   - Ensure MySQL service is running
   - Check database exists and tables are created

2. **404 Errors**
   - Ensure web server document root points to `public/` directory
   - Check `.htaccess` file if using Apache
   - Verify PHP built-in server is running from correct directory

3. **Session Issues**
   - Check PHP session configuration
   - Ensure session directory is writable
   - Verify session_start() is called

4. **API Authentication Issues**
   - Verify JWT secret key is consistent
   - Check token expiration
   - Ensure proper Authorization header format
   

## Deployment

### Production Considerations
1. **Environment Variables**: Move sensitive data to environment variables
3. **HTTPS**: Use SSL certificates for production
4. **Database Security**: Use dedicated database users with minimal privileges
5. **File Permissions**: Set appropriate file permissions


## Developer Information

**Implementation Highlights:**
- Pure PHP implementation with minimal external dependencies (only PHPUnit for testing)
- Professional MVC architecture with clean separation of concerns
- Comprehensive security implementation
- RESTful API design with proper HTTP methods
- Advanced testing with PHPUnit and mocking
- Advanced features like pagination, sorting, and validation

**Technical Skills Demonstrated:**
- Advanced PHP programming
- Database design and optimization
- Security best practices
- PHPUnit testing with mocking
- API design and development
- Dependency injection and testing
- Clean code architecture

---
