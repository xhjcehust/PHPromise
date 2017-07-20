<?php

namespace PHPromise\Promise;

class All
{
    private $arrived = false;
    private $resolvedValue = [];
    private $promises;
    /** @var  PromiseResult */
    private $result;

    public function __construct($promises)
    {
        if (is_array($promises))
            $this->promises = $promises;
        else
            $this->promises = [];
    }

    public function then($callback)
    {
        if ($this->promises == []) {
            $value = call_user_func($callback, $this->resolvedValue);
            return new Promise(function ($resolve, $reject) use($value) {
                if ($value instanceof RejectedPromise) {
                    $reject($value->getValue());
                } else if ($value instanceof FulfilledPromise) {
                    $resolve($value->getValue());
                } else {
                    $resolve($value);
                }
            });
        }

        return new Promise(function ($resolve, $reject) use($callback) {
            for ($i = 0; $i < count($this->promises); $i++) {
                $promise = $this->promises[$i];
                if ($promise instanceof Promise) {
                    $promise->then(function ($value) use ($resolve, $i, $callback) {
                        $this->resolvedValue[$i] = $value;
                        if (count($this->resolvedValue) == count($this->promises)) {
                            if ($this->arrived === false) {
                                $this->arrived = true;
                                ksort($this->resolvedValue);
                                $value = call_user_func($callback, $this->resolvedValue);
                                $resolve($value);
                            }
                        }
                    })->otherwise(function ($value) use ($reject) {
                        if ($this->arrived === false) {
                            $this->arrived = true;
                            $reject($value);
                        }
                    });
                } else if ($promise instanceof RejectedPromise) {
                    if ($this->arrived === false) {
                        $this->arrived = true;
                        $reject($promise->getValue());
                        return;
                    }
                } else {
                    if ($promise instanceof FulfilledPromise) {
                        $promise = $promise->getValue();
                    }
                    $this->resolvedValue[$i] = $promise;
                    if (count($this->resolvedValue) == count($this->promises)) {
                        if ($this->arrived === false) {
                            $this->arrived = true;
                            ksort($this->resolvedValue);
                            $value = call_user_func($callback, $this->resolvedValue);
                            $resolve($value);
                        }
                    }
                }
            }
        });

    }

    public function otherwise($callback)
    {
        if ($this->promises == []) {
            return new Promise(function ($resolve, $reject) {
                $resolve([]);
            });
        }

        return new Promise(function ($resolve, $reject) use ($callback) {
            for ($i = 0; $i < count($this->promises); $i++) {
                $promise = $this->promises[$i];
                if ($promise instanceof Promise) {
                    $promise->otherwise(function ($value) use ($callback, $resolve, $reject) {
                        if ($this->arrived === false) {
                            $this->arrived = true;
                            $value = $callback($value);
                            if ($value instanceof RejectedPromise) {
                                $reject($value);
                            } else {
                                if ($value instanceof FulfilledPromise) {
                                    $value = $value->getValue();
                                }
                                $resolve($value);
                            }
                        }
                    })->then(function ($value) use ($resolve, $i) {
                        $this->resolvedValue[$i] = $value;
                        if (count($this->resolvedValue) == count($this->promises)) {
                            if ($this->arrived === false) {
                                $this->arrived = true;
                                ksort($this->resolvedValue);
                                $resolve($this->resolvedValue);
                            }
                        }
                    });
                } else if ($promise instanceof RejectedPromise) {
                    if ($this->arrived === false) {
                        $this->arrived = true;
                        $value = $callback($promise->getValue());
                        if ($value instanceof RejectedPromise) {
                            $reject($value);
                        } else {
                            if ($value instanceof FulfilledPromise) {
                                $value = $value->getValue();
                            }
                            $resolve($value);
                        }
                    }
                } else {
                    if ($promise instanceof FulfilledPromise) {
                        $promise = $promise->getValue();
                    }
                    $this->resolvedValue[$i] = $promise;
                    if (count($this->resolvedValue) == count($this->promises)) {
                        if ($this->arrived === false) {
                            $this->arrived = true;
                            ksort($this->resolvedValue);
                            $resolve($this->resolvedValue);
                        }
                    }
                }
            }
        });
    }
}