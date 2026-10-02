<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

function trackerQueueRedis(): Illuminate\Redis\Connections\Connection
{
    return Redis::connection(config('queue.connections.redis.connection'));
}

function recordFlush(string $command, int $finishedAt, bool $failed = false): void
{
    Redis::connection('cache')->hset(config('cache.prefix').':capacity:metrics', 'command:'.$command.':finished_at', (string) $finishedAt);
    Redis::connection('cache')->hset(config('cache.prefix').':capacity:metrics', 'command:'.$command.':last_failed', $failed ? '1' : '0');
}

function recordHealthyFlushes(): void
{
    foreach (['auto:upsert_peers', 'auto:upsert_histories', 'auto:upsert_announces'] as $command) {
        recordFlush($command, time());
    }
}

beforeEach(function (): void {
    config(['capacity.health.token' => 'health-test-token']);
    Redis::connection('cache')->del(config('cache.prefix').':capacity:metrics');
    trackerQueueRedis()->del('queues:tracker');
});

afterEach(function (): void {
    Redis::connection('cache')->del(config('cache.prefix').':capacity:metrics');
    trackerQueueRedis()->del('queues:tracker');
});

test('the endpoint does not exist without a configured token', function (): void {
    config(['capacity.health.token' => '']);
    recordHealthyFlushes();

    $this->withToken('anything')->getJson(route('health.tracker'))->assertNotFound();
});

test('a wrong or missing token is indistinguishable from a missing endpoint', function (): void {
    recordHealthyFlushes();

    $this->withToken('wrong')->getJson(route('health.tracker'))->assertNotFound();
    $this->getJson(route('health.tracker'))->assertNotFound();
});

test('progressing flushes and an empty queue report healthy', function (): void {
    recordHealthyFlushes();

    $this->withToken('health-test-token')->getJson(route('health.tracker'))
        ->assertOk()
        ->assertExactJson(['healthy' => true, 'failures' => []]);
});

test('a flush that stopped finishing is reported as stalled', function (): void {
    recordHealthyFlushes();
    recordFlush('auto:upsert_histories', time() - 600);

    $this->withToken('health-test-token')->getJson(route('health.tracker'))
        ->assertStatus(503)
        ->assertJsonPath('failures', ['flush_stalled:auto:upsert_histories']);
});

test('a flush whose last run failed is reported even when recent', function (): void {
    recordHealthyFlushes();
    recordFlush('auto:upsert_peers', time(), failed: true);

    $this->withToken('health-test-token')->getJson(route('health.tracker'))
        ->assertStatus(503)
        ->assertJsonPath('failures', ['flush_stalled:auto:upsert_peers']);
});

test('a scheduler that never ran is reported for every flush', function (): void {
    $this->withToken('health-test-token')->getJson(route('health.tracker'))
        ->assertStatus(503)
        ->assertJsonPath('failures', [
            'flush_stalled:auto:upsert_peers',
            'flush_stalled:auto:upsert_histories',
            'flush_stalled:auto:upsert_announces',
        ]);
});

test('an old pending tracker job is reported as queue lag', function (): void {
    recordHealthyFlushes();
    trackerQueueRedis()->rpush('queues:tracker', json_encode(['createdAt' => time() - 300]));

    $this->withToken('health-test-token')->getJson(route('health.tracker'))
        ->assertStatus(503)
        ->assertJsonPath('failures', ['tracker_queue_lagging']);
});

test('a recently queued tracker job within the threshold is healthy', function (): void {
    recordHealthyFlushes();
    trackerQueueRedis()->rpush('queues:tracker', json_encode(['createdAt' => time() - 5]));

    $this->withToken('health-test-token')->getJson(route('health.tracker'))
        ->assertExactJson(['healthy' => true, 'failures' => []]);
});

test('a permanently failed tracker job keeps the check failing until handled', function (): void {
    recordHealthyFlushes();
    DB::table('failed_jobs')->insert([
        'uuid'       => (string) Str::uuid(),
        'connection' => 'redis',
        'queue'      => 'tracker',
        'payload'    => '{}',
        'exception'  => 'MaxAttemptsExceededException',
        'failed_at'  => now()->subDays(3),
    ]);

    $this->withToken('health-test-token')->getJson(route('health.tracker'))
        ->assertStatus(503)
        ->assertJsonPath('failures', ['failed_tracker_jobs']);
});
