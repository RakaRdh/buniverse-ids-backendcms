<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\EventThemePropertyModel;

class ColorSchemaController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Please select an event first.');
        }

        $themeModel = new EventThemePropertyModel();
        $grouped = $themeModel->getGroupedByEvent($eventId);
        $palette = $themeModel->getPaletteForPicker($eventId);

        $this->data['groupedProperties'] = $grouped;
        $this->data['palettePicker']     = $palette;
        $this->data['pageTitle']         = 'Color Schema & CSS Variables';
        $this->data['activeMenu']        = 'color_schema';

        return $this->render('admin/color_schema/index', $this->data);
    }

    public function update()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $themeModel = new EventThemePropertyModel();
        $userId = $this->currentAdmin['id'] ?? session()->get('admin_id') ?? session()->get('admin_user')['id'] ?? 1;

        $properties = $this->request->getPost('properties');
        if (is_array($properties)) {
            foreach ($properties as $id => $data) {
                $id = (int)$id;
                $row = $themeModel->where('id', $id)->where('event_id', $eventId)->first();
                if (!$row) {
                    continue;
                }

                $cssVal = trim($data['value'] ?? '');
                $valType = $data['type'] ?? 'solid';

                $themeModel->update($id, [
                    'css_value'  => $cssVal,
                    'value_type' => $valType,
                    'updated_by' => $userId,
                ]);

                // Sinkronkan --primary-1-N dengan alias warna di kategori primary_1 (misal --yellow-N, --green-N, --gold-N)
                $propName = $row['css_property'] ?? '';
                if (preg_match('/^--primary-1-(\d+)$/', $propName, $m)) {
                    $step = (int)$m[1];
                    $themeModel->where('event_id', $eventId)
                        ->whereIn('category', ['primary_1', 'primary-1'])
                        ->where('id !=', $id)
                        ->where("css_property REGEXP '^--[a-zA-Z]+-{$step}$'", null, false)
                        ->set([
                            'css_value'  => $cssVal,
                            'value_type' => $valType,
                            'updated_by' => $userId,
                        ])
                        ->update();
                }
            }
        }

        $this->purgeFrontendCache();

        return redirect()->to(base_url('admin/color-schema'))->with('success', 'Color schema and theme variables updated successfully.');
    }
}
