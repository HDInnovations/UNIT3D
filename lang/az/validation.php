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
    'accepted'       => ':attribute qəbul edilməlidir',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => ':attribute doğru URL deyil',
    'after'          => ':attribute :date tarixindən sonra olmalıdır',
    'after_or_equal' => ':attribute :date tarixi ilə eyni və ya sonra olmalıdır',
    'alpha'          => ':attribute yalnız hərflərdən ibarət ola bilər',
    'alpha_dash'     => ':attribute yalnız hərf, rəqəm və tire simvolundan ibarət ola bilər',
    'alpha_num'      => ':attribute yalnız hərf və rəqəmlərdən ibarət ola bilər',
    'array'          => ':attribute massiv formatında olmalıdır',
    'attributes'     => [
    ],
    'before'          => ':attribute :date tarixindən əvvəl olmalıdır',
    'before_or_equal' => ':attribute :date tarixindən əvvəl və ya bərabər olmalıdır',
    'between'         => [
        'array'   => ':attribute :min ilə :max intervalında hissədən ibarət olmalıdır',
        'file'    => ':attribute :min ilə :max KB ölçüsü intervalında olmalıdır',
        'numeric' => ':attribute :min ilə :max arasında olmalıdır',
        'string'  => ':attribute :min ilə :max simvolu intervalında olmalıdır'
    ],
    'boolean'          => ' :attribute doğru və ya yanlış ola bilər',
    'confirmed'        => ' :attribute doğrulanması yanlışdır',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => ' :attribute tarix formatında olmalıdır',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => ' :attribute :format formatında olmalıdır',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => ' :attribute və :other fərqli olmalıdır',
    'digits'         => ' :attribute :digits rəqəmli olmalıdır',
    'digits_between' => ' :attribute :min ilə :max rəqəmləri intervalında olmalıdır',
    'dimensions'     => ' :attribute doğru şəkil ölçülərində deyil',
    'distinct'       => ' :attribute dublikat qiymətlidir',
    'email'          => ' :attribute doğru email formatında deyil',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => ' seçilmiş :attribute yanlışdır',
    'file'           => ' :attribute fayl formatında olmalıdır',
    'filled'         => ' :attribute qiyməti olmalıdır',
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
    'image'    => ' :attribute şəkil formatında olmalıdır',
    'in'       => ' seçilmiş :attribute yanlışdır',
    'in_array' => ' :attribute :other qiymətləri arasında olmalıdır',
    'integer'  => ' :attribute tam ədəd olmalıdır',
    'ip'       => ' :attribute İP adres formatında olmalıdır',
    'ipv4'     => ' :attribute İPv4 adres formatında olmalıdır',
    'ipv6'     => ' :attribute İPv6 adres formatında olmalıdır',
    'json'     => ' :attribute JSON formatında olmalıdır',
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
        'array'   => ' :attribute maksimum :max hədd\'dən ibarət ola bilər',
        'file'    => ' :attribute maksimum :max KB ölçüsündə ola bilər',
        'numeric' => ' :attribute maksiumum :max rəqəmdən ibarət ola bilər',
        'string'  => ' :attribute maksimum :max simvoldan ibarət ola bilər'
    ],
    'mimes'     => ' :attribute :values tipində fayl olmalıdır',
    'mimetypes' => ' :attribute :values tipində fayl olmalıdır',
    'min'       => [
        'array'   => ' :attribute minimum :min hədd\'dən ibarət ola bilər',
        'file'    => ' :attribute minimum :min KB ölçüsündə ola bilər',
        'numeric' => ' :attribute minimum :min rəqəmdən ibarət ola bilər',
        'string'  => ' :attribute minimum :min simvoldan ibarət ola bilər'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => ' seçilmiş :attribute yanlışdır',
    'not_regex'   => 'The :attribute format is invalid.',
    'numeric'     => ' :attribute rəqəmlərdən ibarət olmalıdır',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => ' :attribute iştirak etməlidir',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => ' :attribute formatı yanlışdır',
    'required'             => ' :attribute mütləqdir',
    'required_if'          => ' :attribute (:other :value ikən) mütləqdir',
    'required_unless'      => ' :attribute (:other :values \'ə daxil ikən) mütləqdir',
    'required_with'        => ' :attribute (:values var ikən) mütləqdir',
    'required_with_all'    => ' :attribute (:values var ikən) mütləqdir',
    'required_without'     => ' :attribute (:values yox ikən) mütləqdir',
    'required_without_all' => ' :attribute (:values yox ikən) mütləqdir',
    'same'                 => ' :attribute və :other eyni olmalıdır',
    'size'                 => [
        'array'   => ' :attribute :size hədd\'dən ibarət olmalıdır',
        'file'    => ' :attribute :size KB ölçüsündə olmalıdır',
        'numeric' => ' :attribute :size ölçüsündə olmalıdır',
        'string'  => ' :attribute :size simvoldan ibarət olmalıdır'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => ' :attribute hərf formatında olmalıdır',
    'timezone'    => ' :attribute ərazi formatında olmalıdır',
    'unique'      => ' :attribute artıq iştirak edib',
    'uploaded'    => ' :attribute yüklənməsi mümkün olmadı',
    'url'         => ' :attribute formatı yanlışdır',
    'uuid'        => 'The :attribute must be a valid UUID.'
];
