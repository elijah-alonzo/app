<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paulinian Student Government E-Portfolio and Ranking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white px-8 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/psgears.png') }}" alt="PSG" class="h-10 w-auto">
            </div>
            <div class="flex items-center space-x-3">
                <a href="/admin/login" class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-6 rounded-full">Admin Login</a>
                <a href="/student/login" class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-6 rounded-full">Student Login</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex items-center justify-between max-w-7xl mx-auto px-8 py-20">
        <!-- Left Content -->
        <div class="flex-1 max-w-2xl">

            <!-- Title Image instead of text -->
            <div class="mb-8">
                <img src="{{ asset('images/psgears.png') }}" alt="E-Portfolio & Ranking System" class="h-36 w-auto mb-4">
            </div>

            <p class="text-lg text-gray-600 leading-relaxed mb-8">
                The official digital hub of the Paulinian Student Government for evaluating performance and recognizing outstanding student officers.
            </p>
        </div>

        <!-- Right Content - Large Logo -->
        <div class="flex-1 flex justify-center">
            <div class="relative">
                <img src="{{ asset('images/psg.png') }}" alt="St. Paul University Philippines" class="h-96 w-auto rounded-full">
            </div>
        </div>
    </div>


</body>
</html>
