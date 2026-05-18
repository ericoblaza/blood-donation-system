<?php
// standardizes
declare(strict_types=1);

namespace App\Contracts;

interface BloodRequestRepositoryInterface
{
    public function create(
        int $requesterUserId,
        string $bloodType,
        string $city,
        int $units,
        ?string $notes,
        string $contactName,
        string $contactPhone
    ): void;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllOpen(): array;

    /**
     * @return array<string, mixed>|null
     */
    public function findById(int $id): ?array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function findAllByRequester(int $userId): array;

    public function updateStatus(int $requestId, string $status): void;

    public function update(
        int $id,
        string $bloodType,
        string $city,
        int $units,
        ?string $notes,
        string $contactName,
        string $contactPhone
    ): void;

    public function deleteById(int $id): void;
}
