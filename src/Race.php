<?php

namespace PHPromise\Promise;

class Race
{
    private $arrived = false;
    private $promises;

    public function __construct($promises)
    {
        if (is_array($promises)) {
            $this->promises = $promises;
        } else {
            $this->promises = [];
        }
    }

    public function then($callback)
    {
        if ($this->promises === []) {
            $value = $callback(null);
            return new Promise(function ($resolve, $reject) use ($value) {
                $resolve($value);
            });
        }

        return new Promise(function ($resolve, $reject) use ($callback) {
            foreach ($this->promises as $promise) {
                if ($promise instanceof Promise) {
                    $promise->then(function ($value) use ($callback, $resolve, $reject) {
                        if ($this->arrived === false) {
                            $this->arrived = true;
                            $value = call_user_func($callback, $value);
                            if ($value instanceof FulfilledPromise) {
                                $resolve($value->getValue());
                            } else if ($value instanceof RejectedPromise) {
                                $reject($value->getValue());
                            } else {
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
                    }
                } else {
                    if ($this->arrived === false) {
                        $this->arrived = true;
                        if ($promise instanceof FulfilledPromise) {
                            $promise = $promise->getValue();
                        }
                        $value = call_user_func($callback, $promise);
                        if ($value instanceof FulfilledPromise) {
                            $resolve($value->getValue());
                        } else if ($value instanceof RejectedPromise) {
                            $reject($value->getValue());
                        } else {
                            $resolve($value);
                        }
                    }
                }
            }
        });
    }

    public function otherwise($callback)
    {
        if ($this->promises === []) {
            return new Promise(function ($resolve, $reject) {
                $resolve(null);
            });
        }

        return new Promise(function ($resolve, $reject) use ($callback) {
            foreach ($this->promises as $promise) {
                if ($promise instanceof Promise) {
                    $promise->otherwise(function ($value) use ($callback, $resolve, $reject) {
                        if ($this->arrived === false) {
                            $this->arrived = true;
                            $value = $callback($value);
                            if ($value instanceof RejectedPromise) {
                                $reject($value->getValue());
                            } else {
                                if ($value instanceof FulfilledPromise) {
                                    $value = $value->getValue();
                                }
                                $resolve($value);
                            }
                        }
                    })->then(function ($value) use ($resolve, $reject) {
                        if ($this->arrived === false) {
                            $this->arrived = true;
                            $resolve($value);
                        }
                    });
                } else if ($promise instanceof RejectedPromise) {
                    if ($this->arrived === false) {
                        $this->arrived = true;
                        $value = $callback($promise->getValue());
                        if ($value instanceof RejectedPromise) {
                            $reject($value->getValue());
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
                    if ($this->arrived === false) {
                        $this->arrived = true;
                        $resolve($promise);
                    }
                }
            }
        });
    }
}