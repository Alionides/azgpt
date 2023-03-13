<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>AZGPT</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            p,ul{
                color:#acb0b7;
            }
        </style>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js"></script>
    </head>
    <body class="antialiased" style="background-color: #343541">
    <div class="grid grid-cols-1 p-4 gap-4 place-items-center">
        <div style="border-color:#ececf1" class="border-solid border-4 rounded ">
            <p style="color:#ececf1" class="font-sans font-bold text-3xl p-5">AzGPT</p>
        </div>
        <p>developed by <a href="https://github.com/Alionides" target="_blank">ALI SHIKHIYEV</a> </p>
        <a href="https://www.buymeacoffee.com/alishikhiyev" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a>
    </div>

    <div class="block sm:flex md:block lg:flex items-center justify-center">
        <div class="mt-8 sm:m-8 md:m-0 md:mt-8 lg:m-8 text-center">
            <div class="inline-flex items-center">
                <span class="text-xl ml-2 text-white">{{$visitors}}</span>
            </div>
            <span class="block text-sm text-white mt-2">İstifadəçi sayı</span>
        </div>
        <div class="mt-4 mb-8 sm:m-8 md:m-0 md:mt-4 md:mb-8 lg:m-8 text-center">
            <div class="inline-flex items-center">
                <span class="text-xl ml-2 text-white">{{$questions}}</span>
            </div>
            <span class="block text-sm text-white mt-2">Verilən suallar</span>
        </div>
        <div class="mt-4 mb-8 sm:m-8 md:m-0 md:mt-4 md:mb-8 lg:m-8 text-center">
            <div class="inline-flex items-center">
                <span class="text-xl ml-2 text-white">{{$images}}</span>
            </div>
            <span class="block text-sm text-white mt-2">Hazırlanan şəkillər</span>
        </div>
    </div>

        <div class="flex flex-col space-y-4 p-4">
        @foreach($messages as $message)
            <div style="background-color: #40414f" class="flex rounded-lg p-4 @if ($message['role'] === 'assistant') flex-reverse @else  @endif ">
                <div class="ml-4">
                    <div class="text-lg">
                        @if ($message['role'] === 'assistant')
                            <a href="#" class="font-medium text-white">AZGPT</a>
                        @else
                            <a href="#" class="font-medium text-white">Sən</a>
                        @endif
                    </div>
                    <div class="mt-1">
                        <p class="text-gray-600">
                            @if(isset($message['content']['image']))
                                <img src="{{$message['content']['image']}}">
                            @else
                                {!! \Illuminate\Mail\Markdown::parse($message['content']) !!}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
        <div x-data="{button:'Şəkil hazırla',open:false}">
            <div class="block sm:flex md:block lg:flex items-center justify-center">
                <div class="mt-4 mb-8 sm:m-8 md:m-0 md:mt-4 md:mb-8 lg:m-8 text-center">
                <button x-text="button"  @click="open=!open; open ? button='Sual ver' : button='Şəkil hazırla' " class="bg-gray-800 text-white p-2 rounded-md"></button>
                </div>
            </div>
            <div x-show="!open">
            <form class="p-4 flex space-x-4 justify-center items-center" action="/" method="post">
                @csrf
                <label for="message" style="color:#ececf1">Sual ver:</label>
                <input style="color:white; background-color:#40414f; border: 0px solid; outline: none;" id="message" type="text" name="message" autocomplete="off" class="border rounded-md  p-2 flex-1" />
                <button class="bg-gray-800 text-white p-2 rounded-md">İrəli</button>
{{--                <a class="bg-gray-800 text-white p-2 rounded-md" href="/reset">Təmizlə</a>--}}
            </form>
            </div>
            <div x-show="open">
            <form class="p-4 flex space-x-4 justify-center items-center" action="/image" method="post">
                @csrf
                <label for="message" style="color:#ececf1">Təsvir et:</label>
                <input style="color:white; background-color:#40414f; border: 0px solid; outline: none;" id="message" type="text" name="message" autocomplete="off" class="border rounded-md  p-2 flex-1" />
                <button class="bg-gray-800 text-white p-2 rounded-md">İrəli</button>
{{--                <a class="bg-gray-800 text-white p-2 rounded-md" href="/reset">Təmizlə</a>--}}
            </form>
            </div>
        </div>
    </body>
</html>
