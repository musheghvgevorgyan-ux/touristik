<?php

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Database;
use App\Models\Setting;
use App\Services\ActivityService;
use App\Helpers\Flash;

class SettingsController extends Controller
{
    /**
     * GET /admin/settings
     *
     * Show all settings in an editable form.
     */
    public function index(): void
    {
        $settings = Setting::all(500);

        // Index by key for easier template access
        $settingsMap = [];
        foreach ($settings as $setting) {
            $settingsMap[$setting['key'] ?? $setting['name'] ?? ''] = $setting;
        }

        $this->view('admin.settings.index', [
            'title'       => 'Settings — Admin',
            'settings'    => $settings,
            'settingsMap' => $settingsMap,
        ]);
    }

    /**
     * POST /admin/settings
     *
     * Bulk update settings from the form.
     * Expects POST data as settings[key] = value pairs.
     */
    public function update(): void
    {
        $db = Database::getInstance();

        $submitted = $this->request->post('settings', []);

        if (!is_array($submitted) || empty($submitted)) {
            Flash::error('No settings data received.');
            $this->redirect('/admin/settings');
            return;
        }

        // Load current values for comparison
        $currentSettings = Setting::all(500);
        $currentMap = [];
        foreach ($currentSettings as $s) {
            $keyCol = $s['key'] ?? $s['name'] ?? '';
            $currentMap[$keyCol] = $s;
        }

        $changes  = [];
        $updated  = 0;

        foreach ($submitted as $key => $value) {
            $key   = trim((string) $key);
            $value = trim((string) $value);

            if ($key === '') {
                continue;
            }

            if (isset($currentMap[$key])) {
                $old = $currentMap[$key]['value'] ?? '';
                if ($old !== $value) {
                    // Update existing setting
                    $db->query(
                        "UPDATE settings SET value = ?, updated_at = NOW()
                         WHERE id = ?",
                        [$value, $currentMap[$key]['id']]
                    );
                    $changes[$key] = ['from' => $old, 'to' => $value];
                    $updated++;
                }
            } else {
                // Insert new setting
                $db->query(
                    "INSERT INTO settings (`key`, `value`, created_at, updated_at)
                     VALUES (?, ?, NOW(), NOW())",
                    [$key, $value]
                );
                $changes[$key] = ['from' => null, 'to' => $value];
                $updated++;
            }
        }

        if ($updated > 0) {
            ActivityService::log('settings.updated', 'settings', null, $changes);
            Flash::success("{$updated} setting(s) updated successfully.");
        } else {
            Flash::info('No settings were changed.');
        }

        $this->redirect('/admin/settings');
    }
}
