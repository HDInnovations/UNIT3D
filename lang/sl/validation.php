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
    'accepted'       => ':attribute mora biti sprejet.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute ni pravilen.',
    'after'          => ':attribute mora biti za datumom :date.',
    'after_or_equal' => ':attribute mora biti za ali enak :date.',
    'alpha'          => ':attribute lahko vsebuje samo črke.',
    'alpha_dash'     => ':attribute lahko vsebuje samo črke, številke in črtice.',
    'alpha_num'      => ':attribute lahko vsebuje samo črke in številke.',
    'array'          => ':attribute mora biti polje.',
    'attributes'     => [
    ],
    'before'          => ':attribute mora biti pred datumom :date.',
    'before_or_equal' => ':attribute mora biti pred ali enak :date.',
    'between'         => [
        'array'   => ':attribute mora imeti med :min in :max elementov.',
        'file'    => ':attribute mora biti med :min in :max kilobajti.',
        'numeric' => ':attribute mora biti med :min in :max.',
        'string'  => ':attribute mora biti med :min in :max znaki.'
    ],
    'boolean'          => ':attribute polje mora biti 1 ali 0',
    'confirmed'        => ':attribute potrditev se ne ujema.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'Prilagojeno sporočilo'
        ]
    ],
    'date'           => ':attribute ni veljaven datum.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute se ne ujema z obliko :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute in :other mora biti drugačen.',
    'digits'         => ':attribute mora imeti :digits cifer.',
    'digits_between' => ':attribute mora biti med :min in :max ciframi.',
    'dimensions'     => ':attribute ima napačne dimenzije slike.',
    'distinct'       => ':attribute je duplikat.',
    'email'          => ':attribute mora biti veljaven e-poštni naslov.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'izbran :attribute je neveljaven.',
    'file'           => ':attribute mora biti datoteka.',
    'filled'         => ':attribute mora biti izpolnjen.',
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
    'image'    => ':attribute mora biti slika.',
    'in'       => 'izbran :attribute je neveljaven.',
    'in_array' => ':attribute ne obstaja v :other.',
    'integer'  => ':attribute mora biti število.',
    'ip'       => ':attribute mora biti veljaven IP naslov.',
    'ipv4'     => ':attribute mora biti veljaven IPv4 naslov.',
    'ipv6'     => ':attribute mora biti veljaven IPv6 naslov.',
    'json'     => ':attribute mora biti veljaven JSON tekst.',
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
        'array'   => ':attribute ne smejo imeti več kot :max elementov.',
        'file'    => ':attribute ne sme biti večje :max kilobajtov.',
        'numeric' => ':attribute ne sme biti večje od :max.',
        'string'  => ':attribute ne sme biti večje :max znakov.'
    ],
    'mimes'     => ':attribute mora biti datoteka tipa: :values.',
    'mimetypes' => ':attribute mora biti datoteka tipa: :values.',
    'min'       => [
        'array'   => ':attribute mora imeti vsaj :min elementov.',
        'file'    => ':attribute mora imeti vsaj :min kilobajtov.',
        'numeric' => ':attribute mora biti vsaj dolžine :min.',
        'string'  => ':attribute mora imeti vsaj :min znakov.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'izbran :attribute je neveljaven.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => ':attribute mora biti število.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'Polje :attribute mora biti prisotno.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Format polja :attribute je neveljaven.',
    'required'             => 'Polje :attribute je obvezno.',
    'required_if'          => 'Polje :attribute je obvezno, če je :other enak :value.',
    'required_unless'      => 'Polje :attribute je obvezno, razen če je :other v :values.',
    'required_with'        => 'Polje :attribute je obvezno, če je :values prisoten.',
    'required_with_all'    => 'Polje :attribute je obvezno, če so :values prisoten.',
    'required_without'     => 'Polje :attribute je obvezno, če :values ni prisoten.',
    'required_without_all' => 'Polje :attribute je obvezno, če :values niso prisotni.',
    'same'                 => 'Polje :attribute in :other se morata ujemati.',
    'size'                 => [
        'array'   => ':attribute mora vsebovati :size elementov.',
        'file'    => ':attribute mora biti :size kilobajtov.',
        'numeric' => ':attribute mora biti :size.',
        'string'  => ':attribute mora biti :size znakov.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute mora biti tekst.',
    'timezone'    => ':attribute mora biti časovna cona.',
    'unique'      => ':attribute je že zaseden.',
    'uploaded'    => 'Nalaganje :attribute ni uspelo.',
    'url'         => ':attribute format je neveljaven.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
