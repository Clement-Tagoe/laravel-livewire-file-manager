<section class="w-full">
    <div class="flex justify-center w-full lg:w-2/3">
        <livewire:app.search-form />
    </div>
    <livewire:app.create-folder-form :folder="$folder"/>
    

    <flux:breadcrumbs class="mt-6">
        @foreach ($ancestors as $ancestor)
            @if (!$ancestor->parent_id)
                <flux:breadcrumbs.item :href="route('my-files.index')" >My Files</flux:breadcrumbs.item>
            @else
                <flux:breadcrumbs.item :href="route('my-files.index', $ancestor->path)">{{$ancestor->name}}</flux:breadcrumbs.item>
            @endif
        @endforeach
    </flux:breadcrumbs>

    <div class="mt-2 mb-8">
            <flux:table :paginate="$files" class="w-5/6">
                <flux:table.columns class="bg-gray-200">
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Owner</flux:table.column>
                    <flux:table.column>Last Modified</flux:table.column>
                    <flux:table.column>Size</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($files as $file)
                        <flux:table.row :key="$file->id" x-data x-on:dblclick="$wire.openFolder({{$file->id}})" class="hover:bg-blue-100">
                            <flux:table.cell class="flex items-center gap-3">
                                {{ $file->name }}
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">{{ $file->owner }}</flux:table.cell>
                            <flux:table.cell>
                                {{$file->updated_at->diffForHumans()}}    
                            </flux:table.cell>
                            <flux:table.cell variant="strong">{{ $file->get_file_size() }}</flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="3" class="text-center py-12 text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-3">
                                    <flux:icon.archive-box-x-mark class="opacity-40 size-8" />
                                    <div>
                                        <div class="font-medium text-base">There is no data in this folder</div>
                                    </div>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
    </div>
    
    
</section>
