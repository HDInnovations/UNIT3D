<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dual-stack support for UNIT3D-Announce.
 *
 * Adds per-family IPv4/IPv6 endpoint columns to `peers`. The Rust announcer
 * reads/writes these columns directly (see src/queue/peer_update.rs and the
 * .sqlx query cache), storing the address as raw bytes via INET6_ATON /
 * ip_to_bytes(). Kept raw here (VARBINARY) rather than via Schema::binary()
 * so the exact widths match what the announcer expects (4 bytes for IPv4,
 * 16 for IPv6). Guarded so it is safe to re-run on a DB that already has them.
 */
return new class extends Migration
{
    /**
     * @var array<string, string>
     */
    private array $columns = [
        'ipv4'             => 'VARBINARY(4) NULL DEFAULT NULL',
        'ipv4_port'        => 'SMALLINT UNSIGNED NULL DEFAULT NULL',
        'ipv4_connectable' => 'BOOLEAN NULL DEFAULT NULL',
        'ipv6'             => 'VARBINARY(16) NULL DEFAULT NULL',
        'ipv6_port'        => 'SMALLINT UNSIGNED NULL DEFAULT NULL',
        'ipv6_connectable' => 'BOOLEAN NULL DEFAULT NULL',
    ];

    public function up(): void
    {
        foreach ($this->columns as $name => $definition) {
            if (!Schema::hasColumn('peers', $name)) {
                DB::statement("ALTER TABLE `peers` ADD COLUMN `{$name}` {$definition}");
            }
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->columns) as $name) {
            if (Schema::hasColumn('peers', $name)) {
                DB::statement("ALTER TABLE `peers` DROP COLUMN `{$name}`");
            }
        }
    }
};
