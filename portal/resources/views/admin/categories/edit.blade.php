<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
               Editar Categorias
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div id="messageBox" class="hidden mb-4 p-4 rounded-md text-sm"></div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6">
                        <form id="categoryForm" class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Nome
                                </label>
                                <input type="text" id="name" name="name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>

                            <div class="flex gap-3">
                                <button type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Atualizar
                                </button>

                                <a href="{{ route('admin.categories.index') }}"
                                class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                    Voltar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Lê os parâmetros da URL atual
            // Exemplo: /admin/categories/edit?id=3
            const params = new URLSearchParams(window.location.search);

            // Pega o valor do parâmetro "id", que representa a categoria a ser editada
            const categoryId = params.get('id');

            // Pega o formulário de edição da categoria
            const categoryForm = document.getElementById('categoryForm');

            // Pega a caixa de mensagens para exibir erros ou avisos
            const messageBox = document.getElementById('messageBox');

            /**
             * Exibe uma mensagem visual para o usuário.
             * @param {string} text - Texto da mensagem
             * @param {string} type - Tipo da mensagem: 'success' ou 'error'
             */
            function showMessage(text, type = 'success') {
                // Define as classes CSS conforme o tipo da mensagem
                messageBox.className = type === 'success'
                    ? 'mb-4 p-4 rounded-md text-sm bg-green-100 text-green-800'
                    : 'mb-4 p-4 rounded-md text-sm bg-red-100 text-red-800';

                // Coloca o texto dentro da caixa de mensagem
                messageBox.textContent = text;

                // Remove a classe "hidden" para tornar a mensagem visível
                messageBox.classList.remove('hidden');
            }

            /**
             * Carrega os dados da categoria atual para preencher o formulário.
             */
            async function loadCategory() {
                try {
                    // Faz uma requisição GET para buscar a categoria pelo ID
                    const response = await fetch(`/admin-api/categories/${categoryId}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    // Converte a resposta para JSON
                    const json = await response.json();

                    // Se a resposta não for OK, lança erro
                    if (!response.ok) {
                        throw new Error(json.message || 'Erro ao carregar categoria.');
                    }

                    // Preenche o campo "name" com o nome atual da categoria
                    document.getElementById('name').value = json.data.name ?? '';
                } catch (error) {
                    // Em caso de erro, mostra a mensagem para o usuário
                    showMessage(error.message, 'error');
                }
            }

            /**
             * Evento disparado ao enviar o formulário de edição.
             */
            categoryForm.addEventListener('submit', async (e) => {
                // Impede o comportamento padrão do formulário
                e.preventDefault();

                try {
                    // Faz uma requisição PUT para atualizar a categoria
                    const response = await fetch(`/admin-api/categories/${categoryId}`, {
                        method: 'PUT',
                        headers: {
                            // Define que a resposta esperada é JSON
                            'Accept': 'application/json',

                            // Define que o corpo enviado será JSON
                            'Content-Type': 'application/json',

                            // Envia o token CSRF exigido pelo Laravel
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        // Converte os dados para JSON antes de enviar
                        body: JSON.stringify({
                            name: document.getElementById('name').value
                        })
                    });

                    // Converte a resposta da API para JSON
                    const json = await response.json();

                    // Se a resposta não for OK, lança erro
                    if (!response.ok) {
                        throw new Error(json.message || 'Erro ao atualizar categoria.');
                    }

                    // Se der certo, redireciona para a listagem de categorias
                    window.location.href = '{{ route('admin.categories.index') }}';
                } catch (error) {
                    // Em caso de erro, exibe a mensagem
                    showMessage(error.message, 'error');
                }
            });

            // Ao abrir a página, carrega os dados da categoria
            loadCategory();
        </script>


</x-app-layout>