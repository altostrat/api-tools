# MikroCloud - Laravel Tooling

## Installation

```bash
composer require mikrocloud/mikrocloud
```

### Environment Variables

Add the following environment variables to your `.env` file:

```bash
AUTH0_CLIENT_ID=
AUTH0_COOKIE_SECRET=
API_PREFIX=
```

`AUTH0_CLIENT_ID` should be the client ID of the MikroCloud Auth0 tenant application.
`AUTH0_COOKIE_SECRET` should be a random string of at least 32 characters.
`API_PREFIX` should be the prefix for your API routes, e.g. `v1/my-service`.

### Installation Command

Run the following command run through some boilerplate installation steps:

```bash
php artisan mikrocloud:install
```

Afterward run the following command to verify that everything is working:

```bash
php artisan mikrocloud:check
```

---

## Usage

### Authenticated routes
Whenever you want to register a route that requires authentication, add it to the `routes/authenticated.php` file.
This will require the user to be authenticated and have the correct scopes to access the route.

During the installation a model called `Customer` was created. You can use this model to create relationships with your own models.
The `Customer` model is read-only and only acts as a way to leverage eloquent relationships.

### Billable Models
When creating a billable service, commonly a particular model is used to represent the billable item. If you want to make a model billable, simply extend the model from `MikroCloud\Billable\BillableModel`.

```php
namespace App\Models;

use MikroCloud\Billable\BillableModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MyModel extends BillableModel
{
    use HasUuids;
    
    protected $fillable = [
        'customer_id',
    ];
    
    //
}
```

The model that you wish to make billable must have a UUID `customer_id` column and the `id` column must be a UUID.

> Nothing more is needed, when a user attempts to create an item, a check will be performed to see if the user has a valid subscription.
> If the user is not allowed to create the item, execution will be halted and a response will be returned to the user.

> **WARNING**: When making a model billable, only an authenticated user can create the model.
> That means it must come in through an authenticated route and the `auth()->user()` must be set.
> You cannot create a billable model through a job or a command.
> 
> **HTTP requests only.**

### Helpers
The following helpers are available:
- `new ipv4Address(string $address)` - Creates a new IPv4Address object - use the `->withIsp()` method to get the ISP name.
- `IpLookup::info(string $address)` - Returns an array with information about the IP address.
- `Websocket::push(string $userId, string $event, array $data)` - Pushes an event to the user's websocket connection.
- `GeographicHelper::class` - Helper class for geographic items, like a list of currencies, countries, etc.

## Implementation Notes
Remember to ask the infrastructure team to add the prefix to the ALB with these paths:

- `v1/my-service/*`
- `v1/my-service`

## Service Count Routes

Billable models are automatically made countable.

As an example, if you set the prefix to `v1/my-service`, the following route will be available:

```
GET /v1/my-service/billable-count/{model}
```

The `model` parameter is the name of the model, e.g. `my-model`. Model names should be converted to kebab-case. E.g. `MyModel` becomes `my-model`.

You may optionally pass the `count=1` URL parameter to get the count of the model.

If `count=1` is not passed, the route will return a list like this:

```json
[
    {
        "id": "....", // The ID of the model
        "site_id": "...." // Only if the model has a site_id column otherwise this will be null
    }
]
```

If `count=1` is passed, the route will return a count like this:

```json
{
    "count": 1
}
```

