<?php

namespace Altostrat\Tools\Search;

use Altostrat\Tools\Jobs\PublishSearchJob;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Support\Arrayable;
use LogicException;

class SearchEvent implements Arrayable
{
    // Required for ALL events
    protected string $organizationId;

    protected string $id;

    protected string $eventType;

    // Required for `created` and `updated` events
    protected string $type;

    protected string $name;

    // Optional for all events
    protected ?string $description = null;

    protected array $metadata = [];

    protected ?CarbonInterface $createdAt = null;

    protected ?CarbonInterface $updatedAt = null;

    private function __construct(string $eventType, string $id)
    {
        $this->eventType = $eventType;
        $this->id = $id;
    }

    public static function created(string $id): self
    {
        return new self('created', $id);
    }

    public static function updated(string $id): self
    {
        return new self('updated', $id);
    }

    public static function deleted(string $id): self
    {
        return new self('deleted', $id);
    }

    public function forOrganization(string $organizationId): self
    {
        $this->organizationId = $organizationId;

        return $this;
    }

    public function withType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function withDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function withMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function withTimestamps(?CarbonInterface $createdAt, ?CarbonInterface $updatedAt): self
    {
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Dispatch the event to the queue.
     */
    public function publish(): void
    {
        PublishSearchJob::dispatch($this->toArray());
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        // For a 'deleted' event, we only need the ID in the payload.
        if ($this->eventType === 'deleted') {
            if (! isset($this->organizationId)) {
                throw new LogicException('An organization ID must be provided for a deleted event.');
            }

            return [
                'event_type' => 'deleted',
                'organization_id' => $this->organizationId,
                'payload' => [
                    'id' => $this->id,
                ],
            ];
        }

        // For 'created' and 'updated' events, we require more data.
        // We can add checks here to ensure all required properties are set.
        if (! isset($this->organizationId) || ! isset($this->name) || ! isset($this->type)) {
            throw new LogicException('Organization ID, name, and type are required for created/updated events.');
        }

        return [
            'event_type' => $this->eventType,
            'organization_id' => $this->organizationId,
            'payload' => [
                'id' => $this->id,
                'name' => $this->name,
                'type' => $this->type,
                'description' => $this->description,
                'metadata' => $this->metadata,
                'created_at' => $this->createdAt?->toIso8601String(),
                'updated_at' => $this->updatedAt?->toIso8601String(),
            ],
        ];
    }
}
