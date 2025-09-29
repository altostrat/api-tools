<?php

namespace Altostrat\Tools\Search;

use Illuminate\Database\Eloquent\Model;

trait Searchable
{
    /**
     * Boot the trait.
     * This method is automatically called when a model uses this trait.
     */
    public static function bootSearchable(): void
    {
        // Don't register observers if search is disabled via config
        if (empty(config('altostrat.search_dsn'))) {
            return;
        }

        // After a model is created in the database, publish a 'created' event.
        static::created(function (Model $model) {
            $model->publishSearchEvent('created');
        });

        // After a model is updated, publish an 'updated' event.
        static::updated(function (Model $model) {
            $model->publishSearchEvent('updated');
        });

        // After a model is deleted, publish a 'deleted' event.
        static::deleted(function (Model $model) {
            $model->publishSearchEvent('deleted');
        });

        // If the model uses SoftDeletes, we also handle the 'restored' event.
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(static::class))) {
            static::restored(function (Model $model) {
                $model->publishSearchEvent('updated'); // A restored model is effectively an update
            });
        }
    }

    /**
     * Publishes the appropriate search event for this model.
     */
    public function publishSearchEvent(string $eventType): void
    {
        // For a delete event, we only need the key identifiers.
        if ($eventType === 'deleted') {
            SearchEvent::deleted($this->getSearchId())
                ->forOrganization($this->getSearchOrganizationId())
                ->publish();

            return;
        }

        // For created/updated events, we build the full event.
        SearchEvent::{$eventType}($this->getSearchId())
            ->forOrganization($this->getSearchOrganizationId())
            ->withType($this->getSearchType())
            ->withName($this->getSearchName())
            ->withDescription($this->getSearchDescription())
            ->withMetadata($this->getSearchMetadata())
            ->withTimestamps($this->created_at, $this->updated_at)
            ->publish();
    }

    /**
     * Get the ID to be used for the search index.
     * Defaults to the model's primary key.
     */
    public function getSearchId(): string
    {
        return (string) $this->getKey();
    }

    /**
     * Get the organization ID for the search index.
     *
     * DEVELOPERS MUST IMPLEMENT THIS METHOD IN THEIR MODEL.
     * For example: `return $this->customer_id;`
     */
    abstract public function getSearchOrganizationId(): string;

    /**
     * Get the type name for the search index.
     *
     * DEVELOPERS MUST IMPLEMENT THIS METHOD IN THEIR MODEL.
     * For example: `return 'invoice';` or `return $this->resource_type;`
     */
    abstract public function getSearchType(): string;

    /**
     * Get the name/title for the search index.
     *
     * DEVELOPERS MUST IMPLEMENT THIS METHOD IN THEIR MODEL.
     * For example: `return $this->title;`
     */
    abstract public function getSearchName(): string;

    /**
     * Get the description for the search index.
     * Can be overridden by the model if needed.
     */
    public function getSearchDescription(): ?string
    {
        return $this->description ?? null;
    }

    /**
     * Get the metadata for the search index.
     * Can be overridden by the model to add custom data.
     */
    public function getSearchMetadata(): array
    {
        return [];
    }
}
