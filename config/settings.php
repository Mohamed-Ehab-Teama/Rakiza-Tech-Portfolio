<?php

/*
|--------------------------------------------------------------------------
| Site settings (Dashboard → Settings)
|--------------------------------------------------------------------------
| Every editable setting: its group (a tab in the dashboard), label, field
| type, default value and validation rules. The settings form is built from
| this list, and setting('key') returns the saved value or the default here.
|
| Field types:
|   text, email, url   — one-line input
|   textarea           — several lines of plain text
|   markdown           — textarea that allows **bold**, *italic* and [links](https://…)
|   lines              — textarea, one item per line (read back as an array)
|   select             — dropdown; "options" => [value => label] or "options_from" => "themes"
|   boolean            — on/off switch
|   image              — file upload (stored in storage/app/public/settings)
|
| To add a setting: add it here, then use setting('your_key') in a view.
*/

return [

    'groups' => [

        'general' => [
            'label' => 'General',
            'description' => 'Company name, tagline and the default look of the site.',
            'fields' => [
                'site_name' => [
                    'label' => 'Company name', 'type' => 'text', 'default' => 'Rakiza Tech',
                    'rules' => ['required', 'string', 'max:60'],
                ],
                'site_name_ar' => [
                    'label' => 'Company name in Arabic', 'type' => 'text', 'default' => 'ركيزة تك',
                    'rules' => ['nullable', 'string', 'max:60'],
                    'help' => 'The animated name in the navbar alternates between both names.',
                ],
                'tagline' => [
                    'label' => 'Tagline', 'type' => 'text', 'default' => 'Digital Solutions: Websites, Systems & Apps',
                    'rules' => ['required', 'string', 'max:120'],
                    'help' => 'Shown in the browser tab title next to the company name.',
                ],
                'availability' => [
                    'label' => 'Availability status', 'type' => 'text', 'default' => 'Available for new projects',
                    'rules' => ['nullable', 'string', 'max:60'],
                    'help' => 'The small green status next to the hero and in the footer. Leave empty to hide it.',
                ],
                'location' => [
                    'label' => 'Location', 'type' => 'text', 'default' => 'Cairo / Menofia, Egypt',
                    'rules' => ['nullable', 'string', 'max:80'],
                ],
                'default_theme' => [
                    'label' => 'Default color theme', 'type' => 'select', 'options_from' => 'themes', 'default' => 'system',
                    'rules' => ['required', 'string'],
                    'help' => 'What first-time visitors see. Visitors can still pick their own theme from the menu.',
                ],
            ],
        ],

        'contact' => [
            'label' => 'Contact & social',
            'description' => 'How people reach you. Empty social links are hidden on the site.',
            'fields' => [
                'contact_email' => [
                    'label' => 'Main email', 'type' => 'email', 'default' => 'mohamed111.me111@gmail.com',
                    'rules' => ['required', 'email', 'max:120'],
                ],
                'contact_email_alt' => [
                    'label' => 'Second email', 'type' => 'email', 'default' => 'katchan111.me111@gmail.com',
                    'rules' => ['nullable', 'email', 'max:120'],
                ],
                'contact_phone' => [
                    'label' => 'Phone', 'type' => 'text', 'default' => '+20 109 016 6589',
                    'rules' => ['nullable', 'string', 'max:40'],
                ],
                'notify_new_messages' => [
                    'label' => 'Email me when a contact message arrives', 'type' => 'boolean', 'default' => true,
                    'rules' => ['boolean'],
                    'help' => 'Sent to the main email. Needs a working mail server (MAIL_* in .env).',
                ],
                'github_url' => [
                    'label' => 'GitHub', 'type' => 'url', 'default' => 'https://github.com/Mohamed-Ehab-Teama',
                    'rules' => ['nullable', 'url', 'max:255'],
                ],
                'linkedin_url' => [
                    'label' => 'LinkedIn', 'type' => 'url', 'default' => 'https://www.linkedin.com/in/mohamed-ehab-teama-267154250/',
                    'rules' => ['nullable', 'url', 'max:255'],
                ],
                'x_url' => [
                    'label' => 'X (Twitter)', 'type' => 'url', 'default' => '',
                    'rules' => ['nullable', 'url', 'max:255'],
                ],
                'facebook_url' => [
                    'label' => 'Facebook', 'type' => 'url', 'default' => '',
                    'rules' => ['nullable', 'url', 'max:255'],
                ],
                'instagram_url' => [
                    'label' => 'Instagram', 'type' => 'url', 'default' => '',
                    'rules' => ['nullable', 'url', 'max:255'],
                ],
            ],
        ],

        'home' => [
            'label' => 'Home page texts',
            'description' => 'Headings and paragraphs of the home page sections.',
            'fields' => [
                'hero_title' => [
                    'label' => 'Hero — headline', 'type' => 'text', 'default' => 'Code. Apps. Data.',
                    'rules' => ['required', 'string', 'max:60'],
                ],
                'hero_title_highlight' => [
                    'label' => 'Hero — highlighted word', 'type' => 'text', 'default' => 'Shipped.',
                    'rules' => ['nullable', 'string', 'max:30'],
                    'help' => 'Shown on its own line under the headline, in the gradient color.',
                ],
                'hero_badge' => [
                    'label' => 'Hero — small label', 'type' => 'text', 'default' => 'RAKIZA TECH — DIGITAL SOLUTIONS',
                    'rules' => ['nullable', 'string', 'max:80'],
                ],
                'hero_lead' => [
                    'label' => 'Hero — intro sentence', 'type' => 'textarea',
                    'default' => 'We build the systems behind the screens — designing the database, the APIs and the logic underneath, then the screens on top.',
                    'rules' => ['required', 'string', 'max:300'],
                ],
                'hero_phrases' => [
                    'label' => 'Hero — typed phrases', 'type' => 'lines',
                    'default' => "building Laravel REST APIs\ndesigning MySQL schemas\nshipping full-stack web apps\nwriting clean, testable PHP\nautomating with artisan & CLIs",
                    'rules' => ['required', 'string', 'max:1000'],
                    'help' => 'One phrase per line. They are typed one after another in the hero.',
                ],
                'about_title' => [
                    'label' => 'About — heading', 'type' => 'text', 'default' => 'Engineers who treat the whole stack as one craft.',
                    'rules' => ['required', 'string', 'max:120'],
                ],
                'about_body' => [
                    'label' => 'About — text', 'type' => 'markdown',
                    'default' => "**Rakiza Tech** is a digital solutions company from Egypt. We build websites, systems and apps end to end — designing the database, the APIs and the logic underneath, then the screens on top.\n\nWe've shipped real systems including a garage parking platform, a learning-management system and a student-observation app. We care about clean, maintainable code and data models that stay fast as they grow — a solid foundation, which is exactly what *rakiza* (ركيزة) means.",
                    'rules' => ['required', 'string', 'max:3000'],
                    'help' => 'Leave an empty line between paragraphs. **bold** and *italic* are allowed.',
                ],
                'services_title' => [
                    'label' => 'Services — heading', 'type' => 'text', 'default' => 'What we build, end to end.',
                    'rules' => ['required', 'string', 'max:120'],
                ],
                'services_intro' => [
                    'label' => 'Services — intro', 'type' => 'textarea',
                    'default' => 'From the first line of code to the database under it and the pipeline that ships it — the disciplines we bring to every product.',
                    'rules' => ['nullable', 'string', 'max:400'],
                ],
                'work_title' => [
                    'label' => 'Work — heading', 'type' => 'text', 'default' => 'Selected work.',
                    'rules' => ['required', 'string', 'max:120'],
                ],
                'work_intro' => [
                    'label' => 'Work — intro', 'type' => 'textarea',
                    'default' => 'A focused slice of recent builds — analytics platforms, commerce engines, internal tools and developer tooling. Each shipped end to end, from schema to pixel.',
                    'rules' => ['nullable', 'string', 'max:400'],
                ],
                'websites_title' => [
                    'label' => 'Websites — heading', 'type' => 'text', 'default' => 'Websites we launched.',
                    'rules' => ['required', 'string', 'max:120'],
                ],
                'websites_intro' => [
                    'label' => 'Websites — intro', 'type' => 'textarea',
                    'default' => 'Live sites we designed, built and shipped for our clients — fast, responsive and easy for them to run.',
                    'rules' => ['nullable', 'string', 'max:400'],
                ],
                'metrics_title' => [
                    'label' => 'Numbers dashboard — heading', 'type' => 'text', 'default' => 'By the numbers.',
                    'rules' => ['required', 'string', 'max:120'],
                ],
                'metrics_intro' => [
                    'label' => 'Numbers dashboard — intro', 'type' => 'textarea',
                    'default' => 'A live-feeling snapshot of the work — deploys shipped, uptime held, and the habits behind them. Real instrumentation, rendered like the dashboards we build.',
                    'rules' => ['nullable', 'string', 'max:400'],
                ],
                'stack_title' => [
                    'label' => 'Stack — heading', 'type' => 'text', 'default' => 'Our toolkit.',
                    'rules' => ['required', 'string', 'max:120'],
                ],
                'stack_intro' => [
                    'label' => 'Stack — intro', 'type' => 'textarea',
                    'default' => 'The languages, frameworks and infrastructure we reach for — chosen for the job, not the hype, and battle-tested across products in production.',
                    'rules' => ['nullable', 'string', 'max:400'],
                ],
                'contact_title' => [
                    'label' => 'Contact — heading', 'type' => 'text', 'default' => "Let's build something.",
                    'rules' => ['required', 'string', 'max:120'],
                ],
                'contact_intro' => [
                    'label' => 'Contact — intro', 'type' => 'textarea',
                    'default' => "Have a product, a dashboard, an API or a tool that needs building? Tell us what you're working on — we read every message and reply personally.",
                    'rules' => ['nullable', 'string', 'max:400'],
                ],
                'footer_note' => [
                    'label' => 'Footer — note under "Let\'s talk"', 'type' => 'text',
                    'default' => 'Taking on new websites, systems and apps — usually replying within a day.',
                    'rules' => ['nullable', 'string', 'max:160'],
                ],
                'footer_tagline' => [
                    'label' => 'Footer — about line', 'type' => 'textarea',
                    'default' => 'A digital solutions company building websites, systems and apps — and the solid foundations behind the screens.',
                    'rules' => ['nullable', 'string', 'max:300'],
                ],
            ],
        ],

        'seo' => [
            'label' => 'SEO',
            'description' => 'How the site appears in Google results and when shared on social media.',
            'fields' => [
                'seo_title' => [
                    'label' => 'Page title', 'type' => 'text', 'default' => '',
                    'rules' => ['nullable', 'string', 'max:70'],
                    'help' => 'Leave empty to use "Company name — Tagline". Best under 60 characters.',
                ],
                'seo_description' => [
                    'label' => 'Description', 'type' => 'textarea',
                    'default' => 'Rakiza Tech is a digital solutions company from Egypt. We build websites, systems and apps end to end: databases, APIs, and the screens on top.',
                    'rules' => ['required', 'string', 'max:300'],
                    'help' => 'The grey text under the title in Google. Best between 120 and 160 characters.',
                ],
                'og_image' => [
                    'label' => 'Social sharing image', 'type' => 'image', 'default' => '',
                    'rules' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                    'help' => 'Shown when a link to the site is shared (1200 × 630 px is ideal). Empty = the built-in Rakiza Tech image.',
                ],
                'google_site_verification' => [
                    'label' => 'Google Search Console verification code', 'type' => 'text', 'default' => '',
                    'rules' => ['nullable', 'string', 'max:120'],
                    'help' => 'Only the content="…" value of the meta tag Google gives you.',
                ],
                'google_analytics_id' => [
                    'label' => 'Google Analytics measurement ID', 'type' => 'text', 'default' => '',
                    'rules' => ['nullable', 'string', 'regex:/^G-[A-Z0-9]+$/', 'max:30'],
                    'help' => 'Looks like G-XXXXXXXXXX. Leave empty to disable analytics.',
                ],
            ],
        ],

    ],

];
