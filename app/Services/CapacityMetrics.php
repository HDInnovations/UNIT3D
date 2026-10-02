<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Throwable;

final class CapacityMetrics
{
    private array $commands = [];
    private array $jobs = [];

    public function register(): void
    {
        Event::listen(CommandStarting::class, function (CommandStarting $event): void {
            if (\in_array($event->command, ['auto:upsert_peers', 'auto:upsert_histories', 'auto:upsert_announces', 'auto:sync_peers'], true)) {
                $this->commands[$event->command] = hrtime(true);

                try {
                    Redis::connection('cache')->hset(config('cache.prefix').':capacity:metrics', 'command:'.$event->command.':started_at', (string) time());
                } catch (Throwable $error) {
                    Log::warning('Capacity metrics unavailable', ['exception' => $error::class]);
                }
            }
        });
        Event::listen(CommandFinished::class, function (CommandFinished $event): void {
            if (isset($this->commands[$event->command])) {
                $this->record('command:'.$event->command, $this->commands[$event->command], $event->exitCode !== 0);
                unset($this->commands[$event->command]);
            }
        });
        Event::listen(JobProcessing::class, function (JobProcessing $event): void {
            $this->jobs[spl_object_id($event->job)] = hrtime(true);
        });
        Event::listen(JobProcessed::class, fn (JobProcessed $event) => $this->finishJob($event->job, false));
        Event::listen(JobExceptionOccurred::class, fn (JobExceptionOccurred $event) => $this->finishJob($event->job, true));
    }

    private function finishJob($job, bool $failed): void
    {
        $id = spl_object_id($job);

        if (isset($this->jobs[$id])) {
            $this->record('queue:'.$job->getQueue(), $this->jobs[$id], $failed);
            unset($this->jobs[$id]);
        }
    }

    private function record(string $name, int $started, bool $failed): void
    {
        $milliseconds = (hrtime(true) - $started) / 1_000_000;

        try {
            Redis::connection('cache')->eval(<<<'LUA'
local p = ARGV[1]
local ms = tonumber(ARGV[2])
redis.call('HSET', KEYS[1], p .. ':last_ms', ms, p .. ':finished_at', ARGV[3], p .. ':last_failed', ARGV[4])
redis.call('HINCRBY', KEYS[1], p .. ':count', 1)
redis.call('HINCRBYFLOAT', KEYS[1], p .. ':total_ms', ms)
redis.call('HINCRBY', KEYS[1], p .. ':failures', ARGV[4])
local previous = tonumber(redis.call('HGET', KEYS[1], p .. ':max_ms') or '0')
if ms > previous then redis.call('HSET', KEYS[1], p .. ':max_ms', ms) end
return 1
LUA, 1, config('cache.prefix').':capacity:metrics', $name, (string) $milliseconds, (string) time(), $failed ? '1' : '0');
        } catch (Throwable $error) {
            // Monitoring must not make a successfully committed tracker job retry.
            Log::warning('Capacity metrics unavailable', ['exception' => $error::class]);
        }
    }
}
