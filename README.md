
#### This is a twitter clone test built on Silex


The purpose of this twitter clone
 
##### Includes:
 
 - Service dependency Injection
 - Service Providers (includes custom PDO database driver provider)
 - Namespacing
 - Autoloading through composer (PSR 4)
 - Routings
 - Theme
 
##### Functions that work
 - Posting tweets
 - Login / Logout 
 
##### Installation
Create a database named "twitter" and user named "twitter" and the password is just "password"

Execute included sql file onto the newly created database sql/twitter.sql. This will create two tables - "posts" and "users"
by default there is a user included, and three posts (tweets).

##### Usage
When you first load the application, it will land on a landing page with similar insutructions. 
There will be a login box at the bottom. Login using credential "twitter" and "password".
When you login, you will be redirected to the twitter looking theme and you will be able to post tweets. Use the blue upper right button called "Tweet" to post tweets.
