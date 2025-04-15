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
    'accepted'       => ' :attribute ir jābūt pieņemtam.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ' :attribute ir ar nederīgu linku.',
    'after'          => ' :attribute ir jābūt ar datumu pēc :datums.',
    'after_or_equal' => ' :attribute ir jābūt ar datumu pēc vai vienādu ar :datums.',
    'alpha'          => ' :attribute var saturēt tikai burtus.',
    'alpha_dash'     => ' :attribute var saturēt tikai burtus, nummurus un atstarpes.',
    'alpha_num'      => ' :attribute var tikai saturēt burtus un nummurus.',
    'array'          => ' :attribute ir jābūt sakārtotam.',
    'attributes'     => [
    ],
    'before'          => ' :attribute ir jābūt ar datumu pirms :datums.',
    'before_or_equal' => ' :attribute ir jābūt ar datumu pirms vai vienādu ar :datums.',
    'between'         => [
        'array'   => ' :attribute jābūt no :min līdz :max vienībām.',
        'file'    => ' :attribute jābūt starp :min un :max kilobaiti.',
        'numeric' => ' :attribute jābūt starp :min un :max.',
        'string'  => ' :attribute jābūt no :min līdz :max zīmēm.'
    ],
    'boolean'          => ' :attribute laiciņam jābūt atbilstošam vai neatbilstošam.',
    'confirmed'        => ' :attribute apstiprinājums neatbilst.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'ziņa pēc pieprasījuma'
        ]
    ],
    'date'           => ' :attribute nav derīgs.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ' :attribute neatbilst formātam :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ' :attribute un :other ir jābūt citiem.',
    'digits'         => ' :attribute ir jābūt :digits ciparam.',
    'digits_between' => ' :attribute ir jābūt :min un :max ciparam.',
    'dimensions'     => ' :attribute ir nederīgs attēla izmērs.',
    'distinct'       => ' :attribute laikam ir dubulta vērtība.',
    'email'          => ' :attribute derīgam e-pastam.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Izvēlētais :attribute ir nederīgs.',
    'file'           => ' :attribute jābūt failam.',
    'filled'         => ':attribute lauks ir nepieciešams.',
    'gt'             => [
        'array'   => 'The :attribute must have more than :value items.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'numeric' => 'The :attribute must be greater than :value.',
        'string'  => 'The :attribute must be greater than :value characters.'
    ],
    'gte' => [
        'array'   => 'The :attribute must have :value items or more.',
        'file'    => 'The :attribute must be greater than or equal :value kilobytes.',
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'string'  => 'The :attribute must be greater than or equal :value characters.'
    ],
    'image'    => ' :attribute jābūt attēlam.',
    'in'       => 'Izvēlētais :attribute ir nederīgs.',
    'in_array' => ' :attribute laiks neeksistē :cits.',
    'integer'  => ' :attribute ir jabūt skaitim.',
    'ip'       => ' :attribute jābūt derīgai IP adresei.',
    'ipv4'     => 'The :attribute must be a valid IPv4 address.',
    'ipv6'     => 'The :attribute must be a valid IPv6 address.',
    'json'     => ' :attribute jābūt derīgai JSON virknei.',
    'lt'       => [
        'array'   => 'The :attribute must have less than :value items.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'numeric' => 'The :attribute must be less than :value.',
        'string'  => 'The :attribute must be less than :value characters.'
    ],
    'lte' => [
        'array'   => 'The :attribute must not have more than :value items.',
        'file'    => 'The :attribute must be less than or equal :value kilobytes.',
        'numeric' => 'The :attribute must be less than or equal :value.',
        'string'  => 'The :attribute must be less than or equal :value characters.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ' :attribute nedrīkst pārsniegt :max vienības.',
        'file'    => ' :attribute nedrīkst pārsniegt :max kilobaiti.',
        'numeric' => ' :attribute nedrīkst pārsniegt :max.',
        'string'  => ' :attribute nedrīkst pārsniegt :max zīmes.'
    ],
    'mimes'     => ' :attribute jābūt faila tipam: :values',
    'mimetypes' => ' :attribute jābūt faile tipam: :values.',
    'min'       => [
        'array'   => ' :attribute jāsatur vismaz :min vienības.',
        'file'    => ' :attribute jābūt vismaz :min kilobaiti.',
        'numeric' => ' :attribute jābūt vismaz :min.',
        'string'  => ' :attribute jābūt vismaz :min zīmes.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => ' izvēlieties :attribute ir nederīgs.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => ' :attribute jābūt skaitlim.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ' :attribute laikums ir nepieciešams.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ' :attribute formāts ir nederīgs.',
    'required'             => ' :attribute laukums ir nepieciešams.',
    'required_if'          => ' :attribute laukums ir nepieciešams, ja vien :other ir :values.',
    'required_unless'      => ' :attribute laukums ir nepieciešams, ja vien :other ir :values.',
    'required_with'        => ' :attribute laukums ir nepieciešams, kad :values ir pieejama.',
    'required_with_all'    => ' :attribute laukums ir nepieciešams, kad :values ir pieejama.',
    'required_without'     => ' :attribute laukums ir nepieciešams, kad :values nav pieejama.',
    'required_without_all' => ' :attribute laukums ir nepieciešams, kad neviena no :values nav pieejama.',
    'same'                 => ' :attribute un :citiem ir jāsakrīt.',
    'size'                 => [
        'array'   => ' :attribute jāsatur :size vienības.',
        'file'    => ' :attribute jābūt :size kilobaiti.',
        'numeric' => ' :attribute jābūt :size.',
        'string'  => ' :attribute jābūt :size zīmes.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ' :attribute jābūt virknē.',
    'timezone'    => ' :attribute jābūt derīgā zonā.',
    'unique'      => ' :attribute jau ir aizņemts.',
    'uploaded'    => ' :attribute netika augšuplādēts.',
    'url'         => ' :attribute formāts ir nederīgs.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
