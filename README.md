# GuestHouse Management System

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Database Schema](#database-schema)
- [Usage](#usage)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [File Structure](#file-structure)


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
   You can  download this [guest_house.csv](https://github.com/user-attachments/files/16362196/guest_house.csv) and import this schema file into your phpmyadmin database.
  
   
### Tables

**users:**
- emp_id (INT, PRIMARY KEY)
- name (VARCHAR)
- email (VARCHAR)
- phone_number (VARCHAR)
- password (VARCHAR)
- role (ENUM: 'admin', 'employee')
- guesthouse_id (INT, FOREIGN KEY)
- Department_name (VARCHAR)
- status (ENUM: 'true', 'false')

**guesthouses:**
- id (INT, PRIMARY KEY)
- name (VARCHAR)
- guesthouse_id (INT, FOREIGN KEY)

**meal_bookings:**
- id (INT, PRIMARY KEY)
- user_id (INT, FOREIGN KEY)
- guesthouse_id (INT, FOREIGN KEY)
- meal_date (DATE)
- meal_type (ENUM)
- price (DECIMAL)

**menus:**
- id (INT, PRIMARY KEY)
- guesthouse_id (INT, FOREIGN KEY)
- meal_type (ENUM)
- weekday (ENUM)
- item_name (VARCHAR)
- price (DECIMAL)

## Usage

### User Roles

- **Admin**: Can manage users, view and manage bookings, and manage menus.
- **Employee**: Can book meals, view their booking history and monthly charges.


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

![screencapture-localhost-guesthouse-src-php-home-php-2024-06-30-13_32_21](https://github.com/akshitjain16/guesthouse/assets/113924385/84f46857-beee-48a8-a46d-a9742cadc39a)
![screencapture-localhost-guesthouse-src-php-adminPanel-php-2024-06-30-13_26_03](https://github.com/akshitjain16/guesthouse/assets/113924385/a7dc23a2-7295-428f-b1c4-37156c336c74)
![screencapture-localhost-guesthouse-src-php-view-bookings-php-2024-06-30-13_28_45](https://github.com/akshitjain16/guesthouse/assets/113924385/170987d4-6f3c-415e-bf7b-3fbdca4cfb8f)
![screencapture-localhost-guesthouse-src-php-manage-users-php-2024-06-30-13_29_32](https://github.com/akshitjain16/guesthouse/assets/113924385/ab840bb3-8366-4efc-8bd3-53caad83c11c)
![screencapture-localhost-guesthouse-src-php-view-meals-php-2024-06-30-13_31_24](https://github.com/akshitjain16/guesthouse/assets/113924385/a4346fc5-0eef-407b-9fdb-aa949d62762e)
![screencapture-localhost-guesthouse-src-php-menu-php-2024-06-30-13_31_56](https://github.com/akshitjain16/guesthouse/assets/113924385/8689e320-6974-4217-89ce-f7cf90dda33a)
![screencapture-localhost-guesthouse-src-php-dashboard-php-2024-06-30-13_30_17](https://github.com/akshitjain16/guesthouse/assets/113924385/728c6ed4-3bb4-4976-ab06-b166df2b7de8)


## Contributing

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Commit your changes (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature-branch`).
5. Open a Pull Request.


## File Structure

- `config/`: Contains configuration files.
- `src/php/`: Includes reusable PHP components with HTML, CSS, and JavaScript files.
- `public/assets/`: Publicly accessible files including images, videos.
- `index.php`: The main entry point for the application.
