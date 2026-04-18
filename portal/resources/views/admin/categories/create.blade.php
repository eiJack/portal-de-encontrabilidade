<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Nova categoria
        </h2>
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
                                Salvar
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
    // Pega o formulário de cadastro de categoria
    const categoryForm = document.getElementById('categoryForm');

    // Pega a caixa de mensagem que será usada para mostrar erros
    const messageBox = document.getElementById('messageBox');

    /**
     * Exibe uma mensagem visual para o usuário.
     * @param {string} text - Texto da mensagem
     * @param {string} type - Tipo da mensagem: 'success' ou 'error'
     */
    function showMessage(text, type = 'success') {
        // Define as classes CSS da caixa de mensagem dependendo do tipo
        messageBox.className = type === 'success'
            ? 'mb-4 p-4 rounded-md text-sm bg-green-100 text-green-800'
            : 'mb-4 p-4 rounded-md text-sm bg-red-100 text-red-800';

        // Insere o texto da mensagem
        messageBox.textContent = text;

        // Remove a classe hidden para exibir a mensagem
        messageBox.classList.remove('hidden');
    }

    /**
     * Evento executado quando o formulário é enviado.
     */
    categoryForm.addEventListener('submit', async (e) => {
        // Impede o envio padrão do formulário
        e.preventDefault();

        try {
            // Faz a requisição POST para criar uma nova categoria
            const response = await fetch('/admin-api/categories', {
                method: 'POST',
                headers: {
                    // Define que a resposta esperada será JSON
                    'Accept': 'application/json',

                    // Define que o corpo enviado também será JSON
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

            // Se a resposta não for OK, lança um erro
            if (!response.ok) {
                throw new Error(json.message || 'Erro ao criar categoria.');
            }

            // Se a categoria for criada com sucesso, redireciona para a listagem
            window.location.href = '{{ route('admin.categories.index') }}';
        } catch (error) {
            // Em caso de erro, mostra a mensagem na tela
            showMessage(error.message, 'error');
        }
    });
</script>

</x-app-layout>  