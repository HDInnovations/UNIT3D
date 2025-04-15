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
    'accepted'       => 'Το πεδίο :attribute πρέπει να γίνει αποδεκτό.',
    'accepted_if'    => 'The :attribute must be accepted when :other is :value.',
    'active_url'     => 'Το πεδίο :attribute δεν είναι αποδεκτή διεύθυνση URL.',
    'after'          => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία μετά από :date.',
    'after_or_equal' => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία ίδια ή μετά από :date.',
    'alpha'          => 'Το πεδίο :attribute μπορεί να περιέχει μόνο γράμματα.',
    'alpha_dash'     => 'Το πεδίο :attribute μπορεί να περιέχει μόνο γράμματα, αριθμούς, και παύλες.',
    'alpha_num'      => 'Το πεδίο :attribute μπορεί να περιέχει μόνο γράμματα και αριθμούς.',
    'array'          => 'Το πεδίο :attribute πρέπει να είναι ένας πίνακας.',
    'attributes'     => [
    ],
    'before'          => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία πριν από :date.',
    'before_or_equal' => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία ίδια ή πριν από :date.',
    'between'         => [
        'array'   => 'Το πεδίο :attribute πρέπει να έχει μεταξύ :min - :max αντικείμενα.',
        'file'    => 'Το πεδίο :attribute πρέπει να είναι μεταξύ :min - :max kilobytes.',
        'numeric' => 'Το πεδίο :attribute πρέπει να είναι μεταξύ :min - :max.',
        'string'  => 'Το πεδίο :attribute πρέπει να είναι μεταξύ :min - :max χαρακτήρες.'
    ],
    'boolean'          => 'Το πεδίο :attribute πρέπει να είναι true ή false.',
    'confirmed'        => 'Η επιβεβαίωση του :attribute δεν ταιριάζει.',
    'current_password' => 'The password is incorrect.',
    'custom'           => [
        'attribute-name' => [
            'rule-name' => 'custom-message'
        ]
    ],
    'date'           => 'Το πεδίο :attribute δεν είναι έγκυρη ημερομηνία.',
    'date_equals'    => 'The :attribute must be a date equal to :date.',
    'date_format'    => 'Το πεδίο :attribute δεν είναι της μορφής :format.',
    'declined'       => 'The :attribute must be declined.',
    'declined_if'    => 'The :attribute must be declined when :other is :value.',
    'different'      => 'Το πεδίο :attribute και :other πρέπει να είναι διαφορετικά.',
    'digits'         => 'Το πεδίο :attribute πρέπει να είναι :digits ψηφία.',
    'digits_between' => 'Το πεδίο :attribute πρέπει να είναι μεταξύ :min και :max ψηφία.',
    'dimensions'     => 'Το πεδίο :attribute περιέχει μη έγκυρες διαστάσεις εικόνας.',
    'distinct'       => 'Το πεδίο :attribute περιέχει δύο φορές την ίδια τιμή.',
    'email'          => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση email.',
    'email_list'     => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'ends_with'      => 'The :attribute must end with one of the following: :values.',
    'enum'           => 'The selected :attribute is invalid.',
    'exists'         => 'Το επιλεγμένο :attribute δεν είναι έγκυρο.',
    'file'           => 'Το πεδίο :attribute πρέπει να είναι αρχείο.',
    'filled'         => 'To πεδίο :attribute είναι απαραίτητο.',
    'gt'             => [
        'array'   => 'To πεδίο :attribute πρέπει να έχει περισσότερα από :value αντικείμενα.',
        'file'    => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο από :value kilobytes.',
        'numeric' => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο από :value.',
        'string'  => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο από :value χαρακτήρες.'
    ],
    'gte' => [
        'array'   => 'To πεδίο :attribute πρέπει να έχει :value αντικείμενα ή περισσότερα.',
        'file'    => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο ή ίσο από :value kilobytes.',
        'numeric' => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο ή ίσο από :value.',
        'string'  => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο ή ίσο από :value χαρακτήρες.'
    ],
    'image'    => 'Το πεδίο :attribute πρέπει να είναι εικόνα.',
    'in'       => 'Το επιλεγμένο :attribute δεν είναι έγκυρο.',
    'in_array' => 'Το πεδίο :attribute δεν υπάρχει σε :other.',
    'integer'  => 'Το πεδίο :attribute πρέπει να είναι ακέραιος.',
    'ip'       => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση IP.',
    'ipv4'     => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση IPv4.',
    'ipv6'     => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση IPv6.',
    'json'     => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη συμβολοσειρά JSON.',
    'lt'       => [
        'array'   => 'To πεδίο :attribute πρέπει να έχει λιγότερα από :value αντικείμενα.',
        'file'    => 'To πεδίο :attribute πρέπει να είναι μικρότερo από :value kilobytes.',
        'numeric' => 'To πεδίο :attribute πρέπει να είναι μικρότερo από :value.',
        'string'  => 'To πεδίο :attribute πρέπει να είναι μικρότερo από :value χαρακτήρες.'
    ],
    'lte' => [
        'array'   => 'To πεδίο :attribute δεν πρέπει να υπερβαίνει τα :value αντικείμενα.',
        'file'    => 'To πεδίο :attribute πρέπει να είναι μικρότερo ή ίσο από  :value kilobytes.',
        'numeric' => 'To πεδίο :attribute πρέπει να είναι μικρότερo ή ίσο από :value.',
        'string'  => 'To πεδίο :attribute πρέπει να είναι μικρότερo ή ίσο από  :value χαρακτήρες.'
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max'         => [
        'array'   => 'Το πεδίο :attribute δεν μπορεί να έχει περισσότερα από :max αντικείμενα.',
        'file'    => 'Το πεδίο :attribute δεν μπορεί να είναι μεγαλύτερό :max kilobytes.',
        'numeric' => 'Το πεδίο :attribute δεν μπορεί να είναι μεγαλύτερο από :max.',
        'string'  => 'Το πεδίο :attribute δεν μπορεί να έχει περισσότερους από :max χαρακτήρες.'
    ],
    'mimes'     => 'Το πεδίο :attribute πρέπει να είναι αρχείο τύπου: :values.',
    'mimetypes' => 'Το πεδίο :attribute πρέπει να είναι αρχείο τύπου: :values.',
    'min'       => [
        'array'   => 'Το πεδίο :attribute πρέπει να έχει τουλάχιστον :min αντικείμενα.',
        'file'    => 'Το πεδίο :attribute πρέπει να είναι τουλάχιστον :min kilobytes.',
        'numeric' => 'Το πεδίο :attribute πρέπει να είναι τουλάχιστον :min.',
        'string'  => 'Το πεδίο :attribute πρέπει να έχει τουλάχιστον :min χαρακτήρες.'
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in'      => 'Το επιλεγμένο :attribute δεν είναι αποδεκτό.',
    'not_regex'   => 'Η μορφή του πεδίου :attribute δεν είναι αποδεκτή.',
    'numeric'     => 'Το πεδίο :attribute πρέπει να είναι αριθμός.',
    'password'    => [
        'letters'       => 'The :attribute must contain at least one letter.',
        'mixed'         => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers'       => 'The :attribute must contain at least one number.',
        'symbols'       => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.'
    ],
    'present'              => 'Το πεδίο :attribute πρέπει να υπάρχει.',
    'prohibited'           => 'The :attribute field is prohibited.',
    'prohibited_if'        => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless'    => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits'            => 'The :attribute field prohibits :other from being present.',
    'recaptcha'            => 'Please Complete The ReCaptcha.',
    'regex'                => 'Η μορφή του πεδίου :attribute δεν είναι αποδεκτή.',
    'required'             => 'Το πεδίο :attribute είναι απαραίτητο.',
    'required_if'          => 'Το πεδίο :attribute είναι απαραίτητο όταν το πεδίο :other είναι :value.',
    'required_unless'      => 'Το πεδίο :attribute είναι απαραίτητο εκτός αν το πεδίο :other εμπεριέχει :values.',
    'required_with'        => 'Το πεδίο :attribute είναι απαραίτητο όταν υπάρχει :values.',
    'required_with_all'    => 'Το πεδίο :attribute είναι απαραίτητο όταν υπάρχουν :values.',
    'required_without'     => 'Το πεδίο :attribute είναι απαραίτητο όταν δεν υπάρχει :values.',
    'required_without_all' => 'Το πεδίο :attribute είναι απαραίτητο όταν δεν υπάρχει κανένα από :values.',
    'same'                 => 'Τα πεδία :attribute και :other πρέπει να είναι ίδια.',
    'size'                 => [
        'array'   => 'Το πεδίο :attribute πρέπει να περιέχει :size αντικείμενα.',
        'file'    => 'Το πεδίο :attribute πρέπει να είναι :size kilobytes.',
        'numeric' => 'Το πεδίο :attribute πρέπει να είναι :size.',
        'string'  => 'Το πεδίο :attribute πρέπει να είναι :size χαρακτήρες.'
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string'      => 'Το πεδίο :attribute πρέπει να είναι αλφαριθμητικό.',
    'timezone'    => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη ζώνη ώρας.',
    'unique'      => 'Το πεδίο :attribute έχει ήδη εκχωρηθεί.',
    'uploaded'    => 'Η μεταφόρτωση του πεδίου :attribute απέτυχε.',
    'url'         => 'Το πεδίο :attribute δεν είναι έγκυρη διεύθυνση URL.',
    'uuid'        => 'Το πεδίο :attribute πρέπει να είναι έγκυρο UUID.'
];
