
# Support Monitoring Application for UV Desk

A web-based software designed to enhance the functionality of the open-source support ticketing system, UV Desk. This application is mainly intended for management use and provides advanced reporting and agent task monitoring capabilities.

The free version of UV Desk does not have comprehensive reporting or agent monitoring features. This application, developed by a beginner PHP coder, operates on the same database as UV Desk and provides these missing capabilities.

With this support monitoring application, management can gain improved visibility into the performance of UV Desk and make more informed decisions about how to optimize the use of the software and allocate resources more effectively.

###  Features

 1. login
 2. Ticket dashboard *(Report)*
 3. Agent activity *(Report)*
 4. Customer activity *(Report)*
 5. Individual  Agent  performance *(Report)*
 
 ### System Requirements
-   Web server: Apache  
-   PHP: Latest stable version
-   Database: MySQL  
-   Operating system: Windows, macOS, Linux
-   Memory and storage: 4 GB RAM
-   Browser compatibility: Latest versions of Chrome, Firefox, Safari, etc.

### Installation
Specially remember that this is made for UV Desk monitoring and newly developed system also work in same database so be sure to install UV Desk first. After that change database credentials of this project. Location of config file is  

    support_monitoring > report > public > config

 inside the `config.php` change below code as you need
 

    define('DB_HOST', '127.0.0.1');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_NAME', 'uvdesk');
    
then change you need to change main UV desk project URL as below 

    define('BASE_URL', 'localhost/uv/uvdesk/public');

### Usage
You must be logged in to access the software. Since it is based on the UVdesk database, session checking is done from the UVdesk user table. But because the UVdesk user password was encrypted, it could no longer be used as a password. That is why the user's email address and phone number have been used as credentials.

As per the current settings, All type of users  other than the customer role can access the monitoring system using their email address and phone number.