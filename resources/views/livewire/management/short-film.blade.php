<div>
    <section class="section custom-section">
        <div class="section-header">
            <h1>Management Short Film</h1>
            <button class="btn btn-primary ml-auto" wire:click.prevent="isEditingMode(false)" data-toggle="modal"
                data-backdrop="static" data-keyboard="false" data-target="#shotDataModal">
                <i class="fas fa-plus"></i> Add Shot
            </button>
        </div>

        <div class="section-body">
            <div class="card">
                <h3>Table Management Short Film</h3>
                <div class="card-body">
                    <div class="show-entries">
                        <p class="show-entries-show">Show</p>
                        <select wire:model.live="lengthData" id="length-data">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                            <option value="500">500</option>
                        </select>
                        <p class="show-entries-entries">Entries</p>
                    </div>
                    <div class="search-column">
                        <p>Search: </p><input type="search" wire:model.live.debounce.750ms="searchTerm" id="search-data"
                            placeholder="Search here..." class="form-control" value="">
                    </div>
                    @php
                    // Hitung total subshots dan yang sudah upload
                    $totalSubShots = $data->count();
                    $completedSubShots = $data->whereNotIn('video_file_path', [null, '-'])->count();
                    $progress = $totalSubShots > 0 ? ($completedSubShots / $totalSubShots) * 100 : 0;
                    @endphp

                    {{-- Progress Bar --}}
                    <div class="row tw-px-4">
                        <div class="col-lg-8">
                            <div class="mb-4">
                                <h5>Progress Upload Video (<strong>{{ $completedSubShots }}</strong> dari
                                    <strong>{{ $totalSubShots }}</strong>
                                    sub-shots sudah upload video.)</h5>
                                <div class="progress tw-mt-2" style="height: 25px;">
                                    <div class="progress-bar @if ($progress < 50) bg-danger @elseif ($progress < 80) bg-warning @else bg-success @endif"
                                        role="progressbar" style="width: {{ $progress }}%;"
                                        aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($progress, 1) }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group mt-2">
                                <select wire:model.change="filter_scene" class="form-control">
                                    <option value="ALL">ALL</option>
                                    @foreach ($scenes as $scene)
                                    <option value="{{ $scene->id }}">{{ $scene->scene_number }}. {{ $scene->location }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive tw-max-h-[800px]">
                        <table>
                            <thead class="tw-sticky tw-top-0">
                                <tr class="tw-text-gray-700">
                                    <th width="10%" class="text-center">No. Urut</th>
                                    <th width="45%">Desc. Shooting</th>
                                    <th class="text-center">Duration</th>
                                    <th class="text-center">USER</th>
                                    <th class="text-center"><i class="fas fa-cog"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data->groupBy('scene_id') as $scene_id => $sceneShots)
                                @php
                                $scene = $scenes->where('id', $scene_id)->first();
                                @endphp

                                {{-- Baris Scene --}}
                                <tr class="tw-font-bold tw-bg-gray-100">
                                    <td colspan="5">
                                        SCENE {{ $scene->scene_number }}. {{ $scene->location }}
                                        <span class="float-right">
                                            (Cast: {{ $scene->description }})
                                            [{{ \Carbon\Carbon::parse($scene->shooting_date)->format('Y-m-d') }}]
                                        </span>
                                    </td>
                                </tr>

                                @foreach ($sceneShots->groupBy('shot_id') as $shot_id => $subShots)
                                @php
                                $shot = $subShots->first();
                                @endphp

                                {{-- Baris Shot --}}
                                <tr class="tw-font-semibold">
                                    <td class="text-left">{{ $shot->shot_number }}</td>
                                    <td>{{ $shot->shot_description }}</td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center">
                                        <button wire:click.prevent="editShot({{ $shot->shot_id }})"
                                            class="btn btn-primary" data-toggle="modal" data-target="#shotDataModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click.prevent="deleteConfirmShot({{ $shot->shot_id }})"
                                            class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                @foreach ($subShots as $subShot)
                                {{-- Baris SubShot --}}
                                <tr class="tw-text-sm tw-text-gray-600">
                                    <td class="text-right">{{ $subShot->sub_shot_number }}</td>
                                    <td>{{ $subShot->sub_shot_description }}</td>
                                    <td class="text-center">
                                        {{ $subShot->duration ? $subShot->duration . ' dtk' : '-' }}
                                    </td>
                                    <td class="text-center">{{ $subShot->name }}</td>
                                    <td class="text-center">
                                        @if ($subShot->duration !== NULL)
                                        @if($subShot->video_file_path !== NULL)
                                        <a class="btn btn-info"
                                            href="{{ asset('storage/' . $subShot->video_file_path) }}" target="_blank">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @endif
                                        <button wire:click.prevent="editSubShot({{ $subShot->sub_shot_id }})"
                                            class="btn btn-primary" data-toggle="modal" data-target="#subShotDataModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click.prevent="deleteConfirmSubShot({{ $subShot->sub_shot_id }})"
                                            class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @endforeach

                                {{-- Total Duration Per Scene --}}
                                @php
                                $totalDuration = $sceneShots->sum('duration');
                                @endphp
                                <tr class="tw-font-bold tw-bg-green-100">
                                    <td colspan="2" class="text-right">Total Duration (Scene
                                        {{ $scene->scene_number }}):</td>
                                    <td class="text-center">{{ gmdate('i:s', $totalDuration) }}</td>
                                    <td colspan="2"></td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No data available in the table</td>
                                </tr>
                                @endforelse
                                {{-- 👉 Total Duration Semua Scene --}}
                                @if ($data->count())
                                @php
                                $totalDurationAll = $data->sum('duration');
                                @endphp
                                <tr class="tw-font-bold tw-bg-blue-200">
                                    <td colspan="2" class="text-right tw-bg-gray-100">TOTAL DURATION SEMUA
                                        SCENE:</td>
                                    <td class="text-center tw-bg-gray-100">
                                        {{ gmdate('H:i:s', $totalDurationAll) }}</td>
                                    <td colspan="2" class="tw-bg-gray-100"></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-5 px-3">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
        <button wire:click.prevent="isEditingMode(false)" class="btn-modal" data-toggle="modal" data-backdrop="static"
            data-keyboard="false" data-target="#subShotDataModal">
            <i class="far fa-plus"></i>
        </button>
    </section>

    <div class="modal fade" wire:ignore.self id="shotDataModal" aria-labelledby="shotDataModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shotDataModalLabel">{{ $isEditing ? 'Edit Data' : 'Add Data' }}</h5>
                    <button type="button" wire:click="cancel()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="scene_id">Scene</label>
                            <select wire:model="scene_id" id="scene_id" class="form-control">
                                @foreach ($scenes as $scene)
                                <option value="{{ $scene->id }}">{{ $scene->scene_number }}. {{ $scene->location }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea wire:model="description" id="description" class="form-control"
                                style="height: 120px !important"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="cancel()" class="btn btn-secondary tw-bg-gray-300"
                            data-dismiss="modal">Close</button>
                        <button type="submit" wire:click.prevent="{{ $isEditing ? 'updateShot()' : 'storeShot()' }}"
                            wire:loading.attr="disabled" class="btn btn-primary tw-bg-blue-500">Save Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" wire:ignore.self id="subShotDataModal" aria-labelledby="subShotDataModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subShotDataModalLabel">{{ $isEditing ? 'Edit Data' : 'Add Data' }}</h5>
                    <button type="button" wire:click="cancel()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="scene_id">Scene</label>
                            <select wire:model.change="scene_id" id="scene_id" class="form-control">
                                @foreach ($scenes as $scene)
                                <option value="{{ $scene->id }}">{{ $scene->scene_number }}. {{ $scene->location }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="shot_id">Shot</label>
                            <select wire:model="shot_id" id="shot_id" class="form-control">
                                @foreach ($shots as $shot)
                                <option value="{{ $shot->id }}">{{ $shot->shot_number }} - {{ $shot->description }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sub_shot_description">Sub Shot Description</label>
                            <textarea wire:model="sub_shot_description" id="sub_shot_description" class="form-control"
                                style="height: 120px !important"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="video_file_path">Video File Path</label>
                            <input type="file" wire:model="video_file_path" id="video_file_path" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="duration">Duration</label>
                            <input type="text" wire:model="duration" id="duration" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="cancel()" class="btn btn-secondary tw-bg-gray-300"
                            data-dismiss="modal">Close</button>
                        <button type="submit"
                            wire:click.prevent="{{ $isEditing ? 'updateSubShot()' : 'storeSubShot()' }}"
                            wire:loading.attr="disabled" class="btn btn-primary tw-bg-blue-500">Save Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
