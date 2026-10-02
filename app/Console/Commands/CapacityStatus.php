<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\FpmStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Throwable;

final class CapacityStatus extends Command
{
    protected $signature = 'capacity:status {--json : Emit machine-readable status}';

    protected $description = 'Inspect queue lag, tracker batches, command timings and private FPM capacity';

    public function handle(FpmStatus $fpm): int
    {
        $result = ['timestamp' => now()->toIso8601String(), 'queues' => [], 'batches' => [], 'metrics' => [], 'fpm' => [], 'errors' => []];

        try {
            DB::select('SELECT 1');
            $result['database'] = 'reachable';
        } catch (Throwable $error) {
            $result['database'] = 'unavailable';
            $result['errors']['database'] = $error::class;
        }

        try {
            $queue = Queue::connection('redis');
            $redis = Redis::connection(config('queue.connections.redis.connection'));

            foreach (['tracker', 'default'] as $name) {
                $key = 'queues:'.$name;
                $created = $queue->creationTimeOfOldestPendingJob($name);
                $result['queues'][$name] = [
                    'pending'                    => $redis->llen($key),
                    'delayed'                    => $redis->zcard($key.':delayed'),
                    'reserved'                   => $redis->zcard($key.':reserved'),
                    'oldest_pending_age_seconds' => $created === null ? null : max(0, time() - $created),
                ];
            }

            foreach (['peers', 'histories', 'announces'] as $batch) {
                $result['batches'][$batch] = Redis::connection('announce')->llen(config('cache.prefix').':'.$batch.':batch');
            }
            $result['metrics'] = Redis::connection('cache')->hgetall(config('cache.prefix').':capacity:metrics');
        } catch (Throwable $error) {
            $result['errors']['redis'] = $error::class;
        }

        foreach (config('capacity.fpm_ports') as $pool => $port) {
            try {
                $result['fpm'][$pool] = $fpm->read(config('capacity.fpm_host'), $port);
            } catch (Throwable $error) {
                $result['errors']['fpm:'.$pool] = $error::class;
            }
        }

        $this->line(json_encode($result, JSON_THROW_ON_ERROR | ($this->option('json') ? 0 : JSON_PRETTY_PRINT)));

        return $result['errors'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
