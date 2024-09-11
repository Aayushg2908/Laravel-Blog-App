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