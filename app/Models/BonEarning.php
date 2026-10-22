<?php

declare(strict_types=1);

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Models;

use AllowDynamicProperties;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\BonEarning.
 *
 * @property int                                                                                                            $id
 * @property int                                                                                                            $position
 * @property '1'|'age'|'size'|'seeders'|'leechers'|'times_completed'|'seedtime'|'personal_release'|'internal'|'connectable' $variable
 * @property float                                                                                                          $multiplier
 * @property 'append'|'multiply'                                                                                            $operation
 * @property string                                                                                                         $name
 * @property string                                                                                                         $description
 */
#[AllowDynamicProperties]
final class BonEarning extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * Get the conditions for the bon earning.
     *
     * @return HasMany<BonEarningCondition, $this>
     */
    public function conditions(): HasMany
    {
        return $this->hasMany(BonEarningCondition::class);
    }

    /**
     * Calculate total hourly bonus earnings for a given user.
     */
    public static function calculateHourlyEarningsForUser(int $userId): float
    {
        $bonEarnings = self::query()->with('conditions')->orderBy('position')->get();

        $earningsQuery = str_repeat('(', $bonEarnings->count()).'0';

        foreach ($bonEarnings as $bonEarning) {
            // Raw bindings are fine since all database values are either enums or numeric
            $conditionQuery = '1=1';

            foreach ($bonEarning->conditions as $condition) {
                $conditionQuery .= ' AND '.match ($condition->operand1) {
                    '1'                => '1',
                    'age'              => 'TIMESTAMPDIFF(SECOND, torrents.created_at, NOW())',
                    'size'             => 'torrents.size',
                    'seeders'          => 'torrents.seeders',
                    'leechers'         => 'torrents.leechers',
                    'times_completed'  => 'torrents.times_completed',
                    'internal'         => 'torrents.internal',
                    'personal_release' => 'torrents.personal_release',
                    'type_id'          => 'torrents.type_id',
                    'seedtime'         => 'history.seedtime',
                    'connectable'      => 'MAX(peers.connectable)',
                }.' '.$condition->operator.' '.$condition->operand2;
            }

            $variable = match ($bonEarning->variable) {
                '1'                => '1',
                'age'              => 'TIMESTAMPDIFF(SECOND, torrents.created_at, NOW())',
                'size'             => 'torrents.size',
                'seeders'          => 'torrents.seeders',
                'leechers'         => 'torrents.leechers',
                'times_completed'  => 'torrents.times_completed',
                'internal'         => 'torrents.internal',
                'personal_release' => 'torrents.personal_release',
                'seedtime'         => 'history.seedtime',
                'connectable'      => 'MAX(peers.connectable)',
            };

            $earningsQuery .= match ($bonEarning->operation) {
                'append'   => " + CASE WHEN ({$conditionQuery}) THEN {$variable} * {$bonEarning->multiplier} ELSE 0 END)",
                'multiply' => " * CASE WHEN ({$conditionQuery}) THEN {$variable} * {$bonEarning->multiplier} ELSE 1 END)",
            };
        }

        $query = DB::table('peers')
            ->select([
                'peers.torrent_id',
                DB::raw("({$earningsQuery}) AS hourly_earnings"),
            ])
            ->join('history', fn ($join) => $join->on('history.torrent_id', '=', 'peers.torrent_id')->on('history.user_id', '=', 'peers.user_id'))
            ->join('torrents', 'peers.torrent_id', '=', 'torrents.id')
            ->where('peers.seeder', '=', true)
            ->where('peers.active', '=', true)
            ->where('peers.user_id', '=', $userId)
            ->groupBy([
                'peers.torrent_id',
                'peers.user_id',
                'torrents.name',
                'torrents.type_id',
                'torrents.created_at',
                'torrents.size',
                'torrents.seeders',
                'torrents.leechers',
                'torrents.times_completed',
                'torrents.internal',
                'torrents.personal_release',
                'history.seedtime',
            ]);

        return (float) (DB::query()->fromSub($query, 'earnings_per_torrent')->sum('hourly_earnings') ?? 0);
    }
}
