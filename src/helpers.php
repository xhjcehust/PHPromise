<?php

namespace PHPromise\Promise;

function race($promises)
{
    return new Race($promises);
}

function all($promises)
{
    return new All($promises);
}