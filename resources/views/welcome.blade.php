<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Medicine Search</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-200 dark:from-gray-900 dark:to-gray-800 min-h-screen font-sans">
    <div class="flex flex-col min-h-screen">
        <!-- Navbar -->
        <nav class="flex justify-between items-center px-8 py-4 bg-white dark:bg-gray-900 shadow">
            <div class="text-2xl font-bold text-blue-700 dark:text-blue-300 tracking-tight">
                Medicine Finder
            </div>
            @if (Route::has('login'))
                <div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-blue-700 dark:text-blue-300 font-semibold hover:underline px-4">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-700 dark:text-blue-300 font-semibold hover:underline px-4">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-2 text-blue-700 dark:text-blue-300 font-semibold hover:underline px-4">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>

        <!-- Main Content -->
        <main class="flex flex-1 flex-col items-center justify-center">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl p-10 w-full max-w-lg mt-10">
                <h1 class="text-3xl font-bold text-center text-blue-700 dark:text-blue-200 mb-6">Search for a Medicine</h1>
                <div class="flex flex-col gap-4">
                    <input 
                        type="text" 
                        name="name" 
                        id="drugName"
                        class="h-12 px-4 rounded-lg border border-blue-200 dark:border-gray-700 bg-blue-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-400 outline-none transition"
                        placeholder="Enter medicine name..."
                    >
                    <button 
                        type="button"
                        id="searchBtn"
                        class="h-12 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition"
                    >
                        Search
                    </button>
                </div>
                <div id="results" class="mt-8"></div>
            </div>
        </main>
    </div>
    <script>
        $('#searchBtn').click(function () { 
            const drugName = $('#drugName').val().trim();
            if (!drugName) {
                alert('Please enter a drug name.');
                return;
            }
            $('#results').html('<div class="text-center text-blue-600 dark:text-blue-300">Searching...</div>');
            $.ajax({
                url: '/search-drugs',
                method: 'GET',
                data: { drug_name: drugName },
                success: function (res) {
                    if (!res.success || res.results.length === 0) {
                        $('#results').html('<div class="text-center text-red-600 dark:text-red-400">No results found.</div>');
                        return;
                    }
                    let html = '<h3 class="text-xl font-semibold mb-4 text-blue-700 dark:text-blue-200">Top Results</h3><ul class="space-y-4">';
                    res.results.forEach(drug => {
                        html += `<li class="bg-blue-50 dark:bg-gray-800 rounded-lg p-4 shadow border border-blue-100 dark:border-gray-700">
                            <div class="font-bold text-lg text-blue-800 dark:text-blue-200">${drug.name} <span class="text-xs text-gray-500">(RXCUI: ${drug.rxcui})</span></div>
                            <div class="mt-2 text-gray-700 dark:text-gray-300">
                                <span class="font-semibold">Base Names:</span> ${drug.baseNames.length ? drug.baseNames.join(', ') : 'N/A'}
                            </div>
                            <div class="text-gray-700 dark:text-gray-300">
                                <span class="font-semibold">Dosage Forms:</span> ${drug.dosageForms.length ? drug.dosageForms.join(', ') : 'N/A'}
                            </div>
                        </li>`;
                    });
                    html += '</ul>';
                    $('#results').html(html);
                },
                error: function () {
                    $('#results').html('<div class="text-center text-red-600 dark:text-red-400">Something went wrong with the request.</div>');
                }
            });
        });
    </script>
</body>
</html>
