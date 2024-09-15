<?php

namespace App\Livewire\Blogs;

use App\Events\BlogDeleted;
use App\Events\BlogUpdated;
use App\Events\CommentCreated;
use App\Events\CommentDeleted;
use App\Events\CommentUpdated;
use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class BlogPage extends Component
{
    public $id;
    public $blog;
    public string $comment;
    public $comments;

    public $editingTitle = '';
    public $editingContent = '';
    public $commentId = null;
    public $editingCommentContent = '';

    public function mount()
    {
        $blog = Blog::find($this->id);
        if(!$blog) {
            $this->redirect(route('dashboard', absolute: false), navigate: true);
            return;
        }
        $this->blog = $blog;
        $this->editingTitle = $blog->title;
        $this->editingContent = $blog->content;
        $this->comments = $blog->comments()->latest()->get();
    }

    #[On('echo:blog-updated,BlogUpdated')]
    public function blogUpdated($payload)
    {
        $this->editingTitle = $payload['blog']['title'];
        $this->editingContent = $payload['blog']['content'];
    }

    #[On('echo:blog-deleted,BlogDeleted')]
    public function blogDeleted($payload)
    {
        $blogId = $payload['blogId'];
        if($this->blog->id == $blogId) {
            $this->redirect(route('dashboard', absolute: false), navigate: true);
        }
    }

    #[On('echo:comment-created,CommentCreated')]
    public function commentCreated($payload)
    {
        $comment = $this->blog->comments()->find($payload['comment']['id']);
        $this->comments->prepend($comment);
    }

    #[On('echo:comment-deleted,CommentDeleted')]
    public function commentDeleted($payload)
    {
        $commentId = $payload['commentId'];
        $this->comments = $this->comments->filter(function($comment) use ($commentId) {
            return $comment->id != $commentId;
        });
    }

    #[On('echo:comment-updated,CommentUpdated')]
    public function commentUpdated($payload)
    {
        $this->comments = $this->comments->map(function($c) use ($payload) {
            return $c->id === $payload['comment']['id'] ? Comment::find($payload['comment']['id']) : $c;
        });
    }

    public function updateBlog()
    {
        $this->validate([
            'editingTitle' => 'required',
            'editingContent' => 'required|min:10',
        ]);

        $this->blog->title = $this->editingTitle;
        $this->blog->content = $this->editingContent;
        $this->blog->save();
        BlogUpdated::dispatch($this->blog);
        $this->dispatch('close-modal', 'edit-blog');
    }

    public function deleteBlog()
    {
        $blog = Auth::user()->blogs()->find($this->id);
        $blog->delete();
        BlogDeleted::dispatch($this->id);
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function createComment()
    {
        $this->validate([
            'comment' => 'required|min:5',
        ]);

        $comment = $this->blog->comments()->create([
            'user_id' => Auth::id(),
            'content' => $this->comment,
        ]);

        $this->comment = '';
        CommentCreated::dispatch($comment);
    }

    public function confirmEdit($commentId)
    {
        $this->commentId = $commentId;
        $comment = $this->blog->comments()->find($this->commentId);
        $this->editingCommentContent = $comment->content;
    }

    public function updateComment()
    {
        $this->validate([
            'editingCommentContent' => 'required|min:5',
        ]);

        $comment = $this->blog->comments()->find($this->commentId);
        $comment->content = $this->editingCommentContent;
        $comment->save();
        CommentUpdated::dispatch($comment);
        $this->dispatch('close-modal', 'edit-comment');
    }

    public function confirmDelete($commentId)
    {
        $this->commentId = $commentId;
    }

    public function deleteComment()
    {
        $comment = $this->blog->comments()->find($this->commentId);
        $comment->delete();
        CommentDeleted::dispatch($this->commentId);
    }

    public function render()
    {
        return view('livewire.blogs.blog-page');
    }
}
