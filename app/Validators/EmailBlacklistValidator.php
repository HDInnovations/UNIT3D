<?php
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

namespace App\Validators;

use App\Helpers\EmailBlacklistUpdater;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;

class EmailBlacklistValidator
{
    /**
     * Array of blacklisted domains.
     */
    private $domains = [];

    /**
     * Generate the error message on validation failure.
     *
     * @param $message
     * @param $attribute
     * @param $rule
     * @param $parameters
     *
     * @return string
     */
    public function message($message, $attribute, $rule, $parameters)
    {
        return \sprintf('%s domain is not allowed. Throwaway email providers are blacklisted.', $attribute);
    }

    /**
     * Execute the validation routine.
     *
     * @param string $attribute
     * @param string $value
     * @param array  $parameters
     *
     * @throws \Exception
     *
     * @return bool.
     */
    public function validate($attribute, $value, $parameters)
    {
        // Load blacklisted domains
        $this->refresh();

        // Extract domain from supplied email address
        $domain = Str::after(\strtolower($value), '@');

        // Run validation check
        return ! \in_array($domain, $this->domains, true);
    }

    /**
     * Retrive latest selection of blacklisted domains and cache them.
     *
     * @param null
     *
     * @throws \Exception
     *
     * @return void
     */
    public function refresh()
    {
        $this->shouldUpdate();
        $this->domains = \cache()->get(\config('email-blacklist.cache-key'));
        $this->appendCustomDomains();
    }

    protected function shouldUpdate()
    {
        $autoupdate = \config('email-blacklist.auto-update');

        try {
            if ($autoupdate && ! \cache()->has(\config('email-blacklist.cache-key'))) {
                EmailBlacklistUpdater::update();
            }
        } catch (InvalidArgumentException) {
        }
    }

    protected function appendCustomDomains()
    {
        $appendList = \config('email-blacklist.append');
        if ($appendList === null) {
            return;
        }
        $appendDomains = \explode('|', \strtolower($appendList));
        $this->domains = \array_merge($this->domains, $appendDomains);
    }
}
