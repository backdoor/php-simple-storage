<?php
# src/MC/SimpleStorage/SimpleStorageInterface.php

/**
 *	SIMPLE FLAT FILE STORAGE CLASS
 *
 *	@version 2.0
 *	@author Matthew Colf <mattcolf@mattcolf.com>
 *
 *	@section LICENSE
 *
 *	Copyright 2014 Matthew Colf <mattcolf@mattcolf.com>
 *
 *	Licensed under the Apache License, Version 2.0 (the "License");
 *	you may not use this file except in compliance with the License.
 *	You may obtain a copy of the License at
 *
 *	http://www.apache.org/licenses/LICENSE-2.0
 *
 *	Unless required by applicable law or agreed to in writing, software
 *	distributed under the License is distributed on an "AS IS" BASIS,
 *	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *	See the License for the specific language governing permissions and
 *	limitations under the License.
 */

namespace MC\SimpleStorage;

use Exception;

/**
 *  Simple Storage Interface
 */
interface SimpleStorageInterface
{
    /**
     *  Flush storage changes to filesystem
     *
     *  @return bool
     *  @throws Exception
     */
    public function flush();

    /**
     *  Add a key:value pair to storage
     *
     *  @param string $key
     *  @param mixed $data
     *  @param null string $domain
     *  @return bool
     */
    public function put($key, $data, $domain = null);

    /**
     *  Check if a key exists in storage
     *
     *  @param string $key
     *  @param null string $domain
     *  @return bool
     */
    public function exists($key, $domain = null);

    /**
     *  Get a value from storage
     *
     *  @param string $key
     *  @param null string $domain
     *  @return mixed|null
     */
    public function get($key, $domain = null);

    /**
     *  Remove a key from storage
     *
     *  @param string $key
     *  @param null string $domain
     *  @return bool
     */
    public function remove($key, $domain = null);

    /**
     *  Check if a storage domain exists
     *
     *  @param string $domain
     *  @return bool
     */
    public function domain_exists($domain);

    /**
     *  Add a storage domain
     *
     *  @param string $domain
     *  @return bool
     */
    public function domain_add($domain);

    /**
     *  Remove a storage domain and all associated data
     *
     *  @param string $domain
     *  @return bool
     */
    public function domain_remove($domain);
}