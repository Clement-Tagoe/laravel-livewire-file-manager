<?php

use Livewire\Component;

new class extends Component
{
    
};
?>

<flux:dropdown>
    <flux:button class="w-full">Create New</flux:button>

    <flux:menu> 
         <flux:modal.trigger name="create-folder">
            <flux:menu.item x-data="" x-on:click.prevent="$dispatch('open-modal', 'create-folder')">
                New Folder
            </flux:menu.item>
        </flux:modal>

        <flux:menu.separator />

        <flux:menu.item>
            <livewire:app.file-upload-form />
        </flux:menu.item>

        <flux:menu.separator />

        <flux:menu.item>
            <livewire:app.folder-upload-form />
        </flux:menu.item>
    </flux:menu>

</flux:dropdown>