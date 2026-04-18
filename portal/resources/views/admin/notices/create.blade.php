<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Nova notícia
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div id="messageBox" class="hidden mb-4 p-4 rounded-md text-sm"></div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <form id="noticeForm" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Categoria</label>
                            <select id="category_id" name="category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Título</label>
                            <input type="text" id="title" name="title"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Descrição</label>
                            <input type="text" id="description" name="description"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Texto da notícia</label>
                            <textarea id="notice" name="notice" rows="8"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Imagem</label>
                            <input type="file" id="path_image" name="path_image"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Salvar
                            </button>

                            <a href="{{ route('admin.notices.index') }}"
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
    // Pega o formulário pelo id="noticeForm"
    const noticeForm = document.getElementById('noticeForm');

    // Pega o select de categorias pelo id="category_id"
    const categorySelect = document.getElementById('category_id');

    // Pega a div onde as mensagens de sucesso/erro serão exibidas
    const messageBox = document.getElementById('messageBox');

    /**
     * Exibe uma mensagem visual na tela.
     * @param {string} text - Texto que será mostrado.
     * @param {string} type - Tipo da mensagem: 'success' ou 'error'.
     */
    function showMessage(text, type = 'success') {
        // Define as classes CSS de acordo com o tipo da mensagem
        messageBox.className = type === 'success'
            ? 'mb-4 p-4 rounded-md text-sm bg-green-100 text-green-800'
            : 'mb-4 p-4 rounded-md text-sm bg-red-100 text-red-800';

        // Define o texto da mensagem
        messageBox.textContent = text;

        // Remove a classe "hidden" para tornar a caixa visível
        messageBox.classList.remove('hidden');
    }

    /**
     * Carrega as categorias da API e preenche o <select>.
     */
    async function loadCategories() {
        try {
            // Faz uma requisição GET para buscar as categorias
            const response = await fetch('/admin-api/categories', {
                headers: { 'Accept': 'application/json' }
            });

            // Converte a resposta para JSON
            const json = await response.json();

            // Se a resposta não for OK, dispara erro
            if (!response.ok) {
                throw new Error(json.message || 'Erro ao carregar categorias.');
            }

            // Preenche o select com uma opção padrão + categorias vindas da API
            categorySelect.innerHTML = `<option value="">Selecione</option>` +
                (json.data || []).map(category =>
                    `<option value="${category.id}">${category.name}</option>`
                ).join('');
        } catch (error) {
            // Em caso de erro, mostra mensagem na tela
            showMessage(error.message, 'error');
        }
    }

    /**
     * Evento executado ao enviar o formulário.
     */
    noticeForm.addEventListener('submit', async (e) => {
        // Impede o recarregamento padrão da página ao enviar o form
        e.preventDefault();

        // Cria um objeto FormData para enviar dados normais + arquivo
        const formData = new FormData();

        // Adiciona os campos do formulário ao FormData
        formData.append('category_id', document.getElementById('category_id').value);
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('notice', document.getElementById('notice').value);

        // Pega o arquivo de imagem selecionado
        const image = document.getElementById('path_image').files[0];

        // Se existir imagem, adiciona ao FormData
        if (image) {
            formData.append('path_image', image);
        }

        try {
            // Envia os dados para a API via POST
            const response = await fetch('/admin-api/notices', {
                method: 'POST',
                headers: {
                    // Informa que espera JSON como resposta
                    'Accept': 'application/json',

                    // Envia o token CSRF do Laravel para evitar erro 419
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            // Converte a resposta para JSON
            const json = await response.json();

            // Se a API retornar erro, lança exceção
            if (!response.ok) {
                throw new Error(json.message || 'Erro ao criar notícia.');
            }

            // Se deu certo, redireciona para a listagem de notícias
            window.location.href = '{{ route('admin.notices.index') }}';
        } catch (error) {
            // Se ocorrer erro, exibe a mensagem na tela
            showMessage(error.message, 'error');
        }
    });

    // Chama a função assim que a página carregar para preencher as categorias
    loadCategories();
</script>
</x-app-layout>