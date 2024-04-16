<?php

namespace ATereshchuk\LaravelLikeDislike\Traits;

use ATereshchuk\LaravelLikeDislike\Like;
use Illuminate\Database\Eloquent\Model;

trait Likeable
{
    public function isLikedBy(Model $user): bool
    {
        if (\is_a($user, config('like.user_model') ?? config('auth.providers.users.model'))) {
            if ($this->relationLoaded('likers')) {
                return $this->likers->contains($user);
            }

            return $this->likers()->where(\config('like.user_foreign_key'), $user->getKey())->exists();
        }

        return false;
    }
    public function isDislikedBy(Model $user): bool
    {
        if (\is_a($user, config('like.user_model') ?? config('auth.providers.users.model'))) {
            if ($this->relationLoaded('likers')) {
                return $this->likers->contains($user);
            }

            return $this->dislikers()->where(\config('like.user_foreign_key'), $user->getKey())->exists();
        }

        return false;
    }

    /**
     * Return followers.
     */
    public function likers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            config('like.user_model') ?? config('auth.providers.users.model'),
            config('like.likes_table'),
            'likeable_id',
            config('like.user_foreign_key')
        )
            ->where(config('like.likes_table').".type", Like::$Like)
            ->where('likeable_type', $this->getMorphClass());
    }
    public function dislikers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            config('like.user_model') ?? config('auth.providers.users.model'),
            config('like.likes_table'),
            'likeable_id',
            config('like.user_foreign_key')
        )
            ->where(config('like.likes_table').".type", Like::$DisLike)
            ->where('likeable_type', $this->getMorphClass());
    }
}
