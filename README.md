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

* 0.6 :
    * Add in edit users a button with "generate password" and send the mail with new log to the users
    * Change design in cms

* 0.5 :
    * Add not able to delete or edit Superadmin account
    * Add Equipe Crud 
    * Add Image upload system 
    * Add a lot of cond about roles 
    * Add date picker js in form
    * Add local Mailer with Mailhog for send to a new user password when u create new account as superadmin(WORK)


* 0.4 :
    * Add All Roles
    * Add All Routes about the roles for the security

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
