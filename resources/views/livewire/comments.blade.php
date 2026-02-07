<div class="space-y-6">
    {{-- Comment Form --}}
    <div class="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
        <form wire:submit="addComment">
            <textarea 
                wire:model="newComment"
                rows="3"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                placeholder="Add a comment..."
            ></textarea>
            
            @error('newComment')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            
            <div class="mt-4 flex justify-end">
                <button 
                    type="submit"
                    class="px-6 py-2 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 text-white rounded-lg hover:shadow-lg hover:shadow-purple-500/50 hover:scale-[1.02] transition-all duration-300"
                >
                    Post Comment
                </button>
            </div>
        </form>
    </div>

    {{-- Comments List --}}
    <div class="space-y-4">
        @forelse($comments as $comment)
            <div class="backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold">
                            {{ substr($comment->user->name, 0, 1) }}
                        </div>
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-semibold text-gray-900 dark:text-white">
                                {{ $comment->user->name }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $comment->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <p class="text-gray-700 dark:text-gray-300">
                            {{ $comment->content }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 backdrop-blur-sm bg-white/70 dark:bg-gray-800/70 rounded-2xl border border-gray-200 dark:border-gray-700">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400">No comments yet. Be the first!</p>
            </div>
        @endforelse
    </div>
</div>
