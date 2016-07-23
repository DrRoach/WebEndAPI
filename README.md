#WebEnd API

What is the WebEnd API?

It is a backend website API created so that long tasks that need to be done over and over again, such as creating a login feature for a site, only need to be done once. It also gives a easy wrapper for frontend developers to use.

###Using Databases

To setup your database, first you must open 'Database/Database.php' and set `$ENGINE` to the database engine that you wish to use. The open 'Database/Engines/{YOUR_ENGINE}.php' and enter the required credentials.

Whenever WebEndAPI needs to use a database, it extends the `Database` class. All you need to do, is edit the `$_schema` variable. This array is the structure of the table built by the `Database` class.
