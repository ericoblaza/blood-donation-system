<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Eloquent ORM model for the `blood_requests` table.
 */
class BloodRequest extends Model
{
    protected $table = 'blood_requests';

    protected $fillable = [
        'requester_user_id',
        'blood_type',
        'city',
        'units',
        'status',
        'notes',
        'contact_name',
        'contact_phone',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(BloodRequestResponse::class, 'request_id');
    }

    public static function createRequest(
        int $requesterUserId,
        string $bloodType,
        string $city,
        int $units,
        ?string $notes,
        string $contactName,
        string $contactPhone
    ): void {
        static::query()->create([
            'requester_user_id' => $requesterUserId,
            'blood_type' => $bloodType,
            'city' => $city,
            'units' => $units,
            'notes' => $notes,
            'status' => 'open',
            'contact_name' => $contactName,
            'contact_phone' => $contactPhone,
        ]);
    }

    public static function findAllOpen(): array
    {
        return static::query()
            ->where('status', 'open')
            ->orderByDesc('created_at')
            ->get()
            ->map(static fn (self $row): array => $row->toArray())
            ->all();
    }

    public static function findRequestById(int $id): ?array
    {
        $row = static::query()->find($id);

        return $row instanceof self ? $row->toArray() : null;
    }

    public static function findAllByRequester(int $userId): array
    {
        return static::query()
            ->where('requester_user_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(static fn (self $row): array => $row->toArray())
            ->all();
    }

    public static function updateRequestStatus(int $requestId, string $status): void
    {
        $allowed = ['open', 'fulfilled', 'cancelled'];

        if (!in_array($status, $allowed, true)) {
            throw new \InvalidArgumentException('Invalid request status.');
        }

        static::query()->whereKey($requestId)->update(['status' => $status]);
    }

    public static function updateRequest(
        int $id,
        string $bloodType,
        string $city,
        int $units,
        ?string $notes,
        string $contactName,
        string $contactPhone
    ): void {
        static::query()->whereKey($id)->update([
            'blood_type' => $bloodType,
            'city' => $city,
            'units' => $units,
            'notes' => $notes,
            'contact_name' => $contactName,
            'contact_phone' => $contactPhone,
        ]);
    }

    public static function deleteRequestById(int $id): void
    {
        $request = static::query()->find($id);

        if ($request === null) {
            return;
        }

        $request->responses()->delete();
        $request->delete();
    }
}
