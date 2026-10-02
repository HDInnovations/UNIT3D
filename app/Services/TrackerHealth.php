<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Throwable;

/**
 * Decides whether announce accounting is actually progressing, not merely
 * whether the site answers HTTP. Each failed check names what an operator
 * must look at; see LOCAL_PRODUCTION.md "Tracker health alerts".
 */
final class TrackerHealth
{
    /** Batch flushes the scheduler runs every five seconds, traffic or not. */
    private const array FLUSH_COMMANDS = ['auto:upsert_peers', 'auto:upsert_histories', 'auto:upsert_announces'];

    /**
     * @return array{healthy: bool, failures: list<string>}
     */
    public function check(): array
    {
        $failures = [];

        try {
            DB::select('SELECT 1');

            if (DB::table('failed_jobs')->where('queue', '=', 'tracker')->exists()) {
                $failures[] = 'failed_tracker_jobs';
            }
        } catch (Throwable) {
            $failures[] = 'database_unavailable';
        }

        try {
            $created = Queue::connection('redis')->creationTimeOfOldestPendingJob('tracker');

            if ($created !== null && time() - $created > config('capacity.health.max_queue_age_seconds')) {
                $failures[] = 'tracker_queue_lagging';
            }

            $metrics = Redis::connection('cache')->hgetall(config('cache.prefix').':capacity:metrics');

            foreach (self::FLUSH_COMMANDS as $command) {
                $finishedAt = (int) ($metrics['command:'.$command.':finished_at'] ?? 0);

                if (time() - $finishedAt > config('capacity.health.max_flush_age_seconds')
                    || ($metrics['command:'.$command.':last_failed'] ?? '0') !== '0'
                ) {
                    $failures[] = 'flush_stalled:'.$command;
                }
            }
        } catch (Throwable) {
            $failures[] = 'redis_unavailable';
        }

        return ['healthy' => $failures === [], 'failures' => $failures];
    }
}
