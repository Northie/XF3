What is Couchbase?
=================

Couchbase is a json based document (or object, or noSQL) database

Couchbase can be installed on linux and mac. See the couchbase website for how to install the server on your system.

To run couchbase with PHP you need the couchbase client sdk and then the couchbase pecl module. See the couchbase website on how to install these on your system.

Concepts
========

Data is stored as JSON in buckets. The exact internals are unknown but a bucket is a way of separating concerns....one server can have up to 10 buckets. A project should probably only use one bucket.

Each json document has a key, which must be unique. It is the responsibility of the application using couchbase to determine the key. Keys can be alpha-numeric and of any length up to 64 characters.

The data can be queried by key or by a SQL-like query language known as n1ql which is documented in the couchbase website

Administration
==============

The web based admin control panel can be used to manage buckets, users, test queries, definne map-reduce fucntions and more

In General
---------

 * make a bucket
 * make a user
   * assign the user roles to buckets

Queries
------

In order to run a n1ql query against a bucket, you will need to create an index on the bucket. A typical primary key index is created by running a query like

    CREATE PRIMARY INDEX `#primary` ON `bucketname`






