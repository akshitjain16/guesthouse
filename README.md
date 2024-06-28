# Guest House Management System

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Database Schema](#database-schema)
- [Usage](#usage)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [License](#license)

## Introduction

The Guest House Management System is a web application designed to streamline the management of guesthouses. It includes features for booking meals, managing users, and handling different guesthouses with admin and employee roles.

## Features

- **User Authentication**: Login and registration system.
- **Admin Panel**: Manage users, bookings, menus, and search for users by employee code.
- **Meal Booking**: Users can book meals and view their booking history and monthly charges.
- **Role-Based Access Control**: Different views and access levels for admins and employees.
- **Guesthouse Management**: Admins can only access data related to their specific guesthouse - Edit user details and Soft delete user accounts.

## Technologies Used

- **Frontend**: HTML, CSS, Bootstrap, JavaScript
- **Backend**: PHP
- **Database**: MySQL

## Installation

### Prerequisites

- PHP >= 7.4
- MySQL
- Apache or any other web server

### Steps

1. Clone the repository:
   ```sh
   git clone https://github.com/akshitjain16/guesthouse
   cd guesthouse

2. Configure the database:
   Import the guest_house.sql file into your MySQL database.
   Update the database configuration in config/config.php with your database credentials.

3. Start the web server:
   If using XAMPP, place the project folder in the htdocs directory.
   Start Apache and MySQL from the XAMPP control panel.
   Access the application at http://localhost/guesthouse/.

## Database Schema

### Tables

**users:**
- emp_id (INT, PRIMARY KEY)
- name (VARCHAR)
- email (VARCHAR)
- phone_number (VARCHAR)
- password (VARCHAR)
- role (ENUM: 'admin', 'employee')
- guesthouse_id (INT, FOREIGN KEY)

**guesthouses:**
- id (INT, PRIMARY KEY)
- name (VARCHAR)
- admin_id (INT, FOREIGN KEY)

**meal_bookings:**
- id (INT, PRIMARY KEY)
- user_id (INT, FOREIGN KEY)
- guesthouse_id (INT, FOREIGN KEY)
- meal_date (DATE)
- meal_type (ENUM)
- price (DECIMAL)

**menus:**
- id (INT, PRIMARY KEY)
- meal_type (ENUM)
- day (ENUM)
- price (DECIMAL)

## Usage

### User Roles

- **Admin**: Can manage users, view and manage bookings, and manage menus.
- **Employee**: Can book meals and view their booking history.

### Booking Meals

1. Login as an employee.
2. Navigate to the "Book Meals" page.
3. Select the meal type and date.
4. Click "Book" to confirm the booking.

### Managing Users (Admin)

1. Login as an admin.
2. Navigate to the "Manage Users" page.
3. Add, edit, or delete users as needed.

### Viewing Bookings (Admin)

1. Login as an admin.
2. Navigate to the "View Bookings" page.
3. Select the guesthouse to view bookings and see a list of users, their details, and monthly charges..
4. Search for users by entering their employee code in the search bar.

## Screenshots


## Contributing

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Commit your changes (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature-branch`).
5. Open a Pull Request.

## File Structure

- `config/`: Contains configuration files.
- `src/php/`: Includes reusable PHP components like header, footer, and navbar.
- `public/`: Publicly accessible files including HTML, CSS, and JavaScript files.
- `index.php`: The main entry point for the application.
