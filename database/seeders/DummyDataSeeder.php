<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $this->command->info('Seeding Organizations...');
        $orgIds = [];
        for ($i = 0; $i < 5; $i++) {
            $orgIds[] = DB::table('organizations')->insertGetId([
                'name' => $faker->company,
                'type' => $faker->randomElement(['dinas', 'kecamatan', 'kelurahan', 'lsm']),
                'code' => strtoupper($faker->bothify('ORG-####')),
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'email' => $faker->companyEmail,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeding Users...');
        $userIds = [1];
        for ($i = 0; $i < 10; $i++) {
            $userIds[] = DB::table('users')->insertGetId([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'username' => $faker->unique()->numerify('199#######000#'),
                'password' => Hash::make('password'),
                'organization_id' => $faker->randomElement($orgIds),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeding Categories...');
        $catIds = [];
        for ($i = 0; $i < 5; $i++) {
            $catIds[] = DB::table('categories')->insertGetId([
                'name' => ucfirst($faker->word) . " Category",
                'slug' => Str::slug($faker->word . "-" . uniqid()),
                'description' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeding Pages...');
        for ($i = 0; $i < 5; $i++) {
            DB::table('pages')->insert([
                'title' => $faker->sentence(4),
                'slug' => Str::slug($faker->sentence(4) . uniqid()),
                'excerpt' => $faker->paragraph,
                'content' => $faker->paragraphs(3, true),
                'status' => 'published',
                'published_at' => now(),
                'author_id' => $faker->randomElement($userIds),
                'reviewer_id' => $faker->randomElement($userIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeding Posts...');
        for ($i = 0; $i < 10; $i++) {
            DB::table('posts')->insert([
                'category_id' => $faker->randomElement($catIds),
                'title' => $faker->sentence,
                'slug' => Str::slug($faker->sentence . uniqid()),
                'excerpt' => $faker->paragraph,
                'content' => $faker->paragraphs(4, true),
                'featured_image' => null,
                'status' => 'published',
                'published_at' => $faker->dateTimeThisYear(),
                'author_id' => $faker->randomElement($userIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeding Publications & Galleries...');
        for ($i = 0; $i < 5; $i++) {
            DB::table('publications')->insert([
                'category_id' => $faker->randomElement($catIds),
                'title' => $faker->sentence,
                'description' => $faker->paragraph,
                'year' => $faker->year,
                'file_path' => 'dummy/path/file.pdf',
                'classification' => 'public',
                'status' => 'published',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('galleries')->insert([
                'title' => $faker->sentence,
                'slug' => Str::slug($faker->sentence . uniqid()),
                'description' => $faker->paragraph,
                'image_path' => 'dummy/path/media.jpg',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeding Services & Facilities...');
        for ($i = 0; $i < 5; $i++) {
            DB::table('services')->insert([
                'organization_id' => $faker->randomElement($orgIds),
                'name' => 'Layanan ' . $faker->words(2, true),
                'slug' => Str::slug('Layanan ' . $faker->words(2, true) . uniqid()),
                'description' => $faker->paragraph,
                'type' => 'online',
                'contact_info' => $faker->phoneNumber,
                'working_hours' => '08:00 - 16:00',
                'status' => 'published',
                'image' => 'dummy_image.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('facilities')->insert([
                'organization_id' => $faker->randomElement($orgIds),
                'name' => 'Fasilitas ' . $faker->words(2, true),
                'slug' => Str::slug('Fasilitas ' . $faker->words(2, true) . uniqid()),
                'description' => $faker->paragraph,
                'category' => 'public',
                'address' => $faker->address,
                'coordinates' => $faker->latitude . ',' . $faker->longitude,
                'status' => 'published',
                'image' => 'dummy_image.png',
                'is_child_friendly' => true,
                'verified_at' => now(),
                'verified_by' => $faker->randomElement($userIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeding Forum Topics & Replies...');
        $topicIds = [];
        for ($i = 0; $i < 5; $i++) {
            $topicIds[] = DB::table('forum_topics')->insertGetId([
                'title' => $faker->sentence,
                'slug' => Str::slug($faker->sentence . uniqid()),
                'content' => $faker->paragraph,
                'author_id' => $faker->randomElement($userIds),
                'category' => 'general',
                'is_locked' => false,
                'is_pinned' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach ($topicIds as $topicId) {
            $replyCount = rand(1, 3);
            for ($j = 0; $j < $replyCount; $j++) {
                DB::table('forum_replies')->insert([
                    'topic_id' => $topicId,
                    'author_id' => $faker->randomElement($userIds),
                    'content' => $faker->paragraph,
                    'is_approved' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Seeding Aspirations & Complaints...');
        for ($i = 0; $i < 5; $i++) {
            DB::table('aspirations')->insert([
                'title' => $faker->sentence,
                'content' => $faker->paragraph,
                'author_name' => $faker->name,
                'author_age' => rand(10, 18),
                'author_region' => $faker->city,
                'status' => $faker->randomElement(['draft', 'review', 'published', 'rejected']),
                'category' => 'education',
                'reviewed_by' => $faker->randomElement($userIds),
                'reviewed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $complaintId = DB::table('complaints')->insertGetId([
                'ticket_number' => 'TKN-' . strtoupper(Str::random(8)),
                'category_id' => $faker->randomElement($catIds),
                'reporter_name' => $faker->name,
                'reporter_contact' => $faker->phoneNumber,
                'is_anonymous' => $faker->boolean,
                'victim_relation' => $faker->randomElement(['self', 'parent', 'other']),
                'chronology' => $faker->paragraphs(2, true),
                'general_location' => $faker->address,
                'urgency' => $faker->randomElement(['low', 'medium', 'high']),
                'consent_at' => now(),
                'status' => $faker->randomElement(['new', 'in_progress', 'resolved']),
                'assigned_to' => $faker->randomElement($userIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('complaint_follow_ups')->insert([
                'complaint_id' => $complaintId,
                'user_id' => $faker->randomElement($userIds),
                'action_type' => 'review',
                'note' => 'Tindak lanjut pertama: ' . $faker->sentence,
                'next_action' => 'investigation',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Seeding Statistics...');
        for ($i = 0; $i < 5; $i++) {
            DB::table('statistics')->insert([
                'category_id' => $faker->randomElement($catIds),
                'title' => 'Statistik ' . $faker->year,
                'year' => $faker->year,
                'region_id' => rand(1, 10),
                'value' => rand(10, 1000),
                'unit' => 'orang',
                'source' => $faker->company,
                'status' => 'published',
                'metadata_json' => json_encode(['foo' => 'bar']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Dummy data seeded successfully!');
    }
}
