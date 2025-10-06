<html data-theme="nord">

<head>
    <style>

        #mindMapSvgHolder > * {
            width: 100%;
        }

        #load-overlay {

        }

    </style>





    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.0/dist/cdn.min.js"></script>
{{--    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.1/dist/full.min.css" rel="stylesheet" />--}}
{{--    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>--}}
    {{-- <script src="https://unpkg.com/@panzoom/panzoom@4.6.0/dist/panzoom.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.1/dist/full.min.css" rel="stylesheet" /> --}}

    @vite(['resources/css/app.css'])

</head>

<body class="bg-base-100 w-full" x-data='{"active": 1, "regenerateCharMindMap": async function() {

                $data.regenerating = true;

                const fetchOptions = {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        "script_id": {{$script_id}}
                    })
                }


                fetch("{{ route('regenCharMindMap') }}", fetchOptions).then( response => {
                    return response.json()
                }).then(json => {
                        let loading = document.getElementById("load-overlay");
                        let mindMapSvgHolder = document.getElementById("mindMapSvgHolder");
                        mindMapSvgHolder.innerHTML = json.html;
                        mindMapSvgHolder.appendChild(loading);

                        let list = document.getElementById("characterList");
                        list.innerHTML = "";

                        json.relations.forEach((relation, index) => {
                            console.log(relation.characterName);

                            list.innerHTML += "<li @click=\"regenerateCharacterMindMapWithCharacter($el.querySelector(`a`).innerHTML)\"><a>" + relation.characterName + "</a></li>"
                        })


                        console.log(json);

                        $data.regenerating = false;
                    })
                    .catch(error => {
                        // Handle any errors that occurred during the fetch or text parsing.
                        console.error("An error occurred:", error);
                    });

                console.log("Regenerated");

            },

            regenerateCharacterMindMapWithCharacter: async function(character) {

                $data.regenerating = true;

                const fetchOptions = {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        "character": character,
                         "script_id": {{$script_id}}
                    })
                }


                fetch("{{ route('regenCharMindMapWithCharacter') }}", fetchOptions).then( response => {
                    return response.json()
                }).then(json => {
                        let loading = document.getElementById("load-overlay");
                        let mindMapSvgHolder = document.getElementById("mindMapSvgHolder");
                        mindMapSvgHolder.innerHTML = json.html;
                        mindMapSvgHolder.appendChild(loading);

                        let list = document.getElementById("characterList");
                        list.innerHTML = "";

                        json.relations.forEach((relation, index) => {
                            console.log(relation.characterName);

                            list.innerHTML += "<li @click=\"activeMmHandler($el.querySelector(`a`).innerHTML)\"><a>" + relation.characterName + "</a></li>"
                        })


                        console.log(json);

                        $data.regenerating = false;
                    })
                    .catch(error => {
                        // Handle any errors that occurred during the fetch or text parsing.
                        console.error("An error occurred:", error);
                    });

                console.log("Regenerated");

            },

            regenPacingMap: function() {

                $data.regenerating = true;

                const fetchOptions = {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        "script_id": {{$script_id}}
                    })
                }


                fetch("{{ route('generatePacingMindMap') }}", fetchOptions).then( response => {
                    return response.json()
                }).then(json => {
                        let loading = document.getElementById("load-overlay");
                        let mindMapSvgHolder = document.getElementById("mindMapSvgHolder");
                        mindMapSvgHolder.innerHTML = json.html;
                        mindMapSvgHolder.appendChild(loading);

                        console.log(json);

                        $data.regenerating = false;
                    })
                    .catch(error => {
                        // Handle any errors that occurred during the fetch or text parsing.
                        console.error("An error occurred:", error);
                    });

                console.log("Regenerated");

            },

            forceRegenPacingMap: function() {

                $data.regenerating = true;

                const fetchOptions = {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        "script_id": {{$script_id}}
                    })
                }


                fetch("{{ route('forceGeneratePacingMindMap') }}", fetchOptions).then( response => {
                    return response.json()
                }).then(json => {
                        let loading = document.getElementById("load-overlay");
                        let mindMapSvgHolder = document.getElementById("mindMapSvgHolder");
                        mindMapSvgHolder.innerHTML = json.html;
                        mindMapSvgHolder.appendChild(loading);

                        console.log(json);

                        $data.regenerating = false;
                    })
                    .catch(error => {
                        // Handle any errors that occurred during the fetch or text parsing.
                        console.error("An error occurred:", error);
                    });

                console.log("Regenerated");

            },

            activeMmHandler: function(character = null) {
                if ($data.active === 1) {

                    $data.regenerateCharacterMindMapWithCharacter(character);

                } else if ($data.active === 3) {

                    $data.regenPacingMap()

                }
            },

            activeMmHandlerRegen: function() {
                if ($data.active == 3) {
                    $data.forceRegenPacingMap()
                }
            },

            "regenerating": false,

            }'>

<script>
    @if ($mindmap == null)
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('regenBtn').click();
    })
    @endif
</script>

<div id="mindMapContainer" class="card rounded-none m-0 bg-base-300 shadow-xl w-full">

    <div class="flex flex-row shadow-sm p-4 justify-between">

        <div class="flex flex-row gap-3">

            <button class="btn" :class="active===1 ? 'btn-active' : '' " @click="active=1; window.location.href='/viewmindmap'">Character Map</button>
            <button class="btn" :class="active===2 ? 'btn-active' : '' " @click="active=2; activeMmHandler()">Story Map</button>
            <button class="btn" :class="active===3 ? 'btn-active' : '' " @click="active=3; activeMmHandler()">Pacing Analysis</button>

        </div>

        <div class="flex flex-row gap-5">

            {{-- <div class="card bg-base-100 shadow-xs">

                <p class="card-title m-0">Viewing Script 1 Mindmaps</p>

            </div> --}}

            <div class="dropdown">
                <div tabindex="0" role="button" class="btn">Select a character</div>
                <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm" id="characterList">


                    @if ($relations != null)
                        @foreach($relations as $relation)
                            <li @click='activeMmHandler($el.querySelector("a").innerHTML);'>
                                <a>{{ $relation->characterName }}</a>
                            </li>
                        @endforeach
                    @endif

                </ul>
            </div>

            <button class="btn" @click="active === 1 ? $data.regenerateCharMindMap : activeMmHandlerRegen()" id="regenBtn">Regenerate</button>

        </div>
    </div>

    <div id="mindMapSvgHolder" class="relative">

        {!! $mindmap !!}

        <div class="absolute inset-0 w-full h-full bg-base-100/[85%] flex flex-row justify-center" id="load-overlay"
             x-show="regenerating">
            <div class="mt-[250px]">
                <span class="">Loading &nbsp;</span>
                <span class="loading loading-spinner loading-xl"></span>
                {{-- <span class="loader"></span> --}}
            </div>
        </div>

    </div>

    <script>

        // document.addEventListener('DOMContentLoaded', function () {

        //     console.log("Hello world");
        //     const elements = document.querySelectorAll('.node');
        //     console.log("Hello world");

        //     elements.forEach(element => {
        //         console.log("Hello world again");
        //         element.addEventListener('click', function (event) {
        //             console.log('Element with class my-class was clicked:', element);
        //         })
        //     });

        // });

    </script>

</div>

</body>


</html>
