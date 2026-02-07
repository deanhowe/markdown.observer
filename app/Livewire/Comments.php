<?php

namespace App\Livewire;

use App\Models\Comment;
use Livewire\Component;

class Comments extends Component
{
    public $commentableType;
    public $commentableId;
    public $newComment = '';
    
    public function mount($commentableType, $commentableId)
    {
        $this->commentableType = $commentableType;
        $this->commentableId = $commentableId;
    }
    
    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|min:3',
        ]);
        
        Comment::create([
            'user_id' => auth()->id(),
            'commentable_type' => $this->commentableType,
            'commentable_id' => $this->commentableId,
            'content' => $this->newComment,
        ]);
        
        $this->newComment = '';
        $this->dispatch('comment-added');
    }
    
    public function render()
    {
        $comments = Comment::where('commentable_type', $this->commentableType)
            ->where('commentable_id', $this->commentableId)
            ->with('user')
            ->latest()
            ->get();
        
        return view('livewire.comments', [
            'comments' => $comments,
        ]);
    }
}
