<?php

/**
 * @see \App\Console\Commands\TestMailSettings
 */
it('runs successfully', function () {
    $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

    $this->artisan('test:email')
        ->assertExitCode(0)
        ->run();

    // TODO: perform additional assertions to ensure the command behaved as expected
});
