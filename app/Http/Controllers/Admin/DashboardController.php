<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Website;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The dashboard home: counts, newest messages and recent activity.
 * Each block only appears when the user may see that area.
 */
class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [];
        $areas = [
            'projects' => [Project::class, 'Projects', 'admin.projects.index'],
            'websites' => [Website::class, 'Websites', 'admin.websites.index'],
            'services' => [Service::class, 'Services', 'admin.services.index'],
            'skills' => [Skill::class, 'Skills', 'admin.skills.index'],
        ];
        foreach ($areas as $area => [$model, $label, $route]) {
            if (Gate::allows("{$area}.view")) {
                $stats[] = [
                    'label' => $label,
                    'total' => $model::count(),
                    'published' => $model::published()->count(),
                    'route' => $route,
                ];
            }
        }

        $canMessages = Gate::allows('messages.view');

        return view('admin.dashboard', [
            'stats' => $stats,
            'unread' => $canMessages ? ContactMessage::unread()->count() : null,
            'messages' => $canMessages ? ContactMessage::latest()->take(5)->get() : collect(),
            'activity' => Gate::allows('activity.view')
                ? ActivityLog::with('user')->latest()->take(8)->get()
                : collect(),
        ]);
    }
}
