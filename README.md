# crud-with-login-oops
  - Start XAMPP or WAMP server,
  - Create a new database `testcrud`,
  - Create following tables in `testcrud` database,
   
      ```
      DROP TABLE IF EXISTS `users`;
      CREATE TABLE IF NOT EXISTS `users` (
        `id` int NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `user_name` varchar(100) NOT NULL,
        `password` varchar(100) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
      
      
      DROP TABLE IF EXISTS `products`;
      CREATE TABLE IF NOT EXISTS `products` (
        `id` int NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `quantity` int NOT NULL,
        `price` decimal(10,2) NOT NULL,
        `user_id` int NOT NULL,
        PRIMARY KEY (`id`),
        KEY `FK_products_1` (`user_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
      ```
  - Add the credentials to `Login-Crud\Config\DBManager.php` file as follows,
      ```
      private $host     = 'localhost';
      private $dbname   = 'testcrud';
      private $username = 'root';
      private $password = '';
      ```
  - Navigate to following path on your browser 
  - http://localhost/crud-with-login-oops/views/user/login.php
  - If you are not registered then first register before loging-in, to register navigate to following path
  - http://localhost/crud-with-login-oops/views/user/register.php

