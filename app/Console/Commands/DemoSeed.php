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

namespace App\Console\Commands;

use App\Models\Torrent;
use App\Models\User;
use App\Services\Tmdb\Client\Movie;
use App\Services\Tmdb\Client\TV;
use App\Services\Tmdb\TMDBScraper;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class DemoSeed extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'demo:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds fake data for demonstration or testing purposes';

    /**
     * Execute the console command.
     *
     * @throws Exception|Throwable If there is an error during the execution of the command.
     */
    final public function handle(): void
    {
        $this->alert('Demo Seeder v2.0 (Author: Poppabear)');
        $this->warn('*** This process could take a few minutes ***');
        $this->warn('Press CTRL + C to abort');

        sleep(5);

        $abort = false;

        foreach ($this->movie_ids() as $id) {
            // Users
            $this->info('Creating user account');

            $uid = User::factory()->create([
                'chatroom_id'    => 1,
                'group_id'       => random_int(1, 20),
                'chat_status_id' => 1,
                'image'          => null,
            ])->id;

            // random boolean
            if ([false, true][random_int(0, 1)]) {
                $movie = $this->fetchMovie($id);

                // Torrents
                $this->info('Creating movie torrents for account ID #'.$uid);

                try {
                    $freeleech = ['0', '25', '50', '75', '100'];
                    $selected = random_int(0, \count($freeleech) - 1);

                    Torrent::factory()->create([
                        'user_id'        => $uid,
                        'tmdb_movie_id'  => $id,
                        'name'           => $movie['title'],
                        'description'    => $movie['overview'],
                        'category_id'    => 1,
                        'type_id'        => random_int(1, 6),
                        'resolution_id'  => random_int(1, 10),
                        'region_id'      => random_int(1, 242),
                        'distributor_id' => random_int(1, 965),
                        'free'           => $freeleech[$selected],
                        'sticky'         => false,
                        'mediainfo'      => '
Complete name                            : Double.Impact.1991.1080p.BluRay.DD+5.1.x264-LoRD.mkv
Format                                   : Matroska
Format version                           : Version 4
File size                                : 14.1 GiB
Duration                                 : 1 h 49 min
Overall bit rate                         : 18.5 Mb/s
Encoded date                             : UTC 2020-02-24 10:07:15
Writing application                      : mkvmerge v43.0.0 (\'The Quartermaster\') 64-bit
Writing library                          : libebml v1.3.10 + libmatroska v1.5.2

Video
ID                                       : 1
Format                                   : AVC
Format/Info                              : Advanced Video Codec
Format profile                           : High@L4.1
Format settings                          : CABAC / 4 Ref Frames
Format settings, CABAC                   : Yes
Format settings, Reference frames        : 4 frames
Codec ID                                 : V_MPEG4/ISO/AVC
Duration                                 : 1 h 49 min
Bit rate                                 : 17.5 Mb/s
Width                                    : 1 920 pixels
Height                                   : 1 040 pixels
Display aspect ratio                     : 1.85:1
Frame rate mode                          : Constant
Frame rate                               : 23.976 (24000/1001) FPS
Color space                              : YUV
Chroma subsampling                       : 4:2:0
Bit depth                                : 8 bits
Scan type                                : Progressive
Bits/(Pixel*Frame)                       : 0.366
Stream size                              : 13.4 GiB (94%)
Title                                    : LoRD
Writing library                          : x264 core 159 r2991+49 ~ LoRD
Encoding settings                        : cabac=1 / ref=4 / deblock=1:-3:-3 / analyse=0x3:0x133 / me=umh / subme=10 / psy=1 / fade_compensate=0.00 / psy_rd=1.10:0.00 / mixed_ref=1 / me_range=32 / chroma_me=1 / trellis=2 / 8x8dct=1 / cqm=0 / deadzone=21,11 / fast_pskip=0 / chroma_qp_offset=-2 / threads=12 / lookahead_threads=2 / sliced_threads=0 / nr=0 / decimate=0 / interlaced=0 / bluray_compat=0 / constrained_intra=0 / fgo=0 / bframes=9 / b_pyramid=2 / b_adapt=2 / b_bias=0 / direct=3 / weightb=1 / open_gop=0 / weightp=2 / keyint=250 / keyint_min=23 / scenecut=40 / intra_refresh=0 / rc_lookahead=60 / rc=2pass / mbtree=0 / bitrate=17500 / ratetol=1.0 / qcomp=0.70 / qpmin=0:0:0 / qpmax=69:69:69 / qpstep=4 / cplxblur=20.0 / qblur=0.5 / vbv_maxrate=62500 / vbv_bufsize=78125 / nal_hrd=none / filler=0 / ip_ratio=1.25 / pb_ratio=1.20 / aq=3:0.55 / aq-sensitivity=10.00 / aq-factor=1.00:1.00:1.00 / aq2=0 / aq3=0
Language                                 : English
Default                                  : Yes
Forced                                   : No

Audio
ID                                       : 2
Format                                   : E-AC-3
Format/Info                              : Enhanced AC-3
Commercial name                          : Dolby Digital Plus
Codec ID                                 : A_EAC3
Duration                                 : 1 h 49 min
Bit rate mode                            : Constant
Bit rate                                 : 1 023 kb/s
Channel(s)                               : 6 channels
Channel layout                           : L R C LFE Ls Rs
Sampling rate                            : 48.0 kHz
Frame rate                               : 93.750 FPS (512 SPF)
Compression mode                         : Lossy
Stream size                              : 803 MiB (6%)
Title                                    : DD+5.1
Language                                 : English
Service kind                             : Complete Main
Default                                  : Yes
Forced                                   : No

Text #1
ID                                       : 3
Format                                   : UTF-8
Codec ID                                 : S_TEXT/UTF8
Codec ID/Info                            : UTF-8 Plain Text
Duration                                 : 1 h 41 min
Bit rate                                 : 26 b/s
Count of elements                        : 669
Stream size                              : 19.9 KiB (0%)
Language                                 : English
Default                                  : No
Forced                                   : No

Text #2
ID                                       : 4
Format                                   : UTF-8
Codec ID                                 : S_TEXT/UTF8
Codec ID/Info                            : UTF-8 Plain Text
Duration                                 : 1 h 48 min
Bit rate                                 : 30 b/s
Count of elements                        : 872
Stream size                              : 24.1 KiB (0%)
Title                                    : SDH
Language                                 : English
Default                                  : No
Forced                                   : No

Text #3
ID                                       : 5
Format                                   : UTF-8
Codec ID                                 : S_TEXT/UTF8
Codec ID/Info                            : UTF-8 Plain Text
Duration                                 : 1 h 41 min
Bit rate                                 : 26 b/s
Count of elements                        : 659
Stream size                              : 19.9 KiB (0%)
Language                                 : French
Default                                  : No
Forced                                   : No

Text #4
ID                                       : 6
Format                                   : UTF-8
Codec ID                                 : S_TEXT/UTF8
Codec ID/Info                            : UTF-8 Plain Text
Duration                                 : 1 h 41 min
Bit rate                                 : 24 b/s
Count of elements                        : 668
Stream size                              : 18.3 KiB (0%)
Title                                    : european
Language                                 : Portuguese
Default                                  : No
Forced                                   : No

Text #5
ID                                       : 7
Format                                   : UTF-8
Codec ID                                 : S_TEXT/UTF8
Codec ID/Info                            : UTF-8 Plain Text
Duration                                 : 1 h 42 min
Bit rate                                 : 26 b/s
Count of elements                        : 765
Stream size                              : 19.5 KiB (0%)
Title                                    : latin
Language                                 : Spanish
Default                                  : No
Forced                                   : No

Menu
00:00:00.000                             : en:Logo/Title/Intro
00:03:24.037                             : en:"My Babies!"
00:09:45.918                             : en:Life In L.A.
00:13:08.621                             : en:"He\'s Your Brother!"
00:21:01.510                             : en:Fencing Benzes
00:29:05.118                             : en:Mistaken Identity
00:37:34.627                             : en:Fishing Expedition
00:42:22.039                             : en:The Drop
00:50:05.711                             : en:Bad Cognac
00:58:56.032                             : en:On the Run
01:07:33.549                             : en:Sibling Rivalry
01:17:59.174                             : en:Hotel Invasion
01:23:51.443                             : en:A Captive Audience
01:28:49.532                             : en:One-On-One Combat
01:34:15.858                             : en:Who Gets Away?
01:44:15.249                             : en:End Credits
                        ',
                        'created_at' => now(),
                        'bumped_at'  => now(),
                        'updated_at' => now(),
                    ]);
                } catch (Exception $exception) {
                    $abort = true;

                    $this->warn($exception->getMessage());

                    break;
                }
            }
        }

        foreach ($this->tv_ids() as $id) {
            // Users
            $this->info('Creating user account');

            $uid = User::factory()->create([
                'chatroom_id'    => 1,
                'group_id'       => random_int(1, 20),
                'chat_status_id' => 1,
                'image'          => null,
            ])->id;

            // random boolean
            if ([false, true][random_int(0, 1)]) {
                $tv = $this->fetchTv($id);

                // Torrents
                $this->info('Creating TV torrents for account ID #'.$uid);

                try {
                    $freeleech = ['0', '25', '50', '75', '100'];
                    $selected = random_int(0, \count($freeleech) - 1);

                    Torrent::factory()->create([
                        'user_id'        => $uid,
                        'tmdb_tv_id'     => $id,
                        'name'           => $tv['name'],
                        'description'    => $tv['overview'],
                        'category_id'    => 2,
                        'type_id'        => random_int(1, 6),
                        'resolution_id'  => random_int(1, 10),
                        'region_id'      => random_int(1, 242),
                        'distributor_id' => random_int(1, 965),
                        'free'           => $freeleech[$selected],
                        'sticky'         => false,
                        'mediainfo'      => '
Complete name                            : Double.Impact.1991.1080p.BluRay.DD+5.1.x264-LoRD.mkv
Format                                   : Matroska
Format version                           : Version 4
File size                                : 14.1 GiB
Duration                                 : 1 h 49 min
Overall bit rate                         : 18.5 Mb/s
Encoded date                             : UTC 2020-02-24 10:07:15
Writing application                      : mkvmerge v43.0.0 (\'The Quartermaster\') 64-bit
Writing library                          : libebml v1.3.10 + libmatroska v1.5.2

Video
ID                                       : 1
Format                                   : AVC
Format/Info                              : Advanced Video Codec
Format profile                           : High@L4.1
Format settings                          : CABAC / 4 Ref Frames
Format settings, CABAC                   : Yes
Format settings, Reference frames        : 4 frames
Codec ID                                 : V_MPEG4/ISO/AVC
Duration                                 : 1 h 49 min
Bit rate                                 : 17.5 Mb/s
Width                                    : 1 920 pixels
Height                                   : 1 040 pixels
Display aspect ratio                     : 1.85:1
Frame rate mode                          : Constant
Frame rate                               : 23.976 (24000/1001) FPS
Color space                              : YUV
Chroma subsampling                       : 4:2:0
Bit depth                                : 8 bits
Scan type                                : Progressive
Bits/(Pixel*Frame)                       : 0.366
Stream size                              : 13.4 GiB (94%)
Title                                    : LoRD
Writing library                          : x264 core 159 r2991+49 ~ LoRD
Encoding settings                        : cabac=1 / ref=4 / deblock=1:-3:-3 / analyse=0x3:0x133 / me=umh / subme=10 / psy=1 / fade_compensate=0.00 / psy_rd=1.10:0.00 / mixed_ref=1 / me_range=32 / chroma_me=1 / trellis=2 / 8x8dct=1 / cqm=0 / deadzone=21,11 / fast_pskip=0 / chroma_qp_offset=-2 / threads=12 / lookahead_threads=2 / sliced_threads=0 / nr=0 / decimate=0 / interlaced=0 / bluray_compat=0 / constrained_intra=0 / fgo=0 / bframes=9 / b_pyramid=2 / b_adapt=2 / b_bias=0 / direct=3 / weightb=1 / open_gop=0 / weightp=2 / keyint=250 / keyint_min=23 / scenecut=40 / intra_refresh=0 / rc_lookahead=60 / rc=2pass / mbtree=0 / bitrate=17500 / ratetol=1.0 / qcomp=0.70 / qpmin=0:0:0 / qpmax=69:69:69 / qpstep=4 / cplxblur=20.0 / qblur=0.5 / vbv_maxrate=62500 / vbv_bufsize=78125 / nal_hrd=none / filler=0 / ip_ratio=1.25 / pb_ratio=1.20 / aq=3:0.55 / aq-sensitivity=10.00 / aq-factor=1.00:1.00:1.00 / aq2=0 / aq3=0
Language                                 : English
Default                                  : Yes
Forced                                   : No

Audio
ID                                       : 2
Format                                   : E-AC-3
Format/Info                              : Enhanced AC-3
Commercial name                          : Dolby Digital Plus
Codec ID                                 : A_EAC3
Duration                                 : 1 h 49 min
Bit rate mode                            : Constant
Bit rate                                 : 1 023 kb/s
Channel(s)                               : 6 channels
Channel layout                           : L R C LFE Ls Rs
Sampling rate                            : 48.0 kHz
Frame rate                               : 93.750 FPS (512 SPF)
Compression mode                         : Lossy
Stream size                              : 803 MiB (6%)
Title                                    : DD+5.1
Language                                 : English
Service kind                             : Complete Main
Default                                  : Yes
Forced                                   : No

Text #1
ID                                       : 3
Format                                   : UTF-8
Codec ID                                 : S_TEXT/UTF8
Codec ID/Info                            : UTF-8 Plain Text
Duration                                 : 1 h 41 min
Bit rate                                 : 26 b/s
Count of elements                        : 669
Stream size                              : 19.9 KiB (0%)
Language                                 : English
Default                                  : No
Forced                                   : No

Text #2
ID                                       : 4
Format                                   : UTF-8
Codec ID                                 : S_TEXT/UTF8
Codec ID/Info                            : UTF-8 Plain Text
Duration                                 : 1 h 48 min
Bit rate                                 : 30 b/s
Count of elements                        : 872
Stream size                              : 24.1 KiB (0%)
Title                                    : SDH
Language                                 : English
Default                                  : No
Forced                                   : No

Text #3
ID                                       : 5
Format                                   : UTF-8
Codec ID                                 : S_TEXT/UTF8
Codec ID/Info                            : UTF-8 Plain Text
Duration                                 : 1 h 41 min
Bit rate                                 : 26 b/s
Count of elements                        : 659
Stream size                              : 19.9 KiB (0%)
Language                                 : French
Default                                  : No
Forced                                   : No

Text #4
ID                                       : 6
Format                                   : UTF-8
Codec ID                                 : S_TEXT/UTF8
Codec ID/Info                            : UTF-8 Plain Text
Duration                                 : 1 h 41 min
Bit rate                                 : 24 b/s
Count of elements                        : 668
Stream size                              : 18.3 KiB (0%)
Title                                    : european
Language                                 : Portuguese
Default                                  : No
Forced                                   : No

Text #5
ID                                       : 7
Format                                   : UTF-8
Codec ID                                 : S_TEXT/UTF8
Codec ID/Info                            : UTF-8 Plain Text
Duration                                 : 1 h 42 min
Bit rate                                 : 26 b/s
Count of elements                        : 765
Stream size                              : 19.5 KiB (0%)
Title                                    : latin
Language                                 : Spanish
Default                                  : No
Forced                                   : No

Menu
00:00:00.000                             : en:Logo/Title/Intro
00:03:24.037                             : en:"My Babies!"
00:09:45.918                             : en:Life In L.A.
00:13:08.621                             : en:"He\'s Your Brother!"
00:21:01.510                             : en:Fencing Benzes
00:29:05.118                             : en:Mistaken Identity
00:37:34.627                             : en:Fishing Expedition
00:42:22.039                             : en:The Drop
00:50:05.711                             : en:Bad Cognac
00:58:56.032                             : en:On the Run
01:07:33.549                             : en:Sibling Rivalry
01:17:59.174                             : en:Hotel Invasion
01:23:51.443                             : en:A Captive Audience
01:28:49.532                             : en:One-On-One Combat
01:34:15.858                             : en:Who Gets Away?
01:44:15.249                             : en:End Credits
                        ',
                        'created_at' => now(),
                        'bumped_at'  => now(),
                        'updated_at' => now(),
                    ]);
                } catch (Exception $exception) {
                    $abort = true;

                    $this->warn($exception->getMessage());

                    break;
                }
            }

            if ($abort) {
                break;
            }
        }

        if ($abort) {
            $this->error('Aborted ...');
            $this->alert('Demo data was only PARTIALLY seeded! This is likely due to an API request timeout.');
            $this->alert('Ensure TMDB api key is set and run "php artisan config:clear"');
        } else {
            $this->alert('Demo data has been successfully seeded!');
        }
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    private function fetchMovie(int $id): mixed
    {
        sleep(2);
        $tmdbScraper = new TMDBScraper();
        $tmdbScraper->movie($id);

        return (new Movie($id))->data;
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    private function fetchTv(int $id): mixed
    {
        sleep(2);
        $tmdbScraper = new TMDBScraper();
        $tmdbScraper->tv($id);

        return (new TV($id))->data;
    }

    /**
     * @return array<int>
     */
    private function movie_ids(): array
    {
        return [
            893397,
            783215,
            570731,
            679014,
            239895,
            413782,
            138038,
            1115579,
            22084,
            302827,
            640393,
            253267,
            1291436,
            1144962,
            977871,
            1081709,
            252293,
            376252,
            918287,
            1026999,
            1294765,
            923579,
            422044,
            710268,
            1124136,
            717088,
            1163186,
            1073274,
            1102786,
            354148,
            533991,
            134508,
            131012,
            467540,
            353495,
            1020896,
            895718,
            125290,
            13245,
            29829,
            639720,
            75704,
            432956,
            62489,
            12161,
            77146,
            239398,
            686941,
            768076,
            1059813,
        ];
    }

    /**
     * @return array<int>
     */
    private function tv_ids(): array
    {
        return [
            201568,
            256057,
            251668,
            71495,
            68728,
            88258,
            74866,
            70508,
            207837,
            255293,
            35468,
            213359,
            4333,
            236316,
            247674,
            249018,
            228549,
            240310,
            211169,
            212204,
            61379,
            1246,
            80884,
            196944,
            232644,
            68597,
            58761,
            46848,
            228958,
            6390,
            226745,
            4706,
            114183,
            39952,
            96128,
            93297,
            122725,
            253254,
            249301,
            238126,
            233327,
            65609,
            128301,
            37528,
            204959,
            208694,
            45343,
            1141,
            124972,
            156933,
        ];
    }
}
