<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PSSC</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        {{-- <link rel="icon" href="/favicon.svg" type="image/svg+xml"> --}}
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">

        </header>
        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                <div class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-es-lg rounded-ee-lg lg:rounded-ss-lg lg:rounded-ee-none">
                    <h1 class="mb-1 text-xl text-[3F507F] font-medium">Welcome!</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">Payroll System for Serbisyong CongPleyto Movement</p>
                    <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">This is a simple payroll system for the Serbisyong CongPleyto Movement.</p>
                    <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">You can manage employees, attendance, payroll, departments, positions, and more.</p>

                    <div class="flex gap-3 text-base leading-normal">
                        @auth
                        <flux:button
                            href="{{ route('dashboard') }}"
                            variant="primary"
                            icon="home"
                        >
                            Go to Dashboard
                        </flux:button>

                        @else
                        <p class="mb-6 text-[#706f6c] dark:text-[#A1A09A]">To get started, please log in to your account.</p>
                        <flux:button
                            class="w-full"
                            variant="primary"
                            icon="key"
                            href="{{ route('login') }}"
                        >
                            Log in
                        </flux:button>
                        @endauth
                    </div>
                </div>
                <div class="bg-[#fff2f2] dark:bg-[#1D0002] p-1 relative lg:-ms-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-e-lg! aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden">


                    <img class="w-full text-[#F53003] dark:text-[#F61500] transition-all translate-y-0 opacity-100 max-w-none duration-750 starting:opacity-0 starting:translate-y-6" src="{{url('/images/sp_logo.png')}}" alt="Logo" srcset="{{url('/images/sp_logo.png')}}">
                    <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-e-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"></div>
                </div>
            </main>
        </div>

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>
