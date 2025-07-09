<?php
return [
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
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted' => 'Το πεδίο :attribute πρέπει να γίνει αποδεκτό.',
    'active_url' => 'Το πεδίο :attribute δεν είναι αποδεκτή διεύθυνση URL.',
    'after' => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία μετά από :date.',
    'after_or_equal' => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία ίδια ή μετά από :date.',
    'alpha' => 'Το πεδίο :attribute μπορεί να περιέχει μόνο γράμματα.',
    'alpha_dash' => 'Το πεδίο :attribute μπορεί να περιέχει μόνο γράμματα, αριθμούς, και παύλες.',
    'alpha_num' => 'Το πεδίο :attribute μπορεί να περιέχει μόνο γράμματα και αριθμούς.',
    'array' => 'Το πεδίο :attribute πρέπει να είναι ένας πίνακας.',
    'before' => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία πριν από :date.',
    'before_or_equal' => 'Το πεδίο :attribute πρέπει να είναι μία ημερομηνία ίδια ή πριν από :date.',
    'between' => [
        'numeric' => 'Το πεδίο :attribute πρέπει να είναι μεταξύ :min - :max.',
        'file' => 'Το πεδίο :attribute πρέπει να είναι μεταξύ :min - :max kilobytes.',
        'string' => 'Το πεδίο :attribute πρέπει να είναι μεταξύ :min - :max χαρακτήρες.',
        'array' => 'Το πεδίο :attribute πρέπει να έχει μεταξύ :min - :max αντικείμενα.',
    ],
    'boolean' => 'Το πεδίο :attribute πρέπει να είναι true ή false.',
    'confirmed' => 'Η επιβεβαίωση του :attribute δεν ταιριάζει.',
    'date' => 'Το πεδίο :attribute δεν είναι έγκυρη ημερομηνία.',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'Το πεδίο :attribute δεν είναι της μορφής :format.',
    'different' => 'Το πεδίο :attribute και :other πρέπει να είναι διαφορετικά.',
    'digits' => 'Το πεδίο :attribute πρέπει να είναι :digits ψηφία.',
    'digits_between' => 'Το πεδίο :attribute πρέπει να είναι μεταξύ :min και :max ψηφία.',
    'dimensions' => 'Το πεδίο :attribute περιέχει μη έγκυρες διαστάσεις εικόνας.',
    'distinct' => 'Το πεδίο :attribute περιέχει δύο φορές την ίδια τιμή.',
    'email' => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση email.',
    'file' => 'Το πεδίο :attribute πρέπει να είναι αρχείο.',
    'filled' => 'To πεδίο :attribute είναι απαραίτητο.',
    'exists' => 'Το επιλεγμένο :attribute δεν είναι έγκυρο.',
    'gt' => [
        'numeric' => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο από :value.',
        'file' => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο από :value kilobytes.',
        'string' => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο από :value χαρακτήρες.',
        'array' => 'To πεδίο :attribute πρέπει να έχει περισσότερα από :value αντικείμενα.',
    ],
    'gte' => [
        'numeric' => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο ή ίσο από :value.',
        'file' => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο ή ίσο από :value kilobytes.',
        'string' => 'To πεδίο :attribute πρέπει να είναι μεγαλύτερο ή ίσο από :value χαρακτήρες.',
        'array' => 'To πεδίο :attribute πρέπει να έχει :value αντικείμενα ή περισσότερα.',
    ],
    'image' => 'Το πεδίο :attribute πρέπει να είναι εικόνα.',
    'in' => 'Το επιλεγμένο :attribute δεν είναι έγκυρο.',
    'in_array' => 'Το πεδίο :attribute δεν υπάρχει σε :other.',
    'integer' => 'Το πεδίο :attribute πρέπει να είναι ακέραιος.',
    'ip' => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση IP.',
    'ipv4' => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση IPv4.',
    'ipv6' => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη διεύθυνση IPv6.',
    'json' => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη συμβολοσειρά JSON.',
    'lt' => [
        'numeric' => 'To πεδίο :attribute πρέπει να είναι μικρότερo από :value.',
        'file' => 'To πεδίο :attribute πρέπει να είναι μικρότερo από :value kilobytes.',
        'string' => 'To πεδίο :attribute πρέπει να είναι μικρότερo από :value χαρακτήρες.',
        'array' => 'To πεδίο :attribute πρέπει να έχει λιγότερα από :value αντικείμενα.',
    ],
    'lte' => [
        'numeric' => 'To πεδίο :attribute πρέπει να είναι μικρότερo ή ίσο από :value.',
        'file' => 'To πεδίο :attribute πρέπει να είναι μικρότερo ή ίσο από  :value kilobytes.',
        'string' => 'To πεδίο :attribute πρέπει να είναι μικρότερo ή ίσο από  :value χαρακτήρες.',
        'array' => 'To πεδίο :attribute δεν πρέπει να υπερβαίνει τα :value αντικείμενα.',
    ],
    'max' => [
        'numeric' => 'Το πεδίο :attribute δεν μπορεί να είναι μεγαλύτερο από :max.',
        'file' => 'Το πεδίο :attribute δεν μπορεί να είναι μεγαλύτερό :max kilobytes.',
        'string' => 'Το πεδίο :attribute δεν μπορεί να έχει περισσότερους από :max χαρακτήρες.',
        'array' => 'Το πεδίο :attribute δεν μπορεί να έχει περισσότερα από :max αντικείμενα.',
    ],
    'mimes' => 'Το πεδίο :attribute πρέπει να είναι αρχείο τύπου: :values.',
    'mimetypes' => 'Το πεδίο :attribute πρέπει να είναι αρχείο τύπου: :values.',
    'min' => [
        'numeric' => 'Το πεδίο :attribute πρέπει να είναι τουλάχιστον :min.',
        'file' => 'Το πεδίο :attribute πρέπει να είναι τουλάχιστον :min kilobytes.',
        'string' => 'Το πεδίο :attribute πρέπει να έχει τουλάχιστον :min χαρακτήρες.',
        'array' => 'Το πεδίο :attribute πρέπει να έχει τουλάχιστον :min αντικείμενα.',
    ],
    'not_in' => 'Το επιλεγμένο :attribute δεν είναι αποδεκτό.',
    'not_regex' => 'Η μορφή του πεδίου :attribute δεν είναι αποδεκτή.',
    'numeric' => 'Το πεδίο :attribute πρέπει να είναι αριθμός.',
    'present' => 'Το πεδίο :attribute πρέπει να υπάρχει.',
    'regex' => 'Η μορφή του πεδίου :attribute δεν είναι αποδεκτή.',
    'required' => 'Το πεδίο :attribute είναι απαραίτητο.',
    'required_if' => 'Το πεδίο :attribute είναι απαραίτητο όταν το πεδίο :other είναι :value.',
    'required_unless' => 'Το πεδίο :attribute είναι απαραίτητο εκτός αν το πεδίο :other εμπεριέχει :values.',
    'required_with' => 'Το πεδίο :attribute είναι απαραίτητο όταν υπάρχει :values.',
    'required_with_all' => 'Το πεδίο :attribute είναι απαραίτητο όταν υπάρχουν :values.',
    'required_without' => 'Το πεδίο :attribute είναι απαραίτητο όταν δεν υπάρχει :values.',
    'required_without_all' => 'Το πεδίο :attribute είναι απαραίτητο όταν δεν υπάρχει κανένα από :values.',
    'same' => 'Τα πεδία :attribute και :other πρέπει να είναι ίδια.',
    'size' => [
        'numeric' => 'Το πεδίο :attribute πρέπει να είναι :size.',
        'file' => 'Το πεδίο :attribute πρέπει να είναι :size kilobytes.',
        'string' => 'Το πεδίο :attribute πρέπει να είναι :size χαρακτήρες.',
        'array' => 'Το πεδίο :attribute πρέπει να περιέχει :size αντικείμενα.',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values',
    'string' => 'Το πεδίο :attribute πρέπει να είναι αλφαριθμητικό.',
    'timezone' => 'Το πεδίο :attribute πρέπει να είναι μία έγκυρη ζώνη ώρας.',
    'unique' => 'Το πεδίο :attribute έχει ήδη εκχωρηθεί.',
    'uploaded' => 'Η μεταφόρτωση του πεδίου :attribute απέτυχε.',
    'url' => 'Το πεδίο :attribute δεν είναι έγκυρη διεύθυνση URL.',
    'uuid' => 'Το πεδίο :attribute πρέπει να είναι έγκυρο UUID.',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */
    'email_list' => 'Sorry, this email domain is not allowed to be used on this site. Please see sites email whitelist.',
    'recaptcha' => 'Please Complete The ReCaptcha.',
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    'accepted_if' => 'Το χαρακτηριστικό πρέπει να γίνει αποδεκτό όταν το other έχει τιμή.',
    'current_password' => 'Ο κωδικός πρόσβασης είναι λανθασμένος.',
    'declined' => 'Το χαρακτηριστικό :attribute πρέπει να απορριφθεί.',
    'declined_if' => 'Το χαρακτηριστικό :attribute πρέπει να απορριφθεί όταν το :other είναι :value.',
    'ends_with' => 'Το χαρακτηριστικό :attribute πρέπει να τελειώνει με ένα από τα ακόλουθα: :values.',
    'enum' => 'Το επιλεγμένο :attribute δεν είναι έγκυρο.',
    'mac_address' => 'Το χαρακτηριστικό :attribute πρέπει να είναι μια έγκυρη διεύθυνση MAC.',
    'multiple_of' => 'Το χαρακτηριστικό :attribute πρέπει να είναι πολλαπλάσιο του :value.',
    'password' => [
        'letters' => 'Το χαρακτηριστικό :attribute πρέπει να περιέχει τουλάχιστον ένα γράμμα.',
        'mixed' => 'Το χαρακτηριστικό :attribute πρέπει να περιέχει τουλάχιστον ένα κεφαλαίο και ένα πεζό γράμμα.',
        'numbers' => 'Το χαρακτηριστικό :attribute πρέπει να περιέχει τουλάχιστον έναν αριθμό.',
        'symbols' => 'Το χαρακτηριστικό :attribute πρέπει να περιέχει τουλάχιστον ένα σύμβολο.',
        'uncompromised' => 'Το δεδομένο :attribute εμφανίστηκε σε μια διαρροή δεδομένων. Επιλέξτε ένα διαφορετικό :attribute',
    ],
    'prohibited' => 'Το πεδίο :attribute απαγορεύεται.',
    'prohibited_if' => 'Το πεδίο :attribute απαγορεύεται όταν το :other είναι :value.',
    'prohibited_unless' => 'Το πεδίο :attribute απαγορεύεται εκτός εάν το :other βρίσκεται στην περιοχή :values.',
    'prohibits' => 'Το πεδίο :attribute απαγορεύει την παρουσία του :other.',
];
