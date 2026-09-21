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
                $cssVal = trim($data['value'] ?? '');
                $valType = $data['type'] ?? 'solid';

                $themeModel->update((int)$id, [
                    'css_value'  => $cssVal,
                    'value_type' => $valType,
                    'updated_by' => $userId,
                ]);
            }
        }

        return redirect()->to(base_url('admin/color-schema'))->with('success', 'Color schema and theme variables updated successfully.');
    }
}
