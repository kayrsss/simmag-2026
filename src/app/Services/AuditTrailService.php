<?php

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditTrailService
{
    public function record(
        Model $entity,
        string $action,
        ?string $previousStatus = null,
        ?string $newStatus = null,
        ?string $notes = null,
        ?User $actor = null
    ): AuditTrail {
        $actor ??= Auth::guard('web')->user();

        $request = $this->resolveRequest();

        return AuditTrail::query()->create([
            'user_id' =>
                $actor?->getKey(),

            'action' =>
                $action,

            'entity_type' =>
                $entity::class,

            'entity_id' =>
                $entity->getKey(),

            'previous_status' =>
                $previousStatus,

            'new_status' =>
                $newStatus,

            'notes' =>
                filled($notes)
                    ? trim((string) $notes)
                    : null,

            'ip_address' =>
                $request?->ip(),

            'user_agent' =>
                $request?->userAgent(),

            'created_at' =>
                now(),
        ]);
    }

    public function statusChanged(
        Model $entity,
        string $previousStatus,
        string $newStatus,
        ?string $notes = null,
        ?User $actor = null
    ): AuditTrail {
        return $this->record(
            entity: $entity,
            action: 'status_changed',
            previousStatus: $previousStatus,
            newStatus: $newStatus,
            notes: $notes,
            actor: $actor
        );
    }

    public function created(
        Model $entity,
        ?string $notes = null,
        ?User $actor = null
    ): AuditTrail {
        return $this->record(
            entity: $entity,
            action: 'created',
            notes: $notes,
            actor: $actor
        );
    }

    public function updated(
        Model $entity,
        ?string $notes = null,
        ?User $actor = null
    ): AuditTrail {
        return $this->record(
            entity: $entity,
            action: 'updated',
            notes: $notes,
            actor: $actor
        );
    }

    public function submitted(
        Model $entity,
        string $previousStatus,
        string $newStatus,
        ?string $notes = null,
        ?User $actor = null
    ): AuditTrail {
        return $this->record(
            entity: $entity,
            action: 'submitted',
            previousStatus: $previousStatus,
            newStatus: $newStatus,
            notes: $notes,
            actor: $actor
        );
    }

    public function approved(
        Model $entity,
        string $previousStatus,
        string $newStatus,
        ?string $notes = null,
        ?User $actor = null
    ): AuditTrail {
        return $this->record(
            entity: $entity,
            action: 'approved',
            previousStatus: $previousStatus,
            newStatus: $newStatus,
            notes: $notes,
            actor: $actor
        );
    }

    public function revisionRequested(
        Model $entity,
        string $previousStatus,
        string $newStatus,
        string $notes,
        ?User $actor = null
    ): AuditTrail {
        return $this->record(
            entity: $entity,
            action: 'revision_requested',
            previousStatus: $previousStatus,
            newStatus: $newStatus,
            notes: $notes,
            actor: $actor
        );
    }

    private function resolveRequest(): ?Request
    {
        if (! app()->bound('request')) {
            return null;
        }

        $request = app('request');

        return $request instanceof Request
            ? $request
            : null;
    }
}