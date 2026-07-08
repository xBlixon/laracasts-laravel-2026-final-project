<x-layout>
    <div>
        <header class="py-8 md:py-12">
            <h1 class="text-3xl font-bold">Ideas</h1>
            <p class="text-muted-foreground text-sm mt-2">Capture your thoughts. Make a plan.</p>

            <x-card
                class="mt-10 cursor-pointer h-32 w-full text-left"
                is="button"
                type="button"
                x-data
                @click="$dispatch('open-modal', 'create-idea')"
                data-test="create-idea-button"
            >
                <p>What's the idea?</p>
            </x-card>
        </header>

        <div>
            <a
                href="{{route('idea.index')}}"
                class="btn {{ request()->has('status') ? 'btn-outlined' : ''}}"
            >All <span class="text-xs pl-3">{{ $statusCounts->get('all') }}</span></a>
            @foreach(App\IdeaStatus::cases() as $status)
                <a
                    href="{{route('idea.index', ['status' => $status->value])}}"
                    class="btn {{ request()->query('status') === $status->value ? '' : 'btn-outlined'}}"
                >{{ $status->label() }} <span class="text-xs pl-3">{{ $statusCounts->get($status->value) }}</span></a>
            @endforeach
        </div>

        <div class="mt-10 text-muted-foreground">
            <div class="grid md:grid-cols-2 gap-6">
                @forelse($ideas as $idea)
                    <x-card href="{{ route('idea.show', $idea) }}">
                        @if($idea->image_path)
                            <div class="mb-4 -mt-4 -mx-4 rounded-t-lg overflow-hidden">
                                <img src="{{ asset('storage/' . $idea->image_path) }}" alt=""
                                     class="w-full h-40 object-cover ">
                            </div>
                        @endif
                        <h3 class="text-foreground text-lg"> {{ $idea->title }} </h3>
                        <div>
                            <x-idea.status-label :status="$idea->status"/>
                        </div>
                        <div class="mt-5 line-clamp-3"> {{ $idea->description }} </div>
                        <div class="mt-4"> {{ $idea->created_at->diffForHumans() }} </div>
                    </x-card>
                @empty
                    <x-card>
                        <p>No ideas yet...</p>
                    </x-card>
                @endforelse
            </div>
        </div>
    </div>

    <x-modal name="create-idea" title="Create new idea">
        <form action="{{ route('idea.store') }}" method="post"
              x-data="{ status: @js(App\IdeaStatus::PENDING->value), newLink: '', links: [], newStep: '', steps: [], hasImage: false }"
              x-bind:enctype="hasImage ? 'multipart/form-data' : 'application/x-www-form-urlencoded'"
        >
            @csrf
            <div class="space-y-6">
                <x-form.field
                    label="Title" name="title"
                    placeholder="Your idea title"
                    autofocus
                    required
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
                />

                <div class="space-y-2">
                    <label for="image" class="label">Featured Image</label>
                    <input type="file" name="image" accept="image/*" @change="hasImage = $event.target.files.length > 0">
                    <x-form.error name="image"/>
                </div>

                <div>
                    <fieldset class="space-y-3">
                        <legend class="label">Actionable Steps</legend>

                        <template x-for="(step, index) in steps" :key="index">
                            <div class="flex gap-x-2 items-center">
                                <input name="steps[]" x-model="step" class="input">
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
                                @click="steps.push(newStep.trim()); newStep = '';"
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
                <button type="submit" class="btn">Create</button>
            </div>
        </form>
    </x-modal>
</x-layout>
