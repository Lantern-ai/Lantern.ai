<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Script Analyser</title>

    <!-- DaisyUI & Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.10.1/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    <style>
        html { font-family: 'Inter', sans-serif; }
        @supports (font-variation-settings: normal) {
            html { font-family: 'Inter var', sans-serif; }
        }
        .tab-content {
            border-top: none;
            background-color: hsl(var(--b2));
        }
        .tabs-lifted > .tab-active {
            background-color: hsl(var(--b2));
        }
        .prose h4 { margin-top: 1.25em; margin-bottom: 0.5em; }
        .prose p { margin-top: 0.25em; }
    </style>
</head>
<body class="bg-base-100">
<div class="min-h-screen">
    <main class="py-10 md:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl text-base-content">
                    Script<span class="text-primary">Analyser</span>
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-base-content/70">
                    The definitive analysis suite for the modern screenwriter.
                </p>
                @if(isset($script_title))
                    <p class="mt-2 text-lg text-base-content/60">
                        Analyzing: <span class="font-semibold">{{ $script_title }}</span>
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:items-start">
                <!-- Left Column: Narrative Analysis -->
                <div class="space-y-8">
                    <!-- Narrative Analysis Card -->
                    <div class="card bg-base-200 shadow-xl border border-base-300/50">
                        <div class="card-body">
                            <h3 class="card-title text-2xl">Narrative Analysis</h3>

                            <!-- Loading State -->
                            <div id="narrative-loading" class="flex flex-col items-center justify-center py-12">
                                <span class="loading loading-spinner loading-lg text-primary"></span>
                                <p class="mt-4 text-base-content/70">Analyzing narrative structure...</p>
                            </div>

                            <!-- Content (Hidden Initially) -->
                            <div id="narrative-content" class="hidden">
                                <!-- AI Storyboard Image -->
                                <div id="storyboard-container" class="mb-6 rounded-lg overflow-hidden hidden">
                                    <img id="storyboard-image" src="" alt="AI Generated Storyboard" class="w-full h-auto">
                                </div>

                                <!-- Three-Act Structure -->
                                <div id="story-structure" class="space-y-4 text-base-content/90 prose max-w-none">
                                    <!-- Content will be injected here -->
                                </div>
                            </div>

                            <!-- Error State -->
                            <div id="narrative-error" class="hidden alert alert-error">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span id="narrative-error-message"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Scores & Breakdowns -->
                <div class="space-y-8">
                    <!-- Scores Card -->
                    <div class="card bg-base-200 shadow-xl border border-base-300/50">
                        <div class="card-body">
                            <h2 class="card-title text-2xl mb-6">Analysis Report</h2>

                            <!-- Loading State -->
                            <div id="scores-loading" class="flex flex-col items-center justify-center py-12">
                                <span class="loading loading-spinner loading-lg text-secondary"></span>
                                <p class="mt-4 text-base-content/70">Calculating scores...</p>
                            </div>

                            <!-- Content (Hidden Initially) -->
                            <div id="scores-content" class="hidden space-y-6">
                                <div>
                                    <div class="flex justify-between items-end mb-1">
                                        <span class="font-bold text-lg">Overall Score</span>
                                        <span id="overall-score" class="font-bold text-xl text-primary">0 / 10</span>
                                    </div>
                                    <progress id="overall-progress" class="progress progress-primary w-full" value="0" max="100"></progress>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                                    <div>
                                        <div class="flex justify-between items-end mb-1">
                                            <span class="font-semibold">Formatting</span>
                                            <span id="formatting-score" class="font-medium text-base-content/70">0</span>
                                        </div>
                                        <progress id="formatting-progress" class="progress progress-accent w-full" value="0" max="100"></progress>
                                    </div>
                                    <div>
                                        <div class="flex justify-between items-end mb-1">
                                            <span class="font-semibold">Structure</span>
                                            <span id="structure-score" class="font-medium text-base-content/70">0</span>
                                        </div>
                                        <progress id="structure-progress" class="progress progress-secondary w-full" value="0" max="100"></progress>
                                    </div>
                                    <div>
                                        <div class="flex justify-between items-end mb-1">
                                            <span class="font-semibold">Character</span>
                                            <span id="character-score" class="font-medium text-base-content/70">0</span>
                                        </div>
                                        <progress id="character-progress" class="progress progress-info w-full" value="0" max="100"></progress>
                                    </div>
                                    <div>
                                        <div class="flex justify-between items-end mb-1">
                                            <span class="font-semibold">Dialogue</span>
                                            <span id="dialogue-score" class="font-medium text-base-content/70">0</span>
                                        </div>
                                        <progress id="dialogue-progress" class="progress progress-warning w-full" value="0" max="100"></progress>
                                    </div>
                                </div>
                            </div>

                            <!-- Error State -->
                            <div id="scores-error" class="hidden alert alert-error">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Failed to load scores</span>
                            </div>
                        </div>
                    </div>

                    <!-- Score Breakdowns Card -->
                    <div class="card bg-base-200 shadow-xl border border-base-300/50">
                        <div class="card-body">
                            <h3 class="card-title text-2xl mb-4">Score Breakdowns</h3>

                            <!-- Loading State -->
                            <div id="breakdowns-loading" class="flex flex-col items-center justify-center py-12">
                                <span class="loading loading-spinner loading-lg text-accent"></span>
                                <p class="mt-4 text-base-content/70">Preparing detailed breakdowns...</p>
                            </div>

                            <!-- Content (Hidden Initially) -->
                            <div id="breakdowns-content" class="hidden">
                                <div role="tablist" class="tabs tabs-lifted tabs-lg">
                                    <input type="radio" name="score_tabs" role="tab" class="tab" aria-label="Formatting" checked />
                                    <div role="tabpanel" class="tab-content bg-base-300/50 p-6">
                                        <div id="formatting-breakdown" class="space-y-4 text-base-content/90"></div>
                                    </div>

                                    <input type="radio" name="score_tabs" role="tab" class="tab" aria-label="Structure" />
                                    <div role="tabpanel" class="tab-content bg-base-300/50 p-6">
                                        <div id="structure-breakdown" class="space-y-4 text-base-content/90"></div>
                                    </div>

                                    <input type="radio" name="score_tabs" role="tab" class="tab" aria-label="Character" />
                                    <div role="tabpanel" class="tab-content bg-base-300/50 p-6">
                                        <div id="character-breakdown" class="space-y-4 text-base-content/90"></div>
                                    </div>

                                    <input type="radio" name="score_tabs" role="tab" class="tab" aria-label="Dialogue" />
                                    <div role="tabpanel" class="tab-content bg-base-300/50 p-6">
                                        <div id="dialogue-breakdown" class="space-y-4 text-base-content/90"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Error State -->
                            <div id="breakdowns-error" class="hidden alert alert-error">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Failed to load breakdowns</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Get script ID from the page
    const scriptId = {{ $script_id ?? 'null' }};
    console.log(scriptId);

    // Fetch and display analysis results
    async function fetchAnalysis() {
        if (!scriptId) {
            console.error('No script ID provided');
            return;
        }

        try {
            console.log("Fetching analysis for script ID:")
            const response = await fetch(`/analyse-script`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ script_id: scriptId })
            });

            const result = await response.json();
            console.log(result);

            if (!result.success) {
                throw new Error(result.error || 'Analysis failed');
            }

            displayResults(result.data);
        } catch (error) {
            displayError(error.message);
        }
    }

    function displayResults(data) {
        // Display Narrative Analysis
        if (data.analyses?.story_structure) {
            displayStoryStructure(data.analyses.story_structure);
        }

        if (data.analyses?.storyboard_image) {
            const storyboardContainer = document.getElementById('storyboard-container');
            const storyboardImage = document.getElementById('storyboard-image');
            storyboardImage.src = `data:image/png;base64,${data.analyses.storyboard_image}`;
            storyboardContainer.classList.remove('hidden');
        }

        // Display Scores
        if (data.scores) {
            displayScores(data.scores);
        }

        // Display Breakdowns
        if (data.analyses) {
            displayBreakdowns(data.analyses);
        }

        // Hide loading, show content
        hideLoading();
    }

    function displayStoryStructure(structureText) {
        const container = document.getElementById('story-structure');
        const lines = structureText.split(/\r\n|\n|\r/);
        let html = '';

        lines.forEach(line => {
            const trimmed = line.trim();
            if (!trimmed) return;

            if (trimmed.startsWith('**Act')) {
                const text = trimmed.replace(/\*\*(.*?)\*\*/g, '$1');
                html += `<h4 class="font-bold text-lg pt-2 !mb-1 text-primary">${escapeHtml(text)}</h4>`;
            } else if (trimmed.startsWith('- ')) {
                html += `<p class="pl-4 !my-0">${escapeHtml(trimmed.substring(2))}</p>`;
            } else if (trimmed.includes('**')) {
                const text = trimmed.replace(/\*\*(.*?)\*\*/g, '<strong class="text-secondary">$1</strong>');
                html += `<p class="pl-4 !my-1">${text}</p>`;
            } else {
                html += `<p class="pl-4">${escapeHtml(trimmed)}</p>`;
            }
        });

        container.innerHTML = html;
    }

    function displayScores(scores) {
        // Overall score
        const overall = scores.overall || 0;
        document.getElementById('overall-score').textContent = `${overall} / 10`;
        document.getElementById('overall-progress').value = overall * 10;

        // Individual scores
        const scoreTypes = ['formatting', 'structure', 'character', 'dialogue'];
        scoreTypes.forEach(type => {
            const score = scores[type] || 0;
            document.getElementById(`${type}-score`).textContent = score;
            document.getElementById(`${type}-progress`).value = score * 10;
        });
    }

    function displayBreakdowns(analyses) {
        const breakdownTypes = ['formatting', 'structure', 'character', 'dialogue'];

        breakdownTypes.forEach(type => {
            const container = document.getElementById(`${type}-breakdown`);
            const text = analyses[type] || 'Analysis not available.';
            const lines = text.split('\n');
            let html = '';

            lines.forEach(line => {
                const trimmed = line.trim();
                if (!trimmed) return;

                const parts = trimmed.split(':', 2);
                const mainPoint = parts[0].trim();
                const subPoint = parts[1] ? parts[1].trim() : '';

                html += '<div>';
                html += `<p class="font-semibold text-base-content">${escapeHtml(mainPoint)}</p>`;
                if (subPoint) {
                    html += `<p class="pl-4 opacity-80">${escapeHtml(subPoint)}</p>`;
                }
                html += '</div>';
            });

            container.innerHTML = html;
        });
    }

    function hideLoading() {
        // Hide loading states
        document.getElementById('narrative-loading').classList.add('hidden');
        document.getElementById('scores-loading').classList.add('hidden');
        document.getElementById('breakdowns-loading').classList.add('hidden');

        // Show content
        document.getElementById('narrative-content').classList.remove('hidden');
        document.getElementById('scores-content').classList.remove('hidden');
        document.getElementById('breakdowns-content').classList.remove('hidden');
    }

    function displayError(message) {
        // Hide loading states
        document.getElementById('narrative-loading').classList.add('hidden');
        document.getElementById('scores-loading').classList.add('hidden');
        document.getElementById('breakdowns-loading').classList.add('hidden');

        // Show errors
        document.getElementById('narrative-error').classList.remove('hidden');
        document.getElementById('narrative-error-message').textContent = message;
        document.getElementById('scores-error').classList.remove('hidden');
        document.getElementById('breakdowns-error').classList.remove('hidden');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Start fetching when page loads
    if (scriptId) {
        fetchAnalysis();
    }
</script>
</body>
</html>
