
🛒 Click N Cart

A simple e-commerce project built for COSC-471 using HTML, CSS, PHP, and MySQL.

## 👥 Authors
- Johney Domas
- David Dooley
- Deep Patel
- Nawal Chishty
- DuJuan Owens

## 📦 Prerequisites
- PHP installed
- MySQL (or MariaDB) installed
- Command-line access

### Installation notes
- macOS/Linux: Use Homebrew to install PHP and MySQL
brew install php mysql
- Windows: Install terminal versions of PHP and MySQL, or use XAMPP (includes PHP and MySQL in one package)

## ⚙️ Database setup
1. Start MySQL service:
brew services start mysql
2. Log in to MySQL:
mysql -u root -p
3. Create database and user:

```sql
CREATE DATABASE myDB;
CREATE USER 'dj'@'localhost' IDENTIFIED BY 'pcplayer';
GRANT ALL PRIVILEGES ON myDB.* TO 'dj'@'localhost';
FLUSH PRIVILEGES;
USE myDB;
```
Create tables:
```
CREATE TABLE users (
    username VARCHAR(25) NOT NULL PRIMARY KEY,
    password VARCHAR(25)
);

CREATE TABLE items (
    sku INT NOT NULL PRIMARY KEY,
    name VARCHAR(25),
    description VARCHAR(250),
    category VARCHAR(25),
    quantity INT,
    price INT
);

CREATE TABLE `order` (
    order_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(25) NOT NULL,
    order_date DATETIME NOT NULL,
    total_items INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (username) REFERENCES users(username)
);

CREATE TABLE order_item (
    order_item_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    sku INT NOT NULL,
    quantity INT NOT NULL,
    item_price DECIMAL(10,2) NOT NULL,
    item_total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES `order`(order_id),
    FOREIGN KEY (sku) REFERENCES items(sku)
);
```
Insert sample data:
```
INSERT INTO users (username, password) VALUES 
('admin', 'emudata'),
('alice', 'mypassword');

INSERT INTO items (sku, name, description, category, quantity, price) VALUES
(101, 'Widget', 'A basic widget', 'Tools', 50, 10),
(102, 'Gadget', 'Useful gadget', 'Electronics', 30, 20),
(103, 'Thingamajig', 'Multi-purpose tool', 'Misc', 15, 25);

```
🔌 Database connection (PHP)
```
<?php
$servername = "localhost";
$username = "dj";
$password = "pcplayer";
$dbname = "myDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// echo "Connected successfully";
?>
```

🚀 Running the project
Inside the COSC-471 project folder, start the PHP server:
`php -S localhost:8000`
Open http://localhost:8000 in your browser.👥 Authors - Johney Domas - David 
