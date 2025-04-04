# Hardcoded Strings Analysis Report

## Summary

| Metric | Value |
| --- | --- |
| Total Blade files scanned | 426 |
| Files with hardcoded strings | 169 (39.67% of all files) |
| Total hardcoded strings found | 812 |
| Average hardcoded strings per affected file | 4.8 |

## Hardcoded strings by category

| Category | Files | Strings | % of Total |
| --- | --- | --- | --- |
| views | 169 | 812 | 100% |

## Top 10 files with most hardcoded strings

| File | Hardcoded Strings |
| --- | --- |
| `resources/views/Staff/dashboard/index.blade.php` | 48 |
| `resources/views/user/profile/show.blade.php` | 31 |
| `resources/views/stats/yearly_overviews/show.blade.php` | 21 |
| `resources/views/livewire/torrent-search.blade.php` | 20 |
| `resources/views/Staff/group/create.blade.php` | 19 |
| `resources/views/Staff/group/edit.blade.php` | 19 |
| `resources/views/Staff/group/index.blade.php` | 19 |
| `resources/views/livewire/bbcode-input.blade.php` | 19 |
| `resources/views/torrent/partials/tools.blade.php` | 18 |
| `resources/views/livewire/top10.blade.php` | 17 |

## Detailed Analysis

### `resources/views/Staff/announce/index.blade.php` (1 strings)

- **Line 13**: "`Announces`"
  - Suggested key: `staff.announces`
  - Context: `    <li class="breadcrumb--active">Announces</li>`

### `resources/views/Staff/automatic-torrent-freeleech/create.blade.php` (4 strings)

- **Line 26**: "`Add An Automatic Torrent Freeleech`"
  - Suggested key: `staff.add_an_automatic_torrent_freel`
  - Context: `        <h2 class="panel__heading">Add An Automatic Torrent Freeleech</h2>`

- **Line 79**: "`Any`"
  - Suggested key: `staff.any`
  - Context: `                        <option hidden selected disabled value="">Any</option>`

- **Line 96**: "`Any`"
  - Suggested key: `staff.any`
  - Context: `                        <option hidden disabled selected value="">Any</option>`

- **Line 112**: "`Any`"
  - Suggested key: `staff.any`
  - Context: `                        <option hidden disabled selected value="">Any</option>`

### `resources/views/Staff/automatic-torrent-freeleech/edit.blade.php` (2 strings)

- **Line 102**: "`Any`"
  - Suggested key: `staff.any`
  - Context: `                        <option hidden disabled selected value="">Any</option>`

- **Line 118**: "`Any`"
  - Suggested key: `staff.any`
  - Context: `                        <option hidden disabled selected value="">Any</option>`

### `resources/views/Staff/automatic-torrent-freeleech/index.blade.php` (6 strings)

- **Line 9**: "`Automatic torrent freeleeches`"
  - Suggested key: `staff.automatic_torrent_freeleeches`
  - Context: `    <li class="breadcrumb--active">Automatic torrent freeleeches</li>`

- **Line 17**: "`Automatic Torrent Freeleeches`"
  - Suggested key: `staff.automatic_torrent_freeleeches`
  - Context: `            <h2 class="panel__heading">Automatic Torrent Freeleeches</h2>`

- **Line 32**: "`Name Regex`"
  - Suggested key: `staff.name_regex`
  - Context: `                        <th>Name Regex</th>`

- **Line 33**: "`Min. Torrent Size`"
  - Suggested key: `staff.min_torrent_size`
  - Context: `                        <th>Min. Torrent Size</th>`

- **Line 37**: "`Freeleech Percentage`"
  - Suggested key: `staff.freeleech_percentage`
  - Context: `                        <th>Freeleech Percentage</th>`

- **Line 103**: "`No automated torrent freeleeches`"
  - Suggested key: `staff.no_automated_torrent_freeleech`
  - Context: `                            <td colspan="10">No automated torrent freeleeches</td>`

### `resources/views/Staff/ban/index.blade.php` (1 strings)

- **Line 75**: "`No bans`"
  - Suggested key: `staff.no_bans`
  - Context: `                            <td colspan="7">No bans</td>`

### `resources/views/Staff/blacklist/clients/index.blade.php` (2 strings)

- **Line 15**: "`Blacklisted Clients`"
  - Suggested key: `staff.blacklisted_clients`
  - Context: `            <h2 class="panel__heading">Blacklisted Clients</h2>`

- **Line 32**: "`Peer ID Prefix`"
  - Suggested key: `staff.peer_id_prefix`
  - Context: `                        <th>Peer ID Prefix</th>`

### `resources/views/Staff/bon_earning/create.blade.php` (12 strings)

- **Line 85**: "`1 (Constant)`"
  - Suggested key: `staff.1_constant`
  - Context: `                        <option class="form__option" value="1">1 (Constant)</option>`

- **Line 114**: "`Variable`"
  - Suggested key: `staff.variable`
  - Context: `                    <label class="form__label form__label--floating" for="autocat">Variable</label>`

- **Line 140**: "`Append`"
  - Suggested key: `staff.append`
  - Context: `                        <option class="form__option" value="append">Append</option>`

- **Line 141**: "`Multiply`"
  - Suggested key: `staff.multiply`
  - Context: `                        <option class="form__option" value="multiply">Multiply</option>`

- **Line 147**: "`Conditions`"
  - Suggested key: `staff.conditions`
  - Context: `                <h3>Conditions</h3>`

- **Line 158**: "`1 (Constant)`"
  - Suggested key: `staff.1_constant`
  - Context: `                                <option class="form__option" value="1">1 (Constant)</option>`

- **Line 205**: "`&amp;lt;`"
  - Suggested key: `staff.lt`
  - Context: `                                <option class="form__option" value="<"><</option>`

- **Line 206**: "`&quot;&gt;&amp;gt;`"
  - Suggested key: `staff.gt`
  - Context: `                                <option class="form__option" value=">">></option>`

- **Line 207**: "`&amp;leq;`"
  - Suggested key: `staff.leq`
  - Context: `                                <option class="form__option" value="<=">≤</option>`

- **Line 208**: "`=&quot;&gt;&amp;geq;`"
  - Suggested key: `staff.geq`
  - Context: `                                <option class="form__option" value=">=">≥</option>`

- **Line 209**: "`&amp;equals;`"
  - Suggested key: `staff.equals`
  - Context: `                                <option class="form__option" value="=">=</option>`

- **Line 210**: "`&quot;&gt;&amp;ne;`"
  - Suggested key: `staff.ne`
  - Context: `                                <option class="form__option" value="<>">≠</option>`

### `resources/views/Staff/bon_earning/edit.blade.php` (2 strings)

- **Line 149**: "`Variable`"
  - Suggested key: `staff.variable`
  - Context: `                    <label class="form__label form__label--floating" for="variable">Variable</label>`

- **Line 194**: "`Conditions`"
  - Suggested key: `staff.conditions`
  - Context: `                <h3>Conditions</h3>`

### `resources/views/Staff/bon_earning/index.blade.php` (5 strings)

- **Line 35**: "`Variable`"
  - Suggested key: `staff.variable`
  - Context: `                        <th>Variable</th>`

- **Line 36**: "`Operation`"
  - Suggested key: `staff.operation`
  - Context: `                        <th>Operation</th>`

- **Line 37**: "`Multiplier`"
  - Suggested key: `staff.multiplier`
  - Context: `                        <th>Multiplier</th>`

- **Line 38**: "`Conditions`"
  - Suggested key: `staff.conditions`
  - Context: `                        <th>Conditions</th>`

- **Line 132**: "`No conditions`"
  - Suggested key: `staff.no_conditions`
  - Context: `                                        <li>No conditions</li>`

### `resources/views/Staff/category/create.blade.php` (6 strings)

- **Line 72**: "`Movie metadata`"
  - Suggested key: `staff.movie_metadata`
  - Context: `                        <option class="form__option" value="movie">Movie metadata</option>`

- **Line 73**: "`TV metadata`"
  - Suggested key: `staff.tv_metadata`
  - Context: `                        <option class="form__option" value="tv">TV metadata</option>`

- **Line 74**: "`Game metadata`"
  - Suggested key: `staff.game_metadata`
  - Context: `                        <option class="form__option" value="game">Game metadata</option>`

- **Line 75**: "`Music metadata`"
  - Suggested key: `staff.music_metadata`
  - Context: `                        <option class="form__option" value="music">Music metadata</option>`

- **Line 76**: "`No metadata`"
  - Suggested key: `staff.no_metadata`
  - Context: `                        <option class="form__option" value="no">No metadata</option>`

- **Line 78**: "`Meta`"
  - Suggested key: `staff.meta`
  - Context: `                    <label class="form__label form__label--floating" for="meta">Meta</label>`

### `resources/views/Staff/category/edit.blade.php` (1 strings)

- **Line 109**: "`Meta`"
  - Suggested key: `staff.meta`
  - Context: `                    <label class="form__label form__label--floating" for="meta">Meta</label>`

### `resources/views/Staff/category/index.blade.php` (6 strings)

- **Line 36**: "`Movie Meta`"
  - Suggested key: `staff.movie_meta`
  - Context: `                        <th>Movie Meta</th>`

- **Line 37**: "`TV Meta`"
  - Suggested key: `staff.tv_meta`
  - Context: `                        <th>TV Meta</th>`

- **Line 38**: "`Game Meta`"
  - Suggested key: `staff.game_meta`
  - Context: `                        <th>Game Meta</th>`

- **Line 39**: "`Music Meta`"
  - Suggested key: `staff.music_meta`
  - Context: `                        <th>Music Meta</th>`

- **Line 40**: "`No Meta`"
  - Suggested key: `staff.no_meta`
  - Context: `                        <th>No Meta</th>`

- **Line 65**: "`N/A`"
  - Suggested key: `staff.n_a`
  - Context: `                                    <span>N/A</span>`

### `resources/views/Staff/chat/bot/index.blade.php` (1 strings)

- **Line 62**: "`emoji`"
  - Suggested key: `staff.emoji`
  - Context: `                                    alt="emoji"`

### `resources/views/Staff/chat/status/edit.blade.php` (1 strings)

- **Line 69**: "`Enter Font Awesome Code Here...`"
  - Suggested key: `staff.enter_font_awesome_code_here`
  - Context: `                        placeholder="Enter Font Awesome Code Here..."`

### `resources/views/Staff/cheated_torrent/index.blade.php` (5 strings)

- **Line 19**: "`Cheated Torrents`"
  - Suggested key: `staff.cheated_torrents`
  - Context: `    <li class="breadcrumb--active">Cheated Torrents</li>`

- **Line 27**: "`Cheated Torrents`"
  - Suggested key: `staff.cheated_torrents`
  - Context: `            <h2 class="panel__heading">Cheated Torrents</h2>`

- **Line 63**: "`Times Cheated`"
  - Suggested key: `staff.times_cheated`
  - Context: `                        <th>Times Cheated</th>`

- **Line 64**: "`All-time Balance`"
  - Suggested key: `staff.all_time_balance`
  - Context: `                        <th>All-time Balance</th>`

- **Line 129**: "`No cheated torrents`"
  - Suggested key: `staff.no_cheated_torrents`
  - Context: `                            <td colspan="10">No cheated torrents</td>`

### `resources/views/Staff/command/index.blade.php` (13 strings)

- **Line 17**: "`Commands`"
  - Suggested key: `staff.commands`
  - Context: `    <li class="breadcrumb--active">Commands</li>`

- **Line 31**: "`Maintenance Mode`"
  - Suggested key: `staff.maintenance_mode`
  - Context: `            <h2 class="panel__heading">Maintenance Mode</h2>`

- **Line 42**: "`This commands enables maintenance mode while whitelisting only your IP Address.`"
  - Suggested key: `staff.this_commands_enables_maintena`
  - Context: `                            title="This commands e...nce mode while whitelisting only your IP Address."`

- **Line 57**: "`This commands disables maintenance mode. Bringing the site backup for all to access.`"
  - Suggested key: `staff.this_commands_disables_mainten`
  - Context: `                            title="This commands d...mode. Bringing the site backup for all to access."`

- **Line 66**: "`Caching`"
  - Suggested key: `staff.caching`
  - Context: `            <h2 class="panel__heading">Caching</h2>`

- **Line 73**: "`This commands clears your sites cache. This cache depends on what driver you are using.`"
  - Suggested key: `staff.this_commands_clears_your_site`
  - Context: `                            title="This commands c... This cache depends on what driver you are using."`

- **Line 84**: "`This commands clears your sites compiled views cache.`"
  - Suggested key: `staff.this_commands_clears_your_site`
  - Context: `                            title="This commands clears your sites compiled views cache."`

- **Line 98**: "`This commands clears your sites compiled routes cache.`"
  - Suggested key: `staff.this_commands_clears_your_site`
  - Context: `                            title="This commands clears your sites compiled routes cache."`

- **Line 112**: "`This commands clears your sites compiled configs cache.`"
  - Suggested key: `staff.this_commands_clears_your_site`
  - Context: `                            title="This commands clears your sites compiled configs cache."`

- **Line 123**: "`This commands clears ALL of your sites cache.`"
  - Suggested key: `staff.this_commands_clears_all_of_yo`
  - Context: `                            title="This commands clears ALL of your sites cache."`

- **Line 134**: "`This commands sets ALL of your sites cache.`"
  - Suggested key: `staff.this_commands_sets_all_of_your`
  - Context: `                            title="This commands sets ALL of your sites cache."`

- **Line 143**: "`Email`"
  - Suggested key: `staff.email`
  - Context: `            <h2 class="panel__heading">Email</h2>`

- **Line 150**: "`This commands tests your email configuration.`"
  - Suggested key: `staff.this_commands_tests_your_email`
  - Context: `                            title="This commands tests your email configuration."`

### `resources/views/Staff/dashboard/index.blade.php` (48 strings)

- **Line 705**: "`SSL Certificate`"
  - Suggested key: `staff.ssl_certificate`
  - Context: `        <h2 class="panel__heading">SSL Certificate</h2>`

- **Line 708**: "`URL`"
  - Suggested key: `staff.url`
  - Context: `                <dt>URL</dt>`

- **Line 713**: "`Connection`"
  - Suggested key: `staff.connection`
  - Context: `                    <dt>Connection</dt>`

- **Line 714**: "`Secure`"
  - Suggested key: `staff.secure`
  - Context: `                    <dd>Secure</dd>`

- **Line 717**: "`Issued By`"
  - Suggested key: `staff.issued_by`
  - Context: `                    <dt>Issued By</dt>`

- **Line 723**: "`Expires`"
  - Suggested key: `staff.expires`
  - Context: `                    <dt>Expires</dt>`

- **Line 730**: "`Connection`"
  - Suggested key: `staff.connection`
  - Context: `                    <dt>Connection</dt>`

- **Line 732**: "`Not Secure`"
  - Suggested key: `staff.not_secure`
  - Context: `                        <strong>Not Secure</strong>`

- **Line 736**: "`Issued By`"
  - Suggested key: `staff.issued_by`
  - Context: `                    <dt>Issued By</dt>`

- **Line 737**: "`N/A`"
  - Suggested key: `staff.n_a`
  - Context: `                    <dd>N/A</dd>`

- **Line 740**: "`Expires`"
  - Suggested key: `staff.expires`
  - Context: `                    <dt>Expires</dt>`

- **Line 741**: "`N/A`"
  - Suggested key: `staff.n_a`
  - Context: `                    <dd>N/A</dd>`

- **Line 747**: "`Server Information`"
  - Suggested key: `staff.server_information`
  - Context: `        <h2 class="panel__heading">Server Information</h2>`

- **Line 754**: "`PHP`"
  - Suggested key: `staff.php`
  - Context: `                <dt>PHP</dt>`

- **Line 758**: "`Database`"
  - Suggested key: `staff.database`
  - Context: `                <dt>Database</dt>`

- **Line 762**: "`Laravel`"
  - Suggested key: `staff.laravel`
  - Context: `                <dt>Laravel</dt>`

- **Line 776**: "`Total`"
  - Suggested key: `staff.total`
  - Context: `                    <dt>Total</dt>`

- **Line 801**: "`Total`"
  - Suggested key: `staff.total`
  - Context: `                    <dt>Total</dt>`

- **Line 809**: "`Inactive`"
  - Suggested key: `staff.inactive`
  - Context: `                    <dt>Inactive</dt>`

- **Line 817**: "`Leeches`"
  - Suggested key: `staff.leeches`
  - Context: `                    <dt>Leeches</dt>`

- **Line 826**: "`Total`"
  - Suggested key: `staff.total`
  - Context: `                    <dt>Total</dt>`

- **Line 830**: "`Validating`"
  - Suggested key: `staff.validating`
  - Context: `                    <dt>Validating</dt>`

- **Line 840**: "`RAM`"
  - Suggested key: `staff.ram`
  - Context: `            <h2 class="panel__heading">RAM</h2>`

- **Line 843**: "`Total`"
  - Suggested key: `staff.total`
  - Context: `                    <dt>Total</dt>`

- **Line 847**: "`Used`"
  - Suggested key: `staff.used`
  - Context: `                    <dt>Used</dt>`

- **Line 857**: "`Disk`"
  - Suggested key: `staff.disk`
  - Context: `            <h2 class="panel__heading">Disk</h2>`

- **Line 860**: "`Total`"
  - Suggested key: `staff.total`
  - Context: `                    <dt>Total</dt>`

- **Line 864**: "`Used`"
  - Suggested key: `staff.used`
  - Context: `                    <dt>Used</dt>`

- **Line 874**: "`Load Average`"
  - Suggested key: `staff.load_average`
  - Context: `            <h2 class="panel__heading">Load Average</h2>`

- **Line 877**: "`1 minute`"
  - Suggested key: `staff.1_minute`
  - Context: `                    <dt>1 minute</dt>`

- **Line 881**: "`5 minutes`"
  - Suggested key: `staff.5_minutes`
  - Context: `                    <dt>5 minutes</dt>`

- **Line 885**: "`15 minutes`"
  - Suggested key: `staff.15_minutes`
  - Context: `                    <dt>15 minutes</dt>`

- **Line 892**: "`Directory Permissions`"
  - Suggested key: `staff.directory_permissions`
  - Context: `        <h2 class="panel__heading">Directory Permissions</h2>`

- **Line 897**: "`Directory`"
  - Suggested key: `staff.directory`
  - Context: `                        <th>Directory</th>`

- **Line 899**: "`Rec.`"
  - Suggested key: `staff.rec`
  - Context: `                        <th><abbr title="Recommended">Rec.</abbr></th>`

- **Line 899**: "`Recommended`"
  - Suggested key: `staff.recommended`
  - Context: `                        <th><abbr title="Recommended">Rec.</abbr></th>`

- **Line 929**: "`External Tracker Stats`"
  - Suggested key: `staff.external_tracker_stats`
  - Context: `                <h2 class="panel__heading">External Tracker Stats</h2>`

- **Line 930**: "`External tracker not enabled.`"
  - Suggested key: `staff.external_tracker_not_enabled`
  - Context: `                <div class="panel__body">External tracker not enabled.</div>`

- **Line 934**: "`External Tracker Stats`"
  - Suggested key: `staff.external_tracker_stats`
  - Context: `                <h2 class="panel__heading">External Tracker Stats</h2>`

- **Line 935**: "`Stats endpoint not found.`"
  - Suggested key: `staff.stats_endpoint_not_found`
  - Context: `                <div class="panel__body">Stats endpoint not found.</div>`

- **Line 939**: "`External Tracker Stats`"
  - Suggested key: `staff.external_tracker_stats`
  - Context: `                <h2 class="panel__heading">External Tracker Stats</h2>`

- **Line 940**: "`Tracker returned an error.`"
  - Suggested key: `staff.tracker_returned_an_error`
  - Context: `                <div class="panel__body">Tracker returned an error.</div>`

- **Line 944**: "`External Tracker Stats`"
  - Suggested key: `staff.external_tracker_stats`
  - Context: `                <h2 class="panel__heading">External Tracker Stats</h2>`

- **Line 964**: "`Last Request At`"
  - Suggested key: `staff.last_request_at`
  - Context: `                        <dt>Last Request At</dt>`

- **Line 975**: "`Last Successful Response At`"
  - Suggested key: `staff.last_successful_response_at`
  - Context: `                        <dt>Last Successful Response At</dt>`

- **Line 989**: "`Interval (s)`"
  - Suggested key: `staff.interval_s`
  - Context: `                            <th style="text-align: right">Interval (s)</th>`

- **Line 990**: "`In (req/s)`"
  - Suggested key: `staff.in_req_s`
  - Context: `                            <th style="text-align: right">In (req/s)</th>`

- **Line 991**: "`Out (req/s)`"
  - Suggested key: `staff.out_req_s`
  - Context: `                            <th style="text-align: right">Out (req/s)</th>`

### `resources/views/Staff/distributor/create.blade.php` (1 strings)

- **Line 23**: "`Add A Torrent Distributor`"
  - Suggested key: `staff.add_a_torrent_distributor`
  - Context: `        <h2 class="panel__heading">Add A Torrent Distributor</h2>`

### `resources/views/Staff/distributor/index.blade.php` (3 strings)

- **Line 9**: "`Torrent Distributors`"
  - Suggested key: `staff.torrent_distributors`
  - Context: `    <li class="breadcrumb--active">Torrent Distributors</li>`

- **Line 17**: "`Torrent Distributors`"
  - Suggested key: `staff.torrent_distributors`
  - Context: `            <h2 class="panel__heading">Torrent Distributors</h2>`

- **Line 68**: "`No distributors`"
  - Suggested key: `staff.no_distributors`
  - Context: `                            <td colspan="2">No distributors</td>`

### `resources/views/Staff/donation/index.blade.php` (9 strings)

- **Line 9**: "`Donations`"
  - Suggested key: `staff.donations`
  - Context: `    <li class="breadcrumb--active">Donations</li>`

- **Line 15**: "`Donation Statistics`"
  - Suggested key: `staff.donation_statistics`
  - Context: `            <h2 class="panel__heading">Donation Statistics</h2>`

- **Line 29**: "`Donations`"
  - Suggested key: `staff.donations`
  - Context: `            <h2 class="panel__heading">Donations</h2>`

- **Line 37**: "`Transaction`"
  - Suggested key: `staff.transaction`
  - Context: `                        <th>Transaction</th>`

- **Line 38**: "`Cost`"
  - Suggested key: `staff.cost`
  - Context: `                        <th>Cost</th>`

- **Line 39**: "`Upload #`"
  - Suggested key: `staff.upload`
  - Context: `                        <th>Upload #</th>`

- **Line 40**: "`Invite #`"
  - Suggested key: `staff.invite`
  - Context: `                        <th>Invite #</th>`

- **Line 41**: "`Bonus #`"
  - Suggested key: `staff.bonus`
  - Context: `                        <th>Bonus #</th>`

- **Line 42**: "`Length`"
  - Suggested key: `staff.length`
  - Context: `                        <th>Length</th>`

### `resources/views/Staff/donation_gateway/create.blade.php` (5 strings)

- **Line 10**: "`Gateways`"
  - Suggested key: `staff.gateways`
  - Context: `        <a href="{{ route('staff.gateways.index') }}" class="breadcrumb__link">Gateways</a>`

- **Line 12**: "`Create Gateway`"
  - Suggested key: `staff.create_gateway`
  - Context: `    <li class="breadcrumb--active">Create Gateway</li>`

- **Line 18**: "`Add New Gateway`"
  - Suggested key: `staff.add_new_gateway`
  - Context: `            <h2 class="panel__heading">Add New Gateway</h2>`

- **Line 28**: "`Address`"
  - Suggested key: `staff.address`
  - Context: `                            <th>Address</th>`

- **Line 57**: "`Address`"
  - Suggested key: `staff.address`
  - Context: `                                    placeholder="Address"`

### `resources/views/Staff/donation_gateway/edit.blade.php` (4 strings)

- **Line 10**: "`Gateways`"
  - Suggested key: `staff.gateways`
  - Context: `        <a href="{{ route('staff.gateways.index') }}" class="breadcrumb__link">Gateways</a>`

- **Line 12**: "`Edit Gateway`"
  - Suggested key: `staff.edit_gateway`
  - Context: `    <li class="breadcrumb--active">Edit Gateway</li>`

- **Line 33**: "`Address`"
  - Suggested key: `staff.address`
  - Context: `                            <th>Address</th>`

- **Line 78**: "`Update`"
  - Suggested key: `staff.update`
  - Context: `                <button type="submit" class="form__button form__button--filled">Update</button>`

### `resources/views/Staff/donation_gateway/index.blade.php` (3 strings)

- **Line 9**: "`Gateways`"
  - Suggested key: `staff.gateways`
  - Context: `    <li class="breadcrumb--active">Gateways</li>`

- **Line 15**: "`Gateways`"
  - Suggested key: `staff.gateways`
  - Context: `            <h2 class="panel__heading">Gateways</h2>`

- **Line 31**: "`Address`"
  - Suggested key: `staff.address`
  - Context: `                        <th>Address</th>`

### `resources/views/Staff/donation_package/create.blade.php` (13 strings)

- **Line 10**: "`Packages`"
  - Suggested key: `staff.packages`
  - Context: `        <a href="{{ route('staff.packages.index') }}" class="breadcrumb__link">Packages</a>`

- **Line 12**: "`Create Package`"
  - Suggested key: `staff.create_package`
  - Context: `    <li class="breadcrumb--active">Create Package</li>`

- **Line 18**: "`Add New Package`"
  - Suggested key: `staff.add_new_package`
  - Context: `            <h2 class="panel__heading">Add New Package</h2>`

- **Line 29**: "`Cost`"
  - Suggested key: `staff.cost`
  - Context: `                            <th>Cost</th>`

- **Line 30**: "`Upload (Bytes)`"
  - Suggested key: `staff.upload_bytes`
  - Context: `                            <th>Upload (Bytes)</th>`

- **Line 31**: "`Invite (#)`"
  - Suggested key: `staff.invite`
  - Context: `                            <th>Invite (#)</th>`

- **Line 32**: "`Bonus (#)`"
  - Suggested key: `staff.bonus`
  - Context: `                            <th>Bonus (#)</th>`

- **Line 33**: "`Supporter (Days)`"
  - Suggested key: `staff.supporter_days`
  - Context: `                            <th>Supporter (Days)</th>`

- **Line 70**: "`Cost`"
  - Suggested key: `staff.cost`
  - Context: `                                    placeholder="Cost"`

- **Line 79**: "`nullable`"
  - Suggested key: `staff.nullable`
  - Context: `                                    placeholder="nullable"`

- **Line 88**: "`nullable`"
  - Suggested key: `staff.nullable`
  - Context: `                                    placeholder="nullable"`

- **Line 97**: "`nullable`"
  - Suggested key: `staff.nullable`
  - Context: `                                    placeholder="nullable"`

- **Line 106**: "`(empty for lifetime)`"
  - Suggested key: `staff.empty_for_lifetime`
  - Context: `                                    placeholder="(empty for lifetime)"`

### `resources/views/Staff/donation_package/edit.blade.php` (8 strings)

- **Line 10**: "`Packages`"
  - Suggested key: `staff.packages`
  - Context: `        <a href="{{ route('staff.packages.index') }}" class="breadcrumb__link">Packages</a>`

- **Line 12**: "`Edit Package`"
  - Suggested key: `staff.edit_package`
  - Context: `    <li class="breadcrumb--active">Edit Package</li>`

- **Line 35**: "`Cost`"
  - Suggested key: `staff.cost`
  - Context: `                                <th>Cost</th>`

- **Line 36**: "`Upload (GiB)`"
  - Suggested key: `staff.upload_gib`
  - Context: `                                <th>Upload (GiB)</th>`

- **Line 37**: "`Invite (#)`"
  - Suggested key: `staff.invite`
  - Context: `                                <th>Invite (#)</th>`

- **Line 38**: "`Bonus (#)`"
  - Suggested key: `staff.bonus`
  - Context: `                                <th>Bonus (#)</th>`

- **Line 39**: "`Supporter (Days)`"
  - Suggested key: `staff.supporter_days`
  - Context: `                                <th>Supporter (Days)</th>`

- **Line 124**: "`Update`"
  - Suggested key: `staff.update`
  - Context: `                <button type="submit" class="form__button form__button--filled">Update</button>`

### `resources/views/Staff/donation_package/index.blade.php` (7 strings)

- **Line 9**: "`Packages`"
  - Suggested key: `staff.packages`
  - Context: `    <li class="breadcrumb--active">Packages</li>`

- **Line 15**: "`Packages`"
  - Suggested key: `staff.packages`
  - Context: `            <h2 class="panel__heading">Packages</h2>`

- **Line 31**: "`Cost`"
  - Suggested key: `staff.cost`
  - Context: `                        <th>Cost</th>`

- **Line 32**: "`Upload (GiB)`"
  - Suggested key: `staff.upload_gib`
  - Context: `                        <th>Upload (GiB)</th>`

- **Line 33**: "`Invite (#)`"
  - Suggested key: `staff.invite`
  - Context: `                        <th>Invite (#)</th>`

- **Line 34**: "`Bonus (#)`"
  - Suggested key: `staff.bonus`
  - Context: `                        <th>Bonus (#)</th>`

- **Line 35**: "`Supporter (Days)`"
  - Suggested key: `staff.supporter_days`
  - Context: `                        <th>Supporter (Days)</th>`

### `resources/views/Staff/forum-category/create.blade.php` (2 strings)

- **Line 31**: "`Add a new Forum Category`"
  - Suggested key: `staff.add_a_new_forum_category`
  - Context: `        <h2 class="panel__heading">Add a new Forum Category</h2>`

- **Line 65**: "`Save Forum`"
  - Suggested key: `staff.save_forum`
  - Context: `                    <button class="form__button form__button--filled">Save Forum</button>`

### `resources/views/Staff/forum-category/edit.blade.php` (1 strings)

- **Line 85**: "`Save Forum`"
  - Suggested key: `staff.save_forum`
  - Context: `                    <button class="form__button form__button--filled">Save Forum</button>`

### `resources/views/Staff/forum/create.blade.php` (7 strings)

- **Line 29**: "`Add a new Forum`"
  - Suggested key: `staff.add_a_new_forum`
  - Context: `        <h2 class="panel__heading">Add a new Forum</h2>`

- **Line 92**: "`None`"
  - Suggested key: `staff.none`
  - Context: `                        <option value="" selected>None</option>`

- **Line 104**: "`Permissions`"
  - Suggested key: `staff.permissions`
  - Context: `                    <h3>Permissions</h3>`

- **Line 110**: "`Read topics`"
  - Suggested key: `staff.read_topics`
  - Context: `                                    <th x-bind="columnHeader">Read topics</th>`

- **Line 111**: "`Start new topic`"
  - Suggested key: `staff.start_new_topic`
  - Context: `                                    <th x-bind="columnHeader">Start new topic</th>`

- **Line 112**: "`Reply to topics`"
  - Suggested key: `staff.reply_to_topics`
  - Context: `                                    <th x-bind="columnHeader">Reply to topics</th>`

- **Line 172**: "`Save Forum`"
  - Suggested key: `staff.save_forum`
  - Context: `                    <button class="form__button form__button--filled">Save Forum</button>`

### `resources/views/Staff/forum/edit.blade.php` (6 strings)

- **Line 122**: "`None`"
  - Suggested key: `staff.none`
  - Context: `                        <option default selected value="">None</option>`

- **Line 144**: "`Permissions`"
  - Suggested key: `staff.permissions`
  - Context: `                    <label class="form__label">Permissions</label>`

- **Line 150**: "`Read topics`"
  - Suggested key: `staff.read_topics`
  - Context: `                                    <th x-bind="columnHeader">Read topics</th>`

- **Line 151**: "`Start new topic`"
  - Suggested key: `staff.start_new_topic`
  - Context: `                                    <th x-bind="columnHeader">Start new topic</th>`

- **Line 152**: "`Reply to topics`"
  - Suggested key: `staff.reply_to_topics`
  - Context: `                                    <th x-bind="columnHeader">Reply to topics</th>`

- **Line 212**: "`Save Forum`"
  - Suggested key: `staff.save_forum`
  - Context: `                    <button class="form__button form__button--filled">Save Forum</button>`

### `resources/views/Staff/group/create.blade.php` (19 strings)

- **Line 23**: "`Add New Group`"
  - Suggested key: `staff.add_new_group`
  - Context: `        <h2 class="panel__heading">Add New Group</h2>`

- **Line 71**: "`Level`"
  - Suggested key: `staff.level`
  - Context: `                    <label class="form__label form__label--floating" for="level">Level</label>`

- **Line 116**: "`GIF Effect`"
  - Suggested key: `staff.gif_effect`
  - Context: `                        placeholder="GIF Effect"`

- **Line 153**: "`Editor`"
  - Suggested key: `staff.editor`
  - Context: `                    <label class="form__label" for="is_editor">Editor</label>`

- **Line 164**: "`Torrent Modo`"
  - Suggested key: `staff.torrent_modo`
  - Context: `                    <label class="form__label" for="is_torrent_modo">Torrent Modo</label>`

- **Line 176**: "`Modo`"
  - Suggested key: `staff.modo`
  - Context: `                    <label class="form__label" for="is_modo">Modo</label>`

- **Line 187**: "`Admin`"
  - Suggested key: `staff.admin`
  - Context: `                    <label class="form__label" for="is_admin">Admin</label>`

- **Line 198**: "`Owner`"
  - Suggested key: `staff.owner`
  - Context: `                    <label class="form__label" for="is_owner">Owner</label>`

- **Line 209**: "`Trusted`"
  - Suggested key: `staff.trusted`
  - Context: `                    <label class="form__label" for="is_trusted">Trusted</label>`

- **Line 220**: "`Immune`"
  - Suggested key: `staff.immune`
  - Context: `                    <label class="form__label" for="is_immune">Immune</label>`

- **Line 253**: "`Refundable Download`"
  - Suggested key: `staff.refundable_download`
  - Context: `                    <label class="form__label" for="is_refundable">Refundable Download</label>`

- **Line 264**: "`Incognito`"
  - Suggested key: `staff.incognito`
  - Context: `                    <label class="form__label" for="is_incognito">Incognito</label>`

- **Line 297**: "`Invite`"
  - Suggested key: `staff.invite`
  - Context: `                    <label class="form__label" for="can_invite">Invite</label>`

- **Line 331**: "`Autogroup`"
  - Suggested key: `staff.autogroup`
  - Context: `                    <label class="form__label" for="autogroup">Autogroup</label>`

- **Line 406**: "`Permissions`"
  - Suggested key: `staff.permissions`
  - Context: `                    <label class="form__label">Permissions</label>`

- **Line 411**: "`Forum Category`"
  - Suggested key: `staff.forum_category`
  - Context: `                                    <th x-bind="columnHeader">Forum Category</th>`

- **Line 413**: "`Read topics`"
  - Suggested key: `staff.read_topics`
  - Context: `                                    <th x-bind="columnHeader">Read topics</th>`

- **Line 414**: "`Start new topic`"
  - Suggested key: `staff.start_new_topic`
  - Context: `                                    <th x-bind="columnHeader">Start new topic</th>`

- **Line 415**: "`Reply to topics`"
  - Suggested key: `staff.reply_to_topics`
  - Context: `                                    <th x-bind="columnHeader">Reply to topics</th>`

### `resources/views/Staff/group/edit.blade.php` (19 strings)

- **Line 83**: "`Level`"
  - Suggested key: `staff.level`
  - Context: `                    <label class="form__label form__label--floating" for="level">Level</label>`

- **Line 130**: "`GIF Effect`"
  - Suggested key: `staff.gif_effect`
  - Context: `                        placeholder="GIF Effect"`

- **Line 171**: "`Editor`"
  - Suggested key: `staff.editor`
  - Context: `                    <label class="form__label" for="is_editor">Editor</label>`

- **Line 183**: "`Torrent Modo`"
  - Suggested key: `staff.torrent_modo`
  - Context: `                    <label class="form__label" for="is_torrent_modo">Torrent Modo</label>`

- **Line 195**: "`Modo`"
  - Suggested key: `staff.modo`
  - Context: `                    <label class="form__label" for="is_modo">Modo</label>`

- **Line 207**: "`Admin`"
  - Suggested key: `staff.admin`
  - Context: `                    <label class="form__label" for="is_admin">Admin</label>`

- **Line 219**: "`Owner`"
  - Suggested key: `staff.owner`
  - Context: `                    <label class="form__label" for="is_owner">Owner</label>`

- **Line 231**: "`Trusted`"
  - Suggested key: `staff.trusted`
  - Context: `                    <label class="form__label" for="is_trusted">Trusted</label>`

- **Line 243**: "`Immune`"
  - Suggested key: `staff.immune`
  - Context: `                    <label class="form__label" for="is_immune">Immune</label>`

- **Line 279**: "`Refundable Download`"
  - Suggested key: `staff.refundable_download`
  - Context: `                    <label class="form__label" for="is_refundable">Refundable Download</label>`

- **Line 291**: "`Incognito`"
  - Suggested key: `staff.incognito`
  - Context: `                    <label class="form__label" for="is_incognito">Incognito</label>`

- **Line 327**: "`Invite`"
  - Suggested key: `staff.invite`
  - Context: `                    <label class="form__label" for="can_invite">Invite</label>`

- **Line 364**: "`Autogroup`"
  - Suggested key: `staff.autogroup`
  - Context: `                    <label class="form__label" for="autogroup">Autogroup</label>`

- **Line 368**: "`Autogroup requirements`"
  - Suggested key: `staff.autogroup_requirements`
  - Context: `                        <legend class="form__legend">Autogroup requirements</legend>`

- **Line 450**: "`Permissions`"
  - Suggested key: `staff.permissions`
  - Context: `                    <label class="form__label">Permissions</label>`

- **Line 455**: "`Forum Category`"
  - Suggested key: `staff.forum_category`
  - Context: `                                    <th x-bind="columnHeader">Forum Category</th>`

- **Line 457**: "`Read topics`"
  - Suggested key: `staff.read_topics`
  - Context: `                                    <th x-bind="columnHeader">Read topics</th>`

- **Line 458**: "`Start new topic`"
  - Suggested key: `staff.start_new_topic`
  - Context: `                                    <th x-bind="columnHeader">Start new topic</th>`

- **Line 459**: "`Reply to topics`"
  - Suggested key: `staff.reply_to_topics`
  - Context: `                                    <th x-bind="columnHeader">Reply to topics</th>`

### `resources/views/Staff/group/index.blade.php` (19 strings)

- **Line 36**: "`Level`"
  - Suggested key: `staff.level`
  - Context: `                        <th>Level</th>`

- **Line 37**: "`DL Slots`"
  - Suggested key: `staff.dl_slots`
  - Context: `                        <th>DL Slots</th>`

- **Line 40**: "`Effect`"
  - Suggested key: `staff.effect`
  - Context: `                        <th>Effect</th>`

- **Line 43**: "`Editor`"
  - Suggested key: `staff.editor`
  - Context: `                        <th>Editor</th>`

- **Line 44**: "`Torrent Modo`"
  - Suggested key: `staff.torrent_modo`
  - Context: `                        <th>Torrent Modo</th>`

- **Line 45**: "`Modo`"
  - Suggested key: `staff.modo`
  - Context: `                        <th>Modo</th>`

- **Line 46**: "`Admin`"
  - Suggested key: `staff.admin`
  - Context: `                        <th>Admin</th>`

- **Line 47**: "`Owner`"
  - Suggested key: `staff.owner`
  - Context: `                        <th>Owner</th>`

- **Line 48**: "`Trusted`"
  - Suggested key: `staff.trusted`
  - Context: `                        <th>Trusted</th>`

- **Line 49**: "`Immune`"
  - Suggested key: `staff.immune`
  - Context: `                        <th>Immune</th>`

- **Line 53**: "`Incognito`"
  - Suggested key: `staff.incognito`
  - Context: `                        <th>Incognito</th>`

- **Line 56**: "`Invite`"
  - Suggested key: `staff.invite`
  - Context: `                        <th>Invite</th>`

- **Line 59**: "`Autogroup`"
  - Suggested key: `staff.autogroup`
  - Context: `                        <th>Autogroup</th>`

- **Line 60**: "`Min Upload`"
  - Suggested key: `staff.min_upload`
  - Context: `                        <th>Min Upload</th>`

- **Line 61**: "`Min Ratio`"
  - Suggested key: `staff.min_ratio`
  - Context: `                        <th>Min Ratio</th>`

- **Line 62**: "`Min Age`"
  - Suggested key: `staff.min_age`
  - Context: `                        <th>Min Age</th>`

- **Line 63**: "`Min Avg Seedtime`"
  - Suggested key: `staff.min_avg_seedtime`
  - Context: `                        <th>Min Avg Seedtime</th>`

- **Line 64**: "`Min Seedsize`"
  - Suggested key: `staff.min_seedsize`
  - Context: `                        <th>Min Seedsize</th>`

- **Line 65**: "`Min Uploads`"
  - Suggested key: `staff.min_uploads`
  - Context: `                        <th>Min Uploads</th>`

### `resources/views/Staff/internals/create.blade.php` (2 strings)

- **Line 10**: "`Internals`"
  - Suggested key: `staff.internals`
  - Context: `        <a href="{{ route('staff.internals.index') }}" class="breadcrumb__link">Internals</a>`

- **Line 56**: "`Effect`"
  - Suggested key: `staff.effect`
  - Context: `                    <label class="form__label form__label--floating" for="effect">Effect</label>`

### `resources/views/Staff/internals/edit.blade.php` (5 strings)

- **Line 10**: "`Internals`"
  - Suggested key: `staff.internals`
  - Context: `        <a href="{{ route('staff.internals.index') }}" class="breadcrumb__link">Internals</a>`

- **Line 68**: "`Effect`"
  - Suggested key: `staff.effect`
  - Context: `                    <label class="form__label form__label--floating" for="effect">Effect</label>`

- **Line 146**: "`Join date`"
  - Suggested key: `staff.join_date`
  - Context: `                    <th>Join date</th>`

- **Line 174**: "`Edit internal user`"
  - Suggested key: `staff.edit_internal_user`
  - Context: `                                            <h3 class="dialog__heading">Edit internal user</h3>`

- **Line 246**: "`No members.`"
  - Suggested key: `staff.no_members`
  - Context: `                            <td colspan="4">No members.</td>`

### `resources/views/Staff/internals/index.blade.php` (3 strings)

- **Line 9**: "`Internals`"
  - Suggested key: `staff.internals`
  - Context: `    <li class="breadcrumb--active">Internals</li>`

- **Line 17**: "`Internal Groups`"
  - Suggested key: `staff.internal_groups`
  - Context: `            <h2 class="panel__heading">Internal Groups</h2>`

- **Line 34**: "`Effect`"
  - Suggested key: `staff.effect`
  - Context: `                        <th>Effect</th>`

### `resources/views/Staff/leaker/index.blade.php` (1 strings)

- **Line 13**: "`Leakers`"
  - Suggested key: `staff.leakers`
  - Context: `    <li class="breadcrumb--active">Leakers</li>`

### `resources/views/Staff/moderation/index.blade.php` (3 strings)

- **Line 98**: "`No pending torrents`"
  - Suggested key: `staff.no_pending_torrents`
  - Context: `                            <td colspan="8">No pending torrents</td>`

- **Line 196**: "`No postponed torrents`"
  - Suggested key: `staff.no_postponed_torrents`
  - Context: `                            <td colspan="9">No postponed torrents</td>`

- **Line 295**: "`No rejected torrents`"
  - Suggested key: `staff.no_rejected_torrents`
  - Context: `                            <td colspan="9">No rejected torrents</td>`

### `resources/views/Staff/page/index.blade.php` (1 strings)

- **Line 94**: "`No pages`"
  - Suggested key: `staff.no_pages`
  - Context: `                            <td colspan="3">No pages</td>`

### `resources/views/Staff/region/create.blade.php` (2 strings)

- **Line 10**: "`Torrent Regions`"
  - Suggested key: `staff.torrent_regions`
  - Context: `        <a href="{{ route('staff.regions.index') }}" class="breadcrumb__link">Torrent Regions</a>`

- **Line 21**: "`Add A Torrent Region`"
  - Suggested key: `staff.add_a_torrent_region`
  - Context: `        <h2 class="panel__heading">Add A Torrent Region</h2>`

### `resources/views/Staff/region/edit.blade.php` (1 strings)

- **Line 10**: "`Torrent Regions`"
  - Suggested key: `staff.torrent_regions`
  - Context: `        <a href="{{ route('staff.regions.index') }}" class="breadcrumb__link">Torrent Regions</a>`

### `resources/views/Staff/region/index.blade.php` (3 strings)

- **Line 9**: "`Torrent Regions`"
  - Suggested key: `staff.torrent_regions`
  - Context: `    <li class="breadcrumb--active">Torrent Regions</li>`

- **Line 17**: "`Torrent Regions`"
  - Suggested key: `staff.torrent_regions`
  - Context: `            <h2 class="panel__heading">Torrent Regions</h2>`

- **Line 128**: "`No regions`"
  - Suggested key: `staff.no_regions`
  - Context: `                            <td colspan="3">No regions</td>`

### `resources/views/Staff/report/show.blade.php` (5 strings)

- **Line 58**: "`Referenced Links:`"
  - Suggested key: `staff.referenced_links`
  - Context: `            <h2 class="panel__heading">Referenced Links:</h2>`

- **Line 73**: "`Verdict`"
  - Suggested key: `staff.verdict`
  - Context: `            <h2 class="panel__heading">Verdict</h2>`

- **Line 113**: "`Solved by`"
  - Suggested key: `staff.solved_by`
  - Context: `        <h2 class="panel__heading">Solved by</h2>`

- **Line 126**: "`Snooze`"
  - Suggested key: `staff.snooze`
  - Context: `        <h2 class="panel__heading">Snooze</h2>`

- **Line 130**: "`Snoozed until`"
  - Suggested key: `staff.snoozed_until`
  - Context: `                    <dt>Snoozed until</dt>`

### `resources/views/Staff/resolution/create.blade.php` (1 strings)

- **Line 23**: "`Add A Torrent Resolution`"
  - Suggested key: `staff.add_a_torrent_resolution`
  - Context: `        <h2 class="panel__heading">Add A Torrent Resolution</h2>`

### `resources/views/Staff/resolution/index.blade.php` (1 strings)

- **Line 81**: "`No resolution`"
  - Suggested key: `staff.no_resolution`
  - Context: `                            <td colspan="3">No resolution</td>`

### `resources/views/Staff/seedbox/index.blade.php` (1 strings)

- **Line 84**: "`No seedboxes`"
  - Suggested key: `staff.no_seedboxes`
  - Context: `                            <td colspan="5">No seedboxes</td>`

### `resources/views/Staff/unregistered-info-hash/index.blade.php` (1 strings)

- **Line 23**: "`Unregistered Info Hashes`"
  - Suggested key: `staff.unregistered_info_hashes`
  - Context: `    <li class="breadcrumb--active">Unregistered Info Hashes</li>`

### `resources/views/Staff/user/edit.blade.php` (7 strings)

- **Line 211**: "`Override Group Can Chat`"
  - Suggested key: `staff.override_group_can_chat`
  - Context: `                    <label for="override_can_chat">Override Group Can Chat</label>`

- **Line 241**: "`Override Group Can Comment`"
  - Suggested key: `staff.override_group_can_comment`
  - Context: `                    <label for="override_can_comment">Override Group Can Comment</label>`

- **Line 270**: "`Override Group Can Invite`"
  - Suggested key: `staff.override_group_can_invite`
  - Context: `                    <label for="override_can_invite">Override Group Can Invite</label>`

- **Line 299**: "`Override Group Can Request`"
  - Suggested key: `staff.override_group_can_request`
  - Context: `                    <label for="override_can_request">Override Group Can Request</label>`

- **Line 328**: "`Override Group Can Upload`"
  - Suggested key: `staff.override_group_can_upload`
  - Context: `                    <label for="override_can_upload">Override Group Can Upload</label>`

- **Line 359**: "`Active Donor?`"
  - Suggested key: `staff.active_donor`
  - Context: `                    <label for="is_donor">Active Donor?</label>`

- **Line 371**: "`Lifetime Donor?`"
  - Suggested key: `staff.lifetime_donor`
  - Context: `                    <label for="is_donor">Lifetime Donor?</label>`

### `resources/views/Staff/watchlist/index.blade.php` (1 strings)

- **Line 20**: "`Watchlist`"
  - Suggested key: `staff.watchlist`
  - Context: `    <li class="breadcrumb--active">Watchlist</li>`

### `resources/views/Staff/whitelisted-image-url/index.blade.php` (10 strings)

- **Line 9**: "`Whitelisted Image URLs`"
  - Suggested key: `staff.whitelisted_image_urls`
  - Context: `    <li class="breadcrumb--active">Whitelisted Image URLs</li>`

- **Line 17**: "`Whitelisted Image URLs`"
  - Suggested key: `staff.whitelisted_image_urls`
  - Context: `            <h2 class="panel__heading">Whitelisted Image URLs</h2>`

- **Line 66**: "`URL Pattern`"
  - Suggested key: `staff.url_pattern`
  - Context: `                    <th>URL Pattern</th>`

- **Line 67**: "`Example bypassed URL`"
  - Suggested key: `staff.example_bypassed_url`
  - Context: `                    <th>Example bypassed URL</th>`

- **Line 173**: "`No whitelisted image urls.`"
  - Suggested key: `staff.no_whitelisted_image_urls`
  - Context: `                            <td colspan="5">No whitelisted image urls.</td>`

- **Line 211**: "`https://evil.example/subdomain.whitelisted-domain.example/image.png`"
  - Suggested key: `staff.https_evil_example_subdomain_w`
  - Context: `                <code>https://evil.example/subdomain.whitelisted-domain.example/image.png</code>`

- **Line 218**: "`https://evilimgur.com`"
  - Suggested key: `staff.https_evilimgur_com`
  - Context: `                <code>https://evilimgur.com</code>`

- **Line 220**: "`https://*imgur.com/**`"
  - Suggested key: `staff.https_imgur_com`
  - Context: `                <code>https://*imgur.com/**</code>`

- **Line 222**: "`https://*.imgur.com/**`"
  - Suggested key: `staff.https_imgur_com`
  - Context: `                <code>https://*.imgur.com/**</code>`

- **Line 224**: "`https://i.imgur.com/**`"
  - Suggested key: `staff.https_i_imgur_com`
  - Context: `                <code>https://i.imgur.com/**</code>`

### `resources/views/Staff/wiki/create.blade.php` (1 strings)

- **Line 10**: "`Wikis`"
  - Suggested key: `staff.wikis`
  - Context: `        <a href="{{ route('staff.wiki_categories.index') }}" class="breadcrumb__link">Wikis</a>`

### `resources/views/Staff/wiki/edit.blade.php` (1 strings)

- **Line 10**: "`Wikis`"
  - Suggested key: `staff.wikis`
  - Context: `        <a href="{{ route('staff.wiki_categories.index') }}" class="breadcrumb__link">Wikis</a>`

### `resources/views/Staff/wiki_category/index.blade.php` (2 strings)

- **Line 9**: "`Wiki Categories`"
  - Suggested key: `staff.wiki_categories`
  - Context: `    <li class="breadcrumb--active">Wiki Categories</li>`

- **Line 104**: "`No Wikis`"
  - Suggested key: `staff.no_wikis`
  - Context: `                                <td colspan="4">No Wikis</td>`

### `resources/views/auth/application/create.blade.php` (1 strings)

- **Line 102**: "`Proofs`"
  - Suggested key: `auth.proofs`
  - Context: `                        <label class="auth-form__label">Proofs</label>`

### `resources/views/auth/verify-email.blade.php` (3 strings)

- **Line 39**: "`Almost done...`"
  - Suggested key: `auth.almost_done`
  - Context: `                        <li class="auth-form__important-info">Almost done...</li>`

- **Line 66**: "`Having issues?`"
  - Suggested key: `auth.having_issues`
  - Context: `                        <summary class="auth-form__dropdown-text">Having issues?</summary>`

- **Line 67**: "`Resend verification email`"
  - Suggested key: `auth.resend_verification_email`
  - Context: `                        <button class="auth-form__primary-button">Resend verification email</button>`

### `resources/views/blocks/latest_topics.blade.php` (1 strings)

- **Line 20**: "`No topics.`"
  - Suggested key: `blocks.no_topics`
  - Context: `        <div class="panel__body">No topics.</div>`

### `resources/views/components/forum/post.blade.php` (3 strings)

- **Line 180**: "`Online`"
  - Suggested key: `common.online`
  - Context: `                        title="Online"`

- **Line 185**: "`Offline`"
  - Suggested key: `common.offline`
  - Context: `                        title="Offline"`

- **Line 202**: "`Joined`"
  - Suggested key: `common.joined`
  - Context: `            <dt>Joined</dt>`

### `resources/views/components/partials/_torrent-icons.blade.php` (2 strings)

- **Line 98**: "`$alwaysFreeleech \|\| (90`"
  - Suggested key: `common.alwaysfreeleech_90`
  - Context: `                    'fa-star' => $alwaysFreeleech ...<= $torrent->free && $torrent->fl_until === null),`

- **Line 99**: "`! $alwaysFreeleech &amp;&amp; $torrent-&gt;free`"
  - Suggested key: `common.alwaysfreeleech_torrent_free`
  - Context: `                    'fa-star-half' => ! $alwaysFre...torrent->free < 90 && $torrent->fl_until === null,`

### `resources/views/components/torrent/card.blade.php` (6 strings)

- **Line 10**: "`&amp;bull;`"
  - Suggested key: `common.bull`
  - Context: `            <span class="torrent-card__meta-separator">•</span>`

- **Line 16**: "`&amp;bull;`"
  - Suggested key: `common.bull`
  - Context: `            <span class="torrent-card__meta-separator">•</span>`

- **Line 24**: "`&amp;bull;`"
  - Suggested key: `common.bull`
  - Context: `            <span class="torrent-card__meta-separator">•</span>`

- **Line 29**: "`&amp;bull;`"
  - Suggested key: `common.bull`
  - Context: `            <span class="torrent-card__meta-separator">•</span>`

- **Line 94**: "`&amp;bull;`"
  - Suggested key: `common.bull`
  - Context: `            <span class="torrent-card__meta-separator">•</span>`

- **Line 117**: "`&amp;bull;`"
  - Suggested key: `common.bull`
  - Context: `            <span class="torrent-card__meta-separator">•</span>`

### `resources/views/components/torrent/comment-listing.blade.php` (2 strings)

- **Line 46**: "`Online`"
  - Suggested key: `common.online`
  - Context: `                        title="Online"`

- **Line 51**: "`Offline`"
  - Suggested key: `common.offline`
  - Context: `                        title="Offline"`

### `resources/views/components/torrent/row.blade.php` (1 strings)

- **Line 202**: "`N/A`"
  - Suggested key: `common.n_a`
  - Context: `        <td class="torrent-search--list__rating">N/A</td>`

### `resources/views/components/tv/card.blade.php` (3 strings)

- **Line 63**: "`Complete Pack`"
  - Suggested key: `common.complete_pack`
  - Context: `                <summary x-bind="complete">Complete Pack</summary>`

- **Line 95**: "`Specials`"
  - Suggested key: `common.specials`
  - Context: `                <summary x-bind="specials">Specials</summary>`

- **Line 162**: "`Season Pack`"
  - Suggested key: `common.season_pack`
  - Context: `                        <summary x-bind="pack">Season Pack</summary>`

### `resources/views/components/user_tag.blade.php` (4 strings)

- **Line 33**: "`Lifetime Donor`"
  - Suggested key: `common.lifetime_donor`
  - Context: `                <i class="fal fa-star" id="lifeline" title="Lifetime Donor"></i>`

- **Line 36**: "`Donor`"
  - Suggested key: `common.donor`
  - Context: `                <i class="fal fa-star text-gold" title="Donor"></i>`

- **Line 69**: "`Lifetime Donor`"
  - Suggested key: `common.lifetime_donor`
  - Context: `            <i class="fal fa-star" id="lifeline" title="Lifetime Donor"></i>`

- **Line 72**: "`Donor`"
  - Suggested key: `common.donor`
  - Context: `            <i class="fal fa-star text-gold" title="Donor"></i>`

### `resources/views/donation/index.blade.php` (7 strings)

- **Line 12**: "`Donate`"
  - Suggested key: `common.donate`
  - Context: `    <li class="breadcrumb--active">Donate</li>`

- **Line 46**: "`Unlimited Download Slots`"
  - Suggested key: `common.unlimited_download_slots`
  - Context: `                                        <li>Unlimited Download Slots</li>`

- **Line 50**: "`Custom User Icon`"
  - Suggested key: `common.custom_user_icon`
  - Context: `                                        <li>Custom User Icon</li>`

- **Line 54**: "`Immunity To Automated Warnings (Don&#039;t Abuse)`"
  - Suggested key: `common.immunity_to_automated_warnings`
  - Context: `                                    <li>Immunity To Automated Warnings (Don't Abuse)</li>`

- **Line 69**: "`Lifetime Donor`"
  - Suggested key: `common.lifetime_donor`
  - Context: `                                                title="Lifetime Donor"`

- **Line 72**: "`Donor`"
  - Suggested key: `common.donor`
  - Context: `                                            <i class="fal fa-star text-gold" title="Donor"></i>`

- **Line 186**: "`Donate`"
  - Suggested key: `common.donate`
  - Context: `                        <button class="form__button form__button--filled">Donate</button>`

### `resources/views/errors/307.blade.php` (3 strings)

- **Line 73**: "`Please forgive the inconvenience.`"
  - Suggested key: `common.please_forgive_the_inconvenien`
  - Context: `    <p>Please forgive the inconvenience.</p>`

- **Line 74**: "`We are currently building or revamping this feature.`"
  - Suggested key: `common.we_are_currently_building_or_r`
  - Context: `    <p>We are currently building or revamping this feature.</p>`

- **Line 75**: "`It&#039;s okay, we&#039;re excited too!`"
  - Suggested key: `common.it_s_okay_we_re_excited_too`
  - Context: `    <p>It's okay, we're excited too!</p>`

### `resources/views/event/show.blade.php` (2 strings)

- **Line 57**: "`No winnings :(`"
  - Suggested key: `event.no_winnings`
  - Context: `                                    <i class="events__prize-message">No winnings :(</i>`

- **Line 64**: "`Check back later!`"
  - Suggested key: `event.check_back_later`
  - Context: `                                    <i class="events__prize-message">Check back later!</i>`

### `resources/views/forum/index.blade.php` (2 strings)

- **Line 43**: "`No forums in category.`"
  - Suggested key: `forum.no_forums_in_category`
  - Context: `                <div class="panel__body">No forums in category.</div>`

- **Line 79**: "`Mark all topics as read`"
  - Suggested key: `forum.mark_all_topics_as_read`
  - Context: `                        title="Mark all topics as read"`

### `resources/views/forum/topic/show.blade.php` (1 strings)

- **Line 297**: "`Edit Topic Priority`"
  - Suggested key: `forum.edit_topic_priority`
  - Context: `            <h2 class="panel__heading">Edit Topic Priority</h2>`

### `resources/views/livewire/announce-search.blade.php` (2 strings)

- **Line 4**: "`Announces`"
  - Suggested key: `common.announces`
  - Context: `            <h2 class="panel__heading">Announces</h2>`

- **Line 53**: "`Loading...`"
  - Suggested key: `common.loading`
  - Context: `        <div class="panel__body" wire:loading.block>Loading...</div>`

### `resources/views/livewire/apikey-search.blade.php` (1 strings)

- **Line 95**: "`No API Keys`"
  - Suggested key: `common.no_api_keys`
  - Context: `                        <td colspan="4">No API Keys</td>`

### `resources/views/livewire/audit-log-search.blade.php` (8 strings)

- **Line 26**: "`Model Name`"
  - Suggested key: `common.model_name`
  - Context: `                    <label class="form__label form__label--floating" for="model">Model Name</label>`

- **Line 39**: "`Model Id`"
  - Suggested key: `common.model_id`
  - Context: `                    <label class="form__label form__label--floating" for="modelId">Model Id</label>`

- **Line 47**: "`Update`"
  - Suggested key: `common.update`
  - Context: `                        <option value="update">Update</option>`

- **Line 63**: "`Record`"
  - Suggested key: `common.record`
  - Context: `                    <label class="form__label form__label--floating" for="record">Record</label>`

- **Line 86**: "`Model`"
  - Suggested key: `common.model`
  - Context: `                    <th>Model</th>`

- **Line 87**: "`Model ID`"
  - Suggested key: `common.model_id`
  - Context: `                    <th>Model ID</th>`

- **Line 89**: "`Changes`"
  - Suggested key: `common.changes`
  - Context: `                    <th>Changes</th>`

- **Line 160**: "`No audits`"
  - Suggested key: `common.no_audits`
  - Context: `                        <td colspan="8">No audits</td>`

### `resources/views/livewire/backup-panel.blade.php` (9 strings)

- **Line 4**: "`UNIT3D Backup Manager`"
  - Suggested key: `common.unit3d_backup_manager`
  - Context: `            <h2 class="panel__heading">UNIT3D Backup Manager</h2>`

- **Line 43**: "`Disk`"
  - Suggested key: `common.disk`
  - Context: `                        <th scope="col">Disk</th>`

- **Line 44**: "`Healthy`"
  - Suggested key: `common.healthy`
  - Context: `                        <th scope="col">Healthy</th>`

- **Line 45**: "`Amount of backups`"
  - Suggested key: `common.amount_of_backups`
  - Context: `                        <th scope="col">Amount of backups</th>`

- **Line 46**: "`Newest backup`"
  - Suggested key: `common.newest_backup`
  - Context: `                        <th scope="col">Newest backup</th>`

- **Line 47**: "`Used storage`"
  - Suggested key: `common.used_storage`
  - Context: `                        <th scope="col">Used storage</th>`

- **Line 113**: "`Delete backup`"
  - Suggested key: `common.delete_backup`
  - Context: `                                            <h3 class="dialog__heading">Delete backup</h3>`

- **Line 145**: "`No backups present`"
  - Suggested key: `common.no_backups_present`
  - Context: `                            <td colspan="4">No backups present</td>`

- **Line 173**: "`Success`"
  - Suggested key: `common.success`
  - Context: `            title: '<strong style=" color: rgb(17,17,17);">Success</strong>',`

### `resources/views/livewire/bbcode-input.blade.php` (19 strings)

- **Line 36**: "`Bold`"
  - Suggested key: `common.bold`
  - Context: `                <abbr title="Bold">`

- **Line 43**: "`Italics`"
  - Suggested key: `common.italics`
  - Context: `                <abbr title="Italics">`

- **Line 54**: "`Underline`"
  - Suggested key: `common.underline`
  - Context: `                <abbr title="Underline">`

- **Line 65**: "`Strikethrough`"
  - Suggested key: `common.strikethrough`
  - Context: `                <abbr title="Strikethrough">`

- **Line 73**: "`Insert Image`"
  - Suggested key: `common.insert_image`
  - Context: `                <abbr title="Insert Image">`

- **Line 80**: "`Insert YouTube`"
  - Suggested key: `common.insert_youtube`
  - Context: `                <abbr title="Insert YouTube">`

- **Line 99**: "`Unordered List`"
  - Suggested key: `common.unordered_list`
  - Context: `                <abbr title="Unordered List">`

- **Line 110**: "`Ordered List`"
  - Suggested key: `common.ordered_list`
  - Context: `                <abbr title="Ordered List">`

- **Line 118**: "`Font Color`"
  - Suggested key: `common.font_color`
  - Context: `                <abbr title="Font Color">`

- **Line 125**: "`Font Size`"
  - Suggested key: `common.font_size`
  - Context: `                <abbr title="Font Size">`

- **Line 136**: "`Font`"
  - Suggested key: `common.font`
  - Context: `                <abbr title="Font Family">Font</abbr>`

- **Line 136**: "`Font Family`"
  - Suggested key: `common.font_family`
  - Context: `                <abbr title="Font Family">Font</abbr>`

- **Line 142**: "`Align left`"
  - Suggested key: `common.align_left`
  - Context: `                <abbr title="Align left">`

- **Line 149**: "`Align center`"
  - Suggested key: `common.align_center`
  - Context: `                <abbr title="Align center">`

- **Line 156**: "`Align right`"
  - Suggested key: `common.align_right`
  - Context: `                <abbr title="Align right">`

- **Line 178**: "`Spoiler`"
  - Suggested key: `common.spoiler`
  - Context: `                <abbr title="Spoiler">`

- **Line 192**: "`Alert`"
  - Suggested key: `common.alert`
  - Context: `                <abbr title="Alert">`

- **Line 199**: "`Table`"
  - Suggested key: `common.table`
  - Context: `                <abbr title="Table">`

- **Line 207**: "`If using MacOS, press Ctrl + Cmd + Space bar&amp;NewLine;If using Windows or Linux, press Windows logo key + .`"
  - Suggested key: `common.if_using_macos_press_ctrl_cmd_`
  - Context: `                    title="If using MacOS, press C...sing Windows or Linux, press Windows logo key + ."`

### `resources/views/livewire/block-ip-address.blade.php` (2 strings)

- **Line 10**: "`Block Ip Address`"
  - Suggested key: `common.block_ip_address`
  - Context: `                    <h3 class="dialog__heading">Block Ip Address</h3>`

- **Line 155**: "`No blocked ip addresses`"
  - Suggested key: `common.no_blocked_ip_addresses`
  - Context: `                        <td colspan="7">No blocked ip addresses</td>`

### `resources/views/livewire/comment.blade.php` (4 strings)

- **Line 15**: "`Reply to this comment`"
  - Suggested key: `common.reply_to_this_comment`
  - Context: `                            <abbr class="comment__reply-abbr" title="Reply to this comment">`

- **Line 17**: "`__(&#039;pm.reply&#039;)`"
  - Suggested key: `common.pm_reply`
  - Context: `                                <span class="sr-only">__('pm.reply')</span>`

- **Line 52**: "`__(&#039;common.edit&#039;)`"
  - Suggested key: `common.common_edit`
  - Context: `                                <span class="sr-only">__('common.edit')</span>`

- **Line 73**: "`__(&#039;common.delete&#039;)`"
  - Suggested key: `common.common_delete`
  - Context: `                                <span class="sr-only">__('common.delete')</span>`

### `resources/views/livewire/email-update-search.blade.php` (1 strings)

- **Line 75**: "`No email updates`"
  - Suggested key: `common.no_email_updates`
  - Context: `                        <td colspan="3">No email updates</td>`

### `resources/views/livewire/failed-login-search.blade.php` (5 strings)

- **Line 111**: "`No failed logins`"
  - Suggested key: `common.no_failed_logins`
  - Context: `                        <td colspan="5">No failed logins</td>`

- **Line 122**: "`Top 10 Failed Logins By IP`"
  - Suggested key: `common.top_10_failed_logins_by_ip`
  - Context: `        <h2 class="panel__heading">Top 10 Failed Logins By IP</h2>`

- **Line 128**: "`Count`"
  - Suggested key: `common.count`
  - Context: `                        <td>Count</td>`

- **Line 129**: "`Most recent`"
  - Suggested key: `common.most_recent`
  - Context: `                        <td>Most recent</td>`

- **Line 148**: "`No IPs with more than 5 failed logins`"
  - Suggested key: `common.no_ips_with_more_than_5_failed`
  - Context: `                            <td colspan="3">No IPs with more than 5 failed logins</td>`

### `resources/views/livewire/forum-category-topic-search.blade.php` (9 strings)

- **Line 15**: "`No topics.`"
  - Suggested key: `common.no_topics`
  - Context: `                <div class="panel__body">No topics.</div>`

- **Line 33**: "`Mark all topics as read`"
  - Suggested key: `common.mark_all_topics_as_read`
  - Context: `                            title="Mark all topics as read"`

- **Line 60**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

- **Line 61**: "`With unread posts`"
  - Suggested key: `common.with_unread_posts`
  - Context: `                            <option value="some">With unread posts</option>`

- **Line 62**: "`Newly added`"
  - Suggested key: `common.newly_added`
  - Context: `                            <option value="none">Newly added</option>`

- **Line 63**: "`Fully read`"
  - Suggested key: `common.fully_read`
  - Context: `                            <option value="all">Fully read</option>`

- **Line 74**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

- **Line 146**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

- **Line 165**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

### `resources/views/livewire/forum-topic-search.blade.php` (9 strings)

- **Line 15**: "`No topics.`"
  - Suggested key: `common.no_topics`
  - Context: `                <div class="panel__body">No topics.</div>`

- **Line 75**: "`Mark all topics in this forum as read`"
  - Suggested key: `common.mark_all_topics_in_this_forum_`
  - Context: `                            title="Mark all topics in this forum as read"`

- **Line 102**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

- **Line 103**: "`With unread posts`"
  - Suggested key: `common.with_unread_posts`
  - Context: `                            <option value="some">With unread posts</option>`

- **Line 104**: "`Newly added`"
  - Suggested key: `common.newly_added`
  - Context: `                            <option value="none">Newly added</option>`

- **Line 105**: "`Fully read`"
  - Suggested key: `common.fully_read`
  - Context: `                            <option value="all">Fully read</option>`

- **Line 116**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

- **Line 188**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

- **Line 207**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

### `resources/views/livewire/gift-log-search.blade.php` (1 strings)

- **Line 120**: "`No gifts`"
  - Suggested key: `common.no_gifts`
  - Context: `                        <td colspan="5">No gifts</td>`

### `resources/views/livewire/history-search.blade.php` (12 strings)

- **Line 51**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="any">Any</option>`

- **Line 53**: "`Incomplete`"
  - Suggested key: `common.incomplete`
  - Context: `                            <option value="exclude">Incomplete</option>`

- **Line 66**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="any">Any</option>`

- **Line 67**: "`Inactive`"
  - Suggested key: `common.inactive`
  - Context: `                            <option value="exclude">Inactive</option>`

- **Line 79**: "`None`"
  - Suggested key: `common.none`
  - Context: `                            <option value="none">None</option>`

- **Line 91**: "`Histories`"
  - Suggested key: `common.histories`
  - Context: `        <h2 class="panel__heading">Histories</h2>`

- **Line 92**: "`Loading...`"
  - Suggested key: `common.loading`
  - Context: `        <div class="panel__body" wire:loading.block>Loading...</div>`

- **Line 507**: "`Inactive`"
  - Suggested key: `common.inactive`
  - Context: `                                                title="Inactive"`

- **Line 528**: "`Immune`"
  - Suggested key: `common.immune`
  - Context: `                                                title="Immune"`

- **Line 533**: "`Not immune`"
  - Suggested key: `common.not_immune`
  - Context: `                                                title="Not immune"`

- **Line 541**: "`Warned`"
  - Suggested key: `common.warned`
  - Context: `                                                title="Warned"`

- **Line 546**: "`Not warned`"
  - Suggested key: `common.not_warned`
  - Context: `                                                title="Not warned"`

### `resources/views/livewire/invite-log-search.blade.php` (4 strings)

- **Line 99**: "`Colors &#039;Percent Inactive&#039; red if above this threshold`"
  - Suggested key: `common.colors_percent_inactive_red_if`
  - Context: `                                title="Colors 'Percent Inactive' red if above this threshold"`

- **Line 114**: "`None`"
  - Suggested key: `common.none`
  - Context: `                            <option value="none">None</option>`

- **Line 273**: "`No invites`"
  - Suggested key: `common.no_invites`
  - Context: `                                    <td colspan="8">No invites</td>`

- **Line 378**: "`No invites`"
  - Suggested key: `common.no_invites`
  - Context: `                                    <td colspan="10">No invites</td>`

### `resources/views/livewire/laravel-log-viewer.blade.php` (9 strings)

- **Line 17**: "`Laravel Log Viewer`"
  - Suggested key: `common.laravel_log_viewer`
  - Context: `    <li class="breadcrumb--active">Laravel Log Viewer</li>`

- **Line 52**: "`Level`"
  - Suggested key: `common.level`
  - Context: `                        <th>Level</th>`

- **Line 54**: "`Exception`"
  - Suggested key: `common.exception`
  - Context: `                        <th>Exception</th>`

- **Line 56**: "`Line`"
  - Suggested key: `common.line`
  - Context: `                        <th>Line</th>`

- **Line 57**: "`Count`"
  - Suggested key: `common.count`
  - Context: `                        <th>Count</th>`

- **Line 106**: "`Environment`"
  - Suggested key: `common.environment`
  - Context: `                                            <th>Environment</th>`

- **Line 107**: "`Stacktrace`"
  - Suggested key: `common.stacktrace`
  - Context: `                                            <th>Stacktrace</th>`

- **Line 140**: "`No logs have been created yet.`"
  - Suggested key: `common.no_logs_have_been_created_yet`
  - Context: `                            <td colspan="7">No logs have been created yet.</td>`

- **Line 155**: "`Entries`"
  - Suggested key: `common.entries`
  - Context: `        <h2 class="panel__heading">Entries</h2>`

### `resources/views/livewire/leaker-search.blade.php` (4 strings)

- **Line 4**: "`Leakers`"
  - Suggested key: `common.leakers`
  - Context: `            <h2 class="panel__heading">Leakers</h2>`

- **Line 57**: "`Loading...`"
  - Suggested key: `common.loading`
  - Context: `        <div class="panel__body" wire:loading.block>Loading...</div>`

- **Line 70**: "`User Agents`"
  - Suggested key: `common.user_agents`
  - Context: `                        <th>User Agents</th>`

- **Line 71**: "`IPs`"
  - Suggested key: `common.ips`
  - Context: `                        <th>IPs</th>`

### `resources/views/livewire/missing-media-search.blade.php` (1 strings)

- **Line 3**: "`Missing Media`"
  - Suggested key: `common.missing_media`
  - Context: `        <h2 class="panel__heading">Missing Media</h2>`

### `resources/views/livewire/note-search.blade.php` (1 strings)

- **Line 81**: "`No notes`"
  - Suggested key: `common.no_notes`
  - Context: `                        <td colspan="6">No notes</td>`

### `resources/views/livewire/passkey-search.blade.php` (1 strings)

- **Line 95**: "`No passkeys`"
  - Suggested key: `common.no_passkeys`
  - Context: `                        <td colspan="4">No passkeys</td>`

### `resources/views/livewire/password-reset-history-search.blade.php` (1 strings)

- **Line 62**: "`No Password Resets`"
  - Suggested key: `common.no_password_resets`
  - Context: `                        <td colspan="4">No Password Resets</td>`

### `resources/views/livewire/peer-search.blade.php` (13 strings)

- **Line 24**: "`IP Address`"
  - Suggested key: `common.ip_address`
  - Context: `                        <label class="form__label form__label--floating" for="ip">IP Address</label>`

- **Line 55**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="any">Any</option>`

- **Line 56**: "`Connectable`"
  - Suggested key: `common.connectable`
  - Context: `                            <option value="connectable">Connectable</option>`

- **Line 57**: "`Unconnectable`"
  - Suggested key: `common.unconnectable`
  - Context: `                            <option value="unconnectable">Unconnectable</option>`

- **Line 70**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="any">Any</option>`

- **Line 71**: "`Inactive`"
  - Suggested key: `common.inactive`
  - Context: `                            <option value="exclude">Inactive</option>`

- **Line 83**: "`None`"
  - Suggested key: `common.none`
  - Context: `                            <option value="none">None</option>`

- **Line 84**: "`User Session`"
  - Suggested key: `common.user_session`
  - Context: `                            <option value="user_session">User Session</option>`

- **Line 85**: "`User IP`"
  - Suggested key: `common.user_ip`
  - Context: `                            <option value="user_ip">User IP</option>`

- **Line 128**: "`Loading...`"
  - Suggested key: `common.loading`
  - Context: `        <div class="panel__body" wire:loading.block>Loading...</div>`

- **Line 412**: "`Connectable`"
  - Suggested key: `common.connectable`
  - Context: `                                                title="Connectable"`

- **Line 417**: "`Not Connectable`"
  - Suggested key: `common.not_connectable`
  - Context: `                                                title="Not Connectable"`

- **Line 441**: "`Inactive`"
  - Suggested key: `common.inactive`
  - Context: `                                            title="Inactive"`

### `resources/views/livewire/random-media.blade.php` (2 strings)

- **Line 30**: "`MOVIE`"
  - Suggested key: `common.movie`
  - Context: `                    <span style="padding-left: 6px">MOVIE</span>`

- **Line 68**: "`MOVIE`"
  - Suggested key: `common.movie`
  - Context: `                    <span style="padding-left: 6px">MOVIE</span>`

### `resources/views/livewire/report-search.blade.php` (4 strings)

- **Line 94**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="">Any</option>`

- **Line 111**: "`Snoozed`"
  - Suggested key: `common.snoozed`
  - Context: `                            <option value="snoozed">Snoozed</option>`

- **Line 114**: "`All open`"
  - Suggested key: `common.all_open`
  - Context: `                            <option value="all_open">All open</option>`

- **Line 224**: "`No reports`"
  - Suggested key: `common.no_reports`
  - Context: `                            <td colspan="8">No reports</td>`

### `resources/views/livewire/rsskey-search.blade.php` (1 strings)

- **Line 95**: "`No rsskeys`"
  - Suggested key: `common.no_rsskeys`
  - Context: `                        <td colspan="4">No rsskeys</td>`

### `resources/views/livewire/similar-torrent.blade.php` (15 strings)

- **Line 147**: "`Bytes`"
  - Suggested key: `common.bytes`
  - Context: `                                    <option value="1" selected>Bytes</option>`

- **Line 149**: "`KiB`"
  - Suggested key: `common.kib`
  - Context: `                                    <option value="1024">KiB</option>`

- **Line 151**: "`MiB`"
  - Suggested key: `common.mib`
  - Context: `                                    <option value="1048576">MiB</option>`

- **Line 153**: "`GiB`"
  - Suggested key: `common.gib`
  - Context: `                                    <option value="1073741824">GiB</option>`

- **Line 155**: "`TiB`"
  - Suggested key: `common.tib`
  - Context: `                                    <option value="1099511627776">TiB</option>`

- **Line 186**: "`Bytes`"
  - Suggested key: `common.bytes`
  - Context: `                                    <option value="1" selected>Bytes</option>`

- **Line 188**: "`KiB`"
  - Suggested key: `common.kib`
  - Context: `                                    <option value="1024">KiB</option>`

- **Line 190**: "`MiB`"
  - Suggested key: `common.mib`
  - Context: `                                    <option value="1048576">MiB</option>`

- **Line 192**: "`GiB`"
  - Suggested key: `common.gib`
  - Context: `                                    <option value="1073741824">GiB</option>`

- **Line 194**: "`TiB`"
  - Suggested key: `common.tib`
  - Context: `                                    <option value="1099511627776">TiB</option>`

- **Line 265**: "`Buff`"
  - Suggested key: `common.buff`
  - Context: `                                <legend class="form__legend">Buff</legend>`

- **Line 360**: "`Tags`"
  - Suggested key: `common.tags`
  - Context: `                                <legend class="form__legend">Tags</legend>`

- **Line 588**: "`Complete Pack`"
  - Suggested key: `common.complete_pack`
  - Context: `                                <summary x-bind="complete">Complete Pack</summary>`

- **Line 633**: "`Specials`"
  - Suggested key: `common.specials`
  - Context: `                                <summary x-bind="specials">Specials</summary>`

- **Line 726**: "`Season Pack`"
  - Suggested key: `common.season_pack`
  - Context: `                                        <summary x-bind="pack">Season Pack</summary>`

### `resources/views/livewire/stats/peer-stats.blade.php` (1 strings)

- **Line 13**: "`Total`"
  - Suggested key: `common.total`
  - Context: `            <dt>Total</dt>`

### `resources/views/livewire/stats/user-stats.blade.php` (3 strings)

- **Line 25**: "`Users active today`"
  - Suggested key: `common.users_active_today`
  - Context: `            <dt>Users active today</dt>`

- **Line 29**: "`Users active this week`"
  - Suggested key: `common.users_active_this_week`
  - Context: `            <dt>Users active this week</dt>`

- **Line 33**: "`Users active this month`"
  - Suggested key: `common.users_active_this_month`
  - Context: `            <dt>Users active this month</dt>`

### `resources/views/livewire/subscribed-forum.blade.php` (1 strings)

- **Line 13**: "`No forums in category.`"
  - Suggested key: `common.no_forums_in_category`
  - Context: `        <div class="panel__body">No forums in category.</div>`

### `resources/views/livewire/subscribed-topic.blade.php` (1 strings)

- **Line 13**: "`No topics.`"
  - Suggested key: `common.no_topics`
  - Context: `        <div class="panel__body">No topics.</div>`

### `resources/views/livewire/ticket-search.blade.php` (1 strings)

- **Line 41**: "`My Assigned Tickets`"
  - Suggested key: `common.my_assigned_tickets`
  - Context: `                        <label class="form__label" for="show">My Assigned Tickets</label>`

### `resources/views/livewire/top-users.blade.php` (2 strings)

- **Line 2**: "`Top Users`"
  - Suggested key: `common.top_users`
  - Context: `    <h2 class="panel__heading">Top Users</h2>`

- **Line 93**: "`Loading...`"
  - Suggested key: `common.loading`
  - Context: `    <div class="panel__body" wire:loading.block>Loading...</div>`

### `resources/views/livewire/top10.blade.php` (17 strings)

- **Line 9**: "`Top Titles`"
  - Suggested key: `common.top_titles`
  - Context: `        <h2 class="panel__heading">Top Titles</h2>`

- **Line 20**: "`Past Day`"
  - Suggested key: `common.past_day`
  - Context: `                        <option value="day">Past Day</option>`

- **Line 21**: "`Past Week`"
  - Suggested key: `common.past_week`
  - Context: `                        <option value="week">Past Week</option>`

- **Line 22**: "`Past Month`"
  - Suggested key: `common.past_month`
  - Context: `                        <option value="month">Past Month</option>`

- **Line 23**: "`Past Year`"
  - Suggested key: `common.past_year`
  - Context: `                        <option value="year">Past Year</option>`

- **Line 24**: "`All-time`"
  - Suggested key: `common.all_time`
  - Context: `                        <option value="all">All-time</option>`

- **Line 25**: "`Weekly`"
  - Suggested key: `common.weekly`
  - Context: `                        <option value="weekly">Weekly</option>`

- **Line 26**: "`Monthly`"
  - Suggested key: `common.monthly`
  - Context: `                        <option value="monthly">Monthly</option>`

- **Line 27**: "`Custom`"
  - Suggested key: `common.custom`
  - Context: `                        <option value="custom">Custom</option>`

- **Line 29**: "`Interval`"
  - Suggested key: `common.interval`
  - Context: `                    <label class="form__label form__label--floating" for="interval">Interval</label>`

- **Line 54**: "`Until`"
  - Suggested key: `common.until`
  - Context: `                        <label class="form__label form__label--floating" for="until">Until</label>`

- **Line 78**: "`Computing...`"
  - Suggested key: `common.computing`
  - Context: `            <div wire:loading.delay class="panel__body">Computing...</div>`

- **Line 83**: "`Week`"
  - Suggested key: `common.week`
  - Context: `                        <th>Week</th>`

- **Line 84**: "`Rankings`"
  - Suggested key: `common.rankings`
  - Context: `                        <th>Rankings</th>`

- **Line 130**: "`Computing...`"
  - Suggested key: `common.computing`
  - Context: `            <div wire:loading.delay class="panel__body">Computing...</div>`

- **Line 136**: "`Rankings`"
  - Suggested key: `common.rankings`
  - Context: `                        <th>Rankings</th>`

- **Line 182**: "`Computing...`"
  - Suggested key: `common.computing`
  - Context: `            <div wire:loading.delay>Computing...</div>`

### `resources/views/livewire/topic-search.blade.php` (10 strings)

- **Line 15**: "`No topics.`"
  - Suggested key: `common.no_topics`
  - Context: `                <div class="panel__body">No topics.</div>`

- **Line 33**: "`Mark all topics as read`"
  - Suggested key: `common.mark_all_topics_as_read`
  - Context: `                            title="Mark all topics as read"`

- **Line 65**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="">Any</option>`

- **Line 83**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

- **Line 84**: "`With unread posts`"
  - Suggested key: `common.with_unread_posts`
  - Context: `                            <option value="some">With unread posts</option>`

- **Line 85**: "`Newly added`"
  - Suggested key: `common.newly_added`
  - Context: `                            <option value="none">Newly added</option>`

- **Line 86**: "`Fully read`"
  - Suggested key: `common.fully_read`
  - Context: `                            <option value="all">Fully read</option>`

- **Line 97**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

- **Line 169**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

- **Line 188**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="" selected default>Any</option>`

### `resources/views/livewire/torrent-download-search.blade.php` (5 strings)

- **Line 17**: "`Torrent Downloads`"
  - Suggested key: `common.torrent_downloads`
  - Context: `    <li class="breadcrumb--active">Torrent Downloads</li>`

- **Line 81**: "`Until`"
  - Suggested key: `common.until`
  - Context: `                        <label class="form__label form__label--floating" for="until">Until</label>`

- **Line 90**: "`None`"
  - Suggested key: `common.none`
  - Context: `                            <option value="none">None</option>`

- **Line 103**: "`Torrent Downloads`"
  - Suggested key: `common.torrent_downloads`
  - Context: `            <h2 class="panel__heading">Torrent Downloads</h2>`

- **Line 124**: "`Loading...`"
  - Suggested key: `common.loading`
  - Context: `        <div class="panel__body" wire:loading.block>Loading...</div>`

### `resources/views/livewire/torrent-request-search.blade.php` (1 strings)

- **Line 297**: "`Primary Language`"
  - Suggested key: `common.primary_language`
  - Context: `                                <legend class="form__legend">Primary Language</legend>`

### `resources/views/livewire/torrent-search.blade.php` (20 strings)

- **Line 175**: "`Bytes`"
  - Suggested key: `common.bytes`
  - Context: `                                <option value="1" selected>Bytes</option>`

- **Line 177**: "`KiB`"
  - Suggested key: `common.kib`
  - Context: `                                <option value="1024">KiB</option>`

- **Line 179**: "`MiB`"
  - Suggested key: `common.mib`
  - Context: `                                <option value="1048576">MiB</option>`

- **Line 181**: "`GiB`"
  - Suggested key: `common.gib`
  - Context: `                                <option value="1073741824">GiB</option>`

- **Line 183**: "`TiB`"
  - Suggested key: `common.tib`
  - Context: `                                <option value="1099511627776">TiB</option>`

- **Line 214**: "`Bytes`"
  - Suggested key: `common.bytes`
  - Context: `                                <option value="1" selected>Bytes</option>`

- **Line 216**: "`KiB`"
  - Suggested key: `common.kib`
  - Context: `                                <option value="1024">KiB</option>`

- **Line 218**: "`MiB`"
  - Suggested key: `common.mib`
  - Context: `                                <option value="1048576">MiB</option>`

- **Line 220**: "`GiB`"
  - Suggested key: `common.gib`
  - Context: `                                <option value="1073741824">GiB</option>`

- **Line 222**: "`TiB`"
  - Suggested key: `common.tib`
  - Context: `                                <option value="1099511627776">TiB</option>`

- **Line 247**: "`Any`"
  - Suggested key: `common.any`
  - Context: `                            <option value="any" selected>Any</option>`

- **Line 248**: "`Include`"
  - Suggested key: `common.include`
  - Context: `                            <option value="include">Include</option>`

- **Line 249**: "`Exclude`"
  - Suggested key: `common.exclude`
  - Context: `                            <option value="exclude">Exclude</option>`

- **Line 251**: "`Adult`"
  - Suggested key: `common.adult`
  - Context: `                        <label class="form__label form__label--floating" for="adult">Adult</label>`

- **Line 357**: "`MAL ID`"
  - Suggested key: `common.mal_id`
  - Context: `                        <label class="form__label form__label--floating" for="malId">MAL ID</label>`

- **Line 443**: "`Buff`"
  - Suggested key: `common.buff`
  - Context: `                            <legend class="form__legend">Buff</legend>`

- **Line 538**: "`Tags`"
  - Suggested key: `common.tags`
  - Context: `                            <legend class="form__legend">Tags</legend>`

- **Line 724**: "`Primary Language`"
  - Suggested key: `common.primary_language`
  - Context: `                            <legend class="form__legend">Primary Language</legend>`

- **Line 758**: "`Layout`"
  - Suggested key: `common.layout`
  - Context: `                        <label class="form__label form__label--floating" for="view">Layout</label>`

- **Line 805**: "`Format`"
  - Suggested key: `common.format`
  - Context: `                                <th class="torrent-search--list__format-header">Format</th>`

### `resources/views/livewire/torrent-trump-search.blade.php` (3 strings)

- **Line 17**: "`Torrent Trumps`"
  - Suggested key: `common.torrent_trumps`
  - Context: `    <li class="breadcrumb--active">Torrent Trumps</li>`

- **Line 22**: "`Torrent Trumps`"
  - Suggested key: `common.torrent_trumps`
  - Context: `        <h2 class="panel__heading">Torrent Trumps</h2>`

- **Line 60**: "`Loading...`"
  - Suggested key: `common.loading`
  - Context: `    <div class="panel__body" wire:loading.block>Loading...</div>`

### `resources/views/livewire/unregistered-info-hash-search.blade.php` (4 strings)

- **Line 3**: "`Unregistered Info Hashes`"
  - Suggested key: `common.unregistered_info_hashes`
  - Context: `        <h2 class="panel__heading">Unregistered Info Hashes</h2>`

- **Line 26**: "`None`"
  - Suggested key: `common.none`
  - Context: `                        <option value="none">None</option>`

- **Line 29**: "`Group By`"
  - Suggested key: `common.group_by`
  - Context: `                    <label class="form__label form__label--floating" for="groupBy">Group By</label>`

- **Line 46**: "`Loading...`"
  - Suggested key: `common.loading`
  - Context: `    <div class="panel__body" wire:loading.block>Loading...</div>`

### `resources/views/livewire/user-active.blade.php` (7 strings)

- **Line 172**: "`Connectable`"
  - Suggested key: `common.connectable`
  - Context: `                                title="Connectable"`

- **Line 190**: "`Visible`"
  - Suggested key: `common.visible`
  - Context: `                        title="Visible"`

- **Line 307**: "`Unknown Connectable Status`"
  - Suggested key: `common.unknown_connectable_status`
  - Context: `                                            title="Unknown Connectable Status"`

- **Line 313**: "`Connectable`"
  - Suggested key: `common.connectable`
  - Context: `                                                title="Connectable"`

- **Line 318**: "`Not Connectable`"
  - Suggested key: `common.not_connectable`
  - Context: `                                                title="Not Connectable"`

- **Line 351**: "`Visible`"
  - Suggested key: `common.visible`
  - Context: `                                        title="Visible"`

- **Line 356**: "`Invisible`"
  - Suggested key: `common.invisible`
  - Context: `                                        title="Invisible"`

### `resources/views/livewire/user-earnings.blade.php` (4 strings)

- **Line 11**: "`Per torrent per hour`"
  - Suggested key: `common.per_torrent_per_hour`
  - Context: `                            <th>Per torrent per hour</th>`

- **Line 140**: "`Connectable`"
  - Suggested key: `common.connectable`
  - Context: `                                    title="Connectable"`

- **Line 254**: "`Connectable`"
  - Suggested key: `common.connectable`
  - Context: `                                            title="Connectable"`

- **Line 257**: "`Not Connectable`"
  - Suggested key: `common.not_connectable`
  - Context: `                                        <span title="Not Connectable">-</span>`

### `resources/views/livewire/user-notes.blade.php` (1 strings)

- **Line 149**: "`No notes`"
  - Suggested key: `common.no_notes`
  - Context: `                        <td colspan="5">No notes</td>`

### `resources/views/livewire/user-search.blade.php` (2 strings)

- **Line 120**: "`Include Soft Deletes`"
  - Suggested key: `common.include_soft_deletes`
  - Context: `                        <label class="form__label" for="show">Include Soft Deletes</label>`

- **Line 227**: "`No users`"
  - Suggested key: `common.no_users`
  - Context: `                                <td colspan="8">No users</td>`

### `resources/views/livewire/user-torrents.blade.php` (5 strings)

- **Line 232**: "`Precision`"
  - Suggested key: `common.precision`
  - Context: `                        <legend class="form__legend">Precision</legend>`

- **Line 657**: "`Warned`"
  - Suggested key: `common.warned`
  - Context: `                                        title="Warned"`

- **Line 662**: "`Not warned`"
  - Suggested key: `common.not_warned`
  - Context: `                                        title="Not warned"`

- **Line 670**: "`Immune`"
  - Suggested key: `common.immune`
  - Context: `                                        title="Immune"`

- **Line 680**: "`Not immune`"
  - Suggested key: `common.not_immune`
  - Context: `                                        title="Not immune"`

### `resources/views/livewire/user-uploads.blade.php` (1 strings)

- **Line 99**: "`Precision`"
  - Suggested key: `common.precision`
  - Context: `                        <legend class="form__legend">Precision</legend>`

### `resources/views/livewire/warning-log-search.blade.php` (2 strings)

- **Line 13**: "`Show Soft Deletes`"
  - Suggested key: `common.show_soft_deletes`
  - Context: `                    <label class="form__label" for="show">Show Soft Deletes</label>`

- **Line 164**: "`No warnings`"
  - Suggested key: `common.no_warnings`
  - Context: `                    <td colspan="7">No warnings</td>`

### `resources/views/livewire/watchlist-search.blade.php` (2 strings)

- **Line 3**: "`Watchlist`"
  - Suggested key: `common.watchlist`
  - Context: `        <h2 class="panel__heading">Watchlist</h2>`

- **Line 97**: "`No watched users`"
  - Suggested key: `common.no_watched_users`
  - Context: `                        <td colspan="5">No watched users</td>`

### `resources/views/mediahub/person/show.blade.php` (1 strings)

- **Line 37**: "`Place of Birth`"
  - Suggested key: `mediahub.place_of_birth`
  - Context: `                <dt>Place of Birth</dt>`

### `resources/views/missing/index.blade.php` (2 strings)

- **Line 4**: "`Missing Media`"
  - Suggested key: `common.missing_media`
  - Context: `    <title>Missing Media</title>`

- **Line 8**: "`Missing Media`"
  - Suggested key: `common.missing_media`
  - Context: `    <li class="breadcrumb--active">Missing Media</li>`

### `resources/views/page/blacklist/client.blade.php` (1 strings)

- **Line 18**: "`Client Name`"
  - Suggested key: `page.client_name`
  - Context: `                        <th>Client Name</th>`

### `resources/views/page/page.blade.php` (2 strings)

- **Line 57**: "`Read The`"
  - Suggested key: `page.read_the`
  - Context: `                        title: '<strong>Read The <u>Rules?</u></strong>',`

- **Line 57**: "`Rules?`"
  - Suggested key: `page.rules`
  - Context: `                        title: '<strong>Read The <u>Rules?</u></strong>',`

### `resources/views/page/staff.blade.php` (1 strings)

- **Line 45**: "`Please contact staff via the helpdesk for account support.`"
  - Suggested key: `page.please_contact_staff_via_the_h`
  - Context: `        <div class="panel__body">Please contact staff via the helpdesk for account support.</div>`

### `resources/views/partials/achievement_modal.blade.php` (1 strings)

- **Line 13**: "`Well done!`"
  - Suggested key: `common.well_done`
  - Context: `        <span>Well done!</span>`

### `resources/views/partials/footer.blade.php` (7 strings)

- **Line 45**: "`Wikis`"
  - Suggested key: `common.wikis`
  - Context: `                <li><a href="{{ route('wikis.index') }}">Wikis</a></li>`

- **Line 61**: "`[View All]`"
  - Suggested key: `common.view_all`
  - Context: `                        <a href="{{ route('pages.index') }}">[View All]</a>`

- **Line 241**: "`Time:`"
  - Suggested key: `common.time`
  - Context: `            <strong>Time:</strong>`

- **Line 246**: "`Used:`"
  - Suggested key: `common.used`
  - Context: `            <strong>Used:</strong>`

- **Line 248**: "`Load:`"
  - Suggested key: `common.load`
  - Context: `            <strong>Load:</strong>`

- **Line 252**: "`Date:`"
  - Suggested key: `common.date`
  - Context: `            <strong>Date:</strong>`

- **Line 264**: "`UNIT3D-Announce`"
  - Suggested key: `common.unit3d_announce`
  - Context: `                <a href="https://github.com/HDInnovations/UNIT3D-Announce">UNIT3D-Announce</a>`

### `resources/views/partials/pagination.blade.php` (2 strings)

- **Line 64**: "`&amp;middot;&amp;middot;&amp;middot;`"
  - Suggested key: `common.middot_middot_middot`
  - Context: `                            <li class="pagination__ellipsis">···</li>`

- **Line 99**: "`&amp;middot;&amp;middot;&amp;middot;`"
  - Suggested key: `common.middot_middot_middot`
  - Context: `                                <li class="pagination__ellipsis">···</li>`

### `resources/views/partials/quick-search-dropdown.blade.php` (2 strings)

- **Line 17**: "`Search movies, tv series, or people`"
  - Suggested key: `common.search_movies_tv_series_or_peo`
  - Context: `                    <p class="quick-search__result-text">Search movies, tv series, or people</p>`

- **Line 24**: "`No results found`"
  - Suggested key: `common.no_results_found`
  - Context: `                    <p class="quick-search__result-text">No results found</p>`

### `resources/views/requests/create.blade.php` (5 strings)

- **Line 151**: "`Numeric digits only.`"
  - Suggested key: `request.numeric_digits_only`
  - Context: `                                <span class="form__hint">Numeric digits only.</span>`

- **Line 185**: "`Numeric digits only.`"
  - Suggested key: `request.numeric_digits_only`
  - Context: `                                <span class="form__hint">Numeric digits only.</span>`

- **Line 226**: "`Numeric digits only.`"
  - Suggested key: `request.numeric_digits_only`
  - Context: `                                <span class="form__hint">Numeric digits only.</span>`

- **Line 260**: "`Numeric digits only.`"
  - Suggested key: `request.numeric_digits_only`
  - Context: `                                <span class="form__hint">Numeric digits only.</span>`

- **Line 301**: "`Numeric digits only.`"
  - Suggested key: `request.numeric_digits_only`
  - Context: `                                <span class="form__hint">Numeric digits only.</span>`

### `resources/views/requests/edit.blade.php` (4 strings)

- **Line 169**: "`Numeric digits only.`"
  - Suggested key: `request.numeric_digits_only`
  - Context: `                                <span class="form__hint">Numeric digits only.</span>`

- **Line 207**: "`Numeric digits only.`"
  - Suggested key: `request.numeric_digits_only`
  - Context: `                                <span class="form__hint">Numeric digits only.</span>`

- **Line 248**: "`Numeric digits only.`"
  - Suggested key: `request.numeric_digits_only`
  - Context: `                                <span class="form__hint">Numeric digits only.</span>`

- **Line 282**: "`Numeric digits only.`"
  - Suggested key: `request.numeric_digits_only`
  - Context: `                                <span class="form__hint">Numeric digits only.</span>`

### `resources/views/rss/index.blade.php` (2 strings)

- **Line 145**: "`No public RSS feeds`"
  - Suggested key: `rss.no_public_rss_feeds`
  - Context: `                            <td colspan="8">No public RSS feeds</td>`

- **Line 298**: "`No private RSS feeds`"
  - Suggested key: `rss.no_private_rss_feeds`
  - Context: `                            <td colspan="9">No private RSS feeds</td>`

### `resources/views/rss/show.blade.php` (2 strings)

- **Line 17**: "`en-us`"
  - Suggested key: `rss.en_us`
  - Context: `        <language>en-us</language>`

- **Line 65**: "`This is a high quality internal release!`"
  - Suggested key: `rss.this_is_a_high_quality_interna`
  - Context: `                                <comments>This is a high quality internal release!</comments>`

### `resources/views/stats/groups/groups-requirements.blade.php` (10 strings)

- **Line 35**: "`Requirement`"
  - Suggested key: `stat.requirement`
  - Context: `                        <th>Requirement</th>`

- **Line 36**: "`Perks`"
  - Suggested key: `stat.perks`
  - Context: `                        <th>Perks</th>`

- **Line 60**: "`Requirement`"
  - Suggested key: `stat.requirement`
  - Context: `                                                <td>Requirement</td>`

- **Line 61**: "`To Advance`"
  - Suggested key: `stat.to_advance`
  - Context: `                                                <td>To Advance</td>`

- **Line 66**: "`Min. Upload`"
  - Suggested key: `stat.min_upload`
  - Context: `                                                <td>Min. Upload</td>`

- **Line 85**: "`Min. Ratio`"
  - Suggested key: `stat.min_ratio`
  - Context: `                                                <td>Min. Ratio</td>`

- **Line 101**: "`Min. Account Age`"
  - Suggested key: `stat.min_account_age`
  - Context: `                                                <td>Min. Account Age</td>`

- **Line 124**: "`Min. Average Seedtime`"
  - Suggested key: `stat.min_average_seedtime`
  - Context: `                                                <td>Min. Average Seedtime</td>`

- **Line 143**: "`Min. Seedsize`"
  - Suggested key: `stat.min_seedsize`
  - Context: `                                                <td>Min. Seedsize</td>`

- **Line 162**: "`Min. Uploads`"
  - Suggested key: `stat.min_uploads`
  - Context: `                                                <td>Min. Uploads</td>`

### `resources/views/stats/index.blade.php` (1 strings)

- **Line 45**: "`Themes`"
  - Suggested key: `stat.themes`
  - Context: `        <a class="nav-tab__link" href="{{ route('themes') }}">Themes</a>`

### `resources/views/stats/themes/index.blade.php` (7 strings)

- **Line 13**: "`Themes`"
  - Suggested key: `stat.themes`
  - Context: `    <li class="breadcrumb--active">Themes</li>`

- **Line 20**: "`Site Stylesheets`"
  - Suggested key: `stat.site_stylesheets`
  - Context: `        <h2 class="panel__heading">Site Stylesheets</h2>`

- **Line 98**: "`None Used`"
  - Suggested key: `stat.none_used`
  - Context: `                        <td colspan="3">None Used</td>`

- **Line 106**: "`External CSS Stylesheets (Stacks on top of above site theme)`"
  - Suggested key: `stat.external_css_stylesheets_stack`
  - Context: `        <h2 class="panel__heading">External CSS Stylesheets (Stacks on top of above site theme)</h2>`

- **Line 117**: "`None Used`"
  - Suggested key: `stat.none_used`
  - Context: `                        <td colspan="3">None Used</td>`

- **Line 125**: "`Standalone CSS Stylesheets (No site theme used)`"
  - Suggested key: `stat.standalone_css_stylesheets_no_`
  - Context: `        <h2 class="panel__heading">Standalone CSS Stylesheets (No site theme used)</h2>`

- **Line 136**: "`None Used`"
  - Suggested key: `stat.none_used`
  - Context: `                        <td colspan="3">None Used</td>`

### `resources/views/stats/users/upload-snatches.blade.php` (2 strings)

- **Line 26**: "`Most Upload Snatches`"
  - Suggested key: `stat.most_upload_snatches`
  - Context: `        <h2 class="panel__heading">Most Upload Snatches</h2>`

- **Line 33**: "`Uploads Snatched`"
  - Suggested key: `stat.uploads_snatched`
  - Context: `                        <th>Uploads Snatched</th>`

### `resources/views/stats/yearly_overviews/index.blade.php` (1 strings)

- **Line 24**: "`Yearly Overviews`"
  - Suggested key: `stat.yearly_overviews`
  - Context: `        <h2 class="panel__heading">Yearly Overviews</h2>`

### `resources/views/stats/yearly_overviews/show.blade.php` (21 strings)

- **Line 38**: "`Yearly Overview`"
  - Suggested key: `stat.yearly_overview`
  - Context: `        <h2 class="panel__heading">Yearly Overview</h2>`

- **Line 41**: "`That&#039;s A Wrap!`"
  - Suggested key: `stat.that_s_a_wrap`
  - Context: `                <h1 class="overview__opening-heading">That's A Wrap!</h1>`

- **Line 52**: "`Top 10 Movies (Based on downloads count)`"
  - Suggested key: `stat.top_10_movies_based_on_downloa`
  - Context: `        <h2 class="panel__heading">Top 10 Movies (Based on downloads count)</h2>`

- **Line 72**: "`5 Worst Movies (Based on downloads count)`"
  - Suggested key: `stat.5_worst_movies_based_on_downlo`
  - Context: `        <h2 class="panel__heading">5 Worst Movies (Based on downloads count)</h2>`

- **Line 92**: "`Top 10 TV Shows (Based on downloads count)`"
  - Suggested key: `stat.top_10_tv_shows_based_on_downl`
  - Context: `        <h2 class="panel__heading">Top 10 TV Shows (Based on downloads count)</h2>`

- **Line 112**: "`5 Worst TV Shows (Based on downloads count)`"
  - Suggested key: `stat.5_worst_tv_shows_based_on_down`
  - Context: `        <h2 class="panel__heading">5 Worst TV Shows (Based on downloads count)</h2>`

- **Line 132**: "`Top 10 Users (Based on number of torrent uploads made)`"
  - Suggested key: `stat.top_10_users_based_on_number_o`
  - Context: `        <h2 class="panel__heading">Top 10 Users (Based on number of torrent uploads made)</h2>`

- **Line 152**: "`Top 10 Users (Based on number of torrent requests made)`"
  - Suggested key: `stat.top_10_users_based_on_number_o`
  - Context: `        <h2 class="panel__heading">Top 10 Users (Based on number of torrent requests made)</h2>`

- **Line 172**: "`Top 10 Users (Based on number of torrent requests filled)`"
  - Suggested key: `stat.top_10_users_based_on_number_o`
  - Context: `        <h2 class="panel__heading">Top 10 Users (Based on number of torrent requests filled)</h2>`

- **Line 192**: "`Top 10 Users (Based on number of comments made)`"
  - Suggested key: `stat.top_10_users_based_on_number_o`
  - Context: `        <h2 class="panel__heading">Top 10 Users (Based on number of comments made)</h2>`

- **Line 212**: "`Top 10 Users (Based on number of posts made)`"
  - Suggested key: `stat.top_10_users_based_on_number_o`
  - Context: `        <h2 class="panel__heading">Top 10 Users (Based on number of posts made)</h2>`

- **Line 232**: "`Top 10 Users (Based on number of thanks given)`"
  - Suggested key: `stat.top_10_users_based_on_number_o`
  - Context: `        <h2 class="panel__heading">Top 10 Users (Based on number of thanks given)</h2>`

- **Line 252**: "`Overall`"
  - Suggested key: `stat.overall`
  - Context: `        <h2 class="panel__heading">Overall</h2>`

- **Line 255**: "`New users this year`"
  - Suggested key: `stat.new_users_this_year`
  - Context: `                <dt>New users this year</dt>`

- **Line 259**: "`Movies uploaded this year`"
  - Suggested key: `stat.movies_uploaded_this_year`
  - Context: `                <dt>Movies uploaded this year</dt>`

- **Line 263**: "`TV Shows uploaded this year`"
  - Suggested key: `stat.tv_shows_uploaded_this_year`
  - Context: `                <dt>TV Shows uploaded this year</dt>`

- **Line 267**: "`Total torrents uploaded this year`"
  - Suggested key: `stat.total_torrents_uploaded_this_y`
  - Context: `                <dt>Total torrents uploaded this year</dt>`

- **Line 271**: "`Total torrents downloaded this year`"
  - Suggested key: `stat.total_torrents_downloaded_this`
  - Context: `                <dt>Total torrents downloaded this year</dt>`

- **Line 277**: "`Closing Remarks`"
  - Suggested key: `stat.closing_remarks`
  - Context: `        <h2 class="panel__heading">Closing Remarks</h2>`

- **Line 279**: "`Thank You!`"
  - Suggested key: `stat.thank_you`
  - Context: `            <h3 class="overview__closing-heading">Thank You!</h3>`

- **Line 283**: "`Special thanks from,`"
  - Suggested key: `stat.special_thanks_from`
  - Context: `            <span class="overview__closing-thanks">Special thanks from,</span>`

### `resources/views/ticket/show.blade.php` (3 strings)

- **Line 35**: "`Add Staff Note`"
  - Suggested key: `ticket.add_staff_note`
  - Context: `                            <h4 class="dialog__heading">Add Staff Note</h4>`

- **Line 123**: "`No notes`"
  - Suggested key: `ticket.no_notes`
  - Context: `                                <td colspan="4">No notes</td>`

- **Line 278**: "`Other Tickets`"
  - Suggested key: `ticket.other_tickets`
  - Context: `            <h2 class="panel__heading">Other Tickets</h2>`

### `resources/views/torrent/create.blade.php` (10 strings)

- **Line 320**: "`Numeric digits only.`"
  - Suggested key: `torrent.numeric_digits_only`
  - Context: `                            <span class="form__hint">Numeric digits only.</span>`

- **Line 355**: "`Numeric digits only.`"
  - Suggested key: `torrent.numeric_digits_only`
  - Context: `                            <span class="form__hint">Numeric digits only.</span>`

- **Line 396**: "`Numeric digits only.`"
  - Suggested key: `torrent.numeric_digits_only`
  - Context: `                            <span class="form__hint">Numeric digits only.</span>`

- **Line 430**: "`Numeric digits only.`"
  - Suggested key: `torrent.numeric_digits_only`
  - Context: `                            <span class="form__hint">Numeric digits only.</span>`

- **Line 471**: "`Numeric digits only.`"
  - Suggested key: `torrent.numeric_digits_only`
  - Context: `                            <span class="form__hint">Numeric digits only.</span>`

- **Line 594**: "`Personal Release?`"
  - Suggested key: `torrent.personal_release`
  - Context: `                    <label class="form__label" for="personal_release">Personal Release?</label>`

- **Line 639**: "`25%`"
  - Suggested key: `torrent.25`
  - Context: `                            <option value="25" @selected(old('free') === '25')>25%</option>`

- **Line 640**: "`50%`"
  - Suggested key: `torrent.50`
  - Context: `                            <option value="50" @selected(old('free') === '50')>50%</option>`

- **Line 641**: "`75%`"
  - Suggested key: `torrent.75`
  - Context: `                            <option value="75" @selected(old('free') === '75')>75%</option>`

- **Line 642**: "`100%`"
  - Suggested key: `torrent.100`
  - Context: `                            <option value="100" @selected(old('free') === '100')>100%</option>`

### `resources/views/torrent/edit.blade.php` (8 strings)

- **Line 182**: "`No Distributor`"
  - Suggested key: `torrent.no_distributor`
  - Context: `                            <option value="">No Distributor</option>`

- **Line 220**: "`No Region`"
  - Suggested key: `torrent.no_region`
  - Context: `                            <option value="">No Region</option>`

- **Line 312**: "`Numeric digits only.`"
  - Suggested key: `torrent.numeric_digits_only`
  - Context: `                            <span class="form__hint">Numeric digits only.</span>`

- **Line 347**: "`Numeric digits only.`"
  - Suggested key: `torrent.numeric_digits_only`
  - Context: `                            <span class="form__hint">Numeric digits only.</span>`

- **Line 389**: "`Numeric digits only.`"
  - Suggested key: `torrent.numeric_digits_only`
  - Context: `                            <span class="form__hint">Numeric digits only.</span>`

- **Line 424**: "`Numeric digits only.`"
  - Suggested key: `torrent.numeric_digits_only`
  - Context: `                            <span class="form__hint">Numeric digits only.</span>`

- **Line 466**: "`Numeric digits only.`"
  - Suggested key: `torrent.numeric_digits_only`
  - Context: `                            <span class="form__hint">Numeric digits only.</span>`

- **Line 591**: "`Personal Release?`"
  - Suggested key: `torrent.personal_release`
  - Context: `                        <label class="form__label" for="personal_release">Personal Release?</label>`

### `resources/views/torrent/external-tracker.blade.php` (10 strings)

- **Line 54**: "`External tracker not enabled.`"
  - Suggested key: `torrent.external_tracker_not_enabled`
  - Context: `            <div class="panel__body">External tracker not enabled.</div>`

- **Line 59**: "`Torrent not found.`"
  - Suggested key: `torrent.torrent_not_found`
  - Context: `            <div class="panel__body">Torrent not found.</div>`

- **Line 64**: "`Tracker returned an error.`"
  - Suggested key: `torrent.tracker_returned_an_error`
  - Context: `            <div class="panel__body">Tracker returned an error.</div>`

- **Line 74**: "`Peer ID`"
  - Suggested key: `torrent.peer_id`
  - Context: `                            <th>Peer ID</th>`

- **Line 83**: "`Visible`"
  - Suggested key: `torrent.visible`
  - Context: `                            <th>Visible</th>`

- **Line 141**: "`---`"
  - Suggested key: `torrent.`
  - Context: `                                    <td>---</td>`

- **Line 142**: "`---`"
  - Suggested key: `torrent.`
  - Context: `                                    <td>---</td>`

- **Line 200**: "`Download Factor`"
  - Suggested key: `torrent.download_factor`
  - Context: `                        <dt>Download Factor</dt>`

- **Line 204**: "`Upload Factor`"
  - Suggested key: `torrent.upload_factor`
  - Context: `                        <dt>Upload Factor</dt>`

- **Line 208**: "`Deleted`"
  - Suggested key: `torrent.deleted`
  - Context: `                        <dt>Deleted</dt>`

### `resources/views/torrent/history.blade.php` (1 strings)

- **Line 103**: "`---`"
  - Suggested key: `torrent.`
  - Context: `                                <td>---</td>`

### `resources/views/torrent/partials/audits.blade.php` (1 strings)

- **Line 22**: "`Modifications`"
  - Suggested key: `torrent.modifications`
  - Context: `                    <th>Modifications</th>`

### `resources/views/torrent/partials/buttons.blade.php` (2 strings)

- **Line 101**: "`NFO`"
  - Suggested key: `torrent.nfo`
  - Context: `                <h4 class="dialog__heading">NFO</h4>`

- **Line 411**: "`seeders`"
  - Suggested key: `torrent.seeders`
  - Context: `    @if ($torrent->seeders <= 2 &&`

### `resources/views/torrent/partials/downloads.blade.php` (1 strings)

- **Line 21**: "`Downloaded at`"
  - Suggested key: `torrent.downloaded_at`
  - Context: `                    <th>Downloaded at</th>`

### `resources/views/torrent/partials/extra_meta.blade.php` (1 strings)

- **Line 9**: "`Relations`"
  - Suggested key: `torrent.relations`
  - Context: `    <h2 class="panel__heading">Relations</h2>`

### `resources/views/torrent/partials/game_meta.blade.php` (6 strings)

- **Line 81**: "`This item was recently updated. Try again tomorrow.`"
  - Suggested key: `torrent.this_item_was_recently_updated`
  - Context: `                                title="This item was recently updated. Try again tomorrow."`

- **Line 109**: "`Platforms`"
  - Suggested key: `torrent.platforms`
  - Context: `            <h2 class="meta__heading">Platforms</h2>`

- **Line 121**: "`Platform`"
  - Suggested key: `torrent.platform`
  - Context: `                    <h2 class="meta-chip__name">Platform</h2>`

- **Line 143**: "`Company`"
  - Suggested key: `torrent.company`
  - Context: `                        <h2 class="meta-chip__name">Company</h2>`

- **Line 150**: "`Extra Information`"
  - Suggested key: `torrent.extra_information`
  - Context: `            <h2 class="meta__heading">Extra Information</h2>`

- **Line 165**: "`Trailer`"
  - Suggested key: `torrent.trailer`
  - Context: `                        <h2 class="meta-chip__name">Trailer</h2>`

### `resources/views/torrent/partials/mediainfo.blade.php` (11 strings)

- **Line 30**: "`Filename`"
  - Suggested key: `torrent.filename`
  - Context: `                <h3>Filename</h3>`

- **Line 36**: "`Format`"
  - Suggested key: `torrent.format`
  - Context: `                    <dt>Format</dt>`

- **Line 38**: "`Duration`"
  - Suggested key: `torrent.duration`
  - Context: `                    <dt>Duration</dt>`

- **Line 40**: "`Bitrate`"
  - Suggested key: `torrent.bitrate`
  - Context: `                    <dt>Bitrate</dt>`

- **Line 56**: "`Format`"
  - Suggested key: `torrent.format`
  - Context: `                                    <dt>Format</dt>`

- **Line 67**: "`Aspect ratio`"
  - Suggested key: `torrent.aspect_ratio`
  - Context: `                                    <dt>Aspect ratio</dt>`

- **Line 71**: "`Frame rate`"
  - Suggested key: `torrent.frame_rate`
  - Context: `                                    <dt>Frame rate</dt>`

- **Line 79**: "`Bit rate`"
  - Suggested key: `torrent.bit_rate`
  - Context: `                                    <dt>Bit rate</dt>`

- **Line 84**: "`HDR format`"
  - Suggested key: `torrent.hdr_format`
  - Context: `                                    <dt>HDR format</dt>`

- **Line 88**: "`Color primaries`"
  - Suggested key: `torrent.color_primaries`
  - Context: `                                    <dt>Color primaries</dt>`

- **Line 92**: "`Transfer characteristics`"
  - Suggested key: `torrent.transfer_characteristics`
  - Context: `                                    <dt>Transfer characteristics</dt>`

### `resources/views/torrent/partials/movie_meta.blade.php` (9 strings)

- **Line 74**: "`Receive notifications every time a new torrent is uploaded.`"
  - Suggested key: `torrent.receive_notifications_every_ti`
  - Context: `                            title="Receive notifications every time a new torrent is uploaded."`

- **Line 91**: "`This item was recently updated. Try again tomorrow.`"
  - Suggested key: `torrent.this_item_was_recently_updated`
  - Context: `                                title="This item was recently updated. Try again tomorrow."`

- **Line 193**: "`Cast`"
  - Suggested key: `torrent.cast`
  - Context: `            <h2 class="meta__heading">Cast</h2>`

- **Line 218**: "`Crew`"
  - Suggested key: `torrent.crew`
  - Context: `        <section class="meta__chip-container" title="Crew">`

- **Line 219**: "`Crew`"
  - Suggested key: `torrent.crew`
  - Context: `            <h2 class="meta__heading">Crew</h2>`

- **Line 245**: "`Extra Information`"
  - Suggested key: `torrent.extra_information`
  - Context: `            <h2 class="meta__heading">Extra Information</h2>`

- **Line 260**: "`Trailer`"
  - Suggested key: `torrent.trailer`
  - Context: `                        <h2 class="meta-chip__name">Trailer</h2>`

- **Line 296**: "`Primary Language`"
  - Suggested key: `torrent.primary_language`
  - Context: `                    <h2 class="meta-chip__name">Primary Language</h2>`

- **Line 320**: "`Company`"
  - Suggested key: `torrent.company`
  - Context: `                        <h2 class="meta-chip__name">Company</h2>`

### `resources/views/torrent/partials/no_meta.blade.php` (4 strings)

- **Line 43**: "`Internet Movie Database`"
  - Suggested key: `torrent.internet_movie_database`
  - Context: `                    title="Internet Movie Database"`

- **Line 57**: "`The Movie Database`"
  - Suggested key: `torrent.the_movie_database`
  - Context: `                    title="The Movie Database"`

- **Line 70**: "`MyAnimeList`"
  - Suggested key: `torrent.myanimelist`
  - Context: `                    title="MyAnimeList"`

- **Line 83**: "`MyAnimeList`"
  - Suggested key: `torrent.myanimelist`
  - Context: `                    title="MyAnimeList"`

### `resources/views/torrent/partials/reports.blade.php` (2 strings)

- **Line 20**: "`Reported`"
  - Suggested key: `torrent.reported`
  - Context: `                    <th>Reported</th>`

- **Line 78**: "`No reports`"
  - Suggested key: `torrent.no_reports`
  - Context: `                        <td colspan="8">No reports</td>`

### `resources/views/torrent/partials/subtitles.blade.php` (1 strings)

- **Line 203**: "`No External Subtitles Available`"
  - Suggested key: `torrent.no_external_subtitles_availabl`
  - Context: `                        <td colspan="8">No External Subtitles Available</td>`

### `resources/views/torrent/partials/tools.blade.php` (18 strings)

- **Line 132**: "`Edit Freeleech`"
  - Suggested key: `torrent.edit_freeleech`
  - Context: `                            <h4 class="dialog__heading">Edit Freeleech</h4>`

- **Line 171**: "`No Limit`"
  - Suggested key: `torrent.no_limit`
  - Context: `                                            <option value="">No Limit</option>`

- **Line 172**: "`1 Day`"
  - Suggested key: `torrent.1_day`
  - Context: `                                            <option value="1">1 Day</option>`

- **Line 173**: "`2 Days`"
  - Suggested key: `torrent.2_days`
  - Context: `                                            <option value="2">2 Days</option>`

- **Line 174**: "`3 Days`"
  - Suggested key: `torrent.3_days`
  - Context: `                                            <option value="3">3 Days</option>`

- **Line 175**: "`4 Days`"
  - Suggested key: `torrent.4_days`
  - Context: `                                            <option value="4">4 Days</option>`

- **Line 176**: "`5 Days`"
  - Suggested key: `torrent.5_days`
  - Context: `                                            <option value="5">5 Days</option>`

- **Line 177**: "`6 Days`"
  - Suggested key: `torrent.6_days`
  - Context: `                                            <option value="6">6 Days</option>`

- **Line 178**: "`7 Days`"
  - Suggested key: `torrent.7_days`
  - Context: `                                            <option value="7">7 Days</option>`

- **Line 210**: "`Edit Double Upload`"
  - Suggested key: `torrent.edit_double_upload`
  - Context: `                            <h4 class="dialog__heading">Edit Double Upload</h4>`

- **Line 220**: "`No Limit`"
  - Suggested key: `torrent.no_limit`
  - Context: `                                            <option value="">No Limit</option>`

- **Line 221**: "`1 Day`"
  - Suggested key: `torrent.1_day`
  - Context: `                                            <option value="1">1 Day</option>`

- **Line 222**: "`2 Days`"
  - Suggested key: `torrent.2_days`
  - Context: `                                            <option value="2">2 Days</option>`

- **Line 223**: "`3 Days`"
  - Suggested key: `torrent.3_days`
  - Context: `                                            <option value="3">3 Days</option>`

- **Line 224**: "`4 Days`"
  - Suggested key: `torrent.4_days`
  - Context: `                                            <option value="4">4 Days</option>`

- **Line 225**: "`5 Days`"
  - Suggested key: `torrent.5_days`
  - Context: `                                            <option value="5">5 Days</option>`

- **Line 226**: "`6 Days`"
  - Suggested key: `torrent.6_days`
  - Context: `                                            <option value="6">6 Days</option>`

- **Line 227**: "`7 Days`"
  - Suggested key: `torrent.7_days`
  - Context: `                                            <option value="7">7 Days</option>`

### `resources/views/torrent/partials/tv_meta.blade.php` (10 strings)

- **Line 74**: "`Receive notifications every time a new torrent is uploaded.`"
  - Suggested key: `torrent.receive_notifications_every_ti`
  - Context: `                            title="Receive notifications every time a new torrent is uploaded."`

- **Line 90**: "`This item was recently updated. Try again tomorrow.`"
  - Suggested key: `torrent.this_item_was_recently_updated`
  - Context: `                                title="This item was recently updated. Try again tomorrow."`

- **Line 192**: "`Cast`"
  - Suggested key: `torrent.cast`
  - Context: `            <h2 class="meta__heading">Cast</h2>`

- **Line 217**: "`Crew`"
  - Suggested key: `torrent.crew`
  - Context: `        <section class="meta__chip-container" title="Crew">`

- **Line 218**: "`Crew`"
  - Suggested key: `torrent.crew`
  - Context: `            <h2 class="meta__heading">Crew</h2>`

- **Line 244**: "`Extra Information`"
  - Suggested key: `torrent.extra_information`
  - Context: `            <h2 class="meta__heading">Extra Information</h2>`

- **Line 259**: "`Trailer`"
  - Suggested key: `torrent.trailer`
  - Context: `                        <h2 class="meta-chip__name">Trailer</h2>`

- **Line 295**: "`Primary Language`"
  - Suggested key: `torrent.primary_language`
  - Context: `                    <h2 class="meta-chip__name">Primary Language</h2>`

- **Line 319**: "`Network`"
  - Suggested key: `torrent.network`
  - Context: `                        <h2 class="meta-chip__name">Network</h2>`

- **Line 343**: "`Company`"
  - Suggested key: `torrent.company`
  - Context: `                        <h2 class="meta-chip__name">Company</h2>`

### `resources/views/torrent/peers.blade.php` (4 strings)

- **Line 66**: "`Connectable`"
  - Suggested key: `torrent.connectable`
  - Context: `                            <th>Connectable</th>`

- **Line 72**: "`Visible`"
  - Suggested key: `torrent.visible`
  - Context: `                        <th>Visible</th>`

- **Line 102**: "`---`"
  - Suggested key: `torrent.`
  - Context: `                                <td>---</td>`

- **Line 103**: "`---`"
  - Suggested key: `torrent.`
  - Context: `                                <td>---</td>`

### `resources/views/user/apikey/index.blade.php` (2 strings)

- **Line 82**: "`No Apikey History`"
  - Suggested key: `user.no_apikey_history`
  - Context: `                            <td colspan="4">No Apikey History</td>`

- **Line 104**: "`You currently do not have an API key.`"
  - Suggested key: `user.you_currently_do_not_have_an_a`
  - Context: `                    <p>You currently do not have an API key.</p>`

### `resources/views/user/buttons/user.blade.php` (5 strings)

- **Line 297**: "`Download Torrent Files`"
  - Suggested key: `user.download_torrent_files`
  - Context: `                    <a class="nav-tab__link" x-bind="showDialog">Download Torrent Files</a>`

- **Line 300**: "`Download Torrent Files`"
  - Suggested key: `user.download_torrent_files`
  - Context: `                        <h3 class="dialog__heading">Download Torrent Files</h3>`

- **Line 307**: "`Select download type:`"
  - Suggested key: `user.select_download_type`
  - Context: `                                <legend>Select download type:</legend>`

- **Line 318**: "`All History`"
  - Suggested key: `user.all_history`
  - Context: `                                    <label for="history">All History</label>`

- **Line 329**: "`Active Peers`"
  - Suggested key: `user.active_peers`
  - Context: `                                    <label for="peer">Active Peers</label>`

### `resources/views/user/email/edit.blade.php` (2 strings)

- **Line 60**: "`New Email`"
  - Suggested key: `user.new_email`
  - Context: `                    <label class="form__label form__label--floating" for="email">New Email</label>`

- **Line 119**: "`No email update history`"
  - Suggested key: `user.no_email_update_history`
  - Context: `                            <td colspan="3">No email update history</td>`

### `resources/views/user/follower/index.blade.php` (1 strings)

- **Line 52**: "`No Followers`"
  - Suggested key: `user.no_followers`
  - Context: `                                <td colspan="3">No Followers</td>`

### `resources/views/user/following/index.blade.php` (1 strings)

- **Line 52**: "`Not following`"
  - Suggested key: `user.not_following`
  - Context: `                                <td colspan="3">Not following</td>`

### `resources/views/user/general_setting/edit.blade.php` (2 strings)

- **Line 52**: "`Style`"
  - Suggested key: `user.style`
  - Context: `                    <legend class="form__legend">Style</legend>`

- **Line 169**: "`Theme`"
  - Suggested key: `user.theme`
  - Context: `                        <label class="form__label form__label--floating" for="style">Theme</label>`

### `resources/views/user/invite-tree/index.blade.php` (7 strings)

- **Line 154**: "`Height`"
  - Suggested key: `user.height`
  - Context: `                <dt>Height</dt>`

- **Line 158**: "`Count`"
  - Suggested key: `user.count`
  - Context: `                <dt>Count</dt>`

- **Line 164**: "`Ancestors`"
  - Suggested key: `user.ancestors`
  - Context: `        <h2 class="panel__heading">Ancestors</h2>`

- **Line 243**: "`Stat`"
  - Suggested key: `user.stat`
  - Context: `                        <th>Stat</th>`

- **Line 244**: "`Average`"
  - Suggested key: `user.average`
  - Context: `                        <th style="text-align: right">Average</th>`

- **Line 245**: "`Total`"
  - Suggested key: `user.total`
  - Context: `                        <th style="text-align: right">Total</th>`

- **Line 303**: "`Count`"
  - Suggested key: `user.count`
  - Context: `                        <th style="text-align: right">Count</th>`

### `resources/views/user/notification_setting/edit.blade.php` (4 strings)

- **Line 288**: "`Mentions`"
  - Suggested key: `user.mentions`
  - Context: `                    <legend class="form__legend">Mentions</legend>`

- **Line 342**: "`Block all notifications from the selected groups.`"
  - Suggested key: `user.block_all_notifications_from_t`
  - Context: `                <h3>Block all notifications from the selected groups.</h3>`

- **Line 464**: "`Mentions`"
  - Suggested key: `user.mentions`
  - Context: `                        <legend class="form__legend">Mentions</legend>`

- **Line 481**: "`Override all notifications.`"
  - Suggested key: `user.override_all_notifications`
  - Context: `                <h3>Override all notifications.</h3>`

### `resources/views/user/passkey/index.blade.php` (1 strings)

- **Line 82**: "`No Passkey History`"
  - Suggested key: `user.no_passkey_history`
  - Context: `                            <td colspan="4">No Passkey History</td>`

### `resources/views/user/password/edit.blade.php` (1 strings)

- **Line 101**: "`No password reset history`"
  - Suggested key: `user.no_password_reset_history`
  - Context: `                            <td>No password reset history</td>`

### `resources/views/user/privacy_setting/edit.blade.php` (1 strings)

- **Line 365**: "`Hide your profile options from the selected groups.`"
  - Suggested key: `user.hide_your_profile_options_from`
  - Context: `                <h3>Hide your profile options from the selected groups.</h3>`

### `resources/views/user/profile/show.blade.php` (31 strings)

- **Line 261**: "`Connectable`"
  - Suggested key: `user.connectable`
  - Context: `                                    <th>Connectable</th>`

- **Line 463**: "`Watchlist`"
  - Suggested key: `user.watchlist`
  - Context: `                    <h2 class="panel__heading">Watchlist</h2>`

- **Line 526**: "`Unwatch`"
  - Suggested key: `user.unwatch`
  - Context: `                                <button class="form__button form__button--text">Unwatch</button>`

- **Line 535**: "`Watched By`"
  - Suggested key: `user.watched_by`
  - Context: `                                <th>Watched By</th>`

- **Line 544**: "`Not watched`"
  - Suggested key: `user.not_watched`
  - Context: `                                    <td colspan="4">Not watched</td>`

- **Line 593**: "`Donations`"
  - Suggested key: `user.donations`
  - Context: `                <h2 class="panel__heading">Donations</h2>`

- **Line 596**: "`Active Donor`"
  - Suggested key: `user.active_donor`
  - Context: `                        <dt>Active Donor</dt>`

- **Line 610**: "`Lifetime Donor`"
  - Suggested key: `user.lifetime_donor`
  - Context: `                        <dt>Lifetime Donor</dt>`

- **Line 624**: "`Latest Donation Amount`"
  - Suggested key: `user.latest_donation_amount`
  - Context: `                        <dt>Latest Donation Amount</dt>`

- **Line 630**: "`Latest Donation Date`"
  - Suggested key: `user.latest_donation_date`
  - Context: `                        <dt>Latest Donation Date</dt>`

- **Line 636**: "`Donation Expire Date`"
  - Suggested key: `user.donation_expire_date`
  - Context: `                        <dt>Donation Expire Date</dt>`

- **Line 640**: "`Lifetime Donor`"
  - Suggested key: `user.lifetime_donor`
  - Context: `                                <i class="fal fa-star" id="lifeline" title="Lifetime Donor"></i>`

- **Line 714**: "`Torrent Count`"
  - Suggested key: `user.torrent_count`
  - Context: `                    <h2 class="panel__heading">Torrent Count</h2>`

- **Line 777**: "`Torrent Count`"
  - Suggested key: `user.torrent_count`
  - Context: `                    <h2 class="panel__heading">Torrent Count</h2>`

- **Line 802**: "`Total Inactive Peers`"
  - Suggested key: `user.total_inactive_peers`
  - Context: `                            <dt>Total Inactive Peers</dt>`

- **Line 886**: "`External Tracker`"
  - Suggested key: `user.external_tracker`
  - Context: `                    <h2 class="panel__heading">External Tracker</h2>`

- **Line 887**: "`External tracker not enabled.`"
  - Suggested key: `user.external_tracker_not_enabled`
  - Context: `                    <div class="panel__body">External tracker not enabled.</div>`

- **Line 891**: "`External Tracker`"
  - Suggested key: `user.external_tracker`
  - Context: `                    <h2 class="panel__heading">External Tracker</h2>`

- **Line 892**: "`User not found.`"
  - Suggested key: `user.user_not_found`
  - Context: `                    <div class="panel__body">User not found.</div>`

- **Line 896**: "`External Tracker`"
  - Suggested key: `user.external_tracker`
  - Context: `                    <h2 class="panel__heading">External Tracker</h2>`

- **Line 897**: "`Tracker returned an error.`"
  - Suggested key: `user.tracker_returned_an_error`
  - Context: `                    <div class="panel__body">Tracker returned an error.</div>`

- **Line 901**: "`External Tracker`"
  - Suggested key: `user.external_tracker`
  - Context: `                    <h2 class="panel__heading">External Tracker</h2>`

- **Line 951**: "`Seed lists`"
  - Suggested key: `user.seed_lists`
  - Context: `                                    <th>Seed lists</th>`

- **Line 952**: "`Window`"
  - Suggested key: `user.window`
  - Context: `                                    <th>Window</th>`

- **Line 953**: "`Max`"
  - Suggested key: `user.max`
  - Context: `                                    <th>Max</th>`

- **Line 954**: "`Lists/h`"
  - Suggested key: `user.lists_h`
  - Context: `                                    <th>Lists/h</th>`

- **Line 977**: "`Leech lists`"
  - Suggested key: `user.leech_lists`
  - Context: `                                    <th>Leech lists</th>`

- **Line 978**: "`Window`"
  - Suggested key: `user.window`
  - Context: `                                    <th>Window</th>`

- **Line 979**: "`Max`"
  - Suggested key: `user.max`
  - Context: `                                    <th>Max</th>`

- **Line 980**: "`Lists/h`"
  - Suggested key: `user.lists_h`
  - Context: `                                    <th>Lists/h</th>`

- **Line 1042**: "`2FA Enabled`"
  - Suggested key: `user.2fa_enabled`
  - Context: `                        <dt>2FA Enabled</dt>`

### `resources/views/user/rsskey/index.blade.php` (1 strings)

- **Line 82**: "`No Rsskey History`"
  - Suggested key: `user.no_rsskey_history`
  - Context: `                            <td colspan="4">No Rsskey History</td>`

### `resources/views/user/transaction/create.blade.php` (1 strings)

- **Line 33**: "`Cost`"
  - Suggested key: `user.cost`
  - Context: `                        <th>Cost</th>`

### `resources/views/user/wish/index.blade.php` (6 strings)

- **Line 55**: "`TMDB ID`"
  - Suggested key: `user.tmdb_id`
  - Context: `                        <label class="form__label form__label--floating" for="tmdb">TMDB ID</label>`

- **Line 107**: "`Not yet uploaded`"
  - Suggested key: `user.not_yet_uploaded`
  - Context: `                                            title="Not yet uploaded"`

- **Line 112**: "`Already uploaded`"
  - Suggested key: `user.already_uploaded`
  - Context: `                                            title="Already uploaded"`

- **Line 119**: "`Not yet uploaded`"
  - Suggested key: `user.not_yet_uploaded`
  - Context: `                                            title="Not yet uploaded"`

- **Line 124**: "`Already uploaded`"
  - Suggested key: `user.already_uploaded`
  - Context: `                                            title="Already uploaded"`

- **Line 153**: "`No wishes`"
  - Suggested key: `user.no_wishes`
  - Context: `                            <td colspan="4">No wishes</td>`

### `resources/views/vendor/mail/html/header.blade.php` (1 strings)

- **Line 5**: "`Laravel Logo`"
  - Suggested key: `common.laravel_logo`
  - Context: `<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">`

### `resources/views/wiki/index.blade.php` (2 strings)

- **Line 12**: "`Wikis`"
  - Suggested key: `common.wikis`
  - Context: `    <li class="breadcrumb--active">Wikis</li>`

- **Line 49**: "`No wikis in category.`"
  - Suggested key: `common.no_wikis_in_category`
  - Context: `                                <td colspan="3">No wikis in category.</td>`

### `resources/views/wiki/show.blade.php` (1 strings)

- **Line 5**: "`Wikis`"
  - Suggested key: `common.wikis`
  - Context: `        <a href="{{ route('wikis.index') }}" class="breadcrumb__link">Wikis</a>`

