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

use App\Models\MediaLanguage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaLanguageSeeder extends Seeder
{
    public function run(): void
    {
        MediaLanguage::upsert([
            [
                'code' => 'aa',
                'name' => 'Afar',
            ],
            [
                'code' => 'ab',
                'name' => 'Abkhazian',
            ],
            [
                'code' => 'ae',
                'name' => 'Avestan',
            ],
            [
                'code' => 'af',
                'name' => 'Afrikaans',
            ],
            [
                'code' => 'ak',
                'name' => 'Akan',
            ],
            [
                'code' => 'am',
                'name' => 'Amharic',
            ],
            [
                'code' => 'an',
                'name' => 'Aragonese',
            ],
            [
                'code' => 'ar',
                'name' => 'Arabic',
            ],
            [
                'code' => 'as',
                'name' => 'Assamese',
            ],
            [
                'code' => 'av',
                'name' => 'Avaric',
            ],
            [
                'code' => 'ay',
                'name' => 'Aymara',
            ],
            [
                'code' => 'az',
                'name' => 'Azerbaijani',
            ],
            [
                'code' => 'ba',
                'name' => 'Bashkir',
            ],
            [
                'code' => 'be',
                'name' => 'Belarusian',
            ],
            [
                'code' => 'bg',
                'name' => 'Bulgarian',
            ],
            [
                'code' => 'bi',
                'name' => 'Bislama',
            ],
            [
                'code' => 'bm',
                'name' => 'Bambara',
            ],
            [
                'code' => 'bn',
                'name' => 'Bengali',
            ],
            [
                'code' => 'bo',
                'name' => 'Tibetan',
            ],
            [
                'code' => 'br',
                'name' => 'Breton',
            ],
            [
                'code' => 'bs',
                'name' => 'Bosnian',
            ],
            [
                'code' => 'ca',
                'name' => 'Catalan',
            ],
            [
                'code' => 'ce',
                'name' => 'Chechen',
            ],
            [
                'code' => 'ch',
                'name' => 'Chamorro',
            ],
            [
                'code' => 'cn',
                'name' => 'Cantonese',
            ],
            [
                'code' => 'co',
                'name' => 'Corsican',
            ],
            [
                'code' => 'cr',
                'name' => 'Cree',
            ],
            [
                'code' => 'cs',
                'name' => 'Czech',
            ],
            [
                'code' => 'cu',
                'name' => 'Slavic',
            ],
            [
                'code' => 'cv',
                'name' => 'Chuvash',
            ],
            [
                'code' => 'cy',
                'name' => 'Welsh',
            ],
            [
                'code' => 'da',
                'name' => 'Danish',
            ],
            [
                'code' => 'de',
                'name' => 'German',
            ],
            [
                'code' => 'dv',
                'name' => 'Divehi',
            ],
            [
                'code' => 'dz',
                'name' => 'Dzongkha',
            ],
            [
                'code' => 'ee',
                'name' => 'Ewe',
            ],
            [
                'code' => 'el',
                'name' => 'Greek',
            ],
            [
                'code' => 'en',
                'name' => 'English',
            ],
            [
                'code' => 'eo',
                'name' => 'Esperanto',
            ],
            [
                'code' => 'es',
                'name' => 'Spanish',
            ],
            [
                'code' => 'et',
                'name' => 'Estonian',
            ],
            [
                'code' => 'eu',
                'name' => 'Basque',
            ],
            [
                'code' => 'fa',
                'name' => 'Persian',
            ],
            [
                'code' => 'ff',
                'name' => 'Fulah',
            ],
            [
                'code' => 'fi',
                'name' => 'Finnish',
            ],
            [
                'code' => 'fj',
                'name' => 'Fijian',
            ],
            [
                'code' => 'fo',
                'name' => 'Faroese',
            ],
            [
                'code' => 'fr',
                'name' => 'French',
            ],
            [
                'code' => 'fy',
                'name' => 'Frisian',
            ],
            [
                'code' => 'ga',
                'name' => 'Irish',
            ],
            [
                'code' => 'gd',
                'name' => 'Gaelic',
            ],
            [
                'code' => 'gl',
                'name' => 'Galician',
            ],
            [
                'code' => 'gn',
                'name' => 'Guarani',
            ],
            [
                'code' => 'gu',
                'name' => 'Gujarati',
            ],
            [
                'code' => 'gv',
                'name' => 'Manx',
            ],
            [
                'code' => 'ha',
                'name' => 'Hausa',
            ],
            [
                'code' => 'he',
                'name' => 'Hebrew',
            ],
            [
                'code' => 'hi',
                'name' => 'Hindi',
            ],
            [
                'code' => 'ho',
                'name' => 'Hiri Motu',
            ],
            [
                'code' => 'hr',
                'name' => 'Croatian',
            ],
            [
                'code' => 'ht',
                'name' => 'Haitian; Haitian Creole',
            ],
            [
                'code' => 'hu',
                'name' => 'Hungarian',
            ],
            [
                'code' => 'hy',
                'name' => 'Armenian',
            ],
            [
                'code' => 'hz',
                'name' => 'Herero',
            ],
            [
                'code' => 'ia',
                'name' => 'Interlingua',
            ],
            [
                'code' => 'id',
                'name' => 'Indonesian',
            ],
            [
                'code' => 'ie',
                'name' => 'Interlingue',
            ],
            [
                'code' => 'ig',
                'name' => 'Igbo',
            ],
            [
                'code' => 'ii',
                'name' => 'Yi',
            ],
            [
                'code' => 'ik',
                'name' => 'Inupiaq',
            ],
            [
                'code' => 'io',
                'name' => 'Ido',
            ],
            [
                'code' => 'is',
                'name' => 'Icelandic',
            ],
            [
                'code' => 'it',
                'name' => 'Italian',
            ],
            [
                'code' => 'iu',
                'name' => 'Inuktitut',
            ],
            [
                'code' => 'ja',
                'name' => 'Japanese',
            ],
            [
                'code' => 'jv',
                'name' => 'Javanese',
            ],
            [
                'code' => 'ka',
                'name' => 'Georgian',
            ],
            [
                'code' => 'kg',
                'name' => 'Kongo',
            ],
            [
                'code' => 'ki',
                'name' => 'Kikuyu',
            ],
            [
                'code' => 'kj',
                'name' => 'Kuanyama',
            ],
            [
                'code' => 'kk',
                'name' => 'Kazakh',
            ],
            [
                'code' => 'kl',
                'name' => 'Kalaallisut',
            ],
            [
                'code' => 'km',
                'name' => 'Khmer',
            ],
            [
                'code' => 'kn',
                'name' => 'Kannada',
            ],
            [
                'code' => 'ko',
                'name' => 'Korean',
            ],
            [
                'code' => 'kr',
                'name' => 'Kanuri',
            ],
            [
                'code' => 'ks',
                'name' => 'Kashmiri',
            ],
            [
                'code' => 'ku',
                'name' => 'Kurdish',
            ],
            [
                'code' => 'kv',
                'name' => 'Komi',
            ],
            [
                'code' => 'kw',
                'name' => 'Cornish',
            ],
            [
                'code' => 'ky',
                'name' => 'Kirghiz',
            ],
            [
                'code' => 'la',
                'name' => 'Latin',
            ],
            [
                'code' => 'lb',
                'name' => 'Letzeburgesch',
            ],
            [
                'code' => 'lg',
                'name' => 'Ganda',
            ],
            [
                'code' => 'li',
                'name' => 'Limburgish',
            ],
            [
                'code' => 'ln',
                'name' => 'Lingala',
            ],
            [
                'code' => 'lo',
                'name' => 'Lao',
            ],
            [
                'code' => 'lt',
                'name' => 'Lithuanian',
            ],
            [
                'code' => 'lu',
                'name' => 'Luba-Katanga',
            ],
            [
                'code' => 'lv',
                'name' => 'Latvian',
            ],
            [
                'code' => 'mg',
                'name' => 'Malagasy',
            ],
            [
                'code' => 'mh',
                'name' => 'Marshall',
            ],
            [
                'code' => 'mi',
                'name' => 'Maori',
            ],
            [
                'code' => 'mk',
                'name' => 'Macedonian',
            ],
            [
                'code' => 'ml',
                'name' => 'Malayalam',
            ],
            [
                'code' => 'mn',
                'name' => 'Mongolian',
            ],
            [
                'code' => 'mo',
                'name' => 'Moldavian',
            ],
            [
                'code' => 'mr',
                'name' => 'Marathi',
            ],
            [
                'code' => 'ms',
                'name' => 'Malay',
            ],
            [
                'code' => 'mt',
                'name' => 'Maltese',
            ],
            [
                'code' => 'my',
                'name' => 'Burmese',
            ],
            [
                'code' => 'na',
                'name' => 'Nauru',
            ],
            [
                'code' => 'nb',
                'name' => 'Norwegian Bokmål',
            ],
            [
                'code' => 'nd',
                'name' => 'Ndebele',
            ],
            [
                'code' => 'ne',
                'name' => 'Nepali',
            ],
            [
                'code' => 'ng',
                'name' => 'Ndonga',
            ],
            [
                'code' => 'nl',
                'name' => 'Dutch',
            ],
            [
                'code' => 'nn',
                'name' => 'Norwegian Nynorsk',
            ],
            [
                'code' => 'no',
                'name' => 'Norwegian',
            ],
            [
                'code' => 'nr',
                'name' => 'Ndebele',
            ],
            [
                'code' => 'nv',
                'name' => 'Navajo',
            ],
            [
                'code' => 'ny',
                'name' => 'Chichewa; Nyanja',
            ],
            [
                'code' => 'oc',
                'name' => 'Occitan',
            ],
            [
                'code' => 'oj',
                'name' => 'Ojibwa',
            ],
            [
                'code' => 'om',
                'name' => 'Oromo',
            ],
            [
                'code' => 'or',
                'name' => 'Oriya',
            ],
            [
                'code' => 'os',
                'name' => 'Ossetian; Ossetic',
            ],
            [
                'code' => 'pa',
                'name' => 'Punjabi',
            ],
            [
                'code' => 'pi',
                'name' => 'Pali',
            ],
            [
                'code' => 'pl',
                'name' => 'Polish',
            ],
            [
                'code' => 'ps',
                'name' => 'Pushto',
            ],
            [
                'code' => 'pt',
                'name' => 'Portuguese',
            ],
            [
                'code' => 'qu',
                'name' => 'Quechua',
            ],
            [
                'code' => 'rm',
                'name' => 'Raeto-Romance',
            ],
            [
                'code' => 'rn',
                'name' => 'Rundi',
            ],
            [
                'code' => 'ro',
                'name' => 'Romanian',
            ],
            [
                'code' => 'ru',
                'name' => 'Russian',
            ],
            [
                'code' => 'rw',
                'name' => 'Kinyarwanda',
            ],
            [
                'code' => 'sa',
                'name' => 'Sanskrit',
            ],
            [
                'code' => 'sc',
                'name' => 'Sardinian',
            ],
            [
                'code' => 'sd',
                'name' => 'Sindhi',
            ],
            [
                'code' => 'se',
                'name' => 'Northern Sami',
            ],
            [
                'code' => 'sg',
                'name' => 'Sango',
            ],
            [
                'code' => 'sh',
                'name' => 'Serbo-Croatian',
            ],
            [
                'code' => 'si',
                'name' => 'Sinhalese',
            ],
            [
                'code' => 'sk',
                'name' => 'Slovak',
            ],
            [
                'code' => 'sl',
                'name' => 'Slovenian',
            ],
            [
                'code' => 'sm',
                'name' => 'Samoan',
            ],
            [
                'code' => 'sn',
                'name' => 'Shona',
            ],
            [
                'code' => 'so',
                'name' => 'Somali',
            ],
            [
                'code' => 'sq',
                'name' => 'Albanian',
            ],
            [
                'code' => 'sr',
                'name' => 'Serbian',
            ],
            [
                'code' => 'ss',
                'name' => 'Swati',
            ],
            [
                'code' => 'st',
                'name' => 'Sotho',
            ],
            [
                'code' => 'su',
                'name' => 'Sundanese',
            ],
            [
                'code' => 'sv',
                'name' => 'Swedish',
            ],
            [
                'code' => 'sw',
                'name' => 'Swahili',
            ],
            [
                'code' => 'ta',
                'name' => 'Tamil',
            ],
            [
                'code' => 'te',
                'name' => 'Telugu',
            ],
            [
                'code' => 'tg',
                'name' => 'Tajik',
            ],
            [
                'code' => 'th',
                'name' => 'Thai',
            ],
            [
                'code' => 'ti',
                'name' => 'Tigrinya',
            ],
            [
                'code' => 'tk',
                'name' => 'Turkmen',
            ],
            [
                'code' => 'tl',
                'name' => 'Tagalog',
            ],
            [
                'code' => 'tn',
                'name' => 'Tswana',
            ],
            [
                'code' => 'to',
                'name' => 'Tonga',
            ],
            [
                'code' => 'tr',
                'name' => 'Turkish',
            ],
            [
                'code' => 'ts',
                'name' => 'Tsonga',
            ],
            [
                'code' => 'tt',
                'name' => 'Tatar',
            ],
            [
                'code' => 'tw',
                'name' => 'Twi',
            ],
            [
                'code' => 'ty',
                'name' => 'Tahitian',
            ],
            [
                'code' => 'ug',
                'name' => 'Uighur',
            ],
            [
                'code' => 'uk',
                'name' => 'Ukrainian',
            ],
            [
                'code' => 'ur',
                'name' => 'Urdu',
            ],
            [
                'code' => 'uz',
                'name' => 'Uzbek',
            ],
            [
                'code' => 've',
                'name' => 'Venda',
            ],
            [
                'code' => 'vi',
                'name' => 'Vietnamese',
            ],
            [
                'code' => 'vo',
                'name' => 'Volapük',
            ],
            [
                'code' => 'wa',
                'name' => 'Walloon',
            ],
            [
                'code' => 'wo',
                'name' => 'Wolof',
            ],
            [
                'code' => 'xh',
                'name' => 'Xhosa',
            ],
            [
                'code' => 'xx',
                'name' => 'No Language',
            ],
            [
                'code' => 'yi',
                'name' => 'Yiddish',
            ],
            [
                'code' => 'yo',
                'name' => 'Yoruba',
            ],
            [
                'code' => 'za',
                'name' => 'Zhuang',
            ],
            [
                'code' => 'zh',
                'name' => 'Mandarin',
            ],
            [
                'code' => 'zu',
                'name' => 'Zulu',
            ],
        ], ['id'], ['updated_at' => DB::raw('updated_at')]);
    }
}
