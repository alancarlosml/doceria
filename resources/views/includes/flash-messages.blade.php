<!-- Flash Messages -->
@if (session('success'))
    <div id="flash-message" class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('flash-message').classList.add('opacity-0');
            setTimeout(() => document.getElementById('flash-message').remove(), 300);
        }, 3000);
    </script>
@endif

@if (session('error'))
    <div id="flash-error" class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300">
        {{ session('error') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('flash-error').classList.add('opacity-0');
            setTimeout(() => document.getElementById('flash-error').remove(), 300);
        }, 3000);
    </script>
@endif

@if (session('warning'))
    <div id="flash-warning" class="fixed top-4 right-4 z-50 bg-yellow-500 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300">
        {{ session('warning') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('flash-warning').classList.add('opacity-0');
            setTimeout(() => document.getElementById('flash-warning').remove(), 300);
        }, 3000);
    </script>
@endif

@if (session('info'))
    <div id="flash-info" class="fixed top-4 right-4 z-50 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300">
        {{ session('info') }}
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('flash-info').classList.add('opacity-0');
            setTimeout(() => document.getElementById('flash-info').remove(), 300);
        }, 3000);
    </script>
@endif
