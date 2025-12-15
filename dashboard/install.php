<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">        
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Installer - ImagePortfolio</title>
        <link rel="stylesheet" href="css/tailwind.css">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>
    <body class="h-full dark:bg-gray-950 bg-white">
        <main>
            <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8 max-w-md bg-neutral-200 dark:bg-gray-900 my-10 m-auto">
                <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                    <img class="mx-auto h-10 w-auto" src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=cyan&shade=600" alt="Your Company">
                    <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-gray-300">Install Minniark</h2>
                </div>

                <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
                    <form class="space-y-6" action="backend_api/install.php" method="POST">
                    <div>
                        <label for="username" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">Username</label>
                        <div class="mt-2">
                        <input type="text" name="username" id="username" required class="block w-full bg-white dark:bg-gray-600 px-3 py-1.5 text-base text-gray-900 dark:text-gray-300 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600 sm:text-sm/6">
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">Email address</label>
                        <div class="mt-2">
                        <input type="email" name="email" id="email" autocomplete="email" required class="block w-full bg-white dark:bg-gray-600 px-3 py-1.5 text-base text-gray-900 dark:text-gray-300 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600 sm:text-sm/6">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">Password</label>
                        </div>
                        <div class="mt-2">
                        <input type="password" name="password" id="password" autocomplete="current-password" required class="block w-full bg-white dark:bg-gray-600 px-3 py-1.5 text-base text-gray-900 dark:text-gray-300 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600 sm:text-sm/6">
                        </div>
                        <div class="flex items-center justify-between">
                        <label for="password_2" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">Re-Type Password</label>
                        </div>
                        <div class="mt-2">
                        <input type="password" name="password_2" id="password_2" autocomplete="current-password" required class="block w-full bg-white dark:bg-gray-600 px-3 py-1.5 text-base text-gray-900 dark:text-gray-300 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600 sm:text-sm/6">
                        </div>
                    </div>
                    <div>
                        <label for="sitename" class="block text-sm/6 font-medium text-gray-900 dark:text-gray-300">Site Name</label>
                        <div class="mt-2">
                        <input type="text" name="sitename" id="sitename" required class="block w-full bg-white dark:bg-gray-600 px-3 py-1.5 text-base text-gray-900 dark:text-gray-300 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-cyan-600 sm:text-sm/6">
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center bg-cyan-600 dark:bg-cyan-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-cyan-400 dark:hover:bg-cyan-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600">Start Installation</button>
                    </div>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>