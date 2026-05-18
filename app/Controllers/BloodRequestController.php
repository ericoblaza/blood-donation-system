<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\BloodRequestRepositoryInterface;
use App\Models\BloodRequestResponse;
use Core\Auth;
use Core\Http\Request;
use Core\Http\Response;
use Core\View\Engine;

class BloodRequestController
{
    public function __construct(
        private readonly BloodRequestRepositoryInterface $bloodRequests,
    ) {
    }

    public function requesterResponses(Request $request): void
{
    Auth::requireUser();

    $requesterUserId = (int) ($_SESSION['user']['id'] ?? 0);

    $responses = BloodRequestResponse::findForRequester($requesterUserId);

    (new Engine())->render('requests/requester_responses', ['responses' => $responses]);
}


    public function history(Request $request): void
{
    Auth::requireUser();

    $userId = (int) ($_SESSION['user']['id'] ?? 0);

    $requests = $this->bloodRequests->findAllByRequester($userId);

    (new Engine())->render('requests/history', ['requests' => $requests]);
}

    public function show(Request $request): void
    {
        Auth::requireUser();

        $id = (int) ($request->route('id') ?? '0');
        if ($id < 1) {
            (new Response())->redirect(app_url('/requests'));
            exit;
        }

        $bloodRequest = $this->bloodRequests->findById($id);

        if ($bloodRequest === null) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        (new Engine())->render('requests/show', ['bloodRequest' => $bloodRequest]);
    }

    public function index(Request $request): void
    {
        Auth::requireUser();
    
        $requests = $this->bloodRequests->findAllOpen();
    
        $currentUserId = (int) ($_SESSION['user']['id'] ?? 0);
        $myResponses = BloodRequestResponse::findAllByDonor($currentUserId);
    
        (new Engine())->render('requests/index', [
            'requests' => $requests,
            'myResponses' => $myResponses,
            'currentUserId' => $currentUserId,
        ]);
    }

    public function showCreate(Request $request): void
    {
        Auth::requireUser();

        $errors = [];
        $old = [
            'blood_type' => '',
            'city' => '',
            'units' => '1',
            'notes' => '',
            'contact_name' => '',
            'contact_phone' => '',
        ];

        (new Engine())->render('requests/create', compact('errors', 'old'));
    }

    public function store(Request $request): void
    {
        Auth::requireUser();

        [$errors, $old] = $this->validatedRequestInput($request);

        if ($errors !== []) {
            (new Engine())->render('requests/create', compact('errors', 'old'));
            return;
        }

        $userId = (int) ($_SESSION['user']['id'] ?? 0);

        $this->bloodRequests->create(
            $userId,
            (string) $old['blood_type'],
            (string) $old['city'],
            (int) $old['units'],
            $old['notes'] === '' ? null : (string) $old['notes'],
            (string) $old['contact_name'],
            (string) $old['contact_phone']
        );

        (new Response())->redirect(app_url('/requests'));
        exit;
    }
    public function accept(Request $request): void
{
    $this->saveDecision($request, 'accept');
}

    public function decline(Request $request): void
    {
        $this->saveDecision($request, 'decline');
    }

    private function saveDecision(Request $request, string $decision): void
    {
        Auth::requireUser();
    
        $requestId = (int) $request->input('request_id', 0);
        $donorUserId = (int) ($_SESSION['user']['id'] ?? 0);
    
        if ($requestId < 1 || !in_array($decision, ['accept', 'decline'], true)) {
            (new Response())->redirect(app_url('/requests'));
            exit;
        }
    
        $requestRow = $this->bloodRequests->findById($requestId);

        // Request does not exist
        if ($requestRow === null) {
            (new Response())->redirect(app_url('/requests'));
            exit;
        }

        if ((string) $requestRow['status'] !== 'open') {
            (new Response())->redirect(app_url('/requests'));
            exit;
        }
    
        // Request owner cannot respond to own request
        if ((int) $requestRow['requester_user_id'] === $donorUserId) {
            (new Response())->redirect(app_url('/requests'));
            exit;
        }
    
        BloodRequestResponse::upsertDecision($requestId, $donorUserId, $decision);

        if ($decision === 'accept') {
            $this->bloodRequests->updateStatus($requestId, 'fulfilled');
        }
    
        (new Response())->redirect(app_url('/requests'));
        exit;
    }

    public function showEdit(Request $request): void
    {
        Auth::requireUser();

        $bloodRequest = $this->findOwnedOpenRequest($request);
        if ($bloodRequest === null) {
            return;
        }

        $errors = [];
        $old = [
            'blood_type' => (string) $bloodRequest['blood_type'],
            'city' => (string) $bloodRequest['city'],
            'units' => (string) $bloodRequest['units'],
            'notes' => (string) ($bloodRequest['notes'] ?? ''),
            'contact_name' => (string) ($bloodRequest['contact_name'] ?? ''),
            'contact_phone' => (string) ($bloodRequest['contact_phone'] ?? ''),
        ];

        (new Engine())->render('requests/edit', [
            'bloodRequest' => $bloodRequest,
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function update(Request $request): void
    {
        Auth::requireUser();

        $bloodRequest = $this->findOwnedOpenRequest($request);
        if ($bloodRequest === null) {
            return;
        }

        $requestId = (int) $bloodRequest['id'];
        [$errors, $old] = $this->validatedRequestInput($request);

        if ($errors !== []) {
            (new Engine())->render('requests/edit', [
                'bloodRequest' => $bloodRequest,
                'errors' => $errors,
                'old' => $old,
            ]);
            return;
        }

        $this->bloodRequests->update(
            $requestId,
            (string) $old['blood_type'],
            (string) $old['city'],
            (int) $old['units'],
            $old['notes'] === '' ? null : (string) $old['notes'],
            (string) $old['contact_name'],
            (string) $old['contact_phone']
        );

        (new Response())->redirect(app_url('/requests/history'));
        exit;
    }

    public function destroy(Request $request): void
    {
        Auth::requireUser();

        $bloodRequest = $this->findOwnedOpenRequest($request);
        if ($bloodRequest === null) {
            return;
        }

        $this->bloodRequests->deleteById((int) $bloodRequest['id']);

        (new Response())->redirect(app_url('/requests/history'));
        exit;
    }

    private function findOwnedOpenRequest(Request $request): ?array
    {
        $id = (int) ($request->route('id') ?? '0');
        if ($id < 1) {
            (new Response())->redirect(app_url('/requests/history'));
            exit;
        }

        $bloodRequest = $this->bloodRequests->findById($id);
        $userId = (int) ($_SESSION['user']['id'] ?? 0);

        if (
            $bloodRequest === null
            || (int) $bloodRequest['requester_user_id'] !== $userId
            || (string) $bloodRequest['status'] !== 'open'
        ) {
            (new Response())->redirect(app_url('/requests/history'));
            exit;
        }

        return $bloodRequest;
    }

    /**
     * @return array{0: array<string, string>, 1: array<string, string>}
     */
    private function validatedRequestInput(Request $request): array
    {
        $bloodType = strtoupper(trim((string) $request->input('blood_type', '')));
        $city = trim((string) $request->input('city', ''));
        $units = (int) $request->input('units', 1);
        $notes = trim((string) $request->input('notes', ''));
        $contactName = trim((string) $request->input('contact_name', ''));
        $contactPhone = trim((string) $request->input('contact_phone', ''));

        $allowedBloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $errors = [];

        if (!in_array($bloodType, $allowedBloodTypes, true)) {
            $errors['blood_type'] = 'Please choose a valid blood type.';
        }

        if ($city === '') {
            $errors['city'] = 'Please enter city.';
        }

        if ($units < 1) {
            $errors['units'] = 'Units must be at least 1.';
        }

        if ($contactName === '') {
            $errors['contact_name'] = 'Please enter a contact name.';
        }

        if ($contactPhone === '') {
            $errors['contact_phone'] = 'Please enter a contact phone number.';
        }

        $old = [
            'blood_type' => $bloodType,
            'city' => $city,
            'units' => (string) max(1, $units),
            'notes' => $notes,
            'contact_name' => $contactName,
            'contact_phone' => $contactPhone,
        ];

        return [$errors, $old];
    }
}