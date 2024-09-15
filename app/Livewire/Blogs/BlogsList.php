<?php

namespace App\Livewire\Blogs;

use App\Events\BlogCreated;
use App\Events\BlogDeleted;
use App\Events\BlogUpdated;
use App\Models\Blog;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class BlogsList extends Component
{
    public $blogs = [];
    public string $title = '';
    public string $content = '';

    public $editingBlogId = null;
    public string $editingTitle = '';
    public string $editingContent = '';

    public $deletingBlogId = null;

    public function mount()
    {
        $this->blogs = Blog::all()->reverse();
    }

    #[On('echo:blog-created,BlogCreated')]
    public function blogCreated($payload)
    {
        $newBlog = Blog::find($payload['blog']['id']);
        $this->blogs->prepend($newBlog);
    }

    #[On('echo:blog-updated,BlogUpdated')]
    public function blogUpdated($payload)
    {
        $this->blogs = $this->blogs->map(function ($blog) use ($payload) {
            return $blog->id === $payload['blog']['id'] ? Blog::find($payload['blog']['id']) : $blog;
        });
    }

    #[On('echo:blog-deleted,BlogDeleted')]
    public function blogDeleted($payload)
    {
        $this->blogs = $this->blogs->filter(function ($blog) use ($payload) {
            return $blog->id !== $payload['blogId'];
        });
    }

    public function createBlog()
    {
        $this->validate([
            'title' => 'required',
            'content' => 'required|min:10',
        ]);

        $blog = Auth::user()->blogs()->create([
            'title' => $this->title,
            'content' => $this->content,
        ]);

        $this->title = '';
        $this->content = '';

        BlogCreated::dispatch($blog);
        $this->dispatch('close-modal', 'create-blog');
    }

    public function editBlog($blogId)
    {
        $this->editingBlogId = $blogId;
        $blog = Blog::find($blogId);
        $this->editingTitle = $blog->title;
        $this->editingContent = $blog->content;
    }

    public function updateBlog()
    {
        $this->validate([
            'editingTitle' => 'required',
            'editingContent' => 'required|min:10',
        ]);

        $blog = Auth::user()->blogs()->where('id', $this->editingBlogId)->first();
        $blog->title = $this->editingTitle;
        $blog->content = $this->editingContent;
        $blog->save();

        BlogUpdated::dispatch($blog);
        $this->dispatch('blog-updated');
        $this->dispatch('close-modal', 'edit-blog');
    }

    public function confirmDelete(string $blog_id)
    {
        $this->deletingBlogId = $blog_id;
    }

    public function deleteBlog()
    {
        Auth::user()->blogs()->where('id', $this->deletingBlogId)->delete();
        BlogDeleted::dispatch($this->deletingBlogId);
        $this->dispatch('blog-updated');
    }

    public function render()
    {
        return view('livewire.blogs.blogs-list');
    }
}
