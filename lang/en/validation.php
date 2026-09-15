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
    /*
    |--------------------------------------------------------------------------
    | Validation language lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'        => 'The :attribute must be accepted.',
    'accepted_if'     => 'The :attribute must be accepted when :other is :value.',
    'active_url'      => 'The :attribute is not a valid URL.',
    'after'           => 'The :attribute must be a date after :date.',
    'after_or_equal'  => 'The :attribute must be a date after or equal to :date.',
    'alpha'           => 'The :attribute must only contain letters.',
    'alpha_dash'      => 'The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num'       => 'The :attribute must only contain letters and numbers.',
    'array'           => 'The :attribute must be an array.',
    'before'          => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between'         => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],

    'confirmed'        => 'The :attribute confirmation does not match.',

    'date'             => 'The :attribute is not a valid date.',
    'date_equals'      => 'The :attribute must be a date equal to :date.',
    'date_format'      => 'The :attribute does not match the format :format.',
    'declined'         => 'The :attribute must be declined.',
    'declined_if'      => 'The :attribute must be declined when :other is :value.',
    'different'        => 'The :attribute and :other must be different.',
    'digits'           => 'The :attribute must be :digits digits.',
    'digits_between'   => 'The :attribute must be between :min and :max digits.',
    'dimensions'       => 'The :attribute has invalid image dimensions.',

    'email'            => 'The :attribute must be a valid email address.',
    'ends_with'        => 'The :attribute must end with one of the following: :values.',

    'file'             => 'The :attribute must be a file.',

    'gt'               => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file'    => 'The :attribute must be greater than :value kilobytes.',
        'string'  => 'The :attribute must be greater than :value characters.',
        'array'   => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal to :value.',
        'file'    => 'The :attribute must be greater than or equal to :value kilobytes.',
        'string'  => 'The :attribute must be greater than or equal to :value characters.',
        'array'   => 'The :attribute must have :value items or more.',
    ],
    'image'       => 'The :attribute must be an image.',

    'in_array'    => 'The :attribute field does not exist in :other.',
    'integer'     => 'The :attribute must be an integer.',
    'ip'          => 'The :attribute must be a valid IP address.',
    'ipv4'        => 'The :attribute must be a valid IPv4 address.',
    'ipv6'        => 'The :attribute must be a valid IPv6 address.',
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'json'        => 'The :attribute must be a valid JSON string.',
    'lt'          => [
        'numeric' => 'The :attribute must be less than :value.',
        'file'    => 'The :attribute must be less than :value kilobytes.',
        'string'  => 'The :attribute must be less than :value characters.',
        'array'   => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'file'    => 'The :attribute must be less than or equal to :value kilobytes.',
        'string'  => 'The :attribute must be less than or equal to :value characters.',
        'array'   => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'The :attribute must not be greater than :max.',
        'file'    => 'The :attribute must not be greater than :max kilobytes.',
        'string'  => 'The :attribute must not be greater than :max characters.',
        'array'   => 'The :attribute must not have more than :max items.',
    ],
    'mimes'     => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min'       => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',

    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => 'The :attribute must be a number.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',

    ],

    'regex'                => 'The :attribute format is invalid.',

    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string'      => 'The :attribute must be a string.',
    'timezone'    => 'The :attribute must be a valid timezone.',

    'url'         => 'The :attribute must be a valid URL.',
    'uuid'        => 'The :attribute must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom validation language lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'email_list' => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'recaptcha'  => 'Please complete the ReCaptcha.',

    'custom' => [
        'attribute-name' => [

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom validation attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],
];
