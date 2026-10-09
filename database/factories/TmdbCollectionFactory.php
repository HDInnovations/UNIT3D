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

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TmdbCollection;
use Override;

/** @extends Factory<TmdbCollection> */
class TmdbCollectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = TmdbCollection::class;

    /**
     * Define the model's default state.
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name'       => $this->faker->name(),
            'name_sort'  => $this->faker->word(),
            'parts'      => $this->faker->word(),
            'overview'   => $this->faker->text(),
            'poster'     => $this->faker->word(),
            'backdrop'   => $this->faker->word(),
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }
}
