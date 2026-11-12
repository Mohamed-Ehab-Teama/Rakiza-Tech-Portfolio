<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\Website;
use Illuminate\Database\Seeder;

/**
 * Example data for LOCAL development only (DatabaseSeeder skips it in
 * production): a few sample websites and inbox messages, so every part of
 * the site and the dashboard has something to show. Replace or delete them
 * from the dashboard.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! Website::exists()) {
            $websites = [
                ['Nile Dental Clinic', 'https://example.com/nile-dental', 'Nile Dental', 'Clinic', 'Booking-first website for a dental clinic: services, doctors, and online appointments.', 'live', '2025-09-14'],
                ['Delta Furniture Store', 'https://example.com/delta-furniture', 'Delta Furniture', 'E-commerce', 'Online store with product filters, cash-on-delivery checkout and an order dashboard.', 'live', '2025-03-02'],
                ['Menofia Academy', 'https://example.com/menofia-academy', 'Menofia Academy', 'Education', 'Course catalogue and student portal for a training academy.', 'in_progress', null],
            ];
            foreach ($websites as $i => [$name, $url, $client, $category, $description, $status, $launched]) {
                Website::create([
                    'name' => $name, 'url' => $url, 'client' => $client, 'category' => $category,
                    'description' => $description, 'status' => $status, 'launched_on' => $launched,
                    'is_published' => true, 'sort_order' => $i + 1,
                ]);
            }
        }

        if (! ContactMessage::exists()) {
            ContactMessage::create([
                'name' => 'Sample visitor',
                'email' => 'visitor@example.com',
                'message' => "Hello! We'd like a website for our clinic with online booking. Could you send us a quote?",
                'ip_address' => '127.0.0.1',
            ]);
            ContactMessage::create([
                'name' => 'Another sample',
                'email' => 'owner@example.com',
                'message' => 'Do you also build admin dashboards for existing systems?',
                'ip_address' => '127.0.0.1',
                'read_at' => now(),
            ]);
        }
    }
}
