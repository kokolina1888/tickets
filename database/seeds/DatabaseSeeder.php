<?php

use App\Concert;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Concert::class)->states('published')->create([
            'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
            'title' => "The Red Chord",
            'subtitle' => "with Animosity and Lethargy",
            'venue' => "The Mosh Pit",
            'venue_address' => "123 Example Lane",
            'city' => "Laraville",
            'state' => "ON",
            'zip' => "17916",
            'date' => Carbon::parse('2016-12-13 8:00pm'),
            'ticket_price' => 3250,
            'additional_information' => "This concert is 19+.",
        ])->addTickets(10);

        factory(App\User::class)->create([
            'email' => 'test4@email.com',
        // 'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
            ]);
    }
}
