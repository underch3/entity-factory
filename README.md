# Symfony Entity factory

# Usage



## How to use

### Create Entity

```php
$factory->create(User::class);

```

### Create multiple entities
```php
$factory->times(10)->create(Beer::class);

/* This will generate 10 persisted beers with fake values */
```

### Create Entity and override some data
```php
$user = $factory->create(
    User::class, 
    [
        'username' => 'BadassAdmin'
        'active'   => true
    ]
);
```

### Create new instance
```php
$post = $factory->make(User::class)
```

### Get fake values for an entity

Maybe you don't want an instance of the entity, but need some fake data to create you entity object. The `values` method will return an array of fake values for an entity.

```php
$productData = $factory->values(User::class);
```
