<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{route('admin.categories.index')}}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Categorias</h3>
                     <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                        Gerenciar Categorias
                     </p>
                </a>


                  <a href="{{route('admin.notices.index')}}" class="block bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Noticias</h3>
                     <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
                        Gerenciar Noticias
                     </p>
                </a>
            </div>
        </div>

        
    </div>

    
        
    </div>
</x-app-layout>
