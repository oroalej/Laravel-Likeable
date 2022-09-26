Laravel Likeable Package
===

### Composer Install
```bash
composer require oroalej/laravel-likeable
```

### Run migration
```bash
php artisan migrate
```

## Usage

### Traits

```php
// Liker
use Illuminate\Foundation\Auth\User as Authenticatable;
use Oroalej\Likeable\Models\Traits\Liker;

Class User extends Authenticatable 
{
  use Liker;
}

// Likeable
use Illuminate\Database\Eloquent\Model;
use Oroalej\Likeable\Models\Traits\Likeable;

Class Post extends Model 
{
  use Likeable;
}
```

### API

```php
$user = User::first();
$post = Post::first();

// Liker
$user->like($post);
$user->unlike($post);

// Likeable
$post->isLikedBy($user);
$post->unlikedBy($user);
```

