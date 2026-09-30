<?php

declare(strict_types=1);

use App\Achievements\Achievement;
use App\Achievements\UserMadeComment;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    config(['achievements.auto_sync' => true]);
    app()->setLocale('en');
});

afterEach(function (): void {
    DB::connection()->disableQueryLog();
    DB::connection()->flushQueryLog();
    app()->setLocale(config('app.locale'));
});

test('synchronizing a badge in another locale does not change shared metadata or its asset identity', function (): void {
    $original = (new UserMadeComment())->getModel();
    $description = $original->description;
    $name = $original->name;
    $id = $original->id;

    app()->setLocale('cs');
    $synchronized = (new UserMadeComment())->getModel();

    expect($synchronized->id)->toBe($id)
        ->and($synchronized->name)->toBe($name)
        ->and($synchronized->description)->toBe($description);
});

test('rendering an existing badge follows the viewer locale without instantiation or database effects', function (): void {
    $details = (new UserMadeComment())->getModel();
    $storedDescription = $details->description;
    $storedName = $details->name;
    $connection = DB::connection();
    $connection->flushQueryLog();
    $connection->enableQueryLog();

    $english = Achievement::descriptionFor($details);
    app()->setLocale('cs');
    $czech = Achievement::descriptionFor($details);
    app()->setLocale('en');
    $englishAgain = Achievement::descriptionFor($details);

    expect($czech)->not->toBe($english)
        ->and($englishAgain)->toBe($english)
        ->and($details->description)->toBe($storedDescription)
        ->and($details->name)->toBe($storedName)
        ->and($connection->getQueryLog())->toBe([]);
});
