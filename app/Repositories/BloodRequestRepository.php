<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\BloodRequestRepositoryInterface;
use App\Models\BloodRequest;

class BloodRequestRepository implements BloodRequestRepositoryInterface
{
    public function create(
        int $requesterUserId,
        string $bloodType,
        string $city,
        int $units,
        ?string $notes,
        string $contactName,
        string $contactPhone
    ): void {
        BloodRequest::createRequest(
            $requesterUserId,
            $bloodType,
            $city,
            $units,
            $notes,
            $contactName,
            $contactPhone
        );
    }

    public function findAllOpen(): array
    {
        return BloodRequest::findAllOpen();
    }

    public function findById(int $id): ?array
    {
        return BloodRequest::findRequestById($id);
    }

    public function findAllByRequester(int $userId): array
    {
        return BloodRequest::findAllByRequester($userId);
    }

    public function updateStatus(int $requestId, string $status): void
    {
        BloodRequest::updateRequestStatus($requestId, $status);
    }

    public function update(
        int $id,
        string $bloodType,
        string $city,
        int $units,
        ?string $notes,
        string $contactName,
        string $contactPhone
    ): void {
        BloodRequest::updateRequest($id, $bloodType, $city, $units, $notes, $contactName, $contactPhone);
    }

    public function deleteById(int $id): void
    {
        BloodRequest::deleteRequestById($id);
    }
}
