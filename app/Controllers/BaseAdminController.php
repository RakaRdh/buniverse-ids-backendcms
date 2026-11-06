<?php

namespace App\Controllers;

use App\Models\EventModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseAdminController extends Controller
{
    protected ?array $currentAdmin = null;
    protected ?array $activeEvent = null;
    protected array $allEvents = [];
    protected array $data = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $session = session();
        $this->currentAdmin = [
            'id'    => $session->get('admin_id'),
            'name'  => $session->get('admin_name') ?? 'Admin',
            'email' => $session->get('admin_email'),
            'role'  => $session->get('admin_role') ?? 'editor',
        ];

        $eventModel = new EventModel();
        $this->allEvents = $eventModel->orderBy('year', 'DESC')->findAll();

        // Role MAP Admin is strictly isolated to Malam Apresiasi (id: 4 or slug: malam-apresiasi-2026)
        if (($this->currentAdmin['role'] ?? '') === 'map_admin') {
            $mapEvent = $eventModel->where('slug', 'malam-apresiasi-2026')->first() ?: $eventModel->find(4);
            if ($mapEvent) {
                $this->activeEvent = $mapEvent;
                $session->set('active_event_id', $mapEvent['id']);
            }
            $this->allEvents = $mapEvent ? [$mapEvent] : [];
        } else {
            $selectedEventId = $session->get('active_event_id');
            if ($selectedEventId) {
                $this->activeEvent = $eventModel->find($selectedEventId);
            }

            if (!$this->activeEvent && !empty($this->allEvents)) {
                $this->activeEvent = $eventModel->getActiveEvent();
                if ($this->activeEvent) {
                    $session->set('active_event_id', $this->activeEvent['id']);
                }
            }
        }

        $this->data = [
            'currentAdmin' => $this->currentAdmin,
            'activeEvent'  => $this->activeEvent,
            'frontendBaseUrl' => $this->getFrontendBaseUrl(),
            'allEvents'    => $this->allEvents,
            'pageTitle'    => 'Dashboard',
            'activeMenu'   => 'dashboard',
        ];
    }

    /**
     * Get the corresponding Frontend base URL for the active event
     */
    protected function getFrontendBaseUrl(?array $event = null): string
    {
        $event = $event ?? $this->activeEvent;
        $slug  = strtolower($event['slug'] ?? '');
        $title = strtolower($event['title'] ?? '');

        // 1. ESG (Investor Daily ESG 2026) -> Port 8083
        if (str_contains($slug, 'esg') || str_contains($title, 'esg')) {
            return env('app.frontendEsgURL', 'http://localhost:8083');
        }

        // 2. Malam Apresiasi 2026 -> Port 8082
        if (str_contains($slug, 'malam-apresiasi') || str_contains($slug, 'map') || str_contains($title, 'malam apresiasi')) {
            return env('app.frontendMapURL', 'http://localhost:8082');
        }

        // 3. Energi & Mineral Festival -> Port 8084
        if (str_contains($slug, 'emf') || str_contains($title, 'energi mineral') || str_contains($title, 'emf')) {
            return env('app.frontendEmfURL', 'http://localhost:8084');
        }

        // 4. Investor Daily Summit -> Port 8080
        if (str_contains($slug, 'ids') || str_contains($title, 'summit') || str_contains($title, 'ids')) {
            return env('app.frontendIdsURL', 'http://localhost:8080');
        }

        return env('app.frontendMapURL', 'http://localhost:8082');
    }

    protected function render(string $view, array $data = []): string
    {
        $mergedData = array_merge($this->data, $data);
        $mergedData['content'] = view($view, $mergedData);
        return view('admin/layout/main', $mergedData);
    }

    /**
     * Invalidate Frontend live cache so CMS changes appear immediately
     */
    protected function purgeFrontendCache(): void
    {
        $frontendUrls = [
            'http://localhost:8000/api/clear-cache',
            'http://127.0.0.1:8000/api/clear-cache',
            'http://localhost:8080/api/clear-cache',
            'http://127.0.0.1:8080/api/clear-cache',
            'http://[::1]:8080/api/clear-cache',
            'http://localhost:8082/api/clear-cache',
            'http://127.0.0.1:8082/api/clear-cache',
            'http://localhost:8083/api/clear-cache',
            'http://127.0.0.1:8083/api/clear-cache',
        ];

        foreach ($frontendUrls as $url) {
            try {
                $client = \Config\Services::curlrequest(['timeout' => 0.8]);
                $client->get($url, ['http_errors' => false]);
                break;
            } catch (\Throwable $e) {
                // Continue fallback
            }
        }
    }
}
