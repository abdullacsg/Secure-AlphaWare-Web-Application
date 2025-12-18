# Secure AlphaWare
![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)
![Security](https://img.shields.io/badge/OWASP-Top%2010%20Aligned-green.svg)
![License](https://img.shields.io/badge/License-MIT-yellow.svg)
## Project Overview
AlphaWare is a simple e‑commerce system originally developed in PHP, MySQLi, HTML, JavaScript, and Bootstrap. It provides a lightweight online store with two main interfaces: a customer‑facing storefront and an administrative backend. 
It was first published on [SourceCodester](https://www.sourcecodester.com/php/11676/alphaware-simple-e-commerce-system.html) in March 2021 as an open‑source learning project.
On the customer side, visitors can register accounts, log in, browse products, add items to their cart, and complete purchases through PayPal integration.
On the admin side, administrators can manage products (CRUD), view transactions, track orders, access customer details, and handle client messages.
The application was designed to make online transactions faster, easier, and more convenient for customers while giving administrators a straightforward way to manage store operations.

### ⚠️ Security Note 
The original AlphaWare release (2021) was functional but contained multiple vulnerabilities, including SQL injection, XSS, CSRF, weak session handling, and Authentication Weaknesses. These issues made the system unsuitable for production use without significant refactoring.
This repository documents the security improvements, refactoring, and migration work performed to align AlphaWare with modern best practices and the OWASP Top 10 standards. The goal is to transform AlphaWare into a more secure, maintainable, and educational resource for developers learning PHP and web application security.

---
## Table of Contents
* [Project Overview](#project-overview)
* [Quick Start Guide](#Quick-start)
* [Features](#features)
* [Security Findings](#security-findings)
  1. [SQL Injection](#1-sql-injection)
  2. [Cross-Site Scripting (XSS)](#2-cross-site-scripting-xss)
  3. [Cross-Site Request Forgery (CSRF)](#3-cross-site-request-forgery-csrf)
  4. [Plaintext Password Storage](#4-plaintext-password-storage)
  5. [Weak Password Rules](#5-weak-password-rules)
  6. [Brute Force](#6-brute-force)
  7. [Session Management](#7-session-management)
  8. [Vulnerable and Outdated Components](#8-vulnerable-and-outdated-components)
* [OWASP Top 10 Analysis](#owasp-top-10-analysis)
* [References](#references)

---

## Quick Start
> [!TIP]
> **Recommendation:** For security and system stability, it is highly recommended to run this application within a **Virtual Machine** (such as VirtualBox or VMware) or a isolated lab environment rather than your primary operating system.

Follow these steps to set up the environment on **Linux** using **XAMPP**.
### 1. Install Prerequisites
First, ensure your system is up to date and install Git:
```bash
sudo apt update && sudo apt install git
```
### 2. Install XAMPP 
Download the installer from the Official [XAMPP](https://www.apachefriends.org/) Website.
Navigate to your Downloads directory:
```bash
cd ~/Downloads
```
Grant execute permissions to the installer:
```bash
sudo chmod +x xampp-linux-x64-8.2.12-0-installer.run
```
Run the installer:

> [!Note]
> In the components setup, you can uncheck "XAMPP Developer Files" to save disk space.
```bash
sudo ./xampp-linux-x64-*-installer.run
```

### 3. Deploy the Web Application
Navigate to the XAMPP web root:
```bash
cd /opt/lampp/htdocs/
```
Clone the Secure AlphaWare:
```bash
sudo git clone https://github.com/abdullacsg/Secure-AlphaWare-Web-Application.git
```
Set directory permissions and ownership:
```bash
sudo chmod -R 755 Secure-AlphaWare-Web-Application/
```
```bash
sudo chown -R daemon:daemon Secure-AlphaWare-Web-Application/
```
### 4. Database Configuration
Start XAMPP services:
```bash
sudo /opt/lampp/lampp start
```
Create the Database:
```bash
/opt/lampp/bin/mysql -u root
```
```SQL
CREATE DATABASE alphaware;
EXIT;
```
Navigate to Alphaware Database directory:
```bash
cd Secure-AlphaWare-Web-Application/db
```
Import the SQL Schema:
```bash
/opt/lampp/bin/mysql -u root alphaware < alphaware.sql
```
### 5. Access the Application


Open your web browser and navigate to: [http://localhost/Secure-AlphaWare-Web-Application/](http://localhost/Secure-AlphaWare-Web-Application/)

---
## Features
AlphaWare provides two main interfaces: a Customer Side and an Admin Panel.

### Customer Side
- Registration & Login Form
- Product Browsing
- Add to Cart Functions
- PayPal Purchase Method

### Admin Panel
- Product Management (CRUD)
- Transaction Details
- Customer Details
- Order Details
- Message Info


---
## Security Findings
During the refactoring and migration of AlphaWare, several vulnerabilities were identified in the original source code. Below is a summary of the issues and the improvements that have been made.

### 1. SQL Injection
- Issue: Legacy queries concatenated user input directly into SQL statements without sanitization or parameter binding. This opened the door to multiple SQL injection attacks across different modules.
  * Login Bypass: by using this payload (' OR '1'='1)
  * Cart Price Manipulation: Attackers could modify the query to change the total cost of their order, effectively purchasing items for free or at reduced prices.
  * Transaction Hijacking: By guessing or enumerating transaction IDs (tid), attackers could view other customers’ transactions and order data.
  * Time-Based SQL Injection: Many inputs (email, product ID pid, transaction ID tid, first name, last name, and others) were vulnerable to time-based SQL injection. Attackers could use payloads like SLEEP(5) to confirm injection points and extract data slowly.
  * Product ID Manipulation: Product pages accepted raw IDs in queries. Attackers could change the product ID to access unreleased or hidden products not meant for public view.
- Fix: Refactored to use prepared statements with parameter binding (mysqli_stmt_bind_param).

Before (vulnerable):
```php
$result = mysqli_query($conn, "SELECT * FROM customer WHERE email = '" . $_POST['email'] . "'");
```

After (secure):
```php
$stmt = $conn->prepare("SELECT * FROM customer WHERE eamil = ?");
$stmt->bind_param("s", $_POST['email']);
$stmt->execute();
```

### 2. Cross-Site Scripting (XSS)
- Issue:
  * User Input: Forms (messages, registration) accepted raw HTML/JavaScript without sanitization.
  * Web Output: Data retrieved from the database was echoed directly into pages without escaping, allowing stored XSS attacks.
- Fix:
  * Input Validation: Sanitized and validated user input before saving to the database.
  * Output Escaping: Escaped all dynamic content before rendering in the browser.

Before (vulnerable input):
```php
$message = $_POST['message'];
mysqli_query($conn, "INSERT INTO messages (content) VALUES ('$message')");
```

After (secure input):
```php
$message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');
$stmt = $conn->prepare("INSERT INTO messages (content) VALUES (?)");
$stmt->bind_param("s", $message);
$stmt->execute();
```

Before (vulnerable output):
```php
echo $row['content'];
```

After (secure output):
```php
echo htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8');
```

### 3. Cross-Site Request Forgery (CSRF)
- Issue: Forms lacked CSRF tokens, allowing forged requests.
- Fix: Added token generation and validation in all sensitive forms.

Implementation Example:
```php
// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
```
```php
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
```

### 4. Plaintext Password Storage
- Issue: Both admin and client passwords were stored in the database without hashing.
This meant that if the database was compromised, all credentials were immediately exposed.
- Fix: Migrated to PHP’s built‑in password_hash() and password_verify() functions.
Implementation Example:
```php
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
```
```php
 if ($row && password_verify($password, $row['password'])) {
        session_regenerate_id(true);
```

### 5. Weak Password Rules
- Issue: No enforcement of complexity (length, uppercase/lowercase, numbers, symbols).
- Fix: Password Policy Enforcement 
Implementation Example:
```php
 // Strong password rules
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#_]).{8,}$/';
    if (!preg_match($pattern, $password)) {
        echo "<script>alert('Password must be at least 8 characters and include uppercase, lowercase, number, and special character.'); window.location.href = 'index.php';</script>";
        exit();
    }
```


### 6. Brute force 
- Issue: Login forms had no rate limiting or lockout mechanism. Therefore, Bots could attempt thousands of passwords automatically.
- Fix: Integrated Google reCAPTCHA to block automated login attempts.

reCAPTCHA check
```php
 $captcha = $_POST['g-recaptcha-response'] ?? '';
    $secretKey = "secretkey";	
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha&remoteip=".$_SERVER['REMOTE_ADDR']);
    $responseKeys = json_decode($response, true);
    if (empty($responseKeys['success'])) {
        echo "<script>alert('reCaptcha verification failed'); window.location.href = 'index.php';</script>";
        exit();
    }
```

Added to the login, admin login, and signup fields:
```html
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<input type="email" name="email" placeholder="Email" style="width:250px;" required>
<input type="password" name="password" placeholder="Password" style="width:250px;" required>
<div class="g-recaptcha" data-sitekey="sitekey"></div>
```

### 7. Session Management
- Issue: The original session check only verified if $_SESSION['id'], with no validation against the database or role check (any user with a session ID could access admin pages).
- Fix: validates the session ID against the database and ensures it belongs to an admin.

Before (vulnerable):
```php
if(!ISSET($_SESSION['id'])) {
    echo "<script>window.location = 'index.php';</script>";
}
```

After (secure):
```php
// Check if user session is set
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit(); 
}
```
```php
// Verify that this ID belongs to an admin
$stmt = $conn->prepare("SELECT adminid FROM admin WHERE adminid = ?");
$stmt->bind_param("i", $_SESSION['id']);
```

### 8. Vulnerable and Outdated Components
- Issue: AlphaWare originally relied on an old version of jQuery (1.7.2) and multiple separate Bootstrap JS files.
- Fix: Migrated to jQuery 3.6.0 with jQuery Migrate 3.3.2 and consolidated Bootstrap into a single modern build.

Before (vulnerable):
```php
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/carousel.js"></script>
<script src="js/button.js"></script>
<!-- many separate Bootstrap components -->
<script src="js/bootstrap.min.js"></script>
```

After (secure):
```php
<!-- Latest jQuery -->
<script src="js/jquery-3.6.0.min.js"></script>
<script src="js/jquery-migrate-3.3.2.min.js"></script>
<script src="js/bootstrap.min.js"></script> 
```

---
## OWASP Top 10 Analysis
### A01: Broken Access Control
- Transaction Hijacking: Attackers could access other users’ transactions by manipulating IDs.
- Product ID Manipulation: Unreleased or hidden products could be viewed by changing the product ID.

### A02: Cryptographic Failures
- Plaintext Password Storage: Admin and client passwords stored without hashing.
- Weak Password Rules: accept weak passwords like 12345.

### A03: Injection
- Login Bypass: ' OR '1'='1 allowed attackers to bypass authentication.
- Cart Price Manipulation: Unsanitized queries let attackers change the total price.
- Time‑Based SQL Injection: Inputs vulnerable to payloads like SLEEP(10).

### A04: Insecure Design
- No CSRF Protection: Forms lacked CSRF tokens, allowing forged requests.
- Weak Session Logic: Original session checks only verified $_SESSION['id'] without role validation.

### A05: Security Misconfiguration
- Error Disclosure: Raw SQL errors exposed sensitive details.

### A06: Vulnerable and Outdated Components
- jQuery 1.7.2 (2012) with known XSS/prototype pollution flaws
- Multiple redundant Bootstrap JS files

### A07: Identification & Authentication Failures
- Login Bypass (SQL Injection): Directly undermined authentication.
- Brute Force Attacks: No rate limiting or bot protection.

### A08: Software and Data Integrity Failures
- Unvalidated PayPal Responses: Relied solely on PayPal’s return without verifying transaction integrity.

---
## References
- [OWASP Top 10 (2021)](https://owasp.org/Top10/2021/)
- [SourceCodester](https://www.sourcecodester.com/php/11676/alphaware-simple-e-commerce-system.html)
- PHP Security Best Practices
- Apache & MariaDB Hardening Guides














