# Gym Management System

## Overview
The Gym Management System is a web application designed to help gym owners manage their facilities efficiently. It provides features for managing members, membership plans, and payment processing.

## Features
- **Member Management**: View and manage member information.
- **Membership Plans**: Display various membership plans with details and pricing.
- **Payment Processing**: Handle payments and display payment history for members.

## Project Structure
```
gym-management-system
├── assets
│   ├── css
│   │   └── style.css
│   └── js
│       └── app.js
│   └── config
│       ├── database.php
│       └── membership-plans.php
├── includes
│   ├── header.php
│   └── footer.php
├── pages
│   ├── members.php
│   ├── membership-plans.php
│   └── payments.php
├── database
│   └── schema.sql
├── index.php
└── README.md
```

## Installation
1. Clone the repository to your local machine.
2. Navigate to the project directory.
3. Set up the database using the SQL statements in `database/schema.sql`.
4. Set the database connection environment variables, or use the local defaults in `assets/config/database.php`.
5. Open `index.php` in your web browser to access the application.

## Deploying to Render

This PHP application is configured to deploy to Render as a Docker web service. It uses MySQL, so create a hosted MySQL database separately; the local XAMPP database cannot be used by the deployed application.

1. Create a hosted MySQL database and import `database/schema.sql` into it.
2. Push the project, including the `Dockerfile`, to GitHub.
3. In Render, select **New** > **Web Service**, connect the GitHub repository, and choose **Docker** as the runtime.
4. Add these environment variables to the Render web service using the values from your MySQL provider:
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USERNAME`
   - `DB_PASSWORD`
5. Deploy the service. Keep database credentials in Render's environment settings; do not commit them to GitHub.

The PHP database connection reads these variables and falls back to the local XAMPP defaults when they are not set.

## Usage
- Navigate through the application using the header links.
- Manage members, view membership plans, and process payments as needed.

## Technologies Used
- PHP for server-side scripting.
- MySQL for database management.
- HTML, CSS, and JavaScript for front-end development.

## Contributing
Feel free to contribute to the project by submitting issues or pull requests. Your feedback and suggestions are welcome!