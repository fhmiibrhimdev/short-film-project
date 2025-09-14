<?php

namespace App\Livewire\Management;

use App\Models\Shots;
use App\Models\Scenes;
use Livewire\Component;
use App\Models\SubShots;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class ShortFilm extends Component
{
    use WithPagination;
    use WithFileUploads;
    #[Title('Short Film')]

    protected $listeners = [
        'delete',
        'deleteShot',
        'deleteSubShot',
    ];
    protected $rules = [
        'scene_id' => 'required',
    ];
    protected $paginationTheme = 'bootstrap';

    public $lengthData = 100;
    public $searchTerm;
    public $previousSearchTerm = '';
    public $isEditing = false;

    public $dataId;
    public $scene_id, $shot_id, $user_id, $shot_number, $description;
    public $shots = [];
    public $subshots = [];

    public $sub_shot_id, $sub_shot_number, $sub_shot_description, $video_file_path, $duration, $scenes, $filter_scene;

    public function mount()
    {
        $this->filter_scene = "ALL";
        $this->scene_id = 1;
        $this->shot_id = 1;
        $this->shots = Shots::where('scene_id', $this->scene_id)->get();
        $this->scenes = Scenes::all();
        $this->video_file_path = NULL;
        $this->duration = 0;
    }

    public function updatingLengthData()
    {
        $this->resetPage();
    }

    private function searchResetPage()
    {
        if ($this->searchTerm !== $this->previousSearchTerm) {
            $this->resetPage();
        }

        $this->previousSearchTerm = $this->searchTerm;
    }

    public function updatedSceneId()
    {
        $shots = Shots::where('scene_id', $this->scene_id);
        $this->shots = $shots->get();
        $this->shot_id = $shots->first()->id ?? NULL;
    }

    // public function updatedfilterScene()
    // {
    //     dd($this->filter_scene);
    // }

    public function render()
    {
        $this->searchResetPage();
        $search = '%' . $this->searchTerm . '%';

        $query = Shots::join('scenes', 'shots.scene_id', '=', 'scenes.id')
            ->leftJoin('sub_shots', 'sub_shots.shot_id', '=', 'shots.id')
            ->leftJoin('users', 'users.id', '=', 'sub_shots.user_id')
            ->select(
                'shots.id as shot_id',
                'shots.shot_number',
                'shots.description as shot_description',
                'scenes.location',
                'shots.scene_id',
                'users.name',
                'sub_shots.id as sub_shot_id',
                'sub_shots.sub_shot_number',
                'sub_shots.sub_shot_description',
                'sub_shots.video_file_path',
                'sub_shots.duration'
            );

        // Tambahkan filter pencarian
        $query->where(function ($q) use ($search) {
            $q->where('shots.scene_id', 'LIKE', $search)
                ->orWhere('sub_shots.sub_shot_description', 'LIKE', $search);
        });

        // Jika filter_scene tidak "ALL", tambahkan filter scene
        if ($this->filter_scene != "ALL") {
            $query->where('scenes.id', $this->filter_scene);
        }

        $data = $query->orderBy('scenes.id', 'DESC')
            ->paginate($this->lengthData);


        return view('livewire.management.short-film', compact('data'));
    }

    private function generateStoragePath($shot_id)
    {
        $shot = Shots::find($shot_id);
        $scene = Scenes::find($shot->scene_id);

        $sceneNumber = str_pad($scene->scene_number, 2, '0', STR_PAD_LEFT);
        $shotNumber = $shot->shot_number;

        return "Scene {$sceneNumber}/{$shotNumber}";
    }

    private function generateSubShotNumber()
    {
        // Cari jumlah subshot yang sudah ada di shot ini
        $count = SubShots::where('shot_id', $this->shot_id)->count();

        // Cari data shot dan scene
        $shot = Shots::find($this->shot_id);
        $scene = Scenes::find($shot->scene_id);

        $sceneNumber = str_pad($scene->scene_number, 2, '0', STR_PAD_LEFT); // 2 digit
        $shotNumber = $shot->shot_number;
        $subNumber = str_pad($count + 1, 2, '0', STR_PAD_LEFT); // 2 digit jugax

        return "{$shotNumber}_{$subNumber}";
    }

    private function dispatchAlert($type, $message, $text)
    {
        $this->dispatch('swal:modal', [
            'type'      => $type,
            'message'   => $message,
            'text'      => $text
        ]);

        $this->resetInputFields();
    }

    public function isEditingMode($mode)
    {
        $this->isEditing = $mode;
    }

    public function resetInputFields()
    {
        $this->scene_id = 1;
        $this->shot_number = '';
        $this->description = '';

        $this->sub_shot_id = null;
        $this->sub_shot_number = '';
        $this->sub_shot_description = '-';
        $this->video_file_path = null;
        $this->duration = 0;
    }

    public function cancel()
    {
        $this->resetInputFields();
    }

    public function storeShot()
    {
        // $this->validate();

        $lastShot = Shots::where('scene_id', $this->scene_id)
            ->orderBy('shot_number', 'desc')
            ->first();

        if ($lastShot) {
            $lastNumber = intval(substr($lastShot->shot_number, -2)); // Ambil 2 digit terakhir
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }

        $scene = Scenes::findOrFail($this->scene_id);
        $sceneNumber = str_pad($scene->scene_number, 2, '0', STR_PAD_LEFT);

        $this->shot_number = $sceneNumber . '_' . $newNumber;

        Shots::create([
            'scene_id'      => $this->scene_id,
            'shot_number'   => $this->shot_number,
            'description'   => $this->description,
        ]);

        $this->dispatchAlert('success', 'Success!', 'Data created successfully.');
    }

    public function storeSubShot()
    {
        // Validasi dulu file harus video atau audio
        $this->validate([
            'video_file_path' => 'nullable|mimes:mp4,mov,avi,wmv,mp3,wav' // validasi tipe file
        ]);

        $subShot = SubShots::create([
            'shot_id' => $this->shot_id,
            'sub_shot_number' => $this->generateSubShotNumber(),
            'sub_shot_description' => $this->sub_shot_description,
            'duration' => $this->duration,
            'video_file_path' => NULL, // default awal
        ]);

        // $path = null;
        if ($this->video_file_path) {
            $folderPath = $this->generateStoragePath($this->shot_id);
            $fileName = $subShot->sub_shot_number . '.' . $this->video_file_path->getClientOriginalExtension();
            $path = $this->video_file_path->storeAs($folderPath, $fileName);
            $subShot->video_file_path = str_replace('public/', '', $path);
            $subShot->save();
        }

        $this->dispatchAlert('success', 'Success!', 'Data created successfully.');
    }

    public function editShot($id)
    {
        $this->isEditing = true;
        $data = Shots::findOrFail($id);
        $this->dataId = $id;
        $this->scene_id  = $data->scene_id;
        $this->shot_number  = $data->shot_number;
        $this->description  = $data->description;
    }

    public function editSubShot($id)
    {
        $this->isEditing = true;
        $data = SubShots::findOrFail($id);
        $this->dataId = $id;
        $this->shot_id  = $data->shot_id;
        $this->user_id  = $data->user_id;
        $this->sub_shot_number  = $data->sub_shot_number;
        $this->sub_shot_description  = $data->sub_shot_description;
        $this->duration  = $data->duration;
    }

    public function updateShot()
    {
        $this->validate();

        if ($this->dataId) {
            Shots::findOrFail($this->dataId)->update([
                'scene_id'      => $this->scene_id,
                'shot_number'   => $this->shot_number,
                'description'   => $this->description,
            ]);

            $this->dispatchAlert('success', 'Success!', 'Data updated successfully.');
            $this->dataId = null;
        }
    }

    public function updateSubShot()
    {
        if ($this->dataId) {
            $this->validate([
                'video_file_path' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,audio/mpeg,audio/wav,audio/ogg',
            ]);

            $subShot = SubShots::findOrFail($this->dataId);

            $subShot->update([
                'sub_shot_description' => $this->sub_shot_description,
                'duration' => $this->duration,
            ]);

            if ($this->video_file_path) {
                $folderPath = $this->generateStoragePath($subShot->shot_id);
                $fileName = $subShot->sub_shot_number . '.' . $this->video_file_path->getClientOriginalExtension();

                if ($subShot->video_file_path && Storage::exists('public/' . $subShot->video_file_path)) {
                    Storage::delete('public/' . $subShot->video_file_path);
                }

                $path = $this->video_file_path->storeAs($folderPath, $fileName);
                $subShot->video_file_path = str_replace('public/', '', $path);
                $subShot->save();
            }

            $this->dispatchAlert('success', 'Success!', 'Data updated successfully.');
            $this->dataId = null;
        }
    }

    public function deleteConfirmShot($id)
    {
        $this->dataId = $id;
        $this->dispatch('swal:confirm:shot', [
            'type'      => 'warning',
            'message'   => 'Are you sure?',
            'text'      => 'If you delete the data, it cannot be restored!'
        ]);
    }

    public function deleteConfirmSubShot($id)
    {
        $this->dataId = $id;
        $this->dispatch('swal:confirm:subshot', [
            'type'      => 'warning',
            'message'   => 'Are you sure?',
            'text'      => 'If you delete the data, it cannot be restored!'
        ]);
    }

    public function deleteShot()
    {
        Shots::findOrFail($this->dataId)->delete();
        $this->dispatchAlert('success', 'Success!', 'Shot deleted successfully.');
    }

    public function deleteSubShot()
    {
        $subShot = SubShots::findOrFail($this->dataId);

        // Hapus file dari storage
        if ($subShot->video_file_path && Storage::disk('public')->exists($subShot->video_file_path)) {
            Storage::disk('public')->delete($subShot->video_file_path);
        }

        // Hapus record dari database
        $subShot->delete();
        $this->dispatchAlert('success', 'Success!', 'SubShot deleted successfully.');
    }
}
