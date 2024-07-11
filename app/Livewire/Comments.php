<?php

namespace App\Livewire;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Comments extends Component
{
    public $user;

    public $userComment;
    public $comments;
    public $commentCount;

    protected $listeners = ['commentAdded' => 'loadComments'];

    public function mount()
    {
        $this->loadComments();
    }

    public function loadComments()
    {
        $this->comments = Comment::latest()->get();
        $this->commentCount = $this->comments->count();
    }
    public function render()
    {
        return view('livewire.comments');
    }
}
