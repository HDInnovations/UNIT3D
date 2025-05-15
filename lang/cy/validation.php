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
    'accepted'       => 'Rhaid derbyn :attribute.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'Nid yw :attribute yn URL dilys.',
    'after'          => 'Rhaid i :attribute fod yn ddyddiad sydd ar ôl :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha'          => 'Dim ond llythrennau\'n unig gall :attribute gynnwys.',
    'alpha_dash'     => 'Dim ond llythrennau, rhifau a dash yn unig gall :attribute gynnwys.',
    'alpha_num'      => 'Dim ond llythrennau a rhifau yn unig gall :attribute gynnwys.',
    'array'          => 'Rhaid i :attribute fod yn array.',
    'attributes'     => [
    ],
    'before'          => 'Rhaid i :attribute fod yn ddyddiad sydd cyn :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between'         => [
        'array'   => 'Rhaid i :attribute fod rhwng :min a :max eitem.',
        'file'    => 'Rhaid i :attribute fod rhwng :min a :max kilobytes.',
        'numeric' => 'Rhaid i :attribute fod rhwng :min a :max.',
        'string'  => 'Rhaid i :attribute fod rhwng :min a :max nodyn.'
    ],
    'boolean'          => 'Rhaid i\'r maes :attribute fod yn wir neu gau.',
    'confirmed'        => 'Nid yw\'r cadarnhad :attribute yn gyfwerth.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Nid yw :attribute yn ddyddiad dilys.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => 'Nid yw :attribute yn y fformat :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Rhaid i :attribute a :other fod yn wahanol.',
    'digits'         => 'Rhaid i :attribute fod yn :digits digid.',
    'digits_between' => 'Rhaid i :attribute fod rhwng :min a :max digid.',
    'dimensions'     => 'The :attribute has invalid image dimensions.',
    'distinct'       => 'The :attribute field has a duplicate value.',
    'email'          => 'Rhaid i :attribute fod yn gyfeiriad ebost dilys.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Nid yw :attribute yn ddilys.',
    'file'           => 'The :attribute must be a file.',
    'filled'         => 'Rhaid cynnwys :attribute.',
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
    'image'    => 'Rhaid i :attribute fod yn lun.',
    'in'       => 'Nid yw :attribute yn ddilys.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer'  => 'Rhaid i :attribute fod yn integer.',
    'ip'       => 'Rhaid i :attribute fod yn gyfeiriad IP dilys.',
    'ipv4'     => 'The :attribute must be a valid IPv4 address.',
    'ipv6'     => 'The :attribute must be a valid IPv6 address.',
    'json'     => 'The :attribute must be a valid JSON string.',
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
        'array'   => 'Ni chai :attribute fod yn fwy na :max eitem.',
        'file'    => 'Ni chai :attribute fod yn fwy na :max kilobytes.',
        'numeric' => 'Ni chai :attribute fod yn fwy na :max.',
        'string'  => 'Ni chai :attribute fod yn fwy na :max nodyn.'
    ],
    'mimes'     => 'Rhaid i :attribute fod yn ffeil o\'r math: :values.',
    'mimetypes' => 'Rhaid i :attribute fod yn ffeil o\'r math: :values.',
    'min'       => [
        'array'   => 'Rhaid i :attribute fod o leiaf :min eitem.',
        'file'    => 'Rhaid i :attribute fod o leiaf :min kilobytes.',
        'numeric' => 'Rhaid i :attribute fod o leiaf :min.',
        'string'  => 'Rhaid i :attribute fod o leiaf :min nodyn.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Nid yw :attribute yn ddilys.',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => 'Rhaid i :attribute fod yn rif.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'The :attribute field must be present.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Nid yw fformat :attribute yn ddilys.',
    'required'             => 'Rhaid cynnwys :attribute.',
    'required_if'          => 'Rhaid cynnwys :attribute pan mae :other yn :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'Rhaid cynnwys :attribute pan mae :values yn bresennol.',
    'required_with_all'    => 'Rhaid cynnwys :attribute pan mae :values yn bresennol.',
    'required_without'     => 'Rhaid cynnwys :attribute pan nad oes :values yn bresennol.',
    'required_without_all' => 'Rhaid cynnwys :attribute pan nad oes :values yn bresennol.',
    'same'                 => 'Rhaid i :attribute a :other fod yn gyfwerth.',
    'size'                 => [
        'array'   => 'Rhaid i :attribute fod yn :size eitem.',
        'file'    => 'Rhaid i :attribute fod yn :size kilobytes.',
        'numeric' => 'Rhaid i :attribute fod yn :size.',
        'string'  => 'Rhaid i :attribute fod yn :size nodyn.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => 'The :attribute must be a string.',
    'timezone'    => 'Rhaid i :attribute fod yn timezone dilys.',
    'unique'      => 'Mae :attribute eisoes yn bodoli.',
    'uploaded'    => 'The :attribute failed to upload.',
    'url'         => 'Nid yw fformat :attribute yn ddilys.',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
