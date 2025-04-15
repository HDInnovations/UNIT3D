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
    'accepted'       => ':attribute moet geaccepteerd zijn.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute is geen geldige URL.',
    'after'          => ':attribute moet een datum na :date zijn.',
    'after_or_equal' => ':attribute moet een datum na of gelijk aan :date zijn.',
    'alpha'          => ':attribute mag alleen letters bevatten.',
    'alpha_dash'     => ':attribute mag alleen letters, nummers, underscores (_) en streepjes (-) bevatten.',
    'alpha_num'      => ':attribute mag alleen letters en nummers bevatten.',
    'array'          => ':attribute moet geselecteerde elementen bevatten.',
    'attributes'     => [
    ],
    'before'          => ':attribute moet een datum voor :date zijn.',
    'before_or_equal' => ':attribute moet een datum voor of gelijk aan :date zijn.',
    'between'         => [
        'array'   => ':attribute moet tussen :min en :max items bevatten.',
        'file'    => ':attribute moet tussen :min en :max kilobytes zijn.',
        'numeric' => ':attribute moet tussen :min en :max zijn.',
        'string'  => ':attribute moet tussen :min en :max karakters zijn.'
    ],
    'boolean'          => ':attribute moet ja of nee zijn.',
    'confirmed'        => ':attribute bevestiging komt niet overeen.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute moet een datum bevatten.',
    'date_equals'    => ':attribute moet een datum gelijk aan :date zijn.',
    'date_format'    => ':attribute moet een geldig datum formaat bevatten.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute en :other moeten verschillend zijn.',
    'digits'         => ':attribute moet bestaan uit :digits cijfers.',
    'digits_between' => ':attribute moet bestaan uit minimaal :min en maximaal :max cijfers.',
    'dimensions'     => ':attribute heeft geen geldige afmetingen voor afbeeldingen.',
    'distinct'       => ':attribute heeft een dubbele waarde.',
    'email'          => ':attribute is geen geldig e-mailadres.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => ':attribute bestaat niet.',
    'file'           => ':attribute moet een bestand zijn.',
    'filled'         => ':attribute is verplicht.',
    'gt'             => [
        'array'   => 'De :attribute moet meer dan :value waardes bevatten.',
        'file'    => 'De :attribute moet groter zijn dan :value kilobytes.',
        'numeric' => 'De :attribute moet groter zijn dan :value.',
        'string'  => 'De :attribute moet meer dan :value tekens bevatten.'
    ],
    'gte' => [
        'array'   => 'De :attribute moet :value waardes of meer bevatten.',
        'file'    => 'De :attribute moet groter of gelijk zijn aan :value kilobytes.',
        'numeric' => 'De :attribute moet groter of gelijk zijn aan :value.',
        'string'  => 'De :attribute moet minimaal :value tekens bevatten.'
    ],
    'image'    => ':attribute moet een afbeelding zijn.',
    'in'       => ':attribute is ongeldig.',
    'in_array' => ':attribute bestaat niet in :other.',
    'integer'  => ':attribute moet een getal zijn.',
    'ip'       => ':attribute moet een geldig IP-adres zijn.',
    'ipv4'     => ':attribute moet een geldig IPv4-adres zijn.',
    'ipv6'     => ':attribute moet een geldig IPv6-adres zijn.',
    'json'     => ':attribute moet een geldige JSON-string zijn.',
    'lt'       => [
        'array'   => 'De :attribute moet minder dan :value waardes bevatten.',
        'file'    => 'De :attribute moet kleiner zijn dan :value kilobytes.',
        'numeric' => 'De :attribute moet kleiner zijn dan :value.',
        'string'  => 'De :attribute moet minder dan :value tekens bevatten.'
    ],
    'lte' => [
        'array'   => 'De :attribute moet :value waardes of minder bevatten.',
        'file'    => 'De :attribute moet kleiner of gelijk zijn aan :value kilobytes.',
        'numeric' => 'De :attribute moet kleiner of gelijk zijn aan :value.',
        'string'  => 'De :attribute moet maximaal :value tekens bevatten.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute mag niet meer dan :max items bevatten.',
        'file'    => ':attribute mag niet meer dan :max kilobytes zijn.',
        'numeric' => ':attribute mag niet hoger dan :max zijn.',
        'string'  => ':attribute mag niet uit meer dan :max tekens bestaan.'
    ],
    'mimes'     => ':attribute moet een bestand zijn van het bestandstype :values.',
    'mimetypes' => ':attribute moet een bestand zijn van het bestandstype :values.',
    'min'       => [
        'array'   => ':attribute moet minimaal :min items bevatten.',
        'file'    => ':attribute moet minimaal :min kilobytes zijn.',
        'numeric' => ':attribute moet minimaal :min zijn.',
        'string'  => ':attribute moet minimaal :min tekens zijn.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Het formaat van :attribute is ongeldig.',
    'not_regex'   => 'De :attribute formaat is ongeldig.',
    'numeric'     => ':attribute moet een nummer zijn.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute moet bestaan.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ':attribute formaat is ongeldig.',
    'required'             => ':attribute is verplicht.',
    'required_if'          => ':attribute is verplicht indien :other gelijk is aan :value.',
    'required_unless'      => ':attribute is verplicht tenzij :other gelijk is aan :values.',
    'required_with'        => ':attribute is verplicht i.c.m. :values',
    'required_with_all'    => ':attribute is verplicht i.c.m. :values',
    'required_without'     => ':attribute is verplicht als :values niet ingevuld is.',
    'required_without_all' => ':attribute is verplicht als :values niet ingevuld zijn.',
    'same'                 => ':attribute en :other moeten overeenkomen.',
    'size'                 => [
        'array'   => ':attribute moet :size items bevatten.',
        'file'    => ':attribute moet :size kilobyte zijn.',
        'numeric' => ':attribute moet :size zijn.',
        'string'  => ':attribute moet :size tekens zijn.'
    ],
    'starts_with' => ':attribute moet starten met een van de volgende: :values',
    'string'      => ':attribute moet een tekst zijn.',
    'timezone'    => ':attribute moet een geldige tijdzone zijn.',
    'unique'      => ':attribute is al in gebruik.',
    'uploaded'    => 'Het uploaden van :attribute is mislukt.',
    'url'         => ':attribute moet een geldig URL zijn.',
    'uuid'        => ':attribute moet een geldig UUID zijn.'
];
