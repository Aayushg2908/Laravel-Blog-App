<?php

namespace App\Livewire\Blogs;

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

    #[On('blog-updated')]
    public function refreshBlogs()
    {
        $this->blogs = Blog::all()->reverse();
    }

    public function createBlog()
    {
        $this->validate([
            'title' => 'required',
            'content' => 'required|min:10',
        ]);

        Auth::user()->blogs()->create([
            'title' => $this->title,
            'content' => $this->content,
        ]);

        $this->title = '';
        $this->content = '';

        $this->dispatch('blog-updated');
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
        $this->dispatch('blog-updated');
    }

    public function render()
    {
        return view('livewire.blogs.blogs-list');
    }
}
