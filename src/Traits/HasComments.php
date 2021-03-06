<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Commentable.
 *
 * (c) Brian Faust <hello@basecode.sh>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Artisanry\Commentable\Traits;

use Artisanry\Commentable\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasComments
{
    /**
     * The name of the comments model.
     */
    public function commentableModel(): string
    {
        return config('commentable.model');
    }

    /**
     * The comments attached to the model.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany($this->commentableModel(), 'commentable');
    }

    /**
     * Create a comment.
     *
     * @return static
     */
    public function comment(array $data, Model $creator, Model $parent = null)
    {
        $commentableModel = $this->commentableModel();

        $comment = (new $commentableModel())->createComment($this, $data, $creator);

        // if (!empty($parent)) {
        //     $parent->appendNode($comment);
        // }

        return $comment;
    }

    /**
     * Update a comment.
     *
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function updateComment($id, $data, Model $parent = null)
    {
        $commentableModel = $this->commentableModel();

        $comment = (new $commentableModel())->updateComment($id, $data);

        // if (!empty($parent)) {
        //     $parent->appendNode($comment);
        // }

        return $comment;
    }

    /**
     * Delete a comment.
     *
     * @return mixed
     */
    public function deleteComment(int $id): bool
    {
        $commentableModel = $this->commentableModel();

        return (bool) (new $commentableModel())->deleteComment($id);
    }

    /**
     * The amount of comments assigned to this model.
     *
     * @return mixed
     */
    public function commentCount(): int
    {
        return $this->comments->count();
    }

    /**
     * Hooking in delete method to delete all polymorph relationships.
     *
     * @return void
     */
    protected static function bootHasComments()
    {
        self::deleting(function ($model) {
            $model->comments()->delete();
        });
    }
}
