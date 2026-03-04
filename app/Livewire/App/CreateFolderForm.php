<?php

namespace App\Livewire\App;

use App\Models\File;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateFolderForm extends Component
{
    public string $name = '';
    public ?int $parent_id = null; 
    public ?File $parent = null;
    public $folder;
    
    public function getRoot()
    {
        return File::query()->whereIsRoot()->where('created_by', Auth::user()->id)->firstOrFail();
    }

    protected function nameRules(?int $user_id = null, ?int $parent_id = null):array
    {
        return [
            'required',
            Rule::unique(File::class, 'name')
                ->where('created_by', $user_id)
                ->where('parent_id', $parent_id)
                ->whereNull('deleted_at')
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

    protected function messages() 
    {
        return [
            'name.unique' => 'Folder :attribute already exists',
        ];
    }

    public function createFolder()
    {
        $user = Auth::user();
        
        $this->parent = File::query()->where('created_by', $user->id)->where('path', $this->folder)->firstOrFail();;
        if ($this->parent) {
            $this->parent_id = $this->parent->id;
        }

        if (!$this->parent) {
            $this->parent = $this->getRoot();
            $this->parent_id = $this->parent->id;
        } 
        
        $this->validate([
            'name' => $this->nameRules($user->id, $this->parent_id),
            'parent_id' => $this->parentRules($user->id),
        ]);

        $folder = new File();
        $folder->is_folder = 1;
        $folder->name = $this->name;
        $folder->created_by = $user->id;
        $folder->updated_by = $user->id;

        $this->parent->appendNode($folder);
        
        $this->redirect(route('my-files.index', $this->parent->path), navigate:true);
    }
    
}
