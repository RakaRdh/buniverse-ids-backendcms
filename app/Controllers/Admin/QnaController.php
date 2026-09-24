<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseAdminController;
use App\Models\QnaModel;
use App\Models\ScheduleDayModel;
use App\Models\ScheduleSessionModel;

class QnaController extends BaseAdminController
{
    public function index()
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->to(base_url('admin/events'))->with('error', 'Select an event first.');
        }

        $qnaModel = new QnaModel();
        $dayModel = new ScheduleDayModel();
        $sessionModel = new ScheduleSessionModel();

        $filterDay     = $this->request->getGet('day_id') ? (int)$this->request->getGet('day_id') : null;
        $filterSession = $this->request->getGet('session_id') ? (int)$this->request->getGet('session_id') : null;

        $this->data['questions']     = $qnaModel->getQuestions($eventId, $filterDay, $filterSession);
        $this->data['days']          = $dayModel->getByEvent($eventId);
        $this->data['sessions']      = $sessionModel->where('event_id', $eventId)->orWhere('schedule_day_id IN (SELECT id FROM schedule_days WHERE event_id = ' . (int)$eventId . ')')->orderBy('start_time', 'ASC')->findAll();
        $this->data['filterDay']     = $filterDay;
        $this->data['filterSession'] = $filterSession;
        $this->data['pageTitle']     = 'Q&A Questions';
        $this->data['activeMenu']    = 'qna';

        return $this->render('admin/qna/index', $this->data);
    }

    public function delete($id)
    {
        $eventId = $this->activeEvent['id'] ?? null;
        if (!$eventId) {
            return redirect()->back()->with('error', 'No active event.');
        }

        $qnaModel = new QnaModel();
        $question = $qnaModel->where('id', (int)$id)->where('event_id', $eventId)->first();

        if (!$question) {
            return redirect()->to(base_url('admin/qna'))->with('error', 'Question not found.');
        }

        $qnaModel->delete($id);
        return redirect()->to(base_url('admin/qna'))->with('success', 'Question deleted successfully.');
    }
}
