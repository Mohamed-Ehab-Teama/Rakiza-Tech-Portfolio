<?php

namespace Database\Seeders;

use App\Models\Metric;
use App\Models\Project;
use App\Models\Service;
use App\Models\SkillCategory;
use Illuminate\Database\Seeder;

/**
 * The website's starting content (what the original static site showed):
 * services, projects, the tech stack and the numbers.
 * Each block only runs when its table is empty, so running the seeder again
 * never duplicates or overwrites what was edited in the dashboard.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->services();
        $this->projects();
        $this->stack();
        $this->metrics();
    }

    private function services(): void
    {
        if (Service::exists()) {
            return;
        }

        $services = [
            ['Code', 'Clean, tested, maintainable code — typed, reviewed and built to outlive the sprint it shipped in.', 'code', ['TypeScript', 'React', 'Node', 'Laravel'], true],
            ['Apps', 'Web & product apps, built end to end — from auth to the final pixel.', 'app', ['SPA', 'PWA', 'REST', 'Auth'], false],
            ['Dashboards', 'Analytics & admin dashboards that turn raw data into decisions.', 'chart', ['Charts', 'Realtime', 'Reports'], false],
            ['Databases', 'Modeling, queries & performance — schemas that scale and stay fast.', 'db', ['PostgreSQL', 'MySQL', 'Redis', 'ORM'], false],
            ['CLI & Automation', 'Tooling, scripts & CI/CD — repeatable pipelines that do the boring work.', 'cli', ['Bash', 'Node', 'CI/CD', 'Cron'], false],
        ];

        foreach ($services as $i => [$title, $description, $visual, $tags, $wide]) {
            Service::create([
                'title' => $title,
                'description' => $description,
                'visual' => $visual,
                'tags' => $tags,
                'is_wide' => $wide,
                'is_published' => true,
                'sort_order' => $i + 1,
            ]);
        }
    }

    private function projects(): void
    {
        if (Project::exists()) {
            return;
        }

        $projects = [
            [
                'title' => 'Garage Parking System',
                'role' => 'Parking management platform',
                'year' => 2025,
                'tags' => ['Laravel', 'MySQL', 'Blade', 'Tailwind'],
                'visual' => 'dashboard',
                'summary' => 'A parking management platform for garages: live space availability, check-in and check-out, subscriptions and revenue reports.',
                'description' => "## The challenge\nGarage staff tracked cars, subscriptions and payments on paper, so nobody knew how many spaces were free or how much the garage earned each day.\n\n## What we built\n- A live view of every parking space and its status\n- Fast check-in / check-out with automatic fee calculation\n- Monthly subscriptions for regular customers\n- Daily and monthly revenue reports\n\n## Result\nOne screen now shows the whole garage at a glance, and the end-of-day report takes seconds instead of an hour.",
            ],
            [
                'title' => 'LMS',
                'role' => 'Learning management system',
                'year' => 2024,
                'tags' => ['Laravel', 'MySQL', 'Bootstrap'],
                'visual' => 'store',
                'summary' => 'A learning management system with courses, lessons, quizzes and student progress tracking.',
                'description' => "## The challenge\nAn academy needed one place to publish courses, enroll students and follow their progress.\n\n## What we built\n- Courses with ordered lessons, videos and attachments\n- Quizzes with automatic grading\n- Student dashboards that show progress per course\n- An admin area for instructors\n\n## Result\nInstructors publish a new course in minutes, and students always know what to study next.",
            ],
            [
                'title' => 'Student Observer',
                'role' => 'Student tracking & reports',
                'year' => 2024,
                'tags' => ['Laravel', 'MySQL', 'Chart.js'],
                'visual' => 'kanban',
                'summary' => 'An app for schools to record student observations and turn them into clear progress reports.',
                'description' => "## The challenge\nTeachers wrote observations in notebooks, which made it hard to spot trends or share them with parents.\n\n## What we built\n- Quick observation entry per student and subject\n- Charts that show each student's progress over time\n- Printable reports for parents and supervisors\n\n## Result\nTeachers see at a glance who needs help, and reports are ready without extra work.",
            ],
            [
                'title' => 'DevTools CLI',
                'role' => 'Developer productivity CLI',
                'year' => 2023,
                'tags' => ['PHP', 'Laravel', 'CLI'],
                'visual' => 'terminal',
                'summary' => 'A command-line toolkit that automates the repetitive parts of starting, testing and deploying Laravel projects.',
                'description' => "## The challenge\nEvery new project repeated the same setup, checks and deployment steps by hand.\n\n## What we built\n- One-command project scaffolding with our conventions\n- Lint, test and build pipelines in a single command\n- Deployment helpers for staging and production\n\n## Result\nSetting up and shipping a project became faster and far less error-prone.",
            ],
            [
                'title' => 'Fleet Tracker',
                'role' => 'Realtime fleet tracking',
                'year' => 2023,
                'tags' => ['Laravel', 'WebSocket', 'MySQL'],
                'visual' => 'map',
                'summary' => 'Real-time tracking of delivery vehicles on a live map, with trip history and alerts.',
                'description' => "## The challenge\nA delivery company could not see where its vehicles were or how long trips really took.\n\n## What we built\n- A live map of every vehicle, updated in real time over WebSockets\n- Trip history with distance and duration\n- Alerts for long stops and late deliveries\n\n## Result\nDispatchers plan routes with real data and customers get accurate delivery times.",
            ],
        ];

        foreach ($projects as $i => $project) {
            Project::create($project + [
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => $i + 1,
            ]);
        }
    }

    private function stack(): void
    {
        if (SkillCategory::exists()) {
            return;
        }

        // [name, level (bar) or null, show in card, marquee row (0 none, 1 top, 2 bottom)]
        $categories = [
            ['Frontend', 'UI / UX', 'frontend', [
                ['JavaScript', null, true, 1], ['TypeScript', 90, true, 1], ['React', 95, true, 1],
                ['Vue', null, true, 1], ['Next.js', null, true, 1], ['Tailwind', null, true, 2],
                ['GSAP', null, true, 2], ['HTML/CSS', null, true, 0], ['Figma', null, false, 2],
            ]],
            ['Backend', 'API / logic', 'backend', [
                ['Node.js', 92, true, 1], ['Express', null, true, 1], ['NestJS', null, true, 1],
                ['PHP', null, true, 1], ['Laravel', 88, true, 1], ['Python', null, true, 1],
                ['REST', null, true, 0], ['GraphQL', null, true, 2],
            ]],
            ['Database', 'data / state', 'database', [
                ['PostgreSQL', 90, true, 1], ['MySQL', null, true, 1], ['MongoDB', null, true, 1],
                ['Redis', 82, true, 1], ['Prisma', null, true, 2], ['SQL', null, true, 0],
            ]],
            ['DevOps & Tools', 'ship / run', 'devops', [
                ['Docker', 85, true, 2], ['Git', null, true, 2], ['CI/CD', 80, true, 2],
                ['Linux', null, true, 2], ['AWS', null, true, 2], ['Vercel', null, true, 2],
                ['Nginx', null, true, 2], ['Bash', null, true, 2],
            ]],
        ];

        foreach ($categories as $i => [$name, $meta, $icon, $skills]) {
            $category = SkillCategory::create([
                'name' => $name, 'meta' => $meta, 'icon' => $icon,
                'is_published' => true, 'sort_order' => $i + 1,
            ]);
            foreach ($skills as $j => [$skill, $level, $inCard, $row]) {
                $category->skills()->create([
                    'name' => $skill, 'level' => $level, 'show_in_card' => $inCard,
                    'marquee_row' => $row, 'is_published' => true, 'sort_order' => $j + 1,
                ]);
            }
        }
    }

    private function metrics(): void
    {
        if (Metric::exists()) {
            return;
        }

        $highlights = [
            ['Years experience', 'years', 6],
            ['Projects shipped', 'projects', 80],
            ['Happy clients', 'clients', 40],
            ['Commits pushed', 'commits', 12400],
        ];
        foreach ($highlights as $i => [$label, $short, $value]) {
            Metric::create([
                'group' => 'highlight', 'label' => $label, 'short_label' => $short,
                'value' => $value, 'decimals' => 0, 'suffix' => '+',
                'is_published' => true, 'sort_order' => $i + 1,
            ]);
        }

        $dashboard = [
            ['Deploys', 1240, 0, '', 'YTD', '+12.4% this month', 'accent'],
            ['Uptime', 99.98, 2, '%', '30d', '+0.03% this month', 'term'],
            ['Projects', 80, 0, '+', 'ALL', '+3 this month', 'violet'],
            ['Coffees', 9800, 0, '', '∞', '+7.1% this month', 'amber'],
        ];
        foreach ($dashboard as $i => [$label, $value, $decimals, $suffix, $badge, $note, $color]) {
            Metric::create([
                'group' => 'dashboard', 'label' => $label, 'value' => $value,
                'decimals' => $decimals, 'suffix' => $suffix ?: null, 'badge' => $badge,
                'note' => $note, 'trend' => 'up', 'color' => $color,
                'is_published' => true, 'sort_order' => 10 + $i,
            ]);
        }
    }
}
