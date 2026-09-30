<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BlacklistClient;
use App\Models\Chatroom;
use App\Models\Forum;
use App\Models\ForumCategory;
use App\Models\Page;
use App\Models\Wiki;
use App\Models\WikiCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommunityContentSeeder extends Seeder
{
    public function run(): void
    {
        ForumCategory::upsert([
            [
                'id'          => 1,
                'position'    => 1,
                'name'        => 'Komunita',
                'slug'        => 'komunita',
                'description' => 'Diskuze členů trackeru.',
            ],
        ], ['id'], ['position', 'name', 'slug', 'description']);

        Forum::upsert([
            [
                'id'                => 1,
                'position'          => 1,
                'name'              => 'Vítejte a obecná diskuze',
                'slug'              => 'vitejte-a-obecna-diskuze',
                'description'       => 'Představte se, sdílejte nápady a diskutujte o trackeru.',
                'forum_category_id' => 1,
            ],
        ], ['id'], ['position', 'name', 'slug', 'description', 'forum_category_id']);

        Chatroom::query()
            ->where('name', 'General')
            ->update(['name' => 'Obecná diskuze']);
        Chatroom::firstOrCreate(['name' => 'Obecná diskuze']);

        Page::upsert([
            [
                'id'      => 1,
                'name'    => 'Pravidla',
                'content' => <<<'RULES'
[b]Vltava — pravidla komunity[/b]

[b]1. Účet je osobní[/b]
Jeden člověk používá jeden účet. Nesdílejte heslo, passkey ani API token; účet neprodávejte, nepůjčujte ani nevyměňujte. Ztrátu přístupu, podezřelé přihlášení nebo únik passkey ihned nahlaste správě.

[b]2. Respekt a soukromí[/b]
Ve fóru, chatu i zprávách komunikujte věcně. Nezveřejňujte cizí osobní údaje, obsah soukromých zpráv ani interní informace. Spam, reklama, vyhrožování, diskriminace a obtěžování sem nepatří.

[b]3. Férové sdílení[/b]
Neobcházejte poměr, hit-and-run, limity připojení, bonusový systém ani ochrany trackeru. Nepoužívejte cheaty, falešný upload, spoofing statistik, upravené klienty nebo automatizaci, která zatěžuje tracker. Seedujte podle požadavků své skupiny a nenechávejte torrent zmizet hned po dokončení.

[b]4. Obsah a nahrávání[/b]
Nahrávejte jen obsah, k němuž máte oprávnění, a dodržujte platné právo. Nevkládejte malware, podvodné archivy, hesla k cizím službám ani osobní data. Před nahráním vyhledejte duplicitní vydání; název, popis, MediaInfo, snímky a zařazení musí odpovídat skutečnému obsahu.

[b]5. Klienti a technika[/b]
Používejte pouze podporované klienty a nezveřejňujte passkey v torrentu, snímku obrazovky ani logu. Zakázaný nebo neověřitelný klient může tracker odmítnout. Výjimky a aktuální klientská politika jsou na stránce Doporučené klienty.

[b]6. Moderace[/b]
Správa může kvůli ochraně komunity obsah upravit, skrýt nebo odstranit a omezit účet při porušení pravidel. Při nejasnosti se nejdřív zeptejte ve fóru nebo založte ticket.
RULES,
            ],
            [
                'id'      => 2,
                'name'    => 'FAQ',
                'content' => <<<'FAQ'
[b]Často kladené otázky[/b]

[b]Jak začít?[/b]
Po přihlášení si zabezpečte účet, přečtěte pravidla, vytvořte API token jen pokud jej opravdu potřebujete a nastavte podporovaného klienta. Passkey z torrentu je tajný údaj.

[b]Proč tracker odmítl announce?[/b]
Zkontrolujte, že používáte torrent stažený ze svého účtu, aktivní účet a povoleného klienta. Neopakujte announce rychle dokola; přečtěte si chybovou zprávu a ověřte klientskou konfiguraci.

[b]Proč nenajdu torrent ve vyhledávání?[/b]
Použijte kratší nebo přesnější dotaz a filtry. Po novém nahrání či obnově indexu se výsledek může zobrazit se zpožděním. Nezakládejte duplicitní torrent jen proto, že výsledek právě nevidíte.

[b]Jak požádat o reseed nebo obsah?[/b]
Použijte požadavky na reseed nebo sekci Požadavky. Uveďte konkrétní titul či vydání; neposílejte hromadné soukromé zprávy uploaderům.

[b]Kde získám pomoc?[/b]
Pro obecné dotazy použijte fórum Vítejte a obecná diskuze. Citlivé věci — účet, passkey, moderace nebo bezpečnost — řešte ticketem, nikdy veřejně.

[b]Co dělat při úniku passkey nebo API tokenu?[/b]
Okamžitě jej obnovte v nastavení účtu, odstraňte starý údaj ze všech klientů a automatizací a ověřte historii připojení.
FAQ,
            ],
            [
                'id'      => 3,
                'name'    => 'Doporučené klienty',
                'content' => <<<'CLIENTS'
[b]Doporučené BitTorrent klienty[/b]

[b]První volba: qBittorrent[/b]
Doporučujeme vždy aktuální stabilní vydání qBittorrentu z [url=https://www.qbittorrent.org/download]oficiální stránky projektu[/url] nebo z oficiálního repozitáře distribuce. Nepoužívejte release candidate, beta ani neověřený upravený build. V klientovi ponechte soukromý torrent soukromý, nevyměňujte jeho tracker URL a nevystavujte passkey v logu.

[b]µTorrent[/b]
µTorrent Classic je zakázán s jedinou trackerovou legacy výjimkou pro řadu 2.2.1. Tracker tuto řadu rozpoznává prefixem peer ID [code]-UT2210-[/code]; výjimka neznamená podporu upravených nebo podvodných buildů. µTorrent Web je zakázán. Pro nový nebo upravovaný počítač použijte qBittorrent.

[b]Zakázané chování klienta[/b]
Zakázán je každý klient či doplněk, který falšuje upload nebo download, obchází announce intervaly, předstírá seeding, mění peer ID za účelem obcházení pravidel, aktivně leechuje bez sdílení nebo rozesílá škodlivý obsah. BitThief je výslovně zakázán. Tracker blokuje známé rozpoznatelné prefixy; peer ID ale není důkaz původu klienta, proto je rozhodující i chování účtu.

[b]Nastavení před připojením[/b]
Stáhněte si torrent z vlastního účtu, nepřepisujte announce URL, nenastavujte sdílený torrent soubor jinému členovi a nepropojujte jeden účet přes cizí seedbox. Při problému přiložte do ticketu text chyby bez passkey a API tokenu.
CLIENTS,
            ],
            [
                'id'      => 4,
                'name'    => 'Průvodce nahráním',
                'content' => <<<'UPLOAD'
[b]Nahrání torrentu[/b]

[b]1. Nejdřív hledejte[/b]
Vyhledejte titul, alternativní název i stejné vydání. Duplicitní torrent nenahrávejte; chybějící variantu nejdřív ověřte v komunitě.

[b]2. Připravte úplné údaje[/b]
Použijte srozumitelný název vydání, správnou kategorii, typ, rozlišení a zdroj. Přidejte věcný popis, MediaInfo tam, kde dává smysl, a snímky obrazovky bez osobních údajů či trackerových tajemství.

[b]3. Zkontrolujte technický stav[/b]
Ověřte soubory, jejich strukturu a že torrent neobsahuje malware, cizí passkey ani omylem přibalené soukromé soubory. Než formulář odešlete, projděte název a metadata ještě jednou.

[b]4. Nastavte publikaci[/b]
Anonymní nahrání skryje vaše uživatelské jméno. Vlastní vydání označte jen tehdy, když jej skutečně vydáváte vy. Důvěryhodný uploader může dobrovolně poslat torrent do moderační fronty. Interní, refund a freeleech jsou správcovské volby; bez oprávnění je neuvidíte.

[b]5. Seedujte[/b]
Po nahrání nechte klient běžet, aby první stahující měli kompletní zdroj. Pokud potřebujete soubory přesunout, přesuňte je až po zastavení klienta a cestu nastavte znovu bez změny torrentu.

[b]6. Reakce na připomínku[/b]
Když moderátor požádá o opravu, reagujte včas a věcně. Nejasnosti řešte ticketem; veřejná hádka v komentáři problém nevyřeší.
UPLOAD,
            ],
            [
                'id'      => 5,
                'name'    => 'Kódy trackeru',
                'content' => <<<'CODES'
[b]Odpovědi trackeru a postup při chybě[/b]

[b]401 / neautorizováno[/b]
Ověřte aktivní účet, osobní API token nebo passkey. Nestahujte cizí torrent a neposílejte tajný údaj do ticketu.

[b]429 / příliš mnoho požadavků[/b]
Zpomalte klienta, indexer nebo vlastní skript. Neopakujte stejný požadavek ve smyčce; počkejte a upravte interval.

[b]Zakázaný klient[/b]
Tracker odmítne rozpoznaný zakázaný prefix nebo nevyhovující chování. Přepněte na aktuální stabilní qBittorrent a znovu přidejte torrent stažený ze svého účtu.

[b]Chybí peer nebo seed[/b]
Zkontrolujte síťovou dostupnost klienta, stav torrentu a zda běží správná instance klienta. Nezaměňujte stav seedu za výsledek vyhledávání.

[b]Kdy založit ticket[/b]
Pokud chyba trvá, přiložte čas, stránku či akci, přesné znění chyby a verzi klienta. Nikdy nepřikládejte passkey, API token, cookies ani úplné announce URL.
CODES,
            ],
            [
                'id'      => 6,
                'name'    => 'Podmínky užití',
                'content' => <<<'TERMS'
[b]Podmínky užití Vltavy[/b]

Používáním Vltavy potvrzujete, že dodržíte pravidla komunity, platné právo a podmínky své skupiny. Za bezpečnost účtu, obsah, který poskytujete, a způsob používání klienta odpovídáte vy.

Služba a její obsah mohou být bez předchozího upozornění upraveny, omezeny nebo nedostupné. Správa může při ochraně komunity vyšetřit porušení pravidel, odstranit závadný obsah, zneplatnit přístupové údaje nebo účet omezit. Pro spory a citlivé záležitosti použijte ticket.

Tyto podmínky nejsou náhradou za právní poradenství. Pokud s nimi nebo s pravidly nesouhlasíte, tracker nepoužívejte.
TERMS,
            ],
            [
                'id'      => 7,
                'name'    => 'API dokumentace',
                'content' => <<<'API_DOC'
[b]API a Torznab[/b]

[b]Osobní token[/b]
API používá osobní API token uživatele. Vytvořte jej jen pro službu, které důvěřujete, a nikdy jej neposílejte do fóra, chatu, snímku obrazovky ani veřejného odkazu. Při podezření na únik jej obnovte.

[b]Dostupné API zdroje[/b]
[code]GET /api/user[/code] vrací údaje přihlášeného uživatele.
[code]GET /api/torrents[/code], [code]GET /api/torrents/filter[/code] a [code]GET /api/torrents/{id}[/code] pracují s torrenty.
[code]POST /api/torrents/upload[/code] nahrává torrent.
[code]GET /api/requests/filter[/code] a [code]GET /api/requests/{id}[/code] pracují s požadavky.

[b]Torznab pro Prowlarr, Sonarr a Radarr[/b]
Použijte osobní token a endpoint [code]/torznab/api[/code]. V Prowlarr nastavte Generic Torznab s base URL [code]https://tracker.iamanro.dev/torznab[/code] a API path [code]/api[/code]. Token patří jen do pole pro API key; nevkládejte jej přímo do sdíleného URL.

[b]Šetrná automatizace[/b]
Respektujte limity odpovědí 429, používejte rozumný interval a nevytvářejte paralelní smyčky stejného hledání. Po obnově databáze musí správce znovu synchronizovat vyhledávací index.
API_DOC,
            ],
            [
                'id'      => 8,
                'name'    => 'O Vltavě',
                'content' => <<<'ABOUT'
[b]O Vltavě[/b]

Vltava je český soukromý BitTorrent tracker. Stojí na třech věcech: kvalitně popsaném obsahu, férovém sdílení a komunitě, která si pomáhá bez zbytečného hluku.

[b]Co od nás čekat[/b]
Přehledný katalog, komunitní fórum, wiki, požadavky, osobní torrentové odkazy a rozhraní pro vlastní automatizaci. Nejsou to důvody k obcházení pravidel: soukromí, bezpečnost účtu a dlouhodobý seeding mají přednost.

[b]Jak přispět[/b]
Seedujte, doplňujte přesná metadata, nahlašujte chyby a pomáhejte novým členům ve fóru. Máte-li návrh na změnu, napište jej věcně do komunity; problém s účtem či bezpečností patří do ticketu.
ABOUT,
            ],
        ], ['id'], ['name', 'content', 'updated_at' => DB::raw('updated_at')]);

        WikiCategory::upsert([
            ['id' => 1, 'name' => 'Průvodce trackerem', 'icon' => 'fa-book', 'position' => 1],
        ], ['id'], ['name', 'icon', 'position']);

        BlacklistClient::upsert([
            [
                'name'           => 'µTorrent Classic mimo legacy 2.2.1',
                'reason'         => 'Povolena je pouze trackerová legacy výjimka odpovídající peer ID -UT2210-.',
                'peer_id_prefix' => '-UT',
            ],
            [
                'name'           => 'µTorrent Web',
                'reason'         => 'µTorrent Web není na Vltavě podporován.',
                'peer_id_prefix' => '-UW',
            ],
        ], ['name'], ['reason', 'peer_id_prefix', 'updated_at' => DB::raw('updated_at')]);

        cache()->forget('cached-pages');
        cache()->forget('client_blacklist');

        foreach ([
            [
                'name'    => 'Začínáme',
                'content' => <<<'START'
[b]Začínáme na Vltavě[/b]

1. Přečtěte pravidla a podmínky užití.
2. Nastavte aktuální stabilní qBittorrent a stáhněte torrent výhradně ze svého účtu.
3. Chraňte heslo, passkey a API token jako heslo k účtu.
4. Po dokončení torrent nenechávejte hned zmizet: seeding je základ soukromého trackeru.
5. Nevíte-li si rady, hledejte ve wiki a pak se zeptejte ve fóru; citlivé věci řešte ticketem.
START,
            ],
            [
                'name'    => 'Vyhledávání a filtry',
                'content' => <<<'SEARCH'
[b]Vyhledávání a filtry[/b]

Globální vyhledávání najde filmy, seriály a osoby. Na stránce Torrentů začněte názvem a pak výsledek zpřesněte kategorií, typem, rozlišením, zdrojem, jazykem nebo stavem seedu.

Proveďte více variant dotazu: originální název, lokální název a rok. Před nahráním vždy hledejte; podobný název nemusí znamenat stejné vydání, ale duplicitní vydání nepatří na tracker.

Výsledek nově nahraného torrentu se může po indexaci objevit se zpožděním. Neobcházejte to opakovaným hledáním ve smyčce ani zakládáním duplikátu.
SEARCH,
            ],
            [
                'name'    => 'Bezpečnost účtu',
                'content' => <<<'SECURITY'
[b]Bezpečnost účtu[/b]

Používejte jedinečné dlouhé heslo a zapněte dvoufázové ověření, pokud je k dispozici. Nikomu neposílejte heslo, passkey, API token, RSS klíč ani soubor torrentu, který obsahuje osobní announce URL.

API token vytvořte samostatně pro každou důvěryhodnou automatizaci. Při ztrátě zařízení, úniku snímku obrazovky nebo podezřelém announce token ihned obnovte, starou integraci odpojte a změňte heslo.

Správa po vás nikdy nebude chtít tajný údaj v soukromé zprávě. Do ticketu patří čas, přesná chyba a verze klienta; tajné hodnoty z logu vždy vymažte.
SECURITY,
            ],
            [
                'name'    => 'Klienti a announce',
                'content' => <<<'ANNOUNCE'
[b]Klienti a announce[/b]

Vltava doporučuje aktuální stabilní qBittorrent z oficiálního zdroje. µTorrent Classic je povolen jen v trackerové legacy řadě 2.2.1; µTorrent Web a ostatní µTorrent Classic verze tracker odmítá.

Trackerový announce odkaz je osobní. Neměňte jej, nepředávejte jinému účtu a nevkládejte jej do veřejného logu. Pokud announce selže, přečtěte chybovou zprávu, zkontrolujte klienta a počkejte; rychlé opakování problém pouze zhorší.

Peer ID může označit rodinu klienta, není však důkazem, že je klient bezpečný nebo nemodifikovaný. Cheaty, falešné statistiky a obcházení intervalů jsou zakázané bez ohledu na zobrazený název klienta.
ANNOUNCE,
            ],
        ] as $wiki) {
            Wiki::updateOrCreate(
                ['name' => $wiki['name'], 'category_id' => 1],
                ['content' => $wiki['content']],
            );
        }
    }
}
