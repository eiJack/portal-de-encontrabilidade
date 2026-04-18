<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Notícias
            </h2>

            <div>
                <a href="{{ route('admin.notices.create') }}"
                    class="px-4 py-2 mx-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Nova notícia
                </a>
                <a href="{{ route('dashboard') }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Voltar
                </a>
            </div>


        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="messageBox" class="hidden mb-4 p-4 rounded-md text-sm"></div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-3 px-2 text-gray-700 dark:text-gray-200">Imagem</th>
                                <th class="text-left py-3 px-2 text-gray-700 dark:text-gray-200">Título</th>
                                <th class="text-left py-3 px-2 text-gray-700 dark:text-gray-200">Categoria</th>
                                <th class="text-left py-3 px-2 text-gray-700 dark:text-gray-200">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="noticesTable">
                            <tr>
                                <td colspan="4" class="py-4 px-2 text-gray-500">Carregando...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pega o elemento da tabela onde as notícias serão exibidas
        const noticesTable = document.getElementById('noticesTable');

        // Pega a caixa de mensagens de feedback (sucesso ou erro)
        const messageBox = document.getElementById('messageBox');

        /**
         * Exibe uma mensagem visual para o usuário.
         * @param {string} text - Texto da mensagem
         * @param {string} type - Tipo da mensagem: 'success' ou 'error'
         */
        function showMessage(text, type = 'success') {
            // Define as classes CSS conforme o tipo da mensagem
            messageBox.className = type === 'success' ?
                'mb-4 p-4 rounded-md text-sm bg-green-100 text-green-800' :
                'mb-4 p-4 rounded-md text-sm bg-red-100 text-red-800';

            // Define o texto da mensagem
            messageBox.textContent = text;

            // Remove a classe "hidden" para tornar a mensagem visível
            messageBox.classList.remove('hidden');
        }

        /**
         * Carrega a lista de notícias da API e monta as linhas da tabela.
         */
        async function loadNotices() {
            try {
                // Faz requisição GET para buscar as notícias
                const response = await fetch('/admin-api/notices', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                // Converte a resposta para JSON
                const json = await response.json();

                // Se a resposta não for OK, lança um erro
                if (!response.ok) {
                    throw new Error(json.message || 'Erro ao carregar notícias.');
                }

                // Pega os dados retornados pela API
                const notices = json.data || [];

                // Se não houver notícias cadastradas, mostra mensagem na tabela
                if (!notices.length) {
                    noticesTable.innerHTML = `
                    <tr>
                        <td colspan="4" class="py-4 px-2 text-gray-500">Nenhuma notícia cadastrada.</td>
                    </tr>
                `;
                    return;
                }

                // Monta as linhas da tabela dinamicamente com os dados das notícias
                noticesTable.innerHTML = notices.map(notice => `
                <tr class="border-b dark:border-gray-700">
                    <td class="py-3 px-2">
                        ${notice.path_image
                            // Se existir imagem, exibe miniatura
                            ? `<img src="/storage/${notice.path_image}" alt="${notice.title}" class="w-24 h-16 object-cover rounded">`
                            // Se não existir imagem, mostra texto padrão
                            : `<span class="text-gray-500">Sem imagem</span>`
                        }
                    </td>

                    <!-- Exibe o título da notícia -->
                    <td class="py-3 px-2 text-gray-900 dark:text-gray-100">${notice.title}</td>

                    <!-- Exibe o nome da categoria da notícia -->
                    <td class="py-3 px-2 text-gray-900 dark:text-gray-100">${notice.category?.name ?? ''}</td>

                    <!-- Exibe os botões de ação -->
                    <td class="py-3 px-2">
                        <div class="flex gap-2">
                            <!-- Link para a tela de edição -->
                            <a href="{{ route('admin.notices.edit') }}?id=${notice.id}"
                               class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                Editar
                            </a>

                            <!-- Botão para excluir a notícia -->
                            <button onclick="deleteNotice(${notice.id})"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                Excluir
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
            } catch (error) {
                // Em caso de erro, mostra a mensagem diretamente dentro da tabela
                noticesTable.innerHTML = `
                <tr>
                    <td colspan="4" class="py-4 px-2 text-red-500">${error.message}</td>
                </tr>
            `;
            }
        }

        /**
         * Exclui uma notícia pelo ID.
         * @param {number} id - ID da notícia
         */
        async function deleteNotice(id) {
            // Exibe uma confirmação antes de excluir
            if (!confirm('Deseja realmente excluir esta notícia?')) return;

            try {
                // Faz requisição DELETE para remover a notícia
                const response = await fetch(`/admin-api/notices/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        // Envia o token CSRF exigido pelo Laravel
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                // Converte a resposta para JSON
                const json = await response.json();

                // Se a resposta não for OK, lança erro
                if (!response.ok) {
                    throw new Error(json.message || 'Erro ao excluir notícia.');
                }

                // Mostra mensagem de sucesso
                showMessage(json.message || 'Notícia removida com sucesso.');

                // Recarrega a tabela após excluir
                loadNotices();
            } catch (error) {
                // Mostra mensagem de erro
                showMessage(error.message, 'error');
            }
        }

        // Carrega as notícias assim que a página abrir
        loadNotices();
    </script>
</x-app-layout>
