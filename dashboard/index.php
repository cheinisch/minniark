<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 bg-white dark:bg-gray-800 shadow-lg flex flex-col">
      <div class="flex items-center justify-center h-16 bg-gray-200 dark:bg-gray-700">
        <h1 class="text-lg font-bold">Dashboard</h1>
      </div>
      <nav class="flex-1 px-4 py-2">
        <ul class="space-y-2">
          <li>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Media</a>
          </li>
          <li>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Albums</a>
          </li>
          <li>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Essays</a>
          </li>
          <li>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Pages</a>
          </li>
        </ul>
      </nav>
      <div class="px-4 py-2 border-t border-gray-300 dark:border-gray-700">
        <ul class="space-y-2">
          <li>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Settings</a>
          </li>
          <li>
            <a href="#" class="block px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">Logout</a>
          </li>
          <li>
            <button 
              id="themeToggle" 
              class="block w-full text-left px-4 py-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
              Dark/Light
            </button>
          </li>
        </ul>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <h2 class="text-2xl font-semibold mb-4">Welcome to the Dashboard</h2>
      <p>Content goes here...</p>
    </div>
  </div>

  <script>
    const themeToggle = document.getElementById('themeToggle');
    themeToggle.addEventListener('click', () => {
      document.documentElement.classList.toggle('dark');
    });
  </script>
</body>
</html>
