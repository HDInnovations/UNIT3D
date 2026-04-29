<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invites', function (Blueprint $table): void {
            $table->timestamp('failed_at')->nullable()->after('accepted_at');
            $table->string('failure_reason')->nullable()->after('failed_at');
        });
    }
};
