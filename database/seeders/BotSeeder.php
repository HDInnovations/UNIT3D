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
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace Database\Seeders;

use App\Models\Bot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BotSeeder extends Seeder
{
    public function run(): void
    {
        Bot::upsert([
            [
                'name'     => 'SystemBot',
                'emoji'    => '1f916',
                'command'  => 'systembot',
                'position' => 1,
                'color'    => '#f1c40f',
                'icon'     => 'fab fa-android',
                'help'     => '{me} v0.1 Help -- Notes about / ! and @{me} tab

All [b]echo[/b] commands begin with / and echo output to current tab.
All [b]room[/b] commands begin with ! and echo output for all in current room.
All [b]private[/b] commands begin with @ or are sent via the @{me} tab. If sent via @{me} tab, no (! | / | @){command} prefix is needed.

Echo & Private commands:
/msg [username] [message] - Send a quick private shoutbox message.
/gift [username] [amount] [message] - Send a quick gift of [amount] to [username] with attached [message].

Available Bots:
{bots}',
                'is_protected' => true,
                'is_systembot' => true,
                'is_nerdbot'   => false,
            ],
            [
                'name'     => 'NerdBot',
                'emoji'    => '1f913',
                'command'  => 'nerdbot',
                'position' => 2,
                'color'    => '#f1c40f',
                'icon'     => 'fab fa-android',
                'help'     => '{me} v0.1 Help -- Notes about / ! and @{me} tab

All [b]echo[/b] commands begin with / and echo output to current tab.
All [b]room[/b] commands begin with ! and echo output for all in current room.
All [b]private[/b] commands begin with @ or are sent via the @{me} tab. If sent via @{me} tab, no (! | / | @){command} prefix is needed.

Public, Echo & Private commands:

(! | / | @)nerdbot help - Displays this help file.

(! | / | @)nerdbot banker - Displays who is currently top BON holder.
(! | / | @)nerdbot bans - Displays # of bans from site in past 24 hours.
(! | / | @)nerdbot doubleupload - Displays # of double upload torrents available on the site.
(! | / | @)nerdbot freeleech - Displays # of freeleech torrents available on the site.
(! | / | @)nerdbot king - Displays who is the one and only king.
(! | / | @)nerdbot logins - Displays # of log ins to site in past 24 hours.
(! | / | @)nerdbot peers - Displays # of peers for torrents on the site.
(! | / | @)nerdbot registrations - Displays # of registrations to site in past 24 hours.
(! | / | @)nerdbot uploads - Displays # of uploads to site in past 24 hours.
(! | / | @)nerdbot warnings - Displays # of H&R warnings issued on site in past 24 hours.

(! | / | @)nerdbot leeched - Displays the top leeched torrent on the site.
(! | / | @)nerdbot seeded - Displays the seeded torrent on the site.
(! | / | @)nerdbot snatched - Displays the top snatched torrent on the site.

Echo & Private commands:

None.

(All NerdBot statistics are cached for 60 minutes)',
                'is_protected' => true,
                'is_systembot' => false,
                'is_nerdbot'   => true,
            ],
        ], ['id'], ['updated_at' => DB::raw('updated_at')]);
    }
}
