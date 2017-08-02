<?php

namespace CodeJet\Bucket;

use CodeJet\Bucket\Exception\NotFoundException;
use Interop\Container\ContainerInterface;

class Bucket implements ContainerInterface
{
    /** @var \Closure[] */
    private $factories = [];

    /** @var array */
    private $values = [];

    /** @var ContainerInterface */
    private $delegateContainer;

    public function __construct(array $values = [])
    {
        foreach ($values as $id => $value) {
            $this->add($id, $value);
        }
    }

    /**
     * @param string $id
     * @param mixed $value
     *
     * @return self
     */
    public function add(string $id, $value)
    {
        if ($value instanceof \Closure) {
            return $this->addFactory($id, $value);
        }

        $this->values[$id] = $value;

        return $this;
    }

    /**
     * @param string $id
     * @param \Closure $value
     *
     * @return self
     */
    public function addFactory(string $id, \Closure $value)
    {
        $this->factories[$id] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException('Item with id "' . $id . '" was not found.');
        }

        if (!isset($this->values[$id])) {
            $this->values[$id] = $this->getFromFactory($id);
        }

        return $this->values[$id];
    }

    /**
     * @param string $id
     * @return mixed
     */
    private function getFromFactory(string $id)
    {
        $factory = $this->factories[$id];
        $containerToUseForDependencies = $this->delegateContainer ?: $this;

        return $factory($containerToUseForDependencies);
    }

    /**
     * @inheritdoc
     */
    public function has($id): bool
    {
        return $this->hasFactory($id) || $this->hasValue($id);
    }

    /**
     * @param $id
     * @return bool
     */
    protected function hasFactory(string $id): bool
    {
        return isset($this->factories[$id]);
    }

    /**
     * @param $id
     * @return bool
     */
    protected function hasValue(string $id): bool
    {
        return isset($this->values[$id]);
    }

    /**
     * @param ContainerInterface $delegateContainer
     */
    public function setDelegateContainer(ContainerInterface $delegateContainer)
    {
        $this->delegateContainer = $delegateContainer;
    }
}
