<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;

/**
 * @see \App\Console\Commands\AutoRecycleInvites
 */
class AutoRecycleInvitesTest extends TestCase
{
    public function testItRunsSuccessfully()
    {
        $this->artisan('auto:recycle_invites')
            ->expectsOutput('Automated Purge Unaccepted Invites Command Complete')
            ->assertExitCode(0)
            ->run();
    }
}
