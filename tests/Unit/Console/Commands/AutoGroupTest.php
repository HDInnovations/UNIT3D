<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;

/**
 * @see \App\Console\Commands\AutoGroup
 */
class AutoGroupTest extends TestCase
{
    public function testItRunsSuccessfully()
    {
        $this->artisan('auto:group')
            ->expectsOutput('Automated User Group Command Complete')
            ->assertExitCode(0)
            ->run();
    }
}
