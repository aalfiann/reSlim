reSlim
=======
[![Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg)](https://github.com/aalfiann/reSlim)
[![reSlim](https://img.shields.io/badge/stable-1.12.0-brightgreen.svg)](https://github.com/aalfiann/reSlim)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/aalfiann/reSlim/blob/master/license.md)

reSlim is Lightweight, Fast, Secure, Simple, Scalable, Flexible and Powerful rest api framework.<br>
reSlim is based on [Slim Framework version 3](http://www.slimframework.com/).<br>

Features:
---------------
Reslim is already build with essentials of user management system in rest api way.

1. User register, login and logout
2. Auto generated token every login
3. User can revoke all active token
4. User can manage their API Keys
5. Included with default role for superuser, admin and member
6. Auto clear current token when logout,user deleted or password was changed
7. Change, reset, forgot password concept is very secure
8. Mailer for sending email or contact form
9. File management system
10. Pages management system
11. Pagination json response
12. Support Multi Language
13. Server Side Caching
14. Scalable architecture with modular concept
15. Load Balancer with multiple database server (master to master or master to slave)
16. Etc

Extensions:
---------------
- **Modules**  
Here is the available modules created by reSlim author >> [reSlim-modules](https://github.com/aalfiann/reSlim-modules).

- **UI Boilerplate**  
Here is the basic UI template for production use with reSlim >> [reSlim-ui-boilerplate](https://github.com/aalfiann/reSlim-ui-boilerplate).

System Requirements
---------------

1. PHP 5.5 or newer (last tested on PHP7.3)
2. MySQL 5.6 or MariaDB
3. Web server with URL rewriting
4. Apache Server (Better to use Apache + Reverse Proxy NGINX)

Getting Started
---------------

### I. Installation
1. Get or download the project
2. Extract then rename folder **reSlim-master** to **reslim**
3. Open shell or CMD then go to **src** folder
    ```
    cd reslim/src
    ```
    
4. Install it using Composer  
    ```
    composer install
    ```
5. Done

### II. Connection Database
1. Create Database reslim in your MySQL
2. Execute or restore **reSlim.sql** which is located at **resources/database/** folder
3. Edit **config.php** which is located at **src/** folder  
    Just only this part,
    ```
    $config['db']['host']   = 'localhost';
    $config['db']['user']   = 'root';
    $config['db']['pass']   = 'root';
    $config['db']['dbname'] = 'reSlim';
    ```
    You can set the rest config later

4. Done

### III. Test
1. Open your browser and visit >> http://localhost:1337/reslim/src/api/

Note: 
    - My apache server is run on port 1337.

### IV. Development
**How to create new app or modules?**  
Please look at this very simple project on [Github.com](https://github.com/aalfiann/reSlim-modules-first_mod).

### V. Deployment
1. Upload all files inside folder src to your server
2. Backup local database and then restore to your server database online
3. Done


About Folder System
---------------  
Folder system is inside **src** folder  
* src/
    * api/
        * htaccess
        * index.php (Call the app)
    * app/
        * app.php (Setup app before running)
        * dependencies.php (Registering the dependencies)
        * index.php (Default forbidden page)
        * middleware.php (middleware as variable on the fly)
    * classes/
        * middleware/
            * ApiKey.php (For handling authentication api key)
            * index.php (Default forbidden page)
            * ValidateParam.php (For handling validation in body form request)
            * ValidateParamJSON.php (For handling validation in JSON request)
            * ValidateParamURL.php (For handling validation in query parameter url)
        * Auth.php (For handling authentication)
        * BaseConverter.php (For encryption)
        * Cors.php (For accessing web resources)
        * CustomHandlers.php (For handle message)
        * index.php (Default forbidden page)
        * JSON.php (Default handler JSON)
        * Logs.php (For handle Log Server)
        * Mailer.php (For sending mail)
        * Pagination.php (For pagination json response)
        * SimpleCache.php (For handle cache server side)
        * Upload.php (For user upload and management file)
        * User.php (For user management)
        * Validation.php (For validation)
    * logs/
        * app.log (Log data will stored in here)
        * index.php (Default forbidden page)
    * modules
        * backup/ (Default module backup for database)
        * flexibleconfig/ (Default module flexibleconfig for extend the app config)
        * packager/ (Default module packager for app management)
        * pages/ (Default module package for pages management)
        * index.php (Default forbidden page)
    * routers/
	    * name.router.php (routes by functionalities)

### api/
    
Here is the place to run your application

### app/

Here is the place for slim framework<br>
We are using PDO MySQL for the Database.

### classes/

reSlim core classes are here.

### classes/middleware

reSlim middleware classes are here.

### logs/

Your custom log will be place in here as default.<br>
You can add your custom log in your any container or router.

### modules/{your-module}

You have to create new folder for each different module project.

**How to create new reSlim modules?**  
Please look at this very simple project on [Github.com](https://github.com/aalfiann/reSlim-modules-first_mod).  

### routers/

All the files with the routes. Each file contents the routes of an specific functionality.<br>
It is very important that the names of the files inside this folder follow this pattern: name.router.php

  
Learn more deeply
-----------------
### * Documentation
Documentation will update on [Wiki](https://github.com/aalfiann/reslim/wiki).

### * Learn from test router
We created several simple function on the **test.router.php** which is located at **src/router/**.  
You can learn from there 

### * Working with postman for testing
I recommend you to use PostMan an add ons in Google Chrome to get Started with test.

1. Import reSlim.sql in your database then config your database connection in config.php inside folder "reSlim/src/"
2. Import file reSlim User.postman_collection.json in your PostMan.
3. Edit the path in PostMan. Because the example test is using my path server which is my server is run in http://localhost:1337 
    The path to run reSlim is inside folder api.<br> 
    Example for my case is: http://localhost:1337/reslim/src/api/<br><br>
    In short, It's depend on your server configuration.
4. Then you can do the test by yourself


### * The concept authentication in reSlim
1. Register account first
2. Then You have to login to get the generated new token

The token is always generated new when You relogin and the token is will expired in 7 days as default.<br>
If You logout or change password or delete user, the token will be clear automatically.

  
How to Contribute
-----------------
### Pull Requests

1. Fork the reSlim repository
2. Create a new branch for each feature or improvement
3. Send a pull request from each feature branch to the develop branch