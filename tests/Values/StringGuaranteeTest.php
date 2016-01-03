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

use ArrayObject;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @coversDefaultClass GanbaroDigital\Guarantees\Values\StringGuarantee
 */
class StringGuaranteeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanInstantiate()
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StringGuarantee(null);

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(StringGuarantee::class, $unit);
    }

    /**
     * @covers ::__construct
     */
    public function testIsGuarantee()
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StringGuarantee(null);

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(Guarantee::class, $unit);
    }

    /**
     * @covers ::__construct
     * @covers ::getOrElse
     * @covers ::isEmpty
     */
    public function testWrapsStrings()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedValue = "hello, world!";

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StringGuarantee($expectedValue);
        $actualValue = $unit->getOrElse(null);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedValue, $actualValue);
        $this->assertFalse($unit->isEmpty());
    }

    /**
     * @covers ::__construct
     * @covers ::getOrElse
     * @covers ::isEmpty
     */
    public function testWrapsEmptyStrings()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedValue = "";

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StringGuarantee($expectedValue);
        $actualValue = $unit->getOrElse(null);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedValue, $actualValue);
        $this->assertFalse($unit->isEmpty());
    }

    /**
     * @covers ::getIterator
     * @dataProvider provideWrapableDataToIterate
     */
    public function testCanIterateOverWrappedData($data, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new StringGuarantee($data);

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = [];
        foreach ($unit as $key => $value) {
            $actualResult[$key] = $value;
        }

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::jsonSerialize
     * @dataProvider provideWrapableDataToSerialize
     */
    public function testCanJsonSerializeWrappedData($data, $expectedResult)
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new StringGuarantee($data);

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = json_encode($unit);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @covers ::__construct
     * @covers ::getOrElse
     * @covers ::isEmpty
     * @covers ::getIterator
     * @covers ::jsonSerialize
     * @dataProvider provideNonWrapableData
     */
    public function testTreatsEverythingElseAsEmptyString($data)
    {
        // ----------------------------------------------------------------
        // setup your test


        // ----------------------------------------------------------------
        // perform the change

        $unit = new StringGuarantee($data);

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($unit->isEmpty());
        $this->assertEquals(null, $unit->getOrElse(null));

        $assembledData = [];
        foreach ($unit as $key => $value) {
            $assembledData[$key] = $value;
        }
        $this->assertEquals([''], $assembledData);
        $this->assertEquals(json_encode(''), json_encode($unit));
    }

    public function provideWrapableDataToIterate()
    {
        return [
            [
                "hello, world",
                [ "hello, world" ]
            ],
            [
                "",
                [ "" ]
            ],
            [
                new StringGuaranteeTest_String("hello, world"),
                [ "hello, world" ]
            ]
        ];
    }

    public function provideWrapableDataToSerialize()
    {
        return [
            [
                "hello, world",
                json_encode("hello, world")
            ],
            [
                "",
                json_encode("")
            ],
            [
                new StringGuaranteeTest_String("hello, world"),
                json_encode("hello, world")
            ]
        ];
    }

    public function provideNonWrapableData()
    {
        $stdClass = new stdClass;
        $stdClass->attr1 = "hello";
        $stdClass->attr2 = "this is our test data";

        return [
            [ null ],
            [ true ],
            [ false ],
            [ function() { return "hello"; } ],
            [ 0.0 ],
            [ 3.1415927 ],
            [ 0 ],
            [ 100 ],
            [ $stdClass ],
            [ STDIN ],
        ];
    }
}

class StringGuaranteeTest_String
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __toString()
    {
        return $this->data;
    }
}