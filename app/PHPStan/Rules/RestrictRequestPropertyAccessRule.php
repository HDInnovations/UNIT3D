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

namespace App\PHPStan\Rules;

use Illuminate\Http\Request;
use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;

/**
 * @implements Rule<PropertyFetch>
 */
class RestrictRequestPropertyAccessRule implements Rule
{
    public function getNodeType(): string
    {
        return PropertyFetch::class;
    }

    /**
     * @return array{}|array{\PHPStan\Rules\RuleError}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!(new ObjectType(Request::class)->isSuperTypeOf($scope->getType($node->var))->yes())) {
            return [];
        }

        return [
            RuleErrorBuilder::message(
                'Property access on Illuminate\Http\Request is not allowed. Use an approved method instead.'
            )->build(),
        ];
    }
}
