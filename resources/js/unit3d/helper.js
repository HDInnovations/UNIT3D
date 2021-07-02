/*
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie, singularity43
 *
 * File Contents:
 *
 * uploadExtensionBuilder - To parse torrent files titles / Used: Upload
 * userFilterBuilder - To add filters for user search / Used: All User Histories
 * torrentBookmarkBuilder - To show bookmark buttons for users / Used: Home, Torrents
 * facetedSearchBuilder - To add filters for search / Used: Torrents
 * forumTipBuilder - To add tip buttons for forum / Used: Topics
 * userExtensionBuilder - To add toggle capabilities to BON / Used: BON
 *
 * After classes, event attachments then globals.
*/

class uploadExtensionBuilder {
    // https://stackoverflow.com/a/10710400
    // get all indexes of ch in str
    getAllIndexes(str, ch) {
      const indices = []
      for (let i = 0; i < str.length; i++) {
            if (str[i] === ch) indices.push(i);
        }
        return indices;
    }
    // is index in range of str?
    inRange(str, index) {
        return (index >= 0 && index < str.length);
    }
    // https://stackoverflow.com/a/8935675
    is_numeric(str) {
        return /^\d+$/.test(str)
    }
    // https://www.geeksforgeeks.org/how-to-replace-a-character-at-a-particular-index-in-javascript/
    replaceChar(origString, replaceChar, index) {
        let firstPart = origString.substr(0, index);
        let lastPart = origString.substr(index + 1);

        let newString = firstPart + replaceChar + lastPart;
        return newString;
    }
    removeDots(title) {
        // replace dots with spaces that are between:
        // 1) letter and letter
        // 2) number and letter
        // 3) . 4 numbers (.year)
        // 4) 4 numbers and 3 numbers followed by i or p (year and resolution, 2020.720p)
        // 5) 4 numbers and 4 numbers followed by i or p (year and resolution, 2020.1080p)
        // 6) S 2 numbers . 3 numbers followed by i or p (season and resolution, S01.720p)
        // 7) S 2 numbers . 4 numbers followed by i or p (season and resolution, S01.1080p)
        // 8) 2 letters . number . number (DD.2.0 or AAC.2.0 or DD+.2.0 or DTS-X.7.1)

        let newTitle = title;

        // array of indexes of each dot location
        const indexes = this.getAllIndexes(title, ".")

        // for each dot dot location
        for (const i of indexes) {
            if (this.inRange(title, i - 1) && this.inRange(title, i + 1)) {
                // characters before and after dot
                const before = title[i - 1]
                const after = title[i + 1]

                if (!this.is_numeric(before) && !this.is_numeric(after)) {
                    // Case 1: letter and letter
                    newTitle = this.replaceChar(newTitle, " ", i)
                    continue;
                } else if (this.is_numeric(before) && !this.is_numeric(after)) {
                    // Case 2: number and letter
                    newTitle = this.replaceChar(newTitle, " ", i)
                    continue;
                }
            }

            // Case 3: . 4 numbers (.year)
            if (this.inRange(title, i + 5)) {
                const after = title.substring(i + 1, i + 5)
                if (this.is_numeric(after)) {
                    newTitle = this.replaceChar(newTitle, " ", i)
                    continue;
                }
            }

            // Case 4: 4 numbers and 3 numbers followed by i or p (year and resolution, 2020.720p)
            if (this.inRange(title, i - 4) && this.inRange(title, i + 4)) {
                const beforeNum = title.substring(i - 4, i) // ex. 1987
                const afterNum = title.substring(i + 1, i + 4) // ex. 480, 576 or 720
                const afterResolution = title.charAt(i + 4).toLowerCase() // i or p

                if (this.is_numeric(beforeNum) && this.is_numeric(afterNum) && (afterResolution == "i" || afterResolution == "p")) {
                    newTitle = this.replaceChar(newTitle, " ", i)
                    continue;
                }
            }

            // Case 5: 4 numbers and 4 numbers followed by i or p (year and resolution, 2020.1080p)
            if (this.inRange(title, i - 4) && this.inRange(title, i + 5)) {
                const beforeNum = title.substring(i - 4, i) // ex. 1987
                const afterNum = title.substring(i + 1, i + 5) // ex. 1080 or 2160
                const afterResolution = title.charAt(i + 5).toLowerCase() // i or p

                if (this.is_numeric(beforeNum) && this.is_numeric(afterNum) && (afterResolution == "i" || afterResolution == "p")) {
                    newTitle = this.replaceChar(newTitle, " ", i)
                    continue;
                }
            }

            // Case 6: S 2 numbers . 3 numbers followed by i or p (season and resolution, S01.720p)
            if (this.inRange(title, i - 3) && this.inRange(i + 4)) {
                const beforeNum = title.substring(i - 3, i - 2).toLowerCase() // ex. S
                const seasonNum = title.substring(i - 2, i).toLowerCase() // ex. 01

                const afterNum = title.substring(i + 1, i + 4) // ex. 480, 576 or 720
                const afterResolution = title.charAt(i + 4).toLowerCase() // i or p

                if (beforeNum == "s" && this.is_numeric(seasonNum) && (afterResolution == "i" || afterResolution == "p")) {
                    newTitle = this.replaceChar(newTitle, " ", i)
                    continue;
                }
            }

            // Case 7: S 2 numbers . 4 numbers followed by i or p (season and resolution, S01.720p)
            if (this.inRange(title, i - 3) && this.inRange(i + 5)) {
                const beforeNum = title.substring(i - 3, i - 2).toLowerCase() // ex. S
                const seasonNum = title.substring(i - 2, i).toLowerCase() // ex. 01

                const afterNum = title.substring(i + 1, i + 5) // ex. 1080 or 2160
                const afterResolution = title.charAt(i + 5).toLowerCase() // i or p

                if (beforeNum == "s" && this.is_numeric(seasonNum) && (afterResolution == "i" || afterResolution == "p")) {
                    newTitle = this.replaceChar(newTitle, " ", i)
                    continue;
                }
            }

            // Case 8: 2 letters . number (DD.2.0 or AAC.2.0 or DD+.2.0 or DTS-X.7.1)
            // looking at it from the first dot only
            if (this.inRange(title, i - 1) && this.inRange(title, i + 1)) {
                const before = title.substring(i - 1, i).toLowerCase() // ex. DD or AC or D+ or -X
                const after = title.charAt(i + 1) // number

                if (!this.is_numeric(before) && this.is_numeric(after)) {
                    newTitle = this.replaceChar(newTitle, " ", i)

                }
            }
        }

        return newTitle;
    }
    hook() {
        let name = document.querySelector('#title');
        let tmdb = document.querySelector('#autotmdb');
        let imdb = document.querySelector('#autoimdb');

        if (!name.value.trim() && !tmdb.value.trim()) {
            let torrent = document.querySelector('#torrent');
            let release;
            if (!name.value) {
                const fileEndings = ['.mkv.torrent', '.mp4.torrent', '.torrent'];
            let newValue = torrent.value
            // strip path
                newValue = newValue.split('\\').pop().split('/').pop();
                // remove file endings
                fileEndings.forEach(function (e) {
                    newValue = newValue.replace(e, '');
                });
                // replace dots with spaces
                name.value = this.removeDots(newValue);
            }

            /* PARSING */
            release = title_parser.parse(name.value, {
                strict: true, // if no main tags found, will throw an exception
                flagged: true, // add flags to generated relese name (like STV, REMASTERED, READNFO)
                erase: [], // add expressions to erase before parsing
                defaults: {"language": "ENGLISH"} // defaults values for : language, resolution and year
            });

            let matcher = name.value.toLowerCase();

            // Torrent Category
            if (release.type === "Movie") {
                $("#autocat").val(1);
            } else if (release.type === "TV Show") {
                $("#autocat").val(2);
            }

            // Torrent Type
            if (matcher.indexOf("bd50") > 0 || matcher.indexOf("bd25") > 0 || matcher.indexOf("untouched") > 0 || matcher.indexOf("dvd5") > 0 || matcher.indexOf("dvd9") > 0 || matcher.indexOf("mpeg-2") > 0 || matcher.indexOf("avc") > 0 || matcher.indexOf("vc-1") > 0) {
                $("#autotype").val(1);
            }
            if (matcher.indexOf("remux") > 0) {
                $("#autotype").val(2);
            }
            if (matcher.indexOf("x264") > 0) {
                $("#autotype").val(3);
            }
            if (matcher.indexOf("x265") > 0) {
                $("#autotype").val(3);
            }
            if (matcher.indexOf("webdl") > 0 || matcher.indexOf("web-dl") > 0) {
                $("#autotype").val(4);
            }
            if (matcher.indexOf("web-rip") > 0 || matcher.indexOf("webrip") > 0) {
                $("#autotype").val(5);
            }
            if (matcher.indexOf("hdtv") > 0) {
                $("#autotype").val(6);
            }

            // Torrent Resolution
            if (release.resolution) {
                $("#autores").val(release.resolution);
            }

            // Torrent Season (TV Only)
            if (release.season) {
                $("#season_number").val(release.season);
            }

            // Torrent Episode (TV Only)
            if (release.episode) {
                $("#episode_number").val(release.episode);
            }

            // Torrent TMDB ID
            if (release.type === "Movie") {
                theMovieDb.search.getMovie({ "query": release.title, "year": release.year }, successCB, errorCB);
            } else if (release.type === "TV Show") {
                theMovieDb.search.getTv({ "query": release.title }, successCB, errorCB);
            }

            function successCB(data) {
                data = JSON.parse(data);
                if (release.type === "Movie") {
                    if (data.results && data.results.length > 0) {
                        $("#autotmdb").val(data.results[0].id);
                        $("#apimatch").val('Found Match: ' + data.results[0].title + ' (' + data.results[0].release_date + ')');
                        theMovieDb.movies.getKeywords({ "id": data.results[0].id }, success, error);
                        theMovieDb.movies.getExternalIds({ "id": data.results[0].id }, s, e);
                    }
                } else if (release.type === "TV Show") {
                    if (data.results && data.results.length > 0) {
                        $("#autotmdb").val(data.results[0].id);
                        $("#apimatch").val('Found Match: ' + data.results[0].name + ' (' + data.results[0].first_air_date + ')');
                        theMovieDb.tv.getKeywords({ "id": data.results[0].id }, success, error);
                        theMovieDb.tv.getExternalIds({ "id": data.results[0].id }, s, e);
                    }
                }
            }
            function errorCB(data) {
                console.log("Error callback: " + data);
            }

            //Torrent Keywords
            function success(data) {
                data = JSON.parse(data);
                if (release.type === "Movie") {
                    let tags = data.keywords.map(({ name }) => name).join(', ');
                    $("#autokeywords").val(tags);
                } else if (release.type === "TV Show") {
                    let tags = data.results.map(({ name }) => name).join(', ');
                    $("#autokeywords").val(tags);
                }
            }
            function error(data) {
                console.log("Error callback: " + data);
            }

            //Torrent External IDs
            function s(data) {
                data = JSON.parse(data);
                let imdb = data.imdb_id;
                imdb = imdb.substring(2);
                if (release.type === "Movie") {
                    $("#autoimdb").val(imdb);
                } else if (release.type === "TV Show") {
                    $("#autoimdb").val(imdb);
                    $("#autotvdb").val(data.tvdb_id);
                }
            }
            function e(data) {
                console.log("Error callback: " + data);
            }

            // Torrent Stream Optimized?
            if (release.container === "MP4" && release.audio === "AAC") {
                document.getElementById("stream").checked = true;
            }
        }
    }
}
class userFilterBuilder {
    constructor() {
        this.csrf = document.querySelector("meta[name='csrf-token']").getAttribute("content");
        this.api = '';
        this.filter = '';
        this.start = 0;
        this.view = 'history';
    }
    set(filter) {
        this.filter = filter;
    }
    get() {
        return this.filter;
    }
    force() {
        this.handle(this.start,true);
    }
    handle(page,nav) {

      const userId = $('#userFilter').attr('userId')
      const userName = $('#userFilter').attr('userName')

      const view = $('#userFilter').attr('view')

      const active = (function () {
        if ($('#active').is(':checked')) {
          return $('#active').val()
        }
      })()

      const seeding = (function () {
        if ($('#seeding').is(':checked')) {
          return $('#seeding').val()
        }
      })()

      const leeching = (function () {
        if ($('#leeching').is(':checked')) {
          return $('#leeching').val()
        }
      })()

      const prewarned = (function () {
        if ($('#prewarned').is(':checked')) {
          return $('#prewarned').val()
        }
      })()

      const hr = (function () {
        if ($('#hr').is(':checked')) {
          return $('#hr').val()
        }
      })()

      const immune = (function () {
        if ($('#immune').is(':checked')) {
          return $('#immune').val()
        }
      })()

      const completed = (function () {
        if ($('#completed').is(':checked')) {
          return $('#completed').val()
        }
      })()

      const pending = (function () {
        if ($('#pending').is(':checked')) {
          return $('#pending').val()
        }
      })()

      const approved = (function () {
        if ($('#approved').is(':checked')) {
          return $('#approved').val()
        }
      })()

      const rejected = (function () {
        if ($('#rejected').is(':checked')) {
          return $('#rejected').val()
        }
      })()

      const dead = (function () {
        if ($('#dead').is(':checked')) {
          return $('#dead').val()
        }
      })()

      const alive = (function () {
        if ($('#alive').is(':checked')) {
          return $('#alive').val()
        }
      })()

      const reseed = (function () {
        if ($('#reseed').is(':checked')) {
          return $('#reseed').val()
        }
      })()

      const error = (function () {
        if ($('#error').is(':checked')) {
          return $('#error').val()
        }
      })()

      const satisfied = (function () {
        if ($('#satisfied').is(':checked')) {
          return $('#satisfied').val()
        }
      })()

      const notsatisfied = (function () {
        if ($('#notsatisfied').is(':checked')) {
          return $('#notsatisfied').val()
        }
      })()

      const rewarded = (function () {
        if ($('#rewarded').is(':checked')) {
          return $('#rewarded').val()
        }
      })()

      const notrewarded = (function () {
        if ($('#notrewarded').is(':checked')) {
          return $('#notrewarded').val()
        }
      })()

      const dying = (function () {
        if ($('#dying').is(':checked')) {
          return $('#dying').val()
        }
      })()

      const legendary = (function () {
        if ($('#legendary').is(':checked')) {
          return $('#legendary').val()
        }
      })()

      const large = (function () {
        if ($('#large').is(':checked')) {
          return $('#large').val()
        }
      })()

      const huge = (function () {
        if ($('#huge').is(':checked')) {
          return $('#huge').val()
        }
      })()

      const everyday = (function () {
        if ($('#everyday').is(':checked')) {
          return $('#everyday').val()
        }
      })()

      const legendary_seeder = (function () {
        if ($('#legendary_seeder').is(':checked')) {
          return $('#legendary_seeder').val()
        }
      })()

      const mvp_seeder = (function () {
        if ($('#mvp_seeder').is(':checked')) {
          return $('#mvp_seeder').val()
        }
      })()

      const committed_seeder = (function () {
        if ($('#committed_seeder').is(':checked')) {
          return $('#committed_seeder').val()
        }
      })()

      const teamplayer_seeder = (function () {
        if ($('#teamplayer_seeder').is(':checked')) {
          return $('#teamplayer_seeder').val()
        }
      })()

      const participant_seeder = (function () {
        if ($('#participant_seeder').is(':checked')) {
          return $('#participant_seeder').val()
        }
      })()

      const old = (function () {
        if ($('#old').is(':checked')) {
          return $('#old').val()
        }
      })()

      const unfilled = (function () {
        if ($('#unfilled').is(':checked')) {
          return $('#unfilled').val()
        }
      })()

      const filled = (function () {
        if ($('#filled').is(':checked')) {
          return $('#filled').val()
        }
      })()

      const claimed = (function () {
        if ($('#claimed').is(':checked')) {
          return $('#claimed').val()
        }
      })()

      const search = $('#search').val()

      const sorting = $('#sorting').val()
      const direction = $('#direction').val()

      if(userFilterXHR != null) {
            userFilterXHR.abort();
        }
        userFilterXHR = $.ajax({
            url: '/users/'+userName+'/userFilters',
            data: {
                _token: this.csrf,
                page: page,
                active: active,
                sorting: sorting,
                direction: direction,
                seeding: seeding,
                prewarned: prewarned,
                completed: completed,
                hr: hr,
                rewarded: rewarded,
                notrewarded: notrewarded,
                immune: immune,
                claimed: claimed,
                filled: filled,
                unfilled: unfilled,
                name: search,
                pending: pending,
                leeching: leeching,
                approved: approved,
                rejected: rejected,
                dead: dead,
                alive: alive,
                satisfied: satisfied,
                notsatisfied: notsatisfied,
                reseed: reseed,
                error: error,
                dying: dying,
                legendary: legendary,
                old: old,
                huge: huge,
                large: large,
                everyday: everyday,
                legendary_seeder: legendary_seeder,
                mvp_seeder: mvp_seeder,
                teamplayer_seeder: teamplayer_seeder,
                committed_seeder: committed_seeder,
                participant_seeder: participant_seeder,
                view: view,
            },
            type: 'post',
            beforeSend: function () {
                $("#userFilter").html('<i class="fal fa-spinner fa-spin fa-3x fa-fw"></i>')
            }
        }).done(function (e) {
            $data = $(e);
            $("#userFilter").html($data);
            if (page) {
                $("#filterHeader")[0].scrollIntoView();
            }
            if (!nav) {
                if (window.history && window.history.replaceState) {
                    window.history.replaceState(null, null, ' ');
                }
            }
            userFilterXHR = null;
        });
    }
    init() {
        $('.userFilter').each(function() {
            if($(this).attr('trigger')) {
                var trigger = $(this).attr('trigger');
            } else {
                var trigger = 'click';
            }
            $(this).off(trigger);
            $(this).on(trigger, _.debounce(function(e) {
                 userFilter.handle();
            }, 400));
        });

      let page = 0
      if (window.location.hash && window.location.hash.indexOf('page')) {
            page = parseInt(window.location.hash.split('/')[1]);
        }
        if (page && page > 0) {
            this.start = page;
            this.force();
        }
    }
}
class facetedSearchBuilder {
    constructor() {
        this.lazyloader = '';
        this.active = '';
        this.start = 0;
        this.memory = {};
        this.api = '';
        this.csrf = document.querySelector("meta[name='csrf-token']").getAttribute("content");
        if($('#facetedSearch') && $('#facetedSearch').attr('font-awesome')) {
            this.font = $('#facetedSearch').attr('font-awesome');
        } else {
            this.font = 'fal';
        }
    }
    put(id) {
        this.lazyloader = id;
    }
    get() {
        return this.lazyloader;
    }
    settings() {
        if ($('#facetedDefault').is(':visible')){
            var force = 2;
        } else {
            var force = 1;
        }
      let localXHR = new XMLHttpRequest()
      localXHR = $.ajax({
            url: '/torrents/filterSettings',
            data: {
                _token: this.csrf,
                force: force,
            },
            type: 'get'
        }).done(function (e) { });
    }
    show(page,nav) {
        if(facetedSearchXHR != null) {
            facetedSearchXHR.abort();
        }
        facetedSearchXHR = new XMLHttpRequest();
        if ($('#facetedDefault').is(':visible')){
            var search = $("#query").val();
        } else {
            var search = $("#search").val();
        }
      const description = $('#description').val()
      const keywords = $('#keywords').val()
      const uploader = $('#uploader').val()
      const imdb = $('#imdb').val()
      const tvdb = $('#tvdb').val()
      const tmdb = $('#tmdb').val()
      const mal = $('#mal').val()
      const igdb = $('#igdb').val()
      const start_year = $('#start_year').val()
      const end_year = $('#end_year').val()
      const categories = []
      const types = []
      const resolutions = []
      const genres = []
      let qty = $('#qty').val()
      const notdownloaded = (function () {
        if ($('#notdownloaded').is(':checked')) {
          return $('#notdownloaded').val()
        }
      })()
      const downloaded = (function () {
        if ($('#downloaded').is(':checked')) {
          return $('#downloaded').val()
        }
      })()
      const idling = (function () {
        if ($('#idling').is(':checked')) {
          return $('#idling').val()
        }
      })()
      const leeching = (function () {
        if ($('#leeching').is(':checked')) {
          return $('#leeching').val()
        }
      })()
      const freeleech = (function () {
        if ($('#freeleech').is(':checked')) {
          return $('#freeleech').val()
        }
      })()
      const doubleupload = (function () {
        if ($('#doubleupload').is(':checked')) {
          return $('#doubleupload').val()
        }
      })()
      const featured = (function () {
        if ($('#featured').is(':checked')) {
          return $('#featured').val()
        }
      })()
      const seeding = (function () {
        if ($('#seeding').is(':checked')) {
          return $('#seeding').val()
        }
      })()
      const stream = (function () {
        if ($('#stream').is(':checked')) {
          return $('#stream').val()
        }
      })()
      const highspeed = (function () {
        if ($('#highspeed').is(':checked')) {
          return $('#highspeed').val()
        }
      })()
      const sd = (function () {
        if ($('#sd').is(':checked')) {
          return $('#sd').val()
        }
      })()
      const internal = (function () {
        if ($('#internal').is(':checked')) {
          return $('#internal').val()
        }
      })()
      const alive = (function () {
        if ($('#alive').is(':checked')) {
          return $('#alive').val()
        }
      })()
      const dying = (function () {
        if ($('#dying').is(':checked')) {
          return $('#dying').val()
        }
      })()
      const dead = (function () {
        if ($('#dead').is(':checked')) {
          return $('#dead').val()
        }
      })()
      $(".category:checked").each(function () {
            categories.push($(this).val());
        });
        $(".type:checked").each(function () {
            types.push($(this).val());
        });
        $(".resolution:checked").each(function () {
            resolutions.push($(this).val());
        });
        $(".genre:checked").each(function () {
            genres.push($(this).val());
        });


        if (this.view == 'card') {
            var sorting = $("#sorting").val();
            var direction = $("#direction").val();
            qty = 33;
        } else if(this.view == 'group') {
            var sorting = $("#sorting").val();
            var direction = $("#direction").val();
        } else {
            if ($('#created_at').attr('state') && $('#created_at').attr('state') > 0) {
                var sorting = 'created_at';
                if ($('#created_at').attr('state') == 1) {
                    var direction = 'asc';
                } else if ($('#created_at').attr('state') == 2) {
                    var direction = 'desc';
                }
            }
            if ($('#name').attr('state') && $('#name').attr('state') > 0) {
                var sorting = 'name';
                if ($('#name').attr('state') == 1) {
                    var direction = 'asc';
                } else if ($('#name').attr('state') == 2) {
                    var direction = 'desc';
                }
            }
            if ($('#seeders').attr('state') && $('#seeders').attr('state') > 0) {
                var sorting = 'seeders';
                if ($('#seeders').attr('state') == 1) {
                    var direction = 'asc';
                } else if ($('#seeders').attr('state') == 2) {
                    var direction = 'desc';
                }
            }
            if ($('#leechers').attr('state') && $('#leechers').attr('state') > 0) {
                var sorting = 'leechers';
                if ($('#leechers').attr('state') == 1) {
                    var direction = 'asc';
                } else if ($('#leechers').attr('state') == 2) {
                    var direction = 'desc';
                }
            }
            if ($('#times_completed').attr('state') && $('#times_completed').attr('state') > 0) {
                var sorting = 'times_completed';
                if ($('#times_completed').attr('state') == 1) {
                    var direction = 'asc';
                } else if ($('#times_completed').attr('state') == 2) {
                    var direction = 'desc';
                }
            }
            if ($('#size').attr('state') && $('#size').attr('state') > 0) {
                var sorting = 'size';
                if ($('#size').attr('state') == 1) {
                    var direction = 'asc';
                } else if ($('#size').attr('state') == 2) {
                    var direction = 'desc';
                }
            }
        }
        facetedSearchXHR = $.ajax({
            url: this.api,
            data: {
                _token: this.csrf,
                search: search,
                description: description,
                keywords: keywords,
                uploader: uploader,
                imdb: imdb,
                tvdb: tvdb,
                notdownloaded: notdownloaded,
                downloaded: downloaded,
                idling: idling,
                leeching: leeching,
                seeding: seeding,
                view: this.view,
                tmdb: tmdb,
                mal: mal,
                igdb: igdb,
                start_year: start_year,
                end_year: end_year,
                categories: categories,
                types: types,
                resolutions: resolutions,
                genres: genres,
                freeleech: freeleech,
                doubleupload: doubleupload,
                featured: featured,
                stream: stream,
                highspeed: highspeed,
                sd: sd,
                internal: internal,
                alive: alive,
                dying: dying,
                dead: dead,
                sorting: sorting,
                direction: direction,
                page: page,
                qty: qty
            },
            type: 'get',
            beforeSend: function () {
                $("#facetedSearch").html('<i class="fal fa-spinner fa-spin fa-3x fa-fw"></i>')
            }
        }).done(function (e) {
            $data = $(e);
            if (!$data[0].hasOwnProperty('result')) {
                $("#facetedSearch").html($data);
                if (page) {
                    $("#facetedHeader")[0].scrollIntoView();
                }
                if (!nav) {
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState(null, null, ' ');
                    }
                }
                torrentBookmark.update();
                facetedSearch.refresh();
                facetedSearchXHR = null;
                // facetedSearch.posters();
            }
        });
    }
    posters() {
        $('.show-poster').each(function() {
            $(this).off('click');
            $(this).on('click', function(e) {
                e.preventDefault();
              const name = $(this).attr('data-name')
              const image = $(this).attr('data-image')
              Swal.fire({
                    showConfirmButton: false,
                    showCloseButton: true,
                    background: 'rgb(35,35,35)',
                    width: 970,
                    html: image,
                    title: name,
                    text: '',
                });
            });
        });
    }
    inform() {
        return this.active;
    }
    handle(id) {
        const trigger = $('#' + id).attr('trigger');
        this.active = id;
        if(trigger && trigger == 'keyup') {
            const length = $('#' + id).val().length;
            if(length == this.memory[id]) return;
            this.memory[id] = length;
        } else if(trigger && trigger == 'sort') {
            $('.facetedSort').each(function() {
                if($(this).attr('id') && $(this).attr('id') == facetedSearch.inform()) {
                    if($('#'+id).length == 0) {
                        return;
                    }
                    if($('#'+id).attr('state') && ($('#'+id).attr('state') == 0 || $('#'+id).attr('state') == 2)) {
                        $('#'+id).attr('state',1);
                    } else if($('#'+id).attr('state') && ($('#'+id).attr('state') == 1)) {
                        $('#'+id).attr('state',2);
                    } else {
                        $('#'+id).attr('state',1);
                    }
                } else {
                    $(this).attr('state', 0);
                }
            });
        }
        this.show(0);
    }
    refresh(callback) {
        $('.facetedSearch').each(function() {
            const $this = $(this);
            const trigger = $this.attr('trigger') ? $this.attr('trigger') : 'click';
            let cloner = trigger;
            if(cloner == 'sort') {
                cloner='click';
            }
            $this.off(cloner);
            if($this.attr('trigger') == 'sort') {
                $this.on(cloner, function(e) {
                    e.preventDefault();
                    facetedSearch.handle($this.attr('id'));
                });
            } else {
                $this.on(cloner, _.debounce(function(e) {
                    facetedSearch.handle($this.attr('id'));
                }, 400));
            }
        });
        $('.facetedLoader').each(function() {
            $(this).off('click');
            $(this).on('click', function(e) {
                e.preventDefault();
                if($(this).attr('torrent')) {
                    facetedSearch.put($(this).attr('torrent'));
                    $('.facetedLoading').each(function () {
                      const check = facetedSearch.get()
                      if ($(this).attr('torrent') && $(this).attr('torrent') == check) {
                            $(this).show();
                        }
                    });
                    $('#facetedCell'+$(this).attr('torrent')).hide();
                }
            });
        });
        $('#facetedFiltersToggle').off('click');
        $('#facetedFiltersToggle').on('click', function(e) {
            facetedSearch.settings();
            e.preventDefault();
            if ($('#facetedDefault').is(':visible')){
                $('#facetedFilters').show();
                $('#facetedDefault').hide();
                return;
            }
            $('#facetedFilters').hide();
            $('#facetedDefault').show();
        });
        if(callback) {
            callback();
        }
    }
    force() {
        this.show(this.start,true);
    }
    init(type) {
        this.type = type;
        if (this.type == 'card') {
            this.api = '/torrents/filter';
            this.view = 'card';
        }
        else if (this.type == 'request') {
            this.api = '/requests/filter';
            this.view = 'request';
        }
        else if (this.type == 'group') {
            this.api = '/torrents/filter';
            this.view = 'group';
        }
        else {
            this.api = '/torrents/filter';
            this.view = 'list';
        }

      let page = 0
      if (window.location.hash && window.location.hash.indexOf('page')) {
            page = parseInt(window.location.hash.split('/')[1]);
        }
        if (page && page > 0) {
            this.start = page;
            this.refresh(function() { facetedSearch.force(); });
        }
        else {
            this.refresh(function() { });
        }
        // this.posters();
    }
}
class userExtensionBuilder {
    constructor() {
        this.csrf = document.querySelector("meta[name='csrf-token']").getAttribute("content");
    }
    handle(flag) {
        if(flag) {
            $('.'+this.extension).each(function() {
                $(this).show();
            });
        }
        else {
            $('.'+this.extension).each(function() {
                $(this).hide();
            });
        }
    }
    init() {
        this.extension = $('#userExtension').attr('extension');
        $('#extended').off('change');
        $('#extended').on('change', function() {
            userExtension.handle(($(this).is(':checked')));
        });
        this.handle();
    }
}
class forumTipBuilder {
    constructor() {
        this.csrf = document.querySelector("meta[name='csrf-token']").getAttribute("content");
        this.leaveTip = $('#forumTip').attr('leaveTip');
        this.quickTip = $('#forumTip').attr('quickTip');
        this.route = $('#forumTip').attr("route");
    }
    handle(user,id) {
        this.user = user;
        this.template = '<div class="some-padding">' +
            '<div class="box">' +
            '<form role="form" method="POST" action="'+this.route+'">' +
            '<input type="hidden" name="_token" value="' + this.csrf + '">' +
            '<input type="hidden" name="recipient" value="'+this.user+'">' +
            '<input type="hidden" name="post" value="'+id+'">'+
            '<input type="number" name="tip" value="0" placeholder="0" class="form-control">' +
            '<button type="submit" class="btn btn-primary">'+this.leaveTip+'</button>' +
            '<br>' +
            '<br>' +
            '<span class="text-green text-bold">'+this.quickTip+'</span>' +
            '<br>' +
            '<button type="submit" value="10" name="tip" class="label label-sm label-success space-me">10 BON</button>' +
            '<button type="submit" value="20" name="tip" class="label label-sm label-danger space-me">20 BON</button>' +
            '<button type="submit" value="50" name="tip" class="label label-sm label-info space-me">50 BON</button>' +
            '<button type="submit" value="100" name="tip" class="label label-sm label-warning space-me">100 BON</button>' +
            '<button type="submit" value="200" name="tip" class="label label-sm label-danger space-me">200 BON</button>' +
            '<button type="submit" value="500" name="tip" class="label label-sm label-primary space-me">500 BON</button>' +
            '<button type="submit" value="1000" name="tip" class="label label-sm label-success space-me">1000 BON</button>' +
            '</form>' +
            '</div>' +
            '</div>';

        $('#forumTip'+id).html(this.template);
        $('#forumTip'+id).show();
    }
    init() {
        $('.forumTip').each(function() {
            $(this).on('click', function(e) {
                e.preventDefault();
                forumTip.handle($(this).attr("user"),$(this).attr("post"));
            });
        });
    }
}
class torrentBookmarkBuilder {
    constructor() {
        this.csrf = document.querySelector("meta[name='csrf-token']").getAttribute("content");
        if($('#facetedBookmark') && $('#facetedBookmark').attr('font-awesome')) {
            this.font = $('#facetedBookmark').attr('font-awesome');
        } else {
            this.font = 'fal';
        }
    }
    update() {
        $('.torrentBookmark').each(function() {
          const active = $(this).attr('state') ? $(this).attr('state') : 0
          const id = $(this).attr('id') ? $(this).attr('id') : 0
          const custom = $(this).attr('custom') ? $(this).attr('custom') : ''
          $(this).off('click');
            if(active == 1) {
                $(this).attr("data-original-title","Unbookmark Torrent");
            } else {
                $(this).attr("data-original-title","Bookmark Torrent");
            }
            $(this).html('<button custom="'+custom+'" state="'+active+'" torrent="'+id+'" class="btn ' + (active > 0 ? 'btn-circle btn-danger' : 'btn-circle btn-primary') + '"><i class="'+this.font+' fal fa-bookmark"></i></button>');
            $(this).on('click', function() {
                torrentBookmark.handle($(this).attr("torrent"),$(this).attr("state"),$(this).attr("custom"));
            });
        });
    }
    handle(id,active,custom) {
        if(!active || active == 0) {
            this.create(id,custom);
            return;
        }
        this.destroy(id,custom);
    }
    create(id,custom) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if(torrentBookmarkXHR != null) {
            torrentBookmarkXHR.abort();
        }

        torrentBookmarkXHR = new XMLHttpRequest();

        torrentBookmarkXHR = $.ajax({
            url: '/bookmarks/' + id + '/store',
            data: {
                _token: this.csrf,
            },
            type: 'POST'
        }).done(function (e) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Torrent Has Been Bookmarked Successfully!',
                showConfirmButton: false,
                timer: 4500,
            });
            if(custom && custom != '') {
                $('#'+custom).html('<button custom="'+custom+'" state="1" torrent="' + id + '" class="btn btn-circle btn-danger"><i class="' + this.font + ' fal fa-bookmark"></i></button>');
                $('#'+custom).attr("state", '1');
                $('#'+custom).attr("data-original-title", 'Unbookmark Torrent');
            } else {
                $('#torrentBookmark' + id).html('<button custom="'+custom+'" state="1" torrent="' + id + '" class="btn btn-circle btn-danger"><i class="' + this.font + ' fal fa-bookmark"></i></button>');
                $('#torrentBookmark' + id).attr("state", '1');
                $('#torrentBookmark' + id).attr("data-original-title", 'Unbookmark Torrent');
            }
            torrentBookmarkXHR = null;
        });
    }
    destroy(id,custom) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if(torrentBookmarkXHR != null) {
            return;
        }

        torrentBookmarkXHR = new XMLHttpRequest();

        torrentBookmarkXHR = $.ajax({
            url: '/bookmarks/' + id + '/destroy',
            data: {
                _token: this.csrf,
                _method: 'DELETE',
            },
            type: 'POST'
        }).done(function (e) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Torrent Has Been Unbookmarked Successfully!',
                showConfirmButton: false,
                timer: 4500,
            });
            if(custom && custom != '') {
                $('#' + custom).html('<button custom="'+custom+'" state="0" torrent="' + id + '" class="btn btn-circle btn-primary"><i class="' + this.font + ' fal fa-bookmark"></i></button>');
                $('#' + custom).attr("state", '0');
                $('#' + custom).attr("data-original-title", 'Bookmark Torrent');
            } else {
                $('#torrentBookmark' + id).html('<button custom="'+custom+'" state="0" torrent="' + id + '" class="btn btn-circle btn-primary"><i class="' + this.font + ' fal fa-bookmark"></i></button>');
                $('#torrentBookmark' + id).attr("state", '0');
                $('#torrentBookmark' + id).attr("data-original-title", 'Bookmark Torrent');
            }
            torrentBookmarkXHR = null;
        });
    }
}

// Global attachments.
// Attach to events using jQuery.

$(document).ajaxComplete(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
$(document).ready(function () {
    if (document.getElementById('request-form-description')) {
        $('#request-form-description').wysibb({});
    }
    if($('#comments').length > 0) {
        if (window.location.hash && window.location.hash.substring) {
            let hash = window.location.hash.substring(1).split('/')[0];
            if (hash == 'comments') {
                $("#comments")[0].scrollIntoView();
            }
        }
    }
    if($('#upload-form-description').length > 0) {
        $('#upload-form-description').wysibb({});
    }
    if(document.getElementById('facetedSearch')) {
      const facetedType = document.getElementById('facetedSearch').getAttribute('type')
      facetedSearch.init(facetedType);
    }
    if(document.getElementById('userFilter')) {
        userFilter.init();
    }
    if(document.getElementById('forumTip')) {
        forumTip.init();
    }
    if(document.getElementById('userExtension')) {
        userExtension.init();
    }
    if(document.getElementById('configExtension')) {
        configExtension.init();
    }
    torrentBookmark.update();
});
$(document).on('click', '.pagination a', function (e) {

    var url = $(this).attr('href');
    if($('#comments').length > 0) {
        e.preventDefault();
        window.location.href = url + '#comments';
        return;
    }

    if(!document.getElementById('facetedSearch') && !document.getElementById('userFilter')) { }
    else {

        e.preventDefault();

      let sub = null
      if (window.location.hash && window.location.hash.substring) {
            sub = window.location.hash.substring(1).split('/')[0];
        }
        if (!sub) {
            sub = 'page';
        }
      const link_url = $(this).attr('href')
      const page = parseInt(link_url.split('page=')[1])
      var url = (window.location.href.split("#")[0]) + '#' + sub + '/' + page;
        if (window.history && window.history.pushState) {
            window.history.pushState("", "", url);
        }
        if (document.getElementById('facetedSearch')) {
            facetedSearch.show(page, true);
        }
        if (document.getElementById('userFilter')) {
            userFilter.handle(page, true);
        }

    }
});
$(document).mousedown(function(){
    if(audioLoaded == 0) {
        window.sounds = new Object();
      const sound = new Audio('/sounds/alert.mp3')
      sound.load();
        window.sounds['alert.mp3'] = sound;
    }
    audioLoaded = 1;
});
if(document.getElementById('torrent')) {
    document.querySelector("#torrent").addEventListener("change", () => {
        uploadExtension.hook();
    });
}
// Globals

const facetedSearch = new facetedSearchBuilder();
const torrentBookmark = new torrentBookmarkBuilder();
const userFilter = new userFilterBuilder();
const forumTip = new forumTipBuilder();
const userExtension = new userExtensionBuilder();
const uploadExtension = new uploadExtensionBuilder();
var userFilterXHR = null;
var facetedSearchXHR = null;
var torrentBookmarkXHR = null;
var audioLoaded = 0;
