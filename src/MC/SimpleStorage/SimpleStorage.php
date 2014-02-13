<?php
# src/MC/SimpleStorage/SimpleStorage.php

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
 *  Simple Storage Class
 */
class SimpleStorage implements SimpleStorageInterface
{
    /**
     *  Default domain name
     */
    const DOMAIN_DEFAULT = 'default';

    /**
     *  Array key for domains data
     */
    const KEY_DOMAINS = 'domains';

    /**
     *  Array key for meta data
     */
    const KEY_META = 'meta';

    /**
     *  The file to store/read data
     *
     *  @var string
     */
    protected $file;

    /**
     *  Whether the current data is dirty or not
     *
     *  @var bool
     */
    protected $dirty;

    /**
     *  The current working domain
     *
     *  @var string
     */
    protected $domain;

    /**
     *  The current data set
     *
     *  @var array
     */
    protected $data;

    /**
     *  Constructor
     *
     *  @param string $file
     *  @param null string $domain
     *  @throws Exception
     */
    public function __construct($file, $domain = null)
    {
        $this->dirty = false;
        $this->data = array(
            self::KEY_DOMAINS => array(
                self::DOMAIN_DEFAULT => array(
                    'foo' => 'bar'
                )
            )
        );

        // check file
        if (is_writable($file)) {
            $this->file = $file;
        } else {
            throw new Exception('Storage file cannot be written to.');
        }

        $json = file_get_contents($this->file);

        // load file
        if (strlen($json) > 0) {
            $data = json_decode($json, true);

            if ($this->data === null) {
                throw new Exception('Unable to decode file.');
            }

            // verify data checksum
            if (!$this->verify($data[self::KEY_META]['checksum'], $data[self::KEY_DOMAINS])) {
                throw new Exception('Data from file is not valid. Fails checksum.');
            }

            // decode domains data
            $domains = json_decode($data[self::KEY_DOMAINS], true);

            if ($domains === null) {
                throw new Exception('Unable to unserialize domain data from file.');
            }

            $this->data[self::KEY_DOMAINS] = $domains;
        }

        if ($domain) {
            $this->domain = $domain;
        } else {
            $this->domain = self::DOMAIN_DEFAULT;
        }

        // add domain if it doesn't exist
        if (!$this->domain_exists($domain)) {
            $this->domain_add($domain);
        }
    }

    /**
     *  Destructor
     */
    public function __destruct()
    {
        $this->flush();
    }

    /**
     *  Flush storage changes to filesystem
     *
     *  @return bool
     *  @throws Exception
     */
    public function flush()
    {
        if ($this->dirty) {

            $json = json_encode($this->data[self::KEY_DOMAINS]);
            $data = array(
                self::KEY_DOMAINS   => $json,
                self::KEY_META      => array(
                    'updated'           => date('c'),
                    'checksum'          => $this->hash($json)
                )
            );

            if (file_put_contents($this->file, $data)) {
                return true;
            } else {
                throw new Exception('Unable to write back to file. Data will be lost.');
            }
        }

        return true;
    }

    /**
     *  Add a key:value pair to storage
     *
     *  @param string $key
     *  @param mixed $data
     *  @param null string $domain
     *  @return bool
     */
    public function put($key, $data, $domain = null)
    {
        if ($domain === null) {
            $domain = $this->domain;
        }

        if (is_string($key) && $this->domain_exists($domain)) {
            $this->data[self::KEY_DOMAINS][$domain][$key] = $data;
            return true;
        }

        return false;
    }

    /**
     *  Check if a key exists in storage
     *
     *  @param string $key
     *  @param null string $domain
     *  @return bool
     */
    public function exists($key, $domain = null)
    {
        if ($domain === null) {
            $domain = $this->domain;
        }

        if (is_string($key) && $this->domain_exists($domain)) {
            return array_key_exists($key, $this->data[self::KEY_DOMAINS][$domain]);
        }

        return false;
    }

    /**
     *  Get a value from storage
     *
     *  @param string $key
     *  @param null string $domain
     *  @return mixed|null
     */
    public function get($key, $domain = null)
    {
        if ($domain === null) {
            $domain = $this->domain;
        }

        if ($this->exists($key, $domain)) {
            return $this->data[self::KEY_DOMAINS][$domain][$key];
        }

        return null;
    }

    /**
     *  Remove a key from storage
     *
     *  @param string $key
     *  @param null string $domain
     *  @return bool
     */
    public function remove($key, $domain = null)
    {
        if ($domain === null) {
            $domain = $this->domain;
        }

        if ($this->exists($key, $domain)) {
            unset($this->data[self::KEY_DOMAINS][$domain][$key]);
            $this->dirty = true;
            return true;
        }

        return false;
    }

    /**
     *  Check if a storage domain exists
     *
     *  @param string $domain
     *  @return bool
     */
    public function domain_exists($domain)
    {
        return (is_string($domain) && array_key_exists($domain, $this->data[self::KEY_DOMAINS]));
    }

    /**
     *  Add a storage domain
     *
     *  @param string $domain
     *  @return bool
     */
    public function domain_add($domain)
    {
        if (is_string($domain) && !$this->domain_exists($domain)) {
            $this->data[self::KEY_DOMAINS][$domain] = array();
            $this->dirty = true;
            return true;
        }

        return false;
    }

    /**
     *  Remove a storage domain and all associated data
     *
     *  @param string $domain
     *  @return bool
     */
    public function domain_remove($domain)
    {
        if ($this->domain_exists($domain)) {
            unset($this->data[self::KEY_DOMAINS][$domain]);
            $this->dirty = true;
            return true;
        }

        return false;
    }

    /**
     *  Generate a checksum for given data
     *
     *  @param string $data
     *  @return string
     */
    protected function hash($data)
    {
        return md5(json_encode($data));
    }

    /**
     *  Verify a data checksum value
     *
     *  @param string $checksum
     *  @param string $data
     *  @return bool
     */
    protected function verify($checksum, $data)
    {
        if ($checksum == $this->hash($data)) {
            return true;
        }

        // add support for old broken hash to prevent 1.0 breaking change
        if ($checksum == md5($data)) {
            return true;
        }

        return false;
    }
}