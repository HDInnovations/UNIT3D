<?php

return [
    // partials/achievement-modal.blade.php
    'well-done' => 'Výborně!',
    'all-achievements' => 'Všechny úspěchy',

    // partials/captcha.blade.php (honeypot label, hidden from real users)
    'honeypot-name' => 'Jméno',

    // partials/comparison.blade.php
    'show' => 'Zobrazit',

    // partials/footer.blade.php
    'view-all' => '[Zobrazit vše]',
    'api-documentation' => 'API dokumentace',
    'built-using' => 'Postaveno na:',
    'wikis' => 'Wiki',

    // partials/quick-search-dropdown.blade.php
    'quick-search-placeholder' => 'Hledat',
    'quick-search-prompt' => 'Hledejte filmy, seriály nebo osoby',
    'quick-search-no-results' => 'Nenalezeny žádné výsledky',

    // partials/statsgroupmenu.blade.php
    'group-requirements' => 'Požadavky skupiny',

    // partials/statsusermenu.blade.php
    'top-upload-snatches' => 'Nejvíce stáhnutí uploadů',
    'top-messages' => 'Nejvíce zpráv',

    // partials/top-nav.blade.php
    'documentation' => 'Informace a dokumentace',
    'about-site' => 'O :site',
    'wiki' => 'Wiki',
    'donate' => 'Přispět',
    'donate-filled' => 'Naplněno z :percent %',
    'support-site' => 'Podpořte :site (:percent %)',
    'support-brand' => 'Podpořte :brand',

    // layout/default.blade.php
    'confirm-action-title' => 'Opravdu?',

    // blocks/latest-posts.blade.php, latest-topics.blade.php
    'no-posts' => 'Žádné příspěvky.',
    'no-topics' => 'Žádná témata.',

    // blocks/chat.blade.php
    'chatbox-loading' => 'Chat se načítá',
    'chatbox' => 'Chat',
    'users-count' => 'Uživatelé:',
    'toggle-typing-notifications' => 'Přepnout upozornění na psaní',
    'room' => 'Místnost',
    'status' => 'Stav',
    'toggle-fullscreen' => 'Přepnout na celou obrazovku',
    'custom-user-icon' => 'Vlastní ikona uživatele',
    'lifetime-donor' => 'Doživotní dárce',
    'donor' => 'Dárce',
    'system-notification' => 'Systémové oznámení',
    'users' => 'Uživatelé',
    'gift-user-bon' => 'Darovat uživateli bony',
    'send-chat-pm' => 'Poslat soukromou zprávu v chatu',
    'write-your-message' => 'Napište svou zprávu...',
    'unknown-user' => 'Neznámý',
    'no-chat-history' => 'Zde není žádná historie chatu. Napište zprávu!',
    'delete-message' => 'Smazat zprávu',
    'typing-several' => 'Několik lidí píše…',
    'typing-one' => ':name píše…',
    'typing-multiple' => ':names píší…',

    // resources/js chatbox.js
    'chat-connection-lost' => 'Spojení bylo ztraceno. Zkouším se znovu připojit…',
    'chat-loading-error' => 'Chyba při načítání chatu. Zkuste to prosím znovu.',

    // resources/js like/dislike/bookmark buttons
    'error-title' => 'Chyba',
    'liked' => 'Líbí se mi',
    'like-this-post' => 'Líbí se mi tento příspěvek',
    'like-applied' => 'Váš úspěšný like byl zaznamenán!',
    'disliked' => 'Nelíbí se mi',
    'dislike-this-post' => 'Nelíbí se mi tento příspěvek',
    'dislike-applied' => 'Váš dislike byl úspěšně zaznamenán!',
    'unbookmark' => 'Zrušit záložku',
    'bookmark' => 'Přidat záložku',
    'bookmark-applied' => 'Torrent byl úspěšně přidán do záložek!',
    'unbookmark-applied' => 'Torrent byl úspěšně odebrán ze záložek!',

    // resources/js/unit3d/helper.js
    'found-match' => 'Nalezena shoda: :title (:year)',

    // errors/layout.blade.php and error pages
    'error-go-home' => 'Domů',
    'error-400-title' => 'Chyba 400: Chybný požadavek!',
    'error-400-description' => 'Server nemohl porozumět požadavku kvůli chybné syntaxi. Klient by neměl požadavek opakovat bez úprav.',
    'error-401-title' => 'Chyba 401: Neautorizováno!',
    'error-401-description' => 'Chybí nebo je neplatný ověřovací token.',
    'error-403-title' => 'Chyba 403: Zakázáno!',
    'error-403-description' => 'Nemáte oprávnění k provedení této akce!',
    'error-404-title' => 'Chyba 404: Stránka nenalezena',
    'error-404-description' => 'Požadovanou stránku se nepodařilo najít! Zkontrolujte adresu a zkuste to znovu!',
    'error-405-title' => 'Chyba 405: Metoda není povolena!',
    'error-405-description' => 'Metoda, kterou se pokoušíte použít, není tímto serverem povolena.',
    'error-444-title' => 'Chyba 444',
    'error-444-description' => 'SPOJENÍ UZAVŘENO BEZ ODPOVĚDI.',
    'error-500-title' => 'Chyba 500: Interní chyba serveru',
    'error-500-description' => 'Na serveru došlo k interní chybě. Omlouváme se za komplikace.',
    'error-502-title' => 'Chyba 502: Chybná brána!',
    'error-502-description' => 'Server jednající jako brána nebo proxy obdržel neplatnou odpověď od nadřazeného serveru.',
    'error-503-title' => 'Chyba 503: Služba nedostupná!',
    'error-503-description' => 'Omlouváme se, provádíme údržbu. Zkuste to prosím znovu později.',
    'error-307-title' => 'Chyba 307: Dočasné přesměrování!',
    'error-307-description-1' => 'Omluvte prosím komplikace.',
    'error-307-description-2' => 'Momentálně tuto funkci budujeme nebo vylepšujeme.',
    'error-307-description-3' => 'To je v pořádku, my se také těšíme!',

    // auth pages — session flash prefixes
    'flash-warning' => 'Varování: :message',
    'flash-info' => 'Info: :message',
    'flash-success' => 'Úspěch: :message',

    // auth/two-factor-challenge.blade.php
    'two-factor-title' => 'Dvoufaktorové ověření',
    'use-recovery-code' => 'Použít obnovovací kód',

    // auth/verify-email.blade.php
    'verify-email-almost-done' => 'Už to skoro máte…',
    'verify-email-instructions' => 'Klikněte na ověřovací odkaz zaslaný na váš e-mail a aktivujte svůj účet.',
    'verify-email-having-issues' => 'Máte potíže?',
    'verify-email-resend' => 'Znovu odeslat ověřovací e-mail',

    // auth/application/create.blade.php
    'application-title' => 'Přihláška',
    'application-description' => 'Přihláška',
    'proofs' => 'Doklady',
    'proof-number' => 'Doklad :number',
    'response-time' => 'Doba odezvy:',
    'memory-used' => 'Využitá paměť:',
    'system-load' => 'Zátěž systému:',
    'site-design-copyright' => 'Web a design © :years :site',
    'versus' => 'proti',
    'pagination-navigation' => 'Stránkovací navigace',
    'achievement-unlocked' => 'Úspěch odemčen',
];
