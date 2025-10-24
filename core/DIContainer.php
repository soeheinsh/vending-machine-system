<?php
class DIContainer {
    private $services = [];
    private $instances = [];

    public function set($name, $definition) {
        $this->services[$name] = $definition;
    }

    public function get($name) {
        if (!isset($this->services[$name])) {
            throw new Exception("Service {$name} not found in container");
        }

        if (!isset($this->instances[$name])) {
            $definition = $this->services[$name];
            if (is_callable($definition)) {
                $this->instances[$name] = $definition($this);
            } else {
                $this->instances[$name] = $definition;
            }
        }

        return $this->instances[$name];
    }

    public function has($name) {
        return isset($this->services[$name]);
    }
}
