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
    public $relative_paths = [];
    public $folder_name;
    public ?int $parent_id = null; 
    public ?File $parent = null;
    public $currentUrl = '';
    public $folder;

    public function mount(Request $request)
    {
        $this->currentUrl = $request->url();
        
    }

    // protected function prepareForValidation()
    // {
    //     $paths = array_filter($this->relative_paths ?? [], fn($f) => $f != null);

    //     $this->merge([
    //         'file_paths' => $paths,
    //         'folder_name' => $this->detectFolderName($paths)
    //     ]);
    // }

    // protected function passedValidation()
    // {
    //     $data = $this->validated();

    //     $this->replace([
    //         'file_tree' => $this->buildFileTree($this->file_paths, $data['files'])
    //     ]);
    // }

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

    public function detectFolderName($paths)
    {
        if (!$paths) {
            return null;
        }

        $parts = explode("/", $paths[0]);

        return $parts[0];
    }

    private function buildFileTree($filePaths, $files)
    {
        $filePaths = array_slice($filePaths, 0, count($files));
        $filePaths = array_filter($filePaths, fn($f) => $f != null);

        $tree = [];

        foreach ($filePaths as $ind => $filePath) {
            $parts = explode('/', $filePath);

            $currentNode = &$tree;
            foreach ($parts as $i => $part) {
                if (!isset($currentNode[$part])) {
                    $currentNode[$part] = [];
                }

                if ($i === count($parts) - 1) {
                    $currentNode[$part] = $files[$ind];
                } else {
                    $currentNode = &$currentNode[$part];
                }

            }
        }

        return $tree;
    }

    public function saveFileTree($fileTree, $parent, $user)
    {
        foreach ($fileTree as $name => $file) {
            if (is_array($file)) {
                $folder = new File();
                $folder->is_folder = 1;
                $folder->name = $name;
                $folder->created_by = $user->id;
                $folder->updated_by = $user->id;

                $parent->appendNode($folder);
                $this->saveFileTree($file, $folder, $user);
            } else {
                $this->saveFile($file, $user, $parent);
            }
        }
    }

    private function saveFile($file, $user, $parent): void
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
                    if (!$this->folder_name) {
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
            }
        ];
    }

    protected function folderNameRules(): array
    {
        return [
            'nullable',
            'string',
            function ($attribute, $value, $fail) {
                if ($value) {
                    /** @var $value \Illuminate\Http\UploadedFile */
                    $file = File::query()->where('name', $value)
                        ->where('created_by', Auth::user()->id)
                        ->where('parent_id', $this->parent_id)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($file) {
                        $fail('Folder "' . $value . '" already exists.');
                    }
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

        $this->folder_name = $this->detectFolderName($this->relative_paths);

        $this->validate([
            'files.*' => $this->fileRules(),
            'folder_name' => $this->folderNameRules(),
            'parent_id' => $this->parentRules($user->id),
        ]);

        $file_tree = $this->buildFileTree($this->relative_paths, $this->files);

        $this->saveFileTree($file_tree, $this->parent, $user);
    }
};
?>

<form method="POST" wire:submit="updatedFiles" class="space-y-6" x-data>
    <flux:input type="file" wire:model="files" label="Upload Folder" multiple class="sr-only" directory webkitdirectory
        x-on:change="$wire.set('relative_paths', Array.from($event.target.files).map(f => f.webkitRelativePath));"
    />
</form>