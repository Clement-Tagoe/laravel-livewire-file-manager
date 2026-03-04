<flux:modal name="create-folder" :show="$errors->isNotEmpty()" focusable class="md:w-xl">
    <form method="POST" wire:submit="createFolder" class="space-y-6">
        <div>
            <flux:heading size="lg">Create New Folder</flux:heading>
        </div>

        <flux:input wire:model="name" :label="__('Folder Name')" type="text" placeholder="Folder Name" />

        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
            <flux:modal.close>
                <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" type="submit">{{ __('Create Folder') }}</flux:button>
        </div>
    </form>
</flux:modal>