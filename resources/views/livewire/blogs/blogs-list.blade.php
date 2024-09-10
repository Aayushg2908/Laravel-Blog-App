<div>
    <x-primary-button class="w-full justify-center" x-on:click.prevent="$dispatch('open-modal', 'create-blog')">Create Blog</x-primary-button>

    @if ($blogs->isEmpty())
    <div class="w-full text-center bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mt-4">
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('No blogs available.') }}
        </h2>
        <p class="mt-1 text-gray-600">
            {{ __('You have not created any blogs yet. Start by creating a new blog!') }}
        </p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
        @foreach ($blogs as $blog)
        <div class="bg-gray-100 shadow-md sm:rounded-lg p-4 mt-4 border">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ $blog->title }}
                </h2>
                <x-dropdown align="left" width="48">
                    <x-slot name="trigger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ellipsis-vertical cursor-pointer">
                            <circle cx="12" cy="12" r="1" />
                            <circle cx="12" cy="5" r="1" />
                            <circle cx="12" cy="19" r="1" />
                        </svg>
                    </x-slot>
                    <x-slot name="content">
                        <div class="p-1 flex flex-col gap-2">
                            <div x-on:click.prevent="$dispatch('open-modal', 'edit-blog')" class="flex items-center gap-x-4 hover:bg-gray-100 p-2 rounded-md" wire:click="editBlog({{ $blog->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil">
                                    <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" />
                                    <path d="m15 5 4 4" />
                                </svg>
                                {{ __('Edit Blog') }}
                            </div>
                            <div x-on:click.prevent="$dispatch('open-modal', 'delete-blog')" class="flex items-center gap-x-4 hover:bg-gray-100 p-2 rounded-md" wire:click="confirmDelete({{ $blog->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2">
                                    <path d="M3 6h18" />
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                    <line x1="10" x2="10" y1="11" y2="17" />
                                    <line x1="14" x2="14" y1="11" y2="17" />
                                </svg>
                                {{ __('Delete Blog') }}
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>
            <p class="mt-2 text-gray-600">
                {{ $blog->content }}
            </p>
        </div>
        @endforeach
    </div>
    @endif

    <x-modal name="create-blog" focusable>
        <form wire:submit="createBlog" class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create A New Blog') }}
            </h2>
            <div class="mt-6 space-y-6">
                <div>
                    <x-input-label for="title" value="{{ __('Title') }}" />
                    <x-text-input
                        wire:model="title"
                        id="title"
                        name="title"
                        type="text"
                        class="mt-1 block w-3/4"
                        placeholder="{{ __('Title') }}" />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="content" value="{{ __('Content') }}" />
                    <x-text-input
                        wire:model="content"
                        id="content"
                        name="content"
                        type="text"
                        class="mt-1 block w-3/4"
                        placeholder="{{ __('Content') }}" />
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
                <x-primary-button class="ms-3">
                    {{ __('Create') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

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
</div>