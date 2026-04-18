<div>
   NOTICIA EDIÇÃO
</div><x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar notícia
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
                            <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                           </select>
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

                        <div id="currentImageBox" class="hidden">
                            <p class="text-sm text-gray-700 dark:text-gray-200 mb-2">Imagem atual</p>
                            <img id="currentImage" src="" alt="Imagem atual" class="w-48 rounded shadow">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nova imagem</label>
                            <input type="file" id="path_image" name="path_image"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Atualizar
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
    // Lê os parâmetros da URL atual
    // Exemplo: /admin/notices/edit?id=5
    const params = new URLSearchParams(window.location.search);

    // Pega o valor do parâmetro "id" da notícia que será editada
    const noticeId = params.get('id');

    // Captura os elementos principais da página
    const noticeForm = document.getElementById('noticeForm');
    const categorySelect = document.getElementById('category_id');
    const messageBox = document.getElementById('messageBox');
    const currentImageBox = document.getElementById('currentImageBox');
    const currentImage = document.getElementById('currentImage');

    /**
     * Mostra uma mensagem visual para o usuário.
     * Pode ser de sucesso ou erro.
     *
     * @param {string} text - Texto da mensagem
     * @param {string} type - Tipo da mensagem: 'success' ou 'error'
     */
    function showMessage(text, type = 'success') {
        // Define as classes CSS conforme o tipo da mensagem
        messageBox.className = type === 'success'
            ? 'mb-4 p-4 rounded-md text-sm bg-green-100 text-green-800'
            : 'mb-4 p-4 rounded-md text-sm bg-red-100 text-red-800';

        // Coloca o texto dentro da caixa
        messageBox.textContent = text;

        // Remove a classe "hidden" para exibir a mensagem
        messageBox.classList.remove('hidden');
    }

    /**
     * Carrega as categorias da API e preenche o <select>.
     *
     * @param {number|string|null} selectedId - ID da categoria que deve vir selecionada
     */
    async function loadCategories(selectedId = null) {
        // Faz requisição para buscar as categorias
        const response = await fetch('/admin-api/categories', {
            headers: { 'Accept': 'application/json' }
        });

        // Converte a resposta para JSON
        const json = await response.json();

        // Monta o select com a opção padrão + categorias da API
        // Se a categoria for igual à da notícia, marca como selected
        categorySelect.innerHTML = `<option value="">Selecione</option>` +
            (json.data || []).map(category =>
                `<option value="${category.id}" ${String(selectedId) === String(category.id) ? 'selected' : ''}>${category.name}</option>`
            ).join('');
    }

    /**
     * Carrega os dados da notícia que será editada.
     * Busca a notícia pelo ID vindo da URL e preenche o formulário.
     */
    async function loadNotice() {
        try {
            // Faz requisição para buscar os dados da notícia
            const response = await fetch(`/admin-api/notices/${noticeId}`, {
                headers: { 'Accept': 'application/json' }
            });

            // Converte a resposta para JSON
            const json = await response.json();

            // Se houver erro na resposta, lança exceção
            if (!response.ok) {
                throw new Error(json.message || 'Erro ao carregar notícia.');
            }

            // Pega os dados da notícia
            const notice = json.data;

            // Carrega as categorias já deixando a categoria atual selecionada
            await loadCategories(notice.category_id);

            // Preenche os campos do formulário com os dados recebidos
            document.getElementById('title').value = notice.title ?? '';
            document.getElementById('description').value = notice.description ?? '';
            document.getElementById('notice').value = notice.notice ?? '';

            // Se a notícia tiver imagem, mostra a imagem atual na tela
            if (notice.path_image) {
                currentImage.src = `/storage/${notice.path_image}`;
                currentImageBox.classList.remove('hidden');
            }
        } catch (error) {
            // Em caso de erro, exibe a mensagem
            showMessage(error.message, 'error');
        }
    }

    /**
     * Evento disparado ao enviar o formulário de edição.
     */
    noticeForm.addEventListener('submit', async (e) => {
        // Impede o comportamento padrão do formulário
        e.preventDefault();

        // Cria um FormData para enviar texto + arquivo
        const formData = new FormData();

        // Como formulários com arquivo nem sempre enviam PUT diretamente,
        // usamos method spoofing do Laravel com _method = PUT
        formData.append('_method', 'PUT');

        // Adiciona os campos ao FormData
        formData.append('category_id', document.getElementById('category_id').value);
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('notice', document.getElementById('notice').value);

        // Pega a nova imagem escolhida pelo usuário
        const image = document.getElementById('path_image').files[0];

        // Se existir uma nova imagem, adiciona ao envio
        if (image) {
            formData.append('path_image', image);
        }

        try {
            // Envia os dados para atualizar a notícia
            const response = await fetch(`/admin-api/notices/${noticeId}`, {
                method: 'POST', // POST com _method=PUT para Laravel interpretar como PUT
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            // Converte a resposta para JSON
            const json = await response.json();

            // Se a resposta não for OK, gera erro
            if (!response.ok) {
                throw new Error(json.message || 'Erro ao atualizar notícia.');
            }

            // Se der certo, redireciona para a listagem de notícias
            window.location.href = '{{ route('admin.notices.index') }}';
        } catch (error) {
            // Em caso de erro, mostra a mensagem na tela
            showMessage(error.message, 'error');
        }
    });

    // Ao carregar a página, busca os dados da notícia para preencher o formulário
    loadNotice();
</script>
</x-app-layout>
