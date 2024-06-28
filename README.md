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
