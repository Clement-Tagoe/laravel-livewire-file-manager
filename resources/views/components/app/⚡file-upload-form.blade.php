<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\File;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

new class extends Component
{
    use WithFileUploads;

    public $files = [];
    public ?int $parent_id = null; 
    public ?File $parent = null;
    public $currentUrl = '';
    public $folder;

    public function mount(Request $request)
    {
        $this->currentUrl = $request->url();
        
    }

    public function getFolderPathFromUrl()
    {
        $parsed = parse_url($this->currentUrl, PHP_URL_PATH);           // /my-files/...
    
        // Option A: using Str helper
        return Str::after($parsed, '/my-files/');          // new-files/...

        // Option B: more defensive
        // return Str::after($parsed, config('filesystems.disks.local.root_prefix', '/my-files'));
    }
    
    public function getRoot()
    {
        return File::query()->whereIsRoot()->where('created_by', Auth::user()->id)->firstOrFail();
    }
    
    protected function saveFile($file, $user, $parent): void
    {
        $path = $file->store('/files/' . $user->id, 'local');

        $model = new File();
        $model->storage_path = $path;
        $model->is_folder = false;
        $model->name = $file->getClientOriginalName();
        $model->mime = $file->getMimeType();
        $model->size = $file->getSize();
        $model->created_by = $user->id;
        $model->updated_by = $user->id;

        $parent->appendNode($model);
        
    }

    protected function fileRules():array
    {
        return [
            'required',
                'file',
                function ($attribute, $value, $fail) {
                        /** @var $value \Illuminate\Http\UploadedFile */
                        $file = File::query()->where('name', $value->getClientOriginalName())
                            ->where('created_by', Auth::user()->id)
                            ->where('parent_id', $this->parent_id)
                            ->whereNull('deleted_at')
                            ->exists();

                        if ($file) {
                            $fail('File "' . $value->getClientOriginalName() . '" already exists.');
                        }
                }
        ];
    }

    protected function parentRules(?int $user_id = null):array
    {
        return [
            Rule::exists(File::class, 'id')
                ->where(function (Builder $query) use ($user_id) {
                    return $query
                        ->where('is_folder', '=', '1')
                        ->where('created_by', '=' , $user_id);
                })
        ];
    }

    public function updatedFiles()
    {
        $user = Auth::user();

        $this->folder = $this->getFolderPathFromUrl();

        if ($this->folder === "/my-files")
        {   
            $this->parent = $this->getRoot();
            $this->parent_id = $this->parent->id;
        } else {
            $this->parent = File::query()->where('created_by', $user->id)->where('path', $this->folder)->firstOrFail();
            $this->parent_id = $this->parent->id;
        }

        $this->validate([
            'files.*' => $this->fileRules(),
            'parent_id' => $this->parentRules($user->id),
        ]);
        
        foreach ($this->files as $file) {
            /** @var \Illuminate\Http\UploadedFile $file */
            $this->saveFile($file, $user, $this->parent);
        }
    }
};
?>

<form method="POST" wire:submit="updatedFiles" class="space-y-6" x-data>
    <flux:input type="file" wire:model="files" label="Upload File" multiple class="sr-only"
        {{-- x-on:change="
            console.log($event.target.files);
            " --}}
    />
</form>