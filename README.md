# Pipelines, Supercharged!

<p align="center"><img src="https://raw.githubusercontent.com/chefhasteeth/pipeline/master/example.png" width="900" alt="Example code showcasing the Pipeline package using the with transaction method and the pipable trait"></p>

## What are pipelines used for?

A Pipeline allows you to pass data through a series of _pipes_ to perform a sequence of operations with the data. Each pipe is a callable piece of code: An invokable class, a closure, etc. Since each pipe operates on the data in isolation (the pipes don't know or care about each other), then that means you can easily compose complex workflows out of reusable actions that are also very easy to test&mdash;because they aren't interdependent.

## What makes it supercharged?

If Laravel is the **batteries included** PHP Framework, then this package can be considered the batteries included version of the `Illuminate\Pipeline\Pipeline` class. It remains (mostly) compatible with Laravel's pipeline, but there are a few differences and added features.

For example, do you see that `withTransaction()` method above? That will tell the Pipeline that we want to run this within a database transaction, which will automatically commit or roll back at the end, depending on whether the pipeline succeeded or failed. It also comes with a trait that gives you a `pipeThrough()` method to automatically send the object it's implemented on through a pipeline.

## What are the differences?

In Laravel's `Pipeline` class, _pipes_ are essentially callables that receive two arguments: The `$passable`, which is the data passed to the pipe, and a `$next` callback that calls the next pipe.

For the purposes of this package, I wanted my pipes to be used easily from anywhere, not just in the pipeline. (For example, this could take the form of a `GenerateThumbnail` action that appears as part of a pipeline, but also might appear in a cron job.) In other words, I don't want to have to pass an empty closure to the class or function to satisfy that `$next` argument.

That's the biggest difference between this package and Laravel's `Pipeline`: The output of the current pipe is the input of the next pipe.

## Sending pipes down the pipeline

When configuring the pipeline, you can send an array of class strings, invokable objects, closures, objects with a `handle()` method, or any other type that passes `is_callable()`.

```php
use Chefhasteeth\Pipeline\Pipeline;

class RegisterController
{
    public function store(StoreRegistrationRequest $request)
    {
        return Pipeline::make()
            ->send($request->all())
            ->through([
                RegisterUser::class,
                AddMemberToTeam::class,
                SendWelcomeEmail::class,
            ])
            ->then(fn ($data) => UserResource::make($data));
    }
}
```

Another approach you can take is to implement this as a trait on a data object. (You could even implement it on your `FormRequest` object if you really wanted.)

```php
use Chefhasteeth\Pipeline\Pipable;

class UserDataObject
{
    use Pipable;

    public string $name;
    public string $email;
    public string $password;
    // ...
}

class RegisterController
{
    public function store(StoreRegistrationRequest $request)
    {
        return UserDataObject::fromRequest($request)
            ->pipeThrough([
                RegisterUser::class,
                AddMemberToTeam::class,
                SendWelcomeEmail::class,
            ])
            ->then(fn ($data) => UserResource::make($data));
    }
}
```

To maintain compatibility with Laravel's `Pipeline` class, the `through()` method can accept either a single array of callables or multiple parameters, where each parameter is one of the callable types listed previously. However, the `pipeThrough()` trait method only accepts an array, since it also has a second optional parameter.

## Using database transactions

When you want to use database transactions in your pipeline, the method will be different depending on if you're using the trait or the `Pipeline` class.

Using the `Pipeline` class:

```php
Pipeline::make()->withTransaction()
```

The `withTransaction()` method will tell the pipeline to use transactions. When you call the `then()` or `thenReturn()` methods, a database transaction will begin before executing the pipes. If an exception is encountered during the pipeline, the transaction will be rolled back so no data is committed to the database. Assuming the pipeline completed successfully, the transaction is committed.

When using the trait, you can pass a second parameter to the `pipeThrough()` method:

```php
$object->pipeThrough($pipes, withTransaction: true);
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
