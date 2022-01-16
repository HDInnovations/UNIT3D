<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */

namespace Database\Factories;

use App\Models\Torrent;
use App\Models\TorrentFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class TorrentFileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TorrentFile::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name'       => $this->faker->name(),
            'size'       => $this->faker->randomNumber(),
            'torrent_id' => fn () => Torrent::factory()->create()->id,
        ];
    }
}
