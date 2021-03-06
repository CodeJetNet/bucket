<?php

namespace CodeJet\Bucket;

use Interop\Container\Exception\NotFoundException;
use Interop\Container\ContainerInterface;

class BucketTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bucket;
     */
    protected $bucket;

    protected function setUp()
    {
        $this->bucket = new Bucket();
    }

    public function testConstructWithValue()
    {
        $constructValues = [
            'firstId' => 'firstValue',
            'secondId' => 'secondValue'
        ];

        $bucket = new Bucket($constructValues);

        $this->assertSame('firstValue', $bucket->get('firstId'));
        $this->assertSame('secondValue', $bucket->get('secondId'));
    }

    public function testValues()
    {
        $id = "myValue";
        $value = "This is a string.";

        $this->bucket->add($id, $value);

        $this->assertTrue($this->bucket->has($id));
        $this->assertSame($value, $this->bucket->get($id));
    }

    public function testFactories()
    {
        $id = "myFactory";
        $factory = function (ContainerInterface $bucket) {
            // Bucket pass itself as the only argument to the closure.
            $this->assertSame($this->bucket, $bucket);

            return new \stdClass();
        };

        $this->bucket->add($id, $factory);

        $this->assertTrue($this->bucket->has($id));
        $this->assertInstanceOf(\stdClass::class, $this->bucket->get($id));
    }

    /**
     * This ensures that the value generated by the factory when retrieved the first time
     * is returned on subsequent requests.
     */
    public function testFactoryIsRunOnlyOnce()
    {
        $id = "myFactory";

        $this->bucket->add($id, function (ContainerInterface $bucket) {
            return new \stdClass();
        });

        $firstFactoryProduct = $this->bucket->get($id);
        $secondFactoryProduct = $this->bucket->get($id);

        $this->assertSame($firstFactoryProduct, $secondFactoryProduct);
    }

    /**
     * According to the container-interop spec, the delegate container should
     * be passed as the dependency provider for factories instead of the master
     * container itself.
     *
     * https://github.com/container-interop/container-interop/blob/master/docs/Delegate-lookup.md
     */
    public function testUsesDelegateContainer()
    {
        $delegateBucket = new Bucket();

        $this->bucket->setDelegateContainer($delegateBucket);

        $id = "factoryReceivingDelegateContainer";

        $this->bucket->add($id, function (ContainerInterface $bucket) use ($delegateBucket) {
            // Ensure the delegate container is passed into the closure and not the (master) bucket itself.
            $this->assertSame($delegateBucket, $bucket);
        });

        $this->bucket->get($id);
    }

    /**
     * According to the container-interop spec, when a call to `get` a non existent value or
     * service id occurs, the container SHOULD throw a Interop\Container\Exception\NotFoundException
     *
     * https://github.com/container-interop/container-interop/blob/master/docs/ContainerInterface.md
     */
    public function testThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);
        $this->bucket->get('This service surely does not exist');
    }
}
