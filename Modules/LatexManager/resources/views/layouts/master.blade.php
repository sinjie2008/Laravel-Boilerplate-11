<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Latex Manager</title>

       {{-- Laravel Vite - CSS File --}}
       {{-- {{ module_vite('build-latexmanager', 'resources/assets/sass/app.scss') }} --}}
       {{-- Note: Vite setup might require further configuration depending on the main app --}}

       <style>
           /* Ensure body takes full height for flex layout in content */
           html, body {
               height: 100%;
               margin: 0;
               padding: 0;
           }
       </style>

    </head>
    <body>
        @yield('content')

        {{-- Laravel Vite - JS File --}}
        {{-- {{ module_vite('build-latexmanager', 'resources/assets/js/app.js') }} --}}
    </body>
</html>
