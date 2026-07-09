@props(['idea' => new App\Models\Idea()])
<x-modal name="{{ $idea->exists ? 'edit-idea' : 'create-idea' }}" title="{{$idea->exists ? 'Edit an Idea' : 'Create a new idea'}}">
    <form action="{{ $idea->exists ? route('idea.update', $idea) : route('idea.store') }}" method="post"
          x-data="{
           status: @js(old('status', $idea->status->value)),
           newLink: '',
           links: @js(old('links', $idea->links ?? [])),
           newStep: '',
           steps: @js(old('steps', $idea->steps->map->only(['id', 'description', 'completed']))),
           hasImage: false
           }"
          x-bind:enctype="hasImage ? 'multipart/form-data' : 'application/x-www-form-urlencoded'"
    >
        @csrf
        @if($idea->exists)
            @method('PATCH')
        @endif
        <div class="space-y-6">
            <x-form.field
                label="Title" name="title"
                placeholder="Your idea title"
                autofocus
                required
                :value="$idea->title"
            />

            <div class="space-y-2">
                <label for="status" class="label">Status</label>

                <div class="flex gap-x-3">
                    @foreach(App\IdeaStatus::cases() as $status)
                        <button
                            type="button"
                            class="btn flex-1 h-10"
                            :class="{'btn-outlined': status !== @js($status->value)}"
                            @click="status = @js($status->value)"
                            data-test="button-status-{{$status->value}}"
                        >
                            {{ $status->label() }}
                        </button>
                    @endforeach
                    <input type="hidden" name="status" :value="status">
                </div>
                <x-form.error name="status"/>
            </div>

            <x-form.field
                label="Description" name="description"
                placeholder="What's on your mind?"
                type="textarea"
                :value="$idea->description"
            />

            <div class="space-y-2">
                <label for="image" class="label">Featured Image</label>

                @if($idea->image_path)
                    <div class="space-y-2">
                        <img src="{{ asset('storage/' . $idea->image_path) }}" alt=""
                             class="w-full h-48 object-cover rounded-lg">
                        <button class="btn btn-outlined h-10 w-full" form="delete-image-form">Remove Image</button>
                    </div>
                @endif

                <input type="file" name="image" accept="image/*" @change="hasImage = $event.target.files.length > 0">
                <x-form.error name="image"/>
            </div>

            <div>
                <fieldset class="space-y-3">
                    <legend class="label">Actionable Steps</legend>

                    <template x-for="(step, index) in steps" :key="index">
                        <div class="flex gap-x-2 items-center">
                            <input :name="`steps[${index}][description]`" x-model="step.description" class="input">
                            <input type="hidden" :name="`steps[${index}][completed]`" x-model="step.completed ? '1' : '0'" class="input">
                            <button
                                type="button"
                                @click="steps.splice(index, 1)"
                                aria-label="Remove step"
                                :id="'remove-step-' + index"
                            >
                                <x-icons.x-mark class="form-muted-icon"/>
                            </button>
                        </div>
                    </template>

                    <div class="flex gap-x-2 items-center">
                        <input
                            x-model="newStep"
                            id="new-step"
                            placeholder="What do we do?"
                            class="input flex-1"
                            spellcheck="false"
                            data-test="new-step"
                        >
                        <button
                            type="button"
                            @click="
                            steps.push({description: newStep.trim(), completed: false }); newStep = '';"
                            :disabled="newStep.trim().length === 0 "
                            data-test="submit-new-step"
                        >
                            <x-icons.x-mark class="rotate-45 form-muted-icon"/>
                        </button>
                    </div>
                </fieldset>
            </div>
        </div>

        <div>
            <fieldset class="space-y-3">
                <legend class="label">Links</legend>

                <template x-for="(link, index) in links" :key="index">
                    <div class="flex gap-x-2 items-center">
                        <input name="links[]" x-model="link" class="input">
                        <button
                            type="button"
                            @click="links.splice(index, 1)"
                            aria-label="Remove link"
                            :id="'remove-link-' + index"
                        >
                            <x-icons.x-mark class="form-muted-icon"/>
                        </button>
                    </div>
                </template>

                <div class="flex gap-x-2 items-center">
                    <input
                        x-model="newLink"
                        type="url"
                        id="new-link"
                        placeholder="http://example.com"
                        autocomplete="url"
                        class="input flex-1"
                        spellcheck="false"
                        data-test="new-link"
                    >
                    <button
                        type="button"
                        @click="links.push(newLink.trim()); newLink = '';"
                        :disabled="newLink.trim().length === 0 "
                        data-test="submit-new-link"
                    >
                        <x-icons.x-mark class="rotate-45 form-muted-icon"/>
                    </button>
                </div>
            </fieldset>
        </div>

        <div class="flex justify-end gap-x-5 mt-4">
            <button type="button" @click="$dispatch('close-modal')">Cancel</button>
            <button type="submit" class="btn">{{ $idea->exists ? 'Update' : 'Create' }}</button>
        </div>
    </form>

    @if($idea->image_path)
        <form action="{{ route('idea.image.destroy', $idea) }}" method="POST" id="delete-image-form">
            @method('DELETE')
            @csrf
        </form>
    @endif
</x-modal>
