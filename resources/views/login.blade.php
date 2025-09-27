@extends('layouts.pltgeneral')


@section('contenidosPrincipales')

    <section>
        <div class="flex flex-col items-center mt-[10rem] px-6 py-8 mx-auto h-screen lg:py-0">
            <div class="bg-gray-300 rounded-2xl shadow-lg md:mt-0 w-[30rem] sm:max-w-[23rem] xl:p-0">
                <div class="p-6 sm:p-10">
                    <h1 class="text-center text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl mb-[2rem]">
                        Iniciar sesión Intranet
                    </h1>

                    @error('credNoValidas')

                        <div id="alert-border-2" class="flex items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50" role="alert">
                            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                            </svg>
                            <div class="ms-3 text-sm font-medium">
                                {{ $message }}
                            </div>
                            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8"  data-dismiss-target="#alert-border-2" aria-label="Close">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            </button>
                        </div>

                    @enderror

                    <form class="space-y-4 md:space-y-6" action="{{ Route('LoginNuevo') }}" method="POST" novalidate>
                        @csrf
                        <div>
                            @if ($errors->has('email'))
                                <label for="email" class="block mb-2 text-sm font-medium text-red-700 ">Email</label>
                                <input value="{{ old('email') }}" type="email" autofocus name="email" id="email" class="bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 " placeholder="name@company.com">
                                <p class="mt-2 text-sm text-red-600 "><span class="font-medium">{{ $errors->first('email') }}</p>
                            @else
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 ">Email</label>
                                <input value="{{ old('email') }}" type="email" autofocus name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 " placeholder="name@company.com">
                            @endif
                        </div>
                        <div>
                            @if ($errors->has('password'))
                                <label for="password" class="block mb-2 text-sm font-medium text-red-700 ">Contraseña</label>
                                <input type="password" name="password" id="password" class="bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 ">
                                <p class="mb-4 mt-2 text-sm text-red-600 "><span class="font-medium">{{ $errors->first('password') }}</p>
                            @else
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 ">Contraseña</label>
                                <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 ">
                            </div>
                            @endif

                        <div class="grid grid-cols-1">
                            <button type="submit" class="btn-uno">Iniciar sesión</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection


