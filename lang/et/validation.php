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
    'accepted'       => ':attribute tuleb aktsepteerida.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute ei ole kehtiv URL.',
    'after'          => ':attribute peab olema kuupäev pärast :date.',
    'after_or_equal' => ':attribute peab olema kuupäev pärast või samastuma :date.',
    'alpha'          => ':attribute võib sisaldada vaid tähemärke.',
    'alpha_dash'     => ':attribute võib sisaldada vaid tähti, numbreid ja kriipse.',
    'alpha_num'      => ':attribute võib sisaldada vaid tähti ja numbreid.',
    'array'          => ':attribute peab olema massiiv.',
    'attributes'     => [
    ],
    'before'          => ':attribute peab olema kuupäev enne :date.',
    'before_or_equal' => ':attribute peab olema kuupäev enne või samastuma :date.',
    'between'         => [
        'array'   => ':attribute peab olema :min ja :max kirje vahel.',
        'file'    => ':attribute peab olema :min ja :max kilobaidi vahel.',
        'numeric' => ':attribute peab olema :min ja :max vahel.',
        'string'  => ':attribute peab olema :min ja :max tähemärgi vahel.'
    ],
    'boolean'          => ':attribute väli peab olema tõene või väär.',
    'confirmed'        => ':attribute kinnitus ei vasta.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'kohandatud-teade'
        ]
    ],
    'date'           => ':attribute pole kehtiv kuupäev.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ':attribute ei vasta formaadile :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ':attribute ja :other peavad olema erinevad.',
    'digits'         => ':attribute peab olema :digits numbrit.',
    'digits_between' => ':attribute peab olema :min ja :max numbri vahel.',
    'dimensions'     => ':attribute on valed pildi suurused.',
    'distinct'       => ':attribute väljal on topeltväärtus.',
    'email'          => ':attribute peab olema kehtiv e-posti aadress.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Valitud :attribute on vigane.',
    'file'           => ':attribute peab olema fail.',
    'filled'         => ':attribute väli on nõutav.',
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
    'image'    => ':attribute peab olema pilt.',
    'in'       => 'Valitud :attribute on vigane.',
    'in_array' => ':attribute väli ei eksisteeri :other sees.',
    'integer'  => ':attribute peab olema täisarv.',
    'ip'       => ':attribute peab olema kehtiv IP aadress.',
    'ipv4'     => ':attribute peab olema kehtiv IPv4 aadress.',
    'ipv6'     => ':attribute peab olema kehtiv IPv6 aadress.',
    'json'     => ':attribute peab olema kehtiv JSON string.',
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
        'array'   => ':attribute ei tohi sisaldada rohkem kui :max kirjet.',
        'file'    => ':attribute ei tohi olla suurem kui :max kilobaiti.',
        'numeric' => ':attribute ei tohi olla suurem kui :max.',
        'string'  => ':attribute ei tohi olla suurem kui :max tähemärki.'
    ],
    'mimes'     => ':attribute peab olema :values tüüpi.',
    'mimetypes' => ':attribute peab olema :values tüüpi.',
    'min'       => [
        'array'   => ':attribute peab olema vähemalt :min kirjet.',
        'file'    => ':attribute peab olema vähemalt :min kilobaiti.',
        'numeric' => ':attribute peab olema vähemalt :min.',
        'string'  => ':attribute peab olema vähemalt :min tähemärki.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Valitud :attribute on vigane.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => ':attribute peab olema number.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ':attribute väli peab olema esindatud.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ':attribute vorming on vigane.',
    'required'             => ':attribute väli on nõutud.',
    'required_if'          => ':attribute väli on nõutud, kui :other on :value.',
    'required_unless'      => ':attribute väli on nõutud, välja arvatud, kui :other on :values.',
    'required_with'        => ':attribute väli on nõutud, kui :values on esindatud.',
    'required_with_all'    => ':attribute väli on nõutud, kui :values on esindatud.',
    'required_without'     => ':attribute väli on nõutud, kui :values ei ole esindatud.',
    'required_without_all' => ':attribute väli on nõutud, kui ükski :values pole esindatud.',
    'same'                 => ':attribute ja :other peavad sobima.',
    'size'                 => [
        'array'   => ':attribute peab sisaldama :size kirjet.',
        'file'    => ':attribute peab olema :size kilobaiti.',
        'numeric' => ':attribute peab olema :size.',
        'string'  => ':attribute peab olema :size tähemärki.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ':attribute peab olema string.',
    'timezone'    => ':attribute peab olema kehtiv tsoon.',
    'unique'      => ':attribute on juba hõivatud.',
    'uploaded'    => ':attribute ei õnnestunud laadida.',
    'url'         => ':attribute vorming on vigane.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
