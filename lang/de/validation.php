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
    'accepted'       => ':attribute muss akzeptiert werden.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute ist keine gültige URL.',
    'after'          => ':attribute muss ein Datum nach dem :date sein.',
    'after_or_equal' => ':attribute muss ein Datum nach dem :date oder gleich dem :date sein.',
    'alpha'          => ':attribute darf nur aus Buchstaben bestehen.',
    'alpha_dash'     => ':attribute darf nur aus Buchstaben, Zahlen, Binde- und Unterstrichen bestehen.',
    'alpha_num'      => ':attribute darf nur aus Buchstaben und Zahlen bestehen.',
    'array'          => ':attribute muss ein Array sein.',
    'attributes'     => [
    ],
    'before'          => ':attribute muss ein Datum vor dem :date sein.',
    'before_or_equal' => ':attribute muss ein Datum vor dem :date oder gleich dem :date sein.',
    'between'         => [
        'array'   => ':attribute muss zwischen :min & :max Elemente haben.',
        'file'    => ':attribute muss zwischen :min & :max Kilobytes groß sein.',
        'numeric' => ':attribute muss zwischen :min & :max liegen.',
        'string'  => ':attribute muss zwischen :min & :max Zeichen lang sein.'
    ],
    'boolean'          => ':attribute muss entweder \'true\' oder \'false\' sein.',
    'confirmed'        => ':attribute stimmt nicht mit der Bestätigung überein.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ':attribute muss ein gültiges Datum sein.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute entspricht nicht dem gültigen Format für :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute und :other müssen sich unterscheiden.',
    'digits'         => ':attribute muss :digits Stellen haben.',
    'digits_between' => ':attribute muss zwischen :min und :max Stellen haben.',
    'dimensions'     => ':attribute hat ungültige Bildabmessungen.',
    'distinct'       => ':attribute beinhaltet einen bereits vorhandenen Wert.',
    'email'          => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'email_list'     => 'Es tut uns leid, diese E-Mail-Provider darf auf dieser Website nicht verwendet werden. Bitte sehe dir die E-Mail-Whitelist der Webseite an.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Der gewählte Wert für :attribute ist ungültig.',
    'file'           => ':attribute muss eine Datei sein.',
    'filled'         => ':attribute muss ausgefüllt sein.',
    'gt'             => [
        'array'   => ':attribute muss mindestens :value Elemente haben.',
        'file'    => ':attribute muss mindestens :value Kilobytes groß sein.',
        'numeric' => ':attribute muss mindestens :value sein.',
        'string'  => ':attribute muss mindestens :value Zeichen lang sein.'
    ],
    'gte' => [
        'array'   => ':attribute muss größer oder gleich :value Elemente haben.',
        'file'    => ':attribute muss größer oder gleich :value Kilobytes sein.',
        'numeric' => ':attribute muss größer oder gleich :value sein.',
        'string'  => ':attribute muss größer oder gleich :value Zeichen lang sein.'
    ],
    'image'    => ':attribute muss ein Bild sein.',
    'in'       => 'Der gewählte Wert für :attribute ist ungültig.',
    'in_array' => 'Der gewählte Wert für :attribute kommt nicht in :other vor.',
    'integer'  => ':attribute muss eine ganze Zahl sein.',
    'ip'       => ':attribute muss eine gültige IP-Adresse sein.',
    'ipv4'     => ':attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6'     => ':attribute muss eine gültige IPv6-Adresse sein.',
    'json'     => ':attribute muss ein gültiger JSON-String sein.',
    'lt'       => [
        'array'   => ':attribute muss kleiner :value Elemente haben.',
        'file'    => ':attribute muss kleiner :value Kilobytes groß sein.',
        'numeric' => ':attribute muss kleiner :value sein.',
        'string'  => ':attribute muss kleiner :value Zeichen lang sein.'
    ],
    'lte' => [
        'array'   => ':attribute muss kleiner oder gleich :value Elemente haben.',
        'file'    => ':attribute muss kleiner oder gleich :value Kilobytes sein.',
        'numeric' => ':attribute muss kleiner oder gleich :value sein.',
        'string'  => ':attribute muss kleiner oder gleich :value Zeichen lang sein.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => ':attribute darf nicht mehr als :max Elemente haben.',
        'file'    => ':attribute darf maximal :max Kilobytes groß sein.',
        'numeric' => ':attribute darf maximal :max sein.',
        'string'  => ':attribute darf maximal :max Zeichen haben.'
    ],
    'mimes'     => ':attribute muss den Dateityp :values haben.',
    'mimetypes' => ':attribute muss den Dateityp :values haben.',
    'min'       => [
        'array'   => ':attribute muss mindestens :min Elemente haben.',
        'file'    => ':attribute muss mindestens :min Kilobytes groß sein.',
        'numeric' => ':attribute muss mindestens :min sein.',
        'string'  => ':attribute muss mindestens :min Zeichen lang sein.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Der gewählte Wert für :attribute ist ungültig.',
    'not_regex'   => ':attribute hat ein ungültiges Format.',
    'numeric'     => ':attribute muss eine Zahl sein.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute muss vorhanden sein.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Bitte absolvieren den ReCaptcha.',
    'regex'                => ':attribute Format ist ungültig.',
    'required'             => ':attribute muss ausgefüllt sein.',
    'required_if'          => ':attribute muss ausgefüllt sein, wenn :other :value ist.',
    'required_unless'      => ':attribute muss ausgefüllt sein, wenn :other nicht :values ist.',
    'required_with'        => ':attribute muss angegeben werden, wenn :values ausgefüllt wurde.',
    'required_with_all'    => ':attribute muss angegeben werden, wenn :values ausgefüllt wurde.',
    'required_without'     => ':attribute muss angegeben werden, wenn :values nicht ausgefüllt wurde.',
    'required_without_all' => ':attribute muss angegeben werden, wenn keines der Felder :values ausgefüllt wurde.',
    'same'                 => ':attribute und :other müssen übereinstimmen.',
    'size'                 => [
        'array'   => ':attribute muss genau :size Elemente haben.',
        'file'    => ':attribute muss :size Kilobyte groß sein.',
        'numeric' => ':attribute muss gleich :size sein.',
        'string'  => ':attribute muss :size Zeichen lang sein.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute muss ein String sein.',
    'timezone'    => ':attribute muss eine gültige Zeitzone sein.',
    'unique'      => ':attribute ist schon vergeben.',
    'uploaded'    => ':attribute konnte nicht hochgeladen werden.',
    'url'         => ':attribute muss eine URL sein.',
    'uuid'        => ':attribute muss ein UUID sein.'
];
