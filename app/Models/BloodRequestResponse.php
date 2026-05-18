<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Eloquent ORM model for the `blood_request_responses` table (composite primary key).
 */
class BloodRequestResponse extends Model
{
    protected $table = 'blood_request_responses';

    public $incrementing = false;

    public const UPDATED_AT = null;

    protected $fillable = [
        'request_id',
        'donor_user_id',
        'decision',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(BloodRequest::class, 'request_id');
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_user_id');
    }

    public static function findForRequester(int $requesterUserId): array
    {
        return static::query()
            ->select([
                'blood_requests.id as request_id',
                'blood_requests.blood_type',
                'blood_requests.city',
                'blood_requests.units',
                'blood_requests.status as request_status',
                'blood_request_responses.decision',
                'blood_request_responses.created_at as responded_at',
                'users.name as donor_name',
                'users.email as donor_email',
            ])
            ->join('blood_requests', 'blood_requests.id', '=', 'blood_request_responses.request_id')
            ->join('users', 'users.id', '=', 'blood_request_responses.donor_user_id')
            ->where('blood_requests.requester_user_id', $requesterUserId)
            ->orderByDesc('blood_request_responses.created_at')
            ->get()
            ->map(static fn ($row): array => (array) $row->getAttributes())
            ->all();
    }

    public static function upsertDecision(int $requestId, int $donorUserId, string $decision): void
    {
        static::query()->updateOrCreate(
            [
                'request_id' => $requestId,
                'donor_user_id' => $donorUserId,
            ],
            ['decision' => $decision]
        );
    }

    /**
     * @return array<int, string> request_id => decision
     */
    public static function findAllByDonor(int $donorUserId): array
    {
        $map = [];

        foreach (static::query()->where('donor_user_id', $donorUserId)->get(['request_id', 'decision']) as $row) {
            $map[(int) $row->request_id] = (string) $row->decision;
        }

        return $map;
    }
}
