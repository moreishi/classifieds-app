<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        $events = [
            [
                'title' => 'Cebu Night Market',
                'slug' => 'cebu-night-market',
                'description' => 'A vibrant night market along the Cebu City coastline featuring local artisans, street food, and live acoustic performances under the stars.',
                'event_date' => now()->addDays(7)->setHour(18)->setMinute(0),
                'location_name' => 'Cebu City Oceanfront',
                'vibe' => 'Party',
                'cover_image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=800&q=80',
            ],
            [
                'title' => 'Weekend Hustle:创业者 Meetup',
                'slug' => 'weekend-hustle-meetup',
                'description' => 'Connect with fellow founders, pitch your startup, and get real feedback from mentors. Bring your business cards and your biggest dreams.',
                'event_date' => now()->addDays(10)->setHour(9)->setMinute(0),
                'location_name' => 'IT Park, Cebu City',
                'vibe' => 'Hustle',
                'cover_image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&q=80',
            ],
            [
                'title' => 'Sinulog Street Art Jam',
                'slug' => 'sinulog-street-art-jam',
                'description' => 'Watch live mural painting, try your hand at graffiti, and explore pop-up galleries featuring Cebu\'s most exciting visual artists.',
                'event_date' => now()->addDays(14)->setHour(14)->setMinute(0),
                'location_name' => 'Colon Street, Cebu City',
                'vibe' => 'Art',
                'cover_image' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?w=800&q=80',
            ],
            [
                'title' => 'Cebu Tech Conference 2026',
                'slug' => 'cebu-tech-conference-2026',
                'description' => 'The biggest tech gathering in the Visayas. Talks on AI, web3, and startup growth from industry leaders. Limited slots available.',
                'event_date' => now()->addDays(21)->setHour(8)->setMinute(0),
                'location_name' => 'Waterfront Cebu City Hotel',
                'vibe' => 'Tech',
                'cover_image' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=800&q=80',
            ],
            [
                'title' => 'Live at The Social: Open Mic Night',
                'slug' => 'live-at-the-social-open-mic',
                'description' => 'Cebu\'s best open mic night. Sing, rap, slam poetry, or just vibe to local talent. Good food, cold drinks, great energy.',
                'event_date' => now()->addDays(5)->setHour(20)->setMinute(0),
                'location_name' => 'The Social, Mandaue City',
                'vibe' => 'Music',
                'cover_image' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?w=800&q=80',
            ],
            [
                'title' => 'Osmeña Food Fest',
                'slug' => 'osmena-food-fest',
                'description' => 'A weekend food festival celebrating Cebuano cuisine. From lechon to ngohiong, over 50 food stalls, cooking demos, and eating contests.',
                'event_date' => now()->addDays(12)->setHour(10)->setMinute(0),
                'location_name' => 'Osmeña Circle, Cebu City',
                'vibe' => 'Food',
                'cover_image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&q=80',
            ],
            [
                'title' => 'Basketball 3v3: Barangay Cup',
                'slug' => 'basketball-3v3-barangay-cup',
                'description' => 'Register your team for the annual Barangay Cup. Cash prizes, trophies, and bragging rights. All skill levels welcome.',
                'event_date' => now()->addDays(18)->setHour(7)->setMinute(0),
                'location_name' => 'Sugbo Sports Complex, Cebu City',
                'vibe' => 'Sports',
                'cover_image' => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=800&q=80',
            ],
            [
                'title' => 'Community Beach Cleanup + Surf',
                'slug' => 'community-beach-cleanup-surf',
                'description' => 'Spend the morning cleaning up our beautiful coastline, then hit the waves. Free lunch for all volunteers. Boards provided.',
                'event_date' => now()->addDays(9)->setHour(6)->setMinute(0),
                'location_name' => 'Moalboal Beach',
                'vibe' => 'Community',
                'cover_image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=80',
            ],
            [
                'title' => 'DJ Night: Bass Underground',
                'slug' => 'dj-night-bass-underground',
                'description' => 'Cebu\'s top DJs spinning bass house, techno, and DnB all night. Rooftop venue, massive sound system, and a crowd that brings the energy.',
                'event_date' => now()->addDays(15)->setHour(22)->setMinute(0),
                'location_name' => 'Skydeck, Cebu City',
                'vibe' => 'Party',
                'cover_image' => 'https://images.unsplash.com/photo-1574169208507-84376144848b?w=800&q=80',
            ],
            [
                'title' => 'Startup Weekend: Build in 48hrs',
                'slug' => 'startup-weekend-build-48hrs',
                'description' => 'Form a team, validate an idea, and launch a prototype in one weekend. Prizes, mentorship, and potential funding for the winning team.',
                'event_date' => now()->addDays(28)->setHour(9)->setMinute(0),
                'location_name' => 'Cebu Startup Center',
                'vibe' => 'Hustle',
                'cover_image' => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=800&q=80',
            ],
        ];

        foreach ($events as $event) {
            Event::create(array_merge($event, ['user_id' => $user->id]));
        }
    }
}
