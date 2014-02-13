Simple Key:Value Flat File Storage Class
========================================

This class provides an interface to a very simple key:value based storage system. All data is 
stored in a flat file using JSON.

Features
--------

- Provides a key:value storage system. Supports most PHP objects including multidimensional arrays.
- Ability to divide data into domains.
- Updated to support installation with composer in 2.0.

Dependencies
------------

- Apache must have R+W access to your storage.json file.

Reference
---------

- 	SimpleStorage::__construct($file, $your_default_domain = "default")

	Throws ```Exception``` on error.

- 	SimpleStorage::flush()

	Writes all stored data back to file. Throws ```Exception``` on error.

- 	SimpleStorage::put($key, $data, $domain = YOUR_DEFAULT_DOMAIN)

	Stores new data under your selected domain (must already be created) that can later be referenced by ```$key```.

-   SimpleStorage::exists($key, $domain = YOUR_DEFAULT_DOMAIN)

    Check if a key exists within a given domain, your default domain by default.

-   SimpleStorage::remove($key, $domain = YOUR_DEFAULT_DOMAIN)

    Remove a key, resident in the specified domain, from storage

- 	SimpleStorage::get($key, $domain = YOUR_DEFAULT_DOMAIN)

	Retrieves data you have already stored within ```$domain```.
	
- 	SimpleStorage::domain_exists($domain)

	Check to see if a domain exists.
	
- 	SimpleStorage::domain_add($domain)

	Adds a new domain. Returns ```false`` on error.

- 	SimpleStorage::domain_remove($domain)

	Remove an existing domain and delete all associated data. Returns ```false``` on error.

Usage
-----

1.  Install [composer](https://getcomposer.org/) if you haven't already.
2.  Add this library to your project's composer.json file.

    ```
    {
        "require": {
            "mattcolf/simple-storage": "*"
        }
    }
    ```

3.  Install all dependencies for your project.

    ```
    > composer.phar install
    ```

4.  Within your project, make sure to load the composer autoloader.

    ```
    require_once "vendor/autoload.php";
    ```

5.  Set your storage file location and instantiate a copy of SimpleStorage.

    ```
    use MC\SimpleStorage;

    $file = '/path/to/storage.json';

    $storage = new SimpleStorage($file);
    ```

6. Put and get content as needed. Note that the storage key must be a string!.

	```
	$book = array(														
		"title" => "A Day In The Life",									
		"author" => "John Smith",										
		"date" => date("c"),											
		"pages" => 428,												
		"contents" => array(
			"chapter1" => "One upon a time...",
			"chapter2" => "...a toad...",
			"chapter3" => "found a home in the forest.".
		)
	);


	$storage->put("book",$book);
	$stored_book = $storage->get("book");
	\\ or
	$storage->put("book",$book,"YOUR_DOMAIN_NAME");
	$stored_book = $storage->get("book","YOUR_DOMAIN_NAME");
	
	print_r($stored_book);
	```

Legal
-----

Copyright 2014 Matthew Colf mattcolf@mattcolf.com

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License. You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.