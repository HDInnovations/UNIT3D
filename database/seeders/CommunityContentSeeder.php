<?php

declare(strict_types=1);

namespace Database\Seeders;

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
            ['id' => 1, 'name' => 'Pravidla', 'content' => "[b]Pravidla komunity[/b]\n\n1. Respektujte ostatní členy a jejich soukromí.\n2. Sdílejte jen obsah, k němuž máte oprávnění.\n3. Udržujte torrent aktivní podle pravidel své skupiny.\n4. Neobcházejte limity, ochrany trackeru ani pravidla klientů.\n5. Pro pomoc použijte fórum nebo kontaktujte správu."],
            ['id' => 2, 'name' => 'FAQ', 'content' => "[b]Často kladené otázky[/b]\n\n[b]Jak začít?[/b]\nDoplňte profil, přečtěte pravidla a sledujte poměr sdílení.\n\n[b]Kde získám pomoc?[/b]\nPoužijte fórum Vítejte a obecná diskuze nebo kontaktujte správu.\n\n[b]Proč se torrent nenajde?[/b]\nVyhledávání pracuje nad indexem trackeru; po nahrání může synchronizace chvíli trvat."],
            ['id' => 3, 'name' => 'Doporučené klienty', 'content' => "[b]Doporučené BitTorrent klienty[/b]\n\nPoužívejte aktuální stabilní verze klientů podporujících HTTPS announce a soukromé trackery. Nastavení klienta musí respektovat seznam zakázaných klientů a pravidla trackeru."],
            ['id' => 4, 'name' => 'Průvodce nahráním', 'content' => "[b]Nahrání torrentu[/b]\n\n1. Zkontrolujte, zda obsah již na trackeru není.\n2. Připravte přesný název, popis, MediaInfo a snímek obrazovky.\n3. Vyberte správnou kategorii, typ a rozlišení.\n4. Po nahrání zůstaňte seedovat."],
            ['id' => 5, 'name' => 'Kódy trackeru', 'content' => "[b]Odpovědi trackeru[/b]\n\n[b]200[/b] — announce byl přijat.\n[b]401[/b] — ověřte passkey, účet a oprávnění.\n[b]429[/b] — zpomalte požadavky klienta nebo API.\n\nChybovou odpověď vždy řešte bez opakovaného rychlého zkoušení."],
            ['id' => 7, 'name' => 'API dokumentace', 'content' => "[b]API a Torznab[/b]\n\nTracker API používá osobní API token uživatele. Token nikdy nesdílejte ani nevkládejte do veřejných odkazů.\n\n[b]Torznab[/b]\nPro Prowlarr, Sonarr nebo Radarr použijte endpoint [code]/torznab/api[/code] a vlastní API token.\n\n[b]Hledání[/b]\nVyhledávání torrentů a quick search indexuje Meilisearch. Po obnově databáze správce provede úplnou synchronizaci indexu."],
        ], ['id'], ['name', 'content', 'updated_at' => DB::raw('updated_at')]);

        WikiCategory::upsert([
            ['id' => 1, 'name' => 'Průvodce trackerem', 'icon' => 'fa-book', 'position' => 1],
        ], ['id'], ['name', 'icon', 'position']);

        foreach ([
            ['name' => 'Začínáme', 'content' => "[b]Začínáme[/b]\n\nPřečtěte pravidla, nastavte podporovaný klient a udržujte aktivní seeding. Potřebujete-li pomoc, napište do komunitního fóra."],
            ['name' => 'Vyhledávání a filtry', 'content' => "[b]Vyhledávání[/b]\n\nGlobální vyhledávání najde filmy, seriály a osoby. Na stránce torrentů lze výsledek zpřesnit kategorií, typem, rozlišením a dalšími filtry."],
            ['name' => 'Bezpečnost účtu', 'content' => "[b]Bezpečnost účtu[/b]\n\nChraňte heslo, passkey a API token. Nepoužívejte stejný token v cizích službách a při podezření na únik token okamžitě obnovte."],
        ] as $wiki) {
            Wiki::updateOrCreate(
                ['name' => $wiki['name'], 'category_id' => 1],
                ['content' => $wiki['content']],
            );
        }
    }
}
