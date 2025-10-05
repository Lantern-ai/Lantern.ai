<div class="drawer lg:drawer-open">
    <input id="sidebar-drawer" type="checkbox" class="drawer-toggle" />
    <div class="drawer-content flex flex-col bg-base-200">
        <header class="navbar bg-base-100 shadow-sm sticky top-0 z-30 px-4">
            <div class="navbar-start">
                <label for="sidebar-drawer" class="btn btn-ghost btn-circle lg:hidden">
                    <i class="bi bi-list text-xl"></i>
                </label>
                <div class="flex items-center ml-2">
                    <i class="bi bi-file-earmark-text-fill text-3xl text-theme"></i>
                    <span class="text-xl ml-2 font-semibold hidden sm:inline">Script Analyzer</span>
                </div>
            </div>

            <div class="navbar-end">
                <div class="dropdown dropdown-end ml-2">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                            <img alt="User Avatar" src="https://via.placeholder.com/40" />
                        </div>
                    </div>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li><a href="#">Login</a></li>
                        <li><a href="#">Register</a></li>
                        <li><hr class="my-1" /></li>
                        <li><a href="#">Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="flex-grow p-4 md:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                <section>
                    <h2 class="text-lg mb-4">Start a new document</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <div @click="create_doc_modal.showModal()" class="card card-compact bg-base-100 border-4 border-base-300 hover:border-primary cursor-pointer rounded-xl">
                            <figure class="h-40 bg-base-200 flex items-center justify-center">
                                <i class="bi bi-file-earmark-plus text-5xl text-theme"></i>
                            </figure>
                            <div class="card-body">
                                <h3 class="card-title text-sm">Blank document</h3>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mt-8">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                        <h2 class="text-lg font-semibold">Recent documents</h2>
                        <div class="flex items-center gap-4">
                            <div class="dropdown">
                                <button tabindex="0" role="button" class="btn btn-sm">
                                    Sort by <i class="bi bi-chevron-down text-xs ml-1"></i>
                                </button>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li><a @click="sortBy('date')">Date</a></li>
                                    <li><a @click="sortBy('size')">Size</a></li>
                                </ul>
                            </div>
                            <div class="join">
                                <button class="join-item btn btn-sm" :class="{ 'btn-active': view === 'grid' }" @click="view = 'grid'">
                                    <i class="bi bi-grid-fill text-lg"></i>
                                </button>
                                <button class="join-item btn btn-sm" :class="{ 'btn-active': view === 'list' }" @click="view = 'list'">
                                    <i class="bi bi-list-ul text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="view === 'grid'" x-transition class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <template x-for="doc in documents" :key="doc.id">
                            <div class="card card-compact bg-base-100 border-4 border-base-300 hover:border-primary cursor-pointer rounded-xl">
                                <figure class="h-40 bg-base-200 text-center font-semibold p-4" x-text="doc.title"></figure>
                                <div class="card-body flex-row justify-between items-center">
                                    <div>
                                        <h3 class="card-title text-sm leading-tight" x-text="doc.title"></h3>
                                        <p class="text-xs text-base-content/70 mt-1">
                                            <i class="bi bi-file-earmark-word-fill text-theme"></i>
                                            Opened <span x-text="doc.opened"></span>
                                        </p>
                                        <p class="text-xs text-base-content/70"><span x-text="doc.size"></span> KB</p>
                                    </div>
                                    <div class="dropdown dropdown-end">
                                        <button tabindex="0" role="button" class="btn btn-ghost btn-xs btn-circle">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                            <li><a>Open</a></li>
                                            <li><a>Edit</a></li>
                                            <li><a @click="deleteDocument(doc.id)">Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="view === 'list'" x-transition class="space-y-2">
                        <template x-for="doc in documents" :key="doc.id">
                            <div class="flex items-center p-3 bg-base-100 rounded-lg hover:bg-base-300 cursor-pointer">
                                <i class="bi bi-file-earmark-word-fill text-theme text-xl mr-4"></i>
                                <span class="font-semibold flex-grow" x-text="doc.title"></span>
                                <span class="text-sm text-base-content/70 mr-8 hidden md:block" x-text="doc.owner"></span>
                                <span class="text-sm text-base-content/70 mr-4" x-text="doc.opened"></span>
                                <span class="text-sm text-base-content/70 w-20 text-right mr-4"><span x-text="doc.size"></span> KB</span>
                                <div class="dropdown dropdown-end">
                                    <button tabindex="0" role="button" class="btn btn-ghost btn-xs btn-circle">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                        <li><a>Open</a></li>
                                        <li><a>Edit</a></li>
                                        <li><a @click="deleteDocument(doc.id)">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <aside class="drawer-side">
        <label for="sidebar-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
        <ul class="menu p-4 w-64 min-h-full bg-base-100 text-base-content gap-1">
            <li class="menu-title">Navigation</li>
            <li><a href="#" class="active"><i class="bi bi-folder2-open text-lg"></i> My Documents</a></li>
            <li><a href="#"><i class="bi bi-plus-square text-lg"></i> New Document</a></li>
            <li><a href="#"><i class="bi bi-star text-lg"></i> Starred</a></li>
            <li><a href="#"><i class="bi bi-trash text-lg"></i> Trash</a></li>
            <li><a href="#"><i class="bi bi-gear text-lg"></i> Settings</a></li>
        </ul>
    </aside>
</div>

<label class="swap btn btn-ghost theme-toggle-fixed">
    <input type="checkbox" x-model="isDarkMode" />
    <span class="swap-on flex items-center gap-2">
        <i class="bi bi-moon-stars-fill text-xl"></i>
        <span>Dark Mode</span>
    </span>
    <span class="swap-off flex items-center gap-2">
        <i class="bi bi-sun-fill text-xl"></i>
        <span>Light Mode</span>
    </span>
</label>
