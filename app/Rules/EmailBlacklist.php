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

namespace App\Rules;

use Illuminate\Support\Str;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Override;

class EmailBlacklist implements ValidationRule
{
    /**
     * Array of blacklisted domains.
     *
     * @var array<string>
     */
    private array $domains = [];

    /**
     * Determine if the validation rule passes.
     */
    #[Override]
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Load blacklisted domains
        $this->domains = cache()->get(config('email-blacklist.cache-key'), []);
        $this->appendCustomDomains();

        // Fail if the domain blacklist cache is empty
        if (empty($this->domains)) {
            $fail('The email blacklist cache is currently empty. Please try again later or contact staff.');

            return;
        }

        // Extract domain from supplied email address
        $domain = Str::after(strtolower((string) $value), '@');

        // Run validation check
        if (\in_array($domain, $this->domains)) {
            $fail('Email domain is not allowed. Throwaway email providers are blacklisted.');
        }
    }

    /**
     * Append custom defined blacklisted domains.
     */
    protected function appendCustomDomains(): void
    {
        $appendList = config('email-blacklist.append');

        if ($appendList === null) {
            return;
        }

        $appendDomains = explode('|', strtolower((string) $appendList));
        $this->domains = array_merge($this->domains, $appendDomains);
    }
}
