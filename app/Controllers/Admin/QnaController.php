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

        $sessionBuilder = $sessionModel
            ->select('schedule_sessions.*')
            ->join('schedule_days', 'schedule_days.id = schedule_sessions.schedule_day_id')
            ->where('schedule_days.event_id', $eventId);

        if (!empty($filterDay)) {
            $sessionBuilder->where('schedule_sessions.schedule_day_id', $filterDay);
        }

        $this->data['questions']     = $qnaModel->getQuestions($eventId, $filterDay, $filterSession);
        $this->data['days']          = $dayModel->getByEvent($eventId);
        $this->data['sessions']      = $sessionBuilder->orderBy('schedule_sessions.start_time', 'ASC')->findAll();
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
