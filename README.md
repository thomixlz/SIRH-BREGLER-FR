# SIRH - MNS - Project

This project is a simulation of a project that could be implemented in any organization wishing to keep track of its employees' leave, travel and time-off.

## Getting Started

### Technologies ?

* HTML / CSS
* Javascript / Jquery
* PHP Symfony 6.4

### Executing program

* How to run the program
```
symfony server:start
```

* Database export location 
```
cd ./database
```

* How to connect the project to a database ?
    * Open .env file and edit this line :

```
DATABASE_URL="mysql://localhost:PasswordHere@127.0.0.1:3306/dbName"
```

## Authors

Contributors names and contact info :

BREGLER Thomas (@thomixlz) 

## Version History

* 0.4 :
    * Add All roles
    * Add All routes about the roles for the security

* 0.3 :
    * Add Base design
    * Add Navbar
    * Add Dashboard design
    * Change Login page design 
    * Add redirect to /login for unlog users 

* 0.2 :
    * Add security & validator bundle for login/register requirements 
    * Config security.yaml for create ADMIN ROLE
    * Create manually the first Admin User
    * Create manually the first User for test
    * Add Login Page
    * Add Register Page 
    * Protect route about /register it's only for admin now

* 0.1 
    * Initial Release
    * Add Read.me
