<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The public layout calls @vite(...); tests don't run an asset
        // build, so avoid requiring a manifest file to exist on disk.
        $this->withoutVite();
    }
}
