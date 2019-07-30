<?php
/**
 *  Copyright 2015 SmartBear Software
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/**
 * 
 *
 * NOTE: This class is auto generated by the swagger code generator program. Do not edit the class manually.
 *
 */

namespace DwollaSwagger\models;

use \ArrayAccess;

class Transfer implements ArrayAccess {
  static $swaggerTypes = array(
      '_links' => 'map[string,HalLink]',
      '_embedded' => 'object',
      'id' => 'string',
      'status' => 'string',
      'amount' => 'Money',
      'created' => 'DateTime',
      'metadata' => 'object',
      'clearing' => 'Clearing',
      'correlation_id' => 'string',
      'individual_ach_id' => 'string'
  );

  static $attributeMap = array(
      '_links' => '_links',
      '_embedded' => '_embedded',
      'id' => 'id',
      'status' => 'status',
      'amount' => 'amount',
      'created' => 'created',
      'metadata' => 'metadata',
      'clearing' => 'clearing',
      'correlation_id' => 'correlationId',
      'individual_ach_id' => 'individualAchId'
  );

  
  public $_links; /* map[string,HalLink] */
  public $_embedded; /* object */
  public $id; /* string */
  public $status; /* string */
  public $amount; /* Money */
  public $created; /* DateTime */
  public $metadata; /* object */
  public $clearing; /* Clearing */
  public $correlation_id; /* string */
  public $individual_ach_id; /* string */

  public function __construct(array $data = null) {
    $this->_links = $data["_links"];
    $this->_embedded = $data["_embedded"];
    $this->id = $data["id"];
    $this->status = $data["status"];
    $this->amount = $data["amount"];
    $this->created = $data["created"];
    $this->metadata = $data["metadata"];
    $this->clearing = $data["clearing"];
    $this->correlation_id = $data["correlation_id"];
    $this->individual_ach_id = $data["individual_ach_id"];
  }

  public function offsetExists($offset) {
    return isset($this->$offset);
  }

  public function offsetGet($offset) {
    return $this->$offset;
  }

  public function offsetSet($offset, $value) {
    $this->$offset = $value;
  }

  public function offsetUnset($offset) {
    unset($this->$offset);
  }
}