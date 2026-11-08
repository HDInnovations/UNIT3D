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
 * @author     Roardom <roardom@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Traits;

use ReflectionClass;
use ReflectionNamedType;
use Closure;

use function theodorejb\polycast\safe_float;
use function theodorejb\polycast\safe_int;
use function theodorejb\polycast\safe_string;

trait CastLivewireProperties
{
    /**
     * Cast incoming livewire property updates from string form to the type of
     * the property if the type is either int, float, or bool. If the type of
     * the property is ?int, ?float, or ?bool and the incoming property update
     * is an empty string, then set it to null.
     *
     * TODO: Livewire will automatically call lifecycle hooks in traits if the
     * function is suffixed by the trait name, however, in livewire v2, this
     * doesn't work when passing the value by reference since livewire v2 uses
     * listeners to pass the value around. In livewire v3, this is fixed, and
     * we can rename this trait to updatingCastLivewireProperties so that
     * livewire uses the method directly.
     */
    public function castLivewireProperties(string $field, mixed &$value): void
    {
        $class = new ReflectionClass(static::class);

        if (!$class->hasProperty($field)) {
            return;
        }

        if (($type = $class->getProperty($field)->getType()) instanceof ReflectionNamedType) {
            if ($type->allowsNull() && $value === '') {
                $value = null;

                return;
            }

            switch ($type->getName()) {
                case 'array':
                    $value = (array) $value;

                    break;
                case 'string':
                    validator([$field => $value], [$field => [
                        function (string $_attribute, mixed $value, Closure $fail): void {
                            if (!safe_string($value)) {
                                $fail(':attribute must be a string');
                            }
                        },
                    ]])->validate();

                    $value = (string) $value;

                    break;
                case 'int':
                    validator([$field => $value], [$field => [
                        function (string $_attribute, mixed $value, Closure $fail): void {
                            if (!safe_int($value)) {
                                $fail(':attribute must be a number');
                            }
                        },
                    ]])->validate();

                    $value = (int) $value;

                    break;
                case 'float':
                    validator([$field => $value], [$field => [
                        function (string $_attribute, mixed $value, Closure $fail): void {
                            if (!safe_float($value)) {
                                $fail(':attribute must be a float');
                            }
                        },
                    ]])->validate();

                    $value = (float) $value;

                    break;
                case 'bool':
                    $value = (bool) $value;

                    break;
            }
        }
    }
}
