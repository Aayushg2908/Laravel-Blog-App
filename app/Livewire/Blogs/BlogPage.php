<?php

namespace App\Livewire\Blogs;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BlogPage extends Component
{
    public $id;
    public $blog;

    public $editingTitle = '';
    public $editingContent = '';

    public function mount()
    {
        $blog = Auth::user()->blogs()->where("id", $this->id)->first();
        $this->blog = $blog;
        $this->editingTitle = $blog->title;
        $this->editingContent = $blog->content;
    }

    public function updateBlog()
    {
        $this->validate([
            'editingTitle' => 'required',
            'editingContent' => 'required|min:10',
        ]);

        $blog = Auth::user()->blogs()->where('id', $this->id)->first();
        $this->blog->title = $this->editingTitle;
        $this->blog->content = $this->editingContent;
        $this->blog->save();
        $this->dispatch('close-modal', 'edit-blog');
    }

    public function deleteBlog()
    {
        $blog = Auth::user()->blogs()->where("id", $this->id)->first();
        $blog->delete();
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function render()
    {
        return view('livewire.blogs.blog-page');
    }
}
