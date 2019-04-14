//https://github.com/laravel/framework/blob/5.8/src/Illuminate/Support/Traits/Macroable.php#L75


/**
     * Dynamically handle calls to the class.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.', static::class, $method
            ));
        }
        if (static::$macros[$method] instanceof Closure) {
            return call_user_func_array(Closure::bind(static::$macros[$method], null, static::class), $parameters);
        }
        return call_user_func_array(static::$macros[$method], $parameters);
    }


    https://github.com/laravel/framework/blob/5.8/src/Illuminate/Database/Query/Builder.php#L2986

    /**
     * Handle dynamic method calls into the method.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }
        if (Str::startsWith($method, 'where')) {
            return $this->dynamicWhere($method, $parameters);
        }
        static::throwBadMethodCallException($method);
    }


    https://github.com/laravel/framework/blob/5.8/src/Illuminate/Database/Eloquent/Builder.php#L1320

    /**
     * Dynamically handle calls into the query instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($method === 'macro') {
            $this->localMacros[$parameters[0]] = $parameters[1];
            return;
        }
        if (isset($this->localMacros[$method])) {
            array_unshift($parameters, $this);
            return $this->localMacros[$method](...$parameters);
        }
        if (isset(static::$macros[$method])) {
            if (static::$macros[$method] instanceof Closure) {
                return call_user_func_array(static::$macros[$method]->bindTo($this, static::class), $parameters);
            }
            return call_user_func_array(static::$macros[$method], $parameters);
        }
        if (method_exists($this->model, $scope = 'scope'.ucfirst($method))) {
            return $this->callScope([$this->model, $scope], $parameters);
        }
        if (in_array($method, $this->passthru)) {
            return $this->toBase()->{$method}(...$parameters);
        }
        $this->forwardCallTo($this->query, $method, $parameters);
        return $this;
    }