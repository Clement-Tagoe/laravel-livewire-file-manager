<?php

namespace App\Livewire\Pages;

use App\Models\File;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class MyFiles extends Component
{
    use WithPagination;

    public $folder;
    public ?File $currentFolder;

    public function openFolder($file_id)
    {
       $folder = File::where('id', $file_id)
                        ->where('is_folder', true)
                        ->firstOrFail();

       $this->redirect(route('my-files.index', $folder->path), navigate: true);
    }


    public function getRoot()
    {
        return File::query()->whereIsRoot()->where('created_by', Auth::user()->id)->firstOrFail();
    }

    public function render()
    {
       
        if ($this->folder) {
            $this->currentFolder = File::query()->where('created_by', Auth::user()->id)->where('path', $this->folder)->firstOrFail();
        }
    
        if (!$this->folder) 
            {
                $this->currentFolder = $this->getRoot();
            }
        
        $folder = $this->currentFolder;
        
        $ancestors = $folder->ancestorsAndSelf($folder->id);
        
        $files = File::query()
                        ->where('parent_id', $folder->id)
                        ->where('created_by', Auth::user()->id)
                        ->orderBy('is_folder', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->paginate(12);

        return view('livewire.pages.my-files', compact('files', 'folder', 'ancestors'));
    }
}
