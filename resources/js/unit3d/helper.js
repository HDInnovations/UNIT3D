class uploadExtensionBuilder {
    removeDots(title) {
        // Remove extensions
        title = title.replace(/\.mkv$/i, '');
        title = title.replace(/\.mk3d$/i, '');
        title = title.replace(/\.mp4$/i, '');
        title = title.replace(/\.iso$/i, '');
        title = title.replace(/\.m2ts$/i, '');
        title = title.replace(/\.ts$/i, '');
        // Replace dots with spaces
        title = title.replaceAll('.', ' ');
        // Fix extra spaces, this will be done at the end again.
        title = title.replace(/ +/g, ' ');
        // Audio Codec channel fixes
        title = title.replace(/ E-?AC-?3 /i, ' DD+ ');
        title = title.replace(/ AC-?3 /i, ' DD ');
        title = title.replace(/ AC-? ?3-?EX /i, ' DD EX ');
        title = title.replace(/( DTS-?H?D? ?MA ?)(\d)( )(\d)/i, ' DTS-HD MA $2.$4');
        title = title.replace(/( DTS-?X ?)(\d)( )(\d)/i, ' DTS:X $2.$4');
        title = title.replace(/( DTS-?X)(-?[^ ]*)$/i, ' DTS:X 7.1$2');
        title = title.replace(/( DTS-?H?D? ?HRA? ?)(\d)( )(\d)/i, ' DTS-HD HRA $2.$4');
        title = title.replace(/( FLAC ?)(\d)( )(\d)/i, '$1 $2.$4');
        title = title.replace(/( L?PCM ?)(\d)( )(\d)/i, '$1 $2.$4');
        title = title.replace(/( DD[P+]? ?)(\d)( )(\d)/i, '$1 $2.$4');
        title = title.replace(/( Opus ?)(\d)( )(\d)/i, ' Opus $2.$4');
        title = title.replace(/( AAC? ?L?C? ?S?B?R? ?)(\d)( )(\d)/i, ' AAC $2.$4');
        title = title.replace(/ (DDP?\+?)[- ]*EX ?(\d) (\d)/i, ' $1 EX $2.$3');
        title = title.replace(/ DDPA ?5.1 /i, ' DD+ 5.1 Atmos ');
        title = title.replace(/( \d\.\d)\+Atmos /i, '$1 Atmos ');
        title = title.replace(/ DDP /i, ' DD+ ');
        title = title.replace(/ (TrueHD|Atmos) (\d) (\d)/i, ' $1 $2.$3');
        // Fix Atmos
        if (!title.includes(' DD+ ')) {
            title = title.replace(/ (TrueHD ?)?Atmos (\d)([ .])(\d)/i, ' TrueHD $2.$4 Atmos');
        }
        title = title.replace(/ +DTSMA /i, ' DTS-HD MA ');
        // Fix for parenthesis around year.
        title = title.replace(/( \()(\d{4})(\) )/i, ' $2 ');
        // Fix 1080P and alike
        title = title.replace(/( )(\d{3,4})(I )/i, ' $2i ');
        title = title.replace(/( )(\d{3,4})(P )/i, ' $2p ');
        // Video codec fixes fix
        title = title.replace(/ +MPEG2 +/i, ' MPEG-2 ');
        title = title.replace(/ +VC1 +/i, ' VC-1 ');
        title = title.replace(/( +dxva)([ -])/i, ' $2');
        // Fixes for H.264/H.265
        title = title.replace(/( +H ?26)(\d)/i, ' H.26$2');
        title = title.replace(/( +X ?26)(\d)/i, ' x26$2');
        // Dolby Vision fix
        title = title.replace(/( +DoVi +)/i, ' DV ');
        title = title.replace(/ +(HDR10Plus|HDR10P|HDR\+|HDR10\+) +/i, ' HDR10+ ');
        title = title.replace(/( +HEVC +)(HDR|HDR10)( +)/i, ' HDR HEVC ');
        // WEB-DL fix
        title = title.replace(/ WEBDL /i, ' WEB-DL ');
        title = title.replace(/ WEB /i, ' WEB-DL ');
        // Fix two common services for WEB
        title = title.replace(/( +Netflix +WEB)/i, ' NF WEB');
        title = title.replace(/( +Amazon +WEB)/i, ' AMZN WEB');
        if (title.includes('WEB-DL')) {
            title = title.replace(/( +X26)(\d)/i, ' H.26$2');
            title = title.replace(/ +AVC/i, ' H.264');
            title = title.replace(/ +HEVC/i, ' H.265');
        }
        // Fix for ##x## instead of S##E##
        title = title.replace(/( +)(\d{2})(x)(\d{2} )/i, ' S$2E$4');
        // Remove episode (non S00/E00)
        if (!title.includes('E00') && !title.includes('S00')) {
            title = title.replace(/(S\d{2}E\d{2}[^ ]* )(.* )(\d{3,4}[ip])/i, '$1 $3');
        }
        //Blu-ray fixes
        title = title.replace(/ +Bluray +/i, ' BluRay ');
        title = title.replace(/ +BDRip +/i, ' BluRay ');
        title = title.replace(/ +Blu-Ray +/i, ' Blu-ray ');
        // Remux fix
        title = title.replace(/( +remux)( *)/i, ' REMUX$2');
        if (title.includes(' REMUX')) {
            let lowerTitle;
            title = title.replace(/ Blu-ray /i, ' BluRay ');
            lowerTitle = title.toLowerCase();
            if (
                !title.includes('BluRay') &&
                !(
                    lowerTitle.includes(' dvd ') ||
                    lowerTitle.includes(' ntsc ') ||
                    lowerTitle.includes(' pal ')
                )
            ) {
                title = title.replace(/ REMUX/i, ' ');
                title = title.replace(/( \d{3,4}[ip] )/i, ' $1 BluRay REMUX ');
            }
        }
        // A fix for Blu-ray Dolby Vision.  All DV on Blu-rays has HDR10 base layer
        if (
            (title.includes('Blu-ray') || title.includes('BluRay')) &&
            title.includes(' DV') &&
            !title.includes('HDR')
        ) {
            title = title.replace(/( DV )(HEVC|[hx]\.?265)/i, ' DV HDR $2 ');
        }
        // 3D location fix, only when it is after the year/season/ep, not if it is before.
        title = title.replace(
            /(S\d{2}E\d{2}|S\d{2}|\d{4} .*)( 3D )(.* \d{3,4}[ip] .*)( Blu-ray|BluRay)/i,
            '$1 $3 3D $4',
        );
        title = title.replace(
            /(S\d{2}E\d{2}|S\d{2}|\d{4}.*)( 3D)( \d{3,4}[ip] .*)( Blu-ray|BluRay)/i,
            '$1$3 3D $4',
        );
        // Fix for date formats
        title = title.replace(/( \d{4})( +)(\d{2})( +)(\d{2} )/, '$1-$3-$5');
        title = title.replace(/( \d{4})( +)(\d{2} )/, '$1-$3');
        title = title.replace(/(UHD)?( BluRay)(.*)( \d{3,4}[ip] )/i, '$4 $1 $2 $3 ');
        // Fix some remux ordering
        title = title.replace(
            /( +\d{3,4}[ip] +)(.*)(UHD)?( +BluRay)(.*)(AVC|HEVC|MPEG-2|VC-1)(.*)(Hybrid)?( +REMUX)/i,
            '$8$1$2$3$4 REMUX $5$6$7',
        );
        title = title.replace(
            /(.*)(NTSC|PAL)?( +DVD)(.*)(Hybrid)?( +REMUX)/i,
            '$1$2 $5 $3 REMUX $4 ',
        );
        // Move the video codec to correct location in a remux.
        if (title.includes('REMUX')) {
            // Move video codec where it should be
            title = title.replace(
                /(BluRay|DVD)( +REMUX +)(.*)(AVC|HEVC|MPEG-2|VC-1)/i,
                '$1$2 $4 $3',
            );
        }
        // Fixing HDR ordering
        if (title.includes('HEVC') || title.includes('H.265') || title.includes('x265')) {
            if (title.includes(' DV ') && title.includes(' HDR10+ ')) {
                // Remove the two HDR formats and place in correct order.
                // Replace with space to not ruin spacing.
                title = title.replace(' DV ', ' ');
                title = title.replace(' HDR10+ ', ' ');
                // Replace them where HEVC/H.265/x265 is.
                title = title.replace(/( )(HEVC|H\.265|x265)/i, ' DV HDR10+ $2');
            } else if (title.includes(' DV ') && title.includes(' HDR ')) {
                // Remove the two HDR formats and place in correct order.
                // Replace with space to not ruin spacing.
                title = title.replace(' DV ', ' ');
                title = title.replace(' HDR ', ' ');
                // Replace them where HEVC/H.265/x265 is.
                title = title.replace(/( )(HEVC|H\.265|x265)/i, ' DV HDR $2');
            } else if (title.includes(' DV ')) {
                // Remove the DV and place in correct order.
                // Replace with space to not ruin spacing.
                title = title.replace(' DV ', ' ');
                // Replace them where HEVC/H.265/x265 is.
                title = title.replace(/( )(HEVC|H\.265|x265)/, ' DV $2');
            } else if (title.includes(' HDR10+ ')) {
                // Remove the HDR10+ place in correct order.
                // Replace with space to not ruin spacing.
                title = title.replace(' HDR10+ ', ' ');
                // Replace them where HEVC/H.265/x265 is.
                title = title.replace(/( )(HEVC|H\.265|x265)/i, ' HDR10+ $2');
            } else if (title.includes(' HDR ')) {
                // Remove the HDR and place in correct order.
                // Replace with space to not ruin spacing.
                title = title.replace(' HDR ', ' ');
                // Replace them where HEVC is.
                title = title.replace(/( )(HEVC|H\.265|x265)/i, ' HDR $2');
            }
        }
        // Fixing UHD being missing in 2160p Blu-rays
        if (!title.includes('UHD')) {
            title = title.replace(/( +2160p)(.*)( Blu-?ray )/i, ' 2160p $2 UHD$3');
        }
        // Fixing hybrid casing/location
        if (title.toLowerCase().includes('hybrid')) {
            title = title.replace(/(.*)( \d{3,4}[ip] )(.*)(Hybrid)(.+)/i, '$1 Hybrid$2$3 $5');
            title = title.replace(
                /(.*)(S\d{2}E\d{2}|S\d{2}|\d{4})( .*)(Hybrid)(.*)( \d{3,4}[ip] )/i,
                '$1$2$3 $5 Hybrid $6',
            );
        }
        // Fix extra spaces
        title = title.replace(/ +/g, ' ');
        // Fix space before group
        title = title.replace(/( ?- ?)([^ ]*)$/i, '-$2');
        return title.trim();
    }
    hook() {
        let name = document.querySelector('#title');
        let tmdb_movie = document.querySelector('#auto_tmdb_movie');
        let tmdb_tv = document.querySelector('#auto_tmdb_tv');
        let imdb = document.querySelector('#autoimdb');

        if (!name.value.trim() && (!tmdb_movie.value.trim() || !tmdb_tv.value.trim())) {
            let torrent = document.querySelector('#torrent');
            let release;
            if (!name.value) {
                const fileEndings = ['.mkv.torrent', '.mp4.torrent', '.torrent'];
                let newValue = torrent.value;
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
                defaults: {
                    language: 'ENGLISH',
                }, // defaults values for : language, resolution and year
            });

            let matcher = name.value.toLowerCase();

            // Torrent Category
            if (release.type === 'Movie') {
                document.getElementById('autocat').value = 1;
            } else if (release.type === 'TV Show') {
                document.getElementById('autocat').value = 2;
            }

            // Torrent Type
            if (
                matcher.indexOf('bd50') > 0 ||
                matcher.indexOf('bd25') > 0 ||
                matcher.indexOf('untouched') > 0 ||
                matcher.indexOf('dvd5') > 0 ||
                matcher.indexOf('dvd9') > 0 ||
                matcher.indexOf('mpeg-2') > 0 ||
                matcher.indexOf('avc') > 0 ||
                matcher.indexOf('vc-1') > 0
            ) {
                document.getElementById('autotype').value = 1;
            }
            if (matcher.indexOf('remux') > 0) {
                document.getElementById('autotype').value = 2;
            }
            if (matcher.indexOf('x264') > 0) {
                document.getElementById('autotype').value = 3;
            }
            if (matcher.indexOf('x265') > 0) {
                document.getElementById('autotype').value = 3;
            }
            if (matcher.indexOf('webdl') > 0 || matcher.indexOf('web-dl') > 0) {
                document.getElementById('autotype').value = 4;
            }
            if (matcher.indexOf('web-rip') > 0 || matcher.indexOf('webrip') > 0) {
                document.getElementById('autotype').value = 5;
            }
            if (matcher.indexOf('hdtv') > 0) {
                document.getElementById('autotype').value = 6;
            }

            // Torrent Resolution
            if (release.resolution) {
                document.getElementById('autores').value = release.resolution;
            }

            // Torrent Season (TV Only)
            if (release.season) {
                document.getElementById('season_number').value = release.season;
            }

            // Torrent Episode (TV Only)
            if (release.episode) {
                document.getElementById('episode_number').value = release.episode;
            }

            // Torrent TMDB ID
            if (release.type === 'Movie') {
                theMovieDb.search.getMovie(
                    {
                        query: release.title,
                        year: release.year,
                    },
                    successCB,
                    errorCB,
                );
            } else if (release.type === 'TV Show') {
                theMovieDb.search.getTv(
                    {
                        query: release.title,
                    },
                    successCB,
                    errorCB,
                );
            }

            function successCB(data) {
                data = JSON.parse(data);
                if (release.type === 'Movie') {
                    if (data.results && data.results.length > 0) {
                        document.getElementById('auto_tmdb_movie').value = data.results[0].id;
                        document.getElementById('apimatch').value =
                            'Found Match: ' +
                            data.results[0].title +
                            ' (' +
                            data.results[0].release_date +
                            ')';
                        theMovieDb.movies.getExternalIds(
                            {
                                id: data.results[0].id,
                            },
                            s,
                            e,
                        );
                    }
                } else if (release.type === 'TV Show') {
                    if (data.results && data.results.length > 0) {
                        document.getElementById('auto_tmdb_tv').value = data.results[0].id;
                        document.getElementById('apimatch').value =
                            'Found Match: ' +
                            data.results[0].name +
                            ' (' +
                            data.results[0].first_air_date +
                            ')';
                        theMovieDb.tv.getExternalIds(
                            {
                                id: data.results[0].id,
                            },
                            s,
                            e,
                        );
                    }
                }
            }

            function errorCB(data) {
                console.log('Error callback: ' + data);
            }

            function error(data) {
                console.log('Error callback: ' + data);
            }

            //Torrent External IDs
            function s(data) {
                data = JSON.parse(data);
                let imdb = data.imdb_id;
                imdb = imdb.substring(2) ?? 0;
                if (release.type === 'Movie') {
                    document.getElementById('autoimdb').value = imdb;
                } else if (release.type === 'TV Show') {
                    document.getElementById('autoimdb').value = imdb;
                    document.getElementById('autotvdb').value = data.tvdb_id ?? 0;
                }
            }

            function e(data) {
                console.log('Error callback: ' + data);
            }
        }
    }
}

// Globals
const uploadExtension = new uploadExtensionBuilder();
