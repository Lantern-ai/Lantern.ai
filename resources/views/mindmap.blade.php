<html data-theme="nord">
<head>
    <style>
        #mindMapSvgHolder > * { width: 100%; }
        [x-cloak] { display: none !important; }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.0/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.1/dist/full.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Blade-safe Alpine factory (no complex JS inside attributes)
        function mindmapPage() {
            return {
                active: 1,
                regenerating: false,

                async regenerateCharMindMap() {
                    try {
                        this.regenerating = true;

                        const res = await fetch('{{ route('regenCharMindMap') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ script_id: @json($script_id) })
                        });

                        const text = await res.text();

                        const holder = document.getElementById('mindMapSvgHolder');
                        const loading = document.getElementById('load-overlay');

                        holder.innerHTML = text;
                        holder.appendChild(loading);
                    } catch (error) {
                        console.error('An error occurred:', error);
                    } finally {
                        this.regenerating = false;
                    }
                }
            };
        }
    </script>
</head>

<body class="bg-base-100 w-full" x-data="mindmapPage()">
<script>
    @if ($mindmap == null)
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('regenBtn')?.click();
    });
    @endif
</script>

<div id="mindMapContainer" class="card rounded-none m-0 bg-base-300 shadow-xl w-full">
    <div class="card-body flex flex-row shadow-sm p-4 justify-between">
        <div class="flex flex-row gap-3">
            <button class="btn" :class="active===1 ? 'btn-active' : ''" @click="active=1">Character Map</button>
            <button class="btn" :class="active===2 ? 'btn-active' : ''" @click="active=2">Story Map</button>
            <button class="btn" :class="active===3 ? 'btn-active' : ''" @click="active=3">Pacing Analysis</button>
        </div>

        <div class="flex flex-row gap-5">
            <div class="card bg-base-100 shadow-xs">
                <p class="card-title mx-3">Viewing Script 1 Mindmaps</p>
            </div>

            <button class="btn" @click="regenerateCharMindMap()" id="regenBtn" x-ref="regenBtn">Regenerate</button>
        </div>
    </div>

    <div id="mindMapSvgHolder" class="relative">
        {!! $mindmap !!}

        <div
            class="absolute inset-0 w-full h-full bg-base-100/[85%] flex flex-row justify-center"
            id="load-overlay"
            x-show="regenerating"
            x-cloak
        >
            <div class="mt-[250px]">
                <span>Loading&nbsp;</span>
                <span class="loading loading-spinner loading-xl"></span>
            </div>
        </div>
    </div>
</div>
</body>
</html>
