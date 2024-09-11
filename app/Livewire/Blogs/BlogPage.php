<?php

namespace App\Livewire\Blogs;

use App\Models\Blog;
use Illuminate\Support\Facades\Auth;
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
        $this->blog = $blog;
        $this->editingTitle = $blog->title;
        $this->editingContent = $blog->content;
        $this->comments = $blog->comments()->latest()->get();
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
        $this->dispatch('close-modal', 'edit-blog');
    }

    public function deleteBlog()
    {
        $blog = Auth::user()->blogs()->find($this->id);
        $blog->delete();
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function createComment()
    {
        $this->validate([
            'comment' => 'required|min:5',
        ]);

        $this->blog->comments()->create([
            'user_id' => Auth::id(),
            'content' => $this->comment,
        ]);

        $this->comments = $this->blog->comments()->latest()->get();
        $this->comment = '';
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
        $this->comments = $this->blog->comments()->latest()->get();
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
        $this->comments = $this->blog->comments()->latest()->get();
    }

    public function render()
    {
        return view('livewire.blogs.blog-page');
    }
}
