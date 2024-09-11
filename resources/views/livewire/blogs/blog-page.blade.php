<div>
    <div class="w-full text-center">
        <h1 class="text-xl font-semibold">{{$blog->title}}</h1>
        <p class="mt-2">{{$blog->content}}</p>
    </div>

    @if (auth()->user() && auth()->user()->id === $blog->user_id)
    <div class="mt-4 flex items-center gap-4 justify-center">
        <x-secondary-button x-on:click.prevent="$dispatch('open-modal', 'edit-blog')">Edit Blog</x-secondary-button>
        <x-danger-button x-on:click.prevent="$dispatch('open-modal', 'delete-blog')">Delete Blog</x-danger-button>
    </div>
    @endif

    <div class="mt-4 flex flex-col items-center justify-center">
        <h1 class="text-xl">Comments</h1>
        <form wire:submit="createComment" class="flex items-center gap-2">
            <x-input-label for="comment" value="{{ __('Comment') }}" class="sr-only" />
            <x-text-input
                wire:model="comment"
                id="comment"
                name="comment"
                class="w-full"
                type="text"></x-text-input>
            <x-input-error :messages="$errors->get('comment')" class="mt-2" />
            <x-primary-button>Create</x-primary-button>
        </form>
        @if ($comments->isEmpty())
        <p class="mt-4">No comments yet. Create One</p>
        @else
        @foreach ($comments as $comment)
        @php
        $username = \App\Models\User::find($comment->user_id)->name;
        @endphp
        <div class="mt-4 flex items-center gap-4 justify-between bg-gray-100 p-2 rounded-lg">
            <div class="flex flex-col">
                <span>{{$comment->content}}</span>
                <span class="text-sm">Created By: {{$username}}</span>
            </div>
            @if (auth()->user() && auth()->user()->id === $comment->user_id)
            <div class="flex items-center gap-2 justify-center">
                <x-secondary-button x-on:click.prevent="$dispatch('open-modal', 'edit-comment')" wire:click="confirmEdit({{$comment->id}})">Edit</x-secondary-button>
                <x-danger-button x-on:click.prevent="$dispatch('open-modal', 'delete-comment')" wire:click="confirmDelete({{$comment->id}})">Delete</x-danger-button>
            </div>
            @endif
        </div>
        @endforeach
        @endif

        <x-modal name="edit-blog" focusable>
            <form wire:submit.prevent="updateBlog" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Edit Blog') }}
                </h2>

                <div class="mt-6 space-y-6">
                    <div>
                        <x-input-label for="editingTitle" value="{{ __('Title') }}" />
                        <x-text-input
                            wire:model="editingTitle"
                            id="editingTitle"
                            name="editingTitle"
                            class="mt-1 block w-full"
                            type="text"
                            required />
                        <x-input-error :messages="$errors->get('editingTitle')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="editingContent" value="{{ __('Content') }}" />
                        <x-text-input
                            wire:model="editingContent"
                            id="editingContent"
                            name="editingContent"
                            class="mt-1 block w-full"
                            type="text"
                            required />
                        <x-input-error :messages="$errors->get('editingContent')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-primary-button class="ms-3">
                        {{ __('Update') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <x-modal name="delete-blog" focusable>
            <form wire:submit.prevent="deleteBlog" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Are you sure you want to delete this Blog?') }}
                </h2>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3" x-on:click="$dispatch('close')">
                        {{ __('Delete Blog') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>

        <x-modal name="edit-comment" focusable>
            <form wire:submit.prevent="updateComment" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Edit Comment') }}
                </h2>

                <div class="mt-6 space-y-6">
                    <x-input-label for="editingCommentContent" value="{{ __('Content') }}" />
                    <x-text-input
                        wire:model="editingCommentContent"
                        id="editingCommentContent"
                        name="editingCommentContent"
                        class="mt-1 block w-full"
                        type="text"
                        required />
                    <x-input-error :messages="$errors->get('editingCommentContent')" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-primary-button class="ms-3">
                        {{ __('Update') }}
                    </x-primary-button>
                </div>
            </form>
        </x-modal>

        <x-modal name="delete-comment" focusable>
            <form wire:submit.prevent="deleteComment" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ __('Are you sure you want to delete this comment?') }}
                </h2>
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancel') }}
                    </x-secondary-button>

                    <x-danger-button class="ms-3" x-on:click="$dispatch('close')">
                        {{ __('Delete Comment') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>