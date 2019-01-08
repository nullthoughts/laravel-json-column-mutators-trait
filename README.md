# laravel-json-column-mutators-trait
Extends Laravel's HasAttributes Trait to enable nested attribute mutators for JSON cast columns

## Usage
1. Copy into App\Traits, and declare `use SetJsonMutator;` within the Model that is using JSON columns
```
<?php

namespace App;

use App\Traits\SetJsonMutator;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use SetJsonMutator;
}
```
2. Create a new Mutator in your Model, example:
  ```
  setPricingMsrpAttribute($value)
  {
    return $value * 2;
  }
  ```
3. Update a record using the JSON `->` syntax:
```
$product = Product::first();

$product->update([
    'pricing->msrp' => 150,
]);

return $product->pricing['msrp']; //300
```
