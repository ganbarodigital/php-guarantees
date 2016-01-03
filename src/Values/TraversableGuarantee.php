<?php

/**
 * Copyright (c) 2016-present Ganbaro Digital Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   Guarantees/Values
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.ganbarodigital.com/php-data-containers
 */

namespace GanbaroDigital\Guarantees\Values;

use ArrayIterator;
use Iterator;
use GanbaroDigital\Reflection\Checks\IsTraversable;
use JsonSerializable;
use stdClass;
use Traversable;

class TraversableGuarantee implements Guarantee
{
    /**
     * the traversable data that we represent
     * @var array|Traversable|stdClass
     */
    private $data = [];

    /**
     * does $data hold real date?
     * @var boolean
     */
    private $isEmpty = true;

    /**
     * create a read-only, traversable guarantee from another piece of data
     *
     * @param mixed $data
     *        the data to wrap
     */
    public function __construct($data)
    {
        // we only assign our data if we have been given something
        // that is Traversable
        if (IsTraversable::check($data)) {
            $this->data = $data;
            $this->isEmpty = false;
        }
    }

    /**
     * return the value of this guarantee (if it has one), or $default if
     * this guarantee is empty
     *
     * @param  array|Traversable|stdClass $default
     *         the value to return if this guarantee is empty
     * @return array|Traversable|stdClass
     */
    public function getOrElse($default)
    {
        // do we have data?
        if ($this->isEmpty()) {
            return $default;
        }

        return $this->data;
    }

    /**
     * does this guarantee contain a value?
     *
     * @return boolean
     *         TRUE if this guarantee does NOT contain a value
     *         FALSE otherwise
     */
    public function isEmpty()
    {
        return $this->isEmpty;
    }

    /**
     * get an iterator to traverse the wrapped data
     *
     * @return Iterator
     */
    public function getIterator()
    {
        // general case - we've wrapped a real array
        if (is_array($this->data)) {
            return new ArrayIterator($this->data);
        }

        // special case - we've wrapped a Traversable object
        if (method_exists($this->data, 'getIterator')) {
            return $this->data->getIterator();
        }

        // special case - we're wrapped a stdClass
        return new ArrayIterator($this->data);
    }

    /**
     * convert our wrapped data into a JSON string
     *
     * @return array|JsonSerializable|stdClass
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}
