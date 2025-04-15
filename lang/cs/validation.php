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

return [
    'accepted'       => ':attribute musí být přijat.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute není platnou URL adresou.',
    'after'          => ':attribute musí být datum po :date.',
    'after_or_equal' => ':attribute musí být datum :date nebo pozdější.',
    'alpha'          => ':attribute může obsahovat pouze písmena.',
    'alpha_dash'     => ':attribute může obsahovat pouze písmena, číslice, pomlčky a podtržítka. České znaky (á, é, í, ó, ú, ů, ž, š, č, ř, ď, ť, ň) nejsou podporovány.',
    'alpha_num'      => ':attribute může obsahovat pouze písmena a číslice.',
    'array'          => ':attribute musí být pole.',
    'attributes'     => [
    ],
    'before'          => ':attribute musí být datum před :date.',
    'before_or_equal' => 'Datum :attribute musí být před nebo rovno :date.',
    'between'         => [
        'array'   => ':attribute musí obsahovat nejméně :min a nesmí obsahovat více než :max prvků.',
        'file'    => ':attribute musí být větší než :min a menší než :max Kilobytů.',
        'numeric' => ':attribute musí být hodnota mezi :min a :max.',
        'string'  => ':attribute musí být delší než :min a kratší než :max znaků.'
    ],
    'boolean'          => ':attribute musí být true nebo false',
    'confirmed'        => ':attribute nesouhlasí.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute musí být platné datum.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute není platný formát data podle :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute a :other se musí lišit.',
    'digits'         => ':attribute musí být :digits pozic dlouhé.',
    'digits_between' => ':attribute musí být dlouhé nejméně :min a nejvíce :max pozic.',
    'dimensions'     => ':attribute má neplatné rozměry.',
    'distinct'       => ':attribute má duplicitní hodnotu.',
    'email'          => ':attribute není platný formát.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Zvolená hodnota pro :attribute není platná.',
    'file'           => ':attribute musí být soubor.',
    'filled'         => ':attribute musí být vyplněno.',
    'gt'             => [
        'array'   => 'Pole :attribute musí mít více prvků než :value.',
        'file'    => 'Velikost souboru :attribute musí být větší než :value kB.',
        'numeric' => ':attribute musí být větší než :value.',
        'string'  => 'Počet znaků :attribute musí být větší :value.'
    ],
    'gte' => [
        'array'   => 'Pole :attribute musí mít :value prvků nebo více.',
        'file'    => 'Velikost souboru :attribute musí být větší nebo rovno :value kB.',
        'numeric' => ':attribute musí být větší nebo rovno :value.',
        'string'  => 'Počet znaků :attribute musí být větší nebo rovno :value.'
    ],
    'image'    => ':attribute musí být obrázek.',
    'in'       => 'Zvolená hodnota pro :attribute je neplatná.',
    'in_array' => ':attribute není obsažen v :other.',
    'integer'  => ':attribute musí být celé číslo.',
    'ip'       => ':attribute musí být platnou IP adresou.',
    'ipv4'     => ':attribute musí být platná IPv4 adresa.',
    'ipv6'     => ':attribute musí být platná IPv6 adresa.',
    'json'     => ':attribute musí být platný JSON řetězec.',
    'lt'       => [
        'array'   => ':attribute by měl obsahovat méně než :value položek.',
        'file'    => 'Velikost souboru :attribute musí být menší než :value kB.',
        'numeric' => ':attribute musí být menší než :value.',
        'string'  => ':attribute musí obsahovat méně než :value znaků.'
    ],
    'lte' => [
        'array'   => ':attribute by měl obsahovat maximálně :value položek.',
        'file'    => 'Velikost souboru :attribute musí být menší než :value kB.',
        'numeric' => ':attribute musí být menší nebo rovno než :value.',
        'string'  => ':attribute nesmí být delší než :value znaků.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute nemůže obsahovat více než :max prvků.',
        'file'    => 'Velikost souboru :attribute musí být menší než :value kB.',
        'numeric' => ':attribute nemůže být větší než :max.',
        'string'  => ':attribute nemůže být delší než :max znaků.'
    ],
    'mimes'     => ':attribute musí být jeden z následujících datových typů :values.',
    'mimetypes' => ':attribute musí být jeden z následujících datových typů :values.',
    'min'       => [
        'array'   => ':attribute musí obsahovat alespoň :min prvků.',
        'file'    => ':attribute musí mít alespoň :min kB.',
        'numeric' => ':attribute musí být alespoň :min.',
        'string'  => ':attribute musí mít délku alespoň :min znaků.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Zvolená hodnota pro :attribute je neplatná.',
    'not_regex'   => ':attribute musí být regulární výraz.',
    'numeric'     => ':attribute musí být číslo.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute musí být vyplněno.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ':attribute nemá správný formát.',
    'required'             => ':attribute musí být vyplněno.',
    'required_if'          => ':attribute musí být vyplněno pokud :other je :value.',
    'required_unless'      => ':attribute musí být vyplněno dokud :other je v :values.',
    'required_with'        => ':attribute musí být vyplněno pokud :values je vyplněno.',
    'required_with_all'    => ':attribute musí být vyplněno pokud :values je zvoleno.',
    'required_without'     => ':attribute musí být vyplněno pokud :values není vyplněno.',
    'required_without_all' => ':attribute musí být vyplněno pokud není žádné z :values zvoleno.',
    'same'                 => ':attribute a :other se musí shodovat.',
    'size'                 => [
        'array'   => ':attribute musí obsahovat právě :size prvků.',
        'file'    => ':attribute musí mít přesně :size Kilobytů.',
        'numeric' => ':attribute musí být přesně :size.',
        'string'  => ':attribute musí být přesně :size znaků dlouhý.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute musí být řetězec znaků.',
    'timezone'    => ':attribute musí být platná časová zóna.',
    'unique'      => ':attribute musí být unikátní.',
    'uploaded'    => 'Nahrávání :attribute se nezdařilo.',
    'url'         => 'Formát :attribute je neplatný.',
    'uuid'        => ':attribute musí být validní UUID.'
];
