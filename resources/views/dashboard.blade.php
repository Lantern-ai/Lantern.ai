@extends('layouts.app')

@section('content')
    <main class="flex-grow p-4 md:p-6 lg:p-8" x-data="{ view: localStorage.getItem('docs_view') || 'grid' }" x-init="$watch('view', value => localStorage.setItem('docs_view', value))">
        <div class="max-w-7xl mx-auto">

            <!-- Create New Document Section -->
            <section>
                <h2 class="text-lg font-semibold mb-4">Start a new document</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <a onclick="create_doc_modal.showModal()"
                       class="card card-compact bg-base-100 border-4 border-base-300 hover:border-primary cursor-pointer rounded-xl transition-all duration-200 hover:shadow-lg">
                        <figure class="h-40 bg-base-200 flex items-center justify-center">
                            <i class="bi bi-file-earmark-plus text-5xl text-theme"></i>
                        </figure>
                        <div class="card-body">
                            <h3 class="card-title text-sm">Blank document</h3>
                        </div>
                    </a>
                </div>
            </section>

            <!-- Recent Documents Section -->
            <section class="mt-8">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <h2 class="text-lg font-semibold">Recent documents</h2>

                    <div class="flex items-center gap-4">
                        <!-- Sort Dropdown -->
                        <div class="dropdown dropdown-end">
                            <button tabindex="0" role="button" class="btn btn-sm">
                                Sort by <i class="bi bi-chevron-down text-xs ml-1"></i>
                            </button>
                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                <li>
                                    <a href="{{ route('dashboard', ['sort' => 'date', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                        <i class="bi bi-calendar"></i> Date
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard', ['sort' => 'title', 'order' => request('order') === 'asc' ? 'desc' : 'asc']) }}">
                                        <i class="bi bi-sort-alpha-down"></i> Title
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- View Toggle -->
                        <div class="join">
                            <button class="join-item btn btn-sm"
                                    :class="{ 'btn-active': view === 'grid' }"
                                    @click="view = 'grid'">
                                <i class="bi bi-grid-fill text-lg"></i>
                            </button>
                            <button class="join-item btn btn-sm"
                                    :class="{ 'btn-active': view === 'list' }"
                                    @click="view = 'list'">
                                <i class="bi bi-list-ul text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                @if($scripts->isEmpty())
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <i class="bi bi-folder2-open text-6xl text-base-content/30 mb-4"></i>
                        <p class="text-base-content/70 mb-4">No documents yet. Create your first document to get started!</p>
                        <a href="{{ route('script.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle mr-2"></i> Create Document
                        </a>
                    </div>
                @else
                    <!-- Grid View -->
                    <div x-show="view === 'grid'"
                         x-transition
                         class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($scripts as $script)
                            <div class="card card-compact bg-base-100 border-4 border-base-300 hover:border-primary cursor-pointer rounded-xl transition-all duration-200 hover:shadow-lg">
                                <a href="{{ route('script.editor', $script->id) }}">
                                    <figure class="h-40 bg-base-200 flex items-center justify-center text-center font-semibold p-4">
                                        <span class="text-2xl">{{ Str::limit($script->title, 30) }}</span>
                                    </figure>
                                </a>
                                <div class="card-body">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-grow">
                                            <a href="{{ route('script.editor', $script->id) }}">
                                                <h3 class="card-title text-sm leading-tight mb-2 hover:text-primary">
                                                    {{ $script->title }}
                                                </h3>
                                            </a>
                                            <p class="text-xs text-base-content/70 mb-1">
                                                <i class="bi bi-file-earmark-word-fill text-theme"></i>
                                                {{ $script->language }}
                                            </p>
                                            <p class="text-xs text-base-content/70 mb-1">
                                                Updated {{ $script->updated_at->format('d M Y') }}
                                            </p>
                                            @if($script->description)
                                                <p class="text-xs text-base-content/70 line-clamp-2">
                                                    {{ $script->description }}
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Actions Dropdown -->
                                        <div class="dropdown dropdown-end">
                                            <button tabindex="0" role="button" class="btn btn-ghost btn-xs btn-circle">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                                <li>
                                                    <a href="{{ route('script.editor', $script->id) }}">
                                                        <i class="bi bi-eye"></i> Open
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('script.editor', $script->id) }}">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action=""
                                                          method="POST"
                                                          onsubmit="return confirm('Are you sure you want to delete this document?');"
                                                          class="w-full">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-error w-full text-left">
                                                            <i class="bi bi-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- List View -->
                    <div x-show="view === 'list'"
                         x-transition
                         class="space-y-2">
                        @foreach($scripts as $script)
                            <div class="flex items-center p-3 bg-base-100 rounded-lg hover:bg-base-300 transition-colors duration-200">
                                <i class="bi bi-file-earmark-word-fill text-theme text-xl mr-4"></i>
                                <div class="flex-grow">
                                    <a href="{{ route('script.editor', $script->id) }}" class="hover:text-primary">
                                        <div class="font-semibold">{{ $script->title }}</div>
                                    </a>
                                    @if($script->description)
                                        <div class="text-xs text-base-content/70">{{ Str::limit($script->description, 100) }}</div>
                                    @endif
                                </div>
                                <span class="text-sm text-base-content/70 mr-8 hidden md:block">{{ $script->language }}</span>
                                <span class="text-sm text-base-content/70 mr-4">{{ $script->updated_at->format('d M Y') }}</span>

                                <!-- Actions Dropdown -->
                                <div class="dropdown dropdown-end">
                                    <button tabindex="0" role="button" class="btn btn-ghost btn-xs btn-circle">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                        <li>
                                            <a href="{{ route('script.editor', $script->id) }}">
                                                <i class="bi bi-eye"></i> Open
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('script.editor', $script->id) }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                        </li>
                                        <li>
                                            <form action=""
                                                  method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this document?');"
                                                  class="w-full">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-error w-full text-left">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

        </div>
    </main>

    <!-- Create Document Modal -->
    <dialog id="create_doc_modal" class="modal">
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>

            <h3 class="font-bold text-lg mb-4">Create a New Document</h3>

            <form action="{{ route('script.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <!-- Title Input -->
                    <div>
                        <label class="label">
                            <span class="label-text">Title</span>
                        </label>
                        <input type="text"
                               name="title"
                               placeholder="Document Title"
                               class="input input-bordered w-full"
                               value="{{ old('title') }}"
                               required>
                        @error('title')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Language Select -->
                    <div>
                        <label class="label">
                            <span class="label-text">Language</span>
                        </label>
                        <select name="language" class="select select-bordered w-full" required>
                            <option value="English" {{ old('language') === 'English' ? 'selected' : '' }}>English</option>
                            <option value="Malayalam" {{ old('language') === 'Malayalam' ? 'selected' : '' }}>Malayalam</option>
                        </select>
                        @error('language')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description Textarea -->
                    <div>
                        <label class="label">
                            <span class="label-text">Description</span>
                        </label>
                        <textarea
                            name="description"
                            class="textarea textarea-bordered w-full"
                            placeholder="A brief description..."
                            rows="3">{{ old('description') }}</textarea>
                        @error('description')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="modal-action">
                    <button type="button" class="btn" onclick="create_doc_modal.close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>

        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
@endsection

@section('script')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('docsDashboard', () => ({
                // Theme Management
                isDarkMode: (() => {
                    const saved = localStorage.getItem('docs_theme_dark');
                    if (saved !== null) return saved === 'true';
                    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                })(),

                // Initialize
                init() {
                    this.applyTheme();

                    // Watch for theme changes
                    this.$watch('isDarkMode', (value) => {
                        localStorage.setItem('docs_theme_dark', value.toString());
                        this.applyTheme();
                    });
                },

                // Apply theme to document
                applyTheme() {
                    const theme = this.isDarkMode ? 'dracula' : 'nord';
                    document.documentElement.setAttribute('data-theme', theme);
                }
            }));
        });
    </script>
@endsection
