<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Categorias
            </h2>


        <a href="{{route('admin.categories.create')}}"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
            Nova Categoria
        </a>

        </div>

    </x-slot>


      <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="messageBox" class="hidden mb-4 p-4 rounded-md text-sm"></div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b dark:border-gray-700">
                                      <th class="text-left py-3 px-2 text-gray-700 dark:text-gray-200">ID</th>
                                      <th class="text-left py-3 px-2 text-gray-700 dark:text-gray-200">NOME</th>
                                      <th class="text-left py-3 px-2 text-gray-700 dark:text-gray-200">SLUG</th>
                                      <th class="text-left py-3 px-2 text-gray-700 dark:text-gray-200">AÇÕES</th>
                                       </tr>
                            </thead>
                            <tbody id='categoriesTable'>
                                <tr>
                                    <td colspan="4" class="py-4 px-2 text-gray-500">Carregando ...</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <script>
        // Pego elemento da tabela onde as categorias serão iniciadas
        const categoriesTable = document.getElementById('categoriesTable');

        // Pega a caixa de mensagens para ver se deu sucesso ou erro
        const messaBox = document.getElementById('messageBox');


        //Exibe um mensagem visual para o usuário
        //  @param {string} text - Texto da messagem
        // @param {string} type - Tipo da messagem: 'sucess' ou 'error'
        function showMessage(text, type='sucess') {

            messaBox.className = type === 'sucess'
                ? 'mb-4 p-4 rounded-md text-sm bg-green-100 text-green-800'
                : 'mb-4 p-4 rounded-md text-sm bg-red-100 text-red-800';

            messaBox.textContent = text;

            messaBox.classList.remove('list');
        }


        //Carregs as categorias da Api e monta as linnhas da tabela
        async function loadCategories() {
            try {
                // Faz a requisição para buscar as categorias
                const response = await fetch('/admin-api/categories', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                // Converte a resposta para JSON
                const json = await response.json();

                // Se a resposta da API não for OK, lança erro
                if (!response.ok) {
                    throw new Error(json.message || 'Erro ao carregar categorias.');
                }

                // Pega o array de categorias retornado pela API
                const categories = json.data || [];

                // Se não houver categorias cadastradas, mostra mensagem na tabela
                if (!categories.length) {
                    categoriesTable.innerHTML = `
                        <tr>
                            <td colspan="4" class="py-4 px-2 text-black">Nenhuma categoria cadastrada.</td>
                        </tr>
                    `;
                    return;
                }

                // Monta as linhas da tabela dinamicamente com os dados das categorias
                categoriesTable.innerHTML = categories.map(category => `
                    <tr class="border-b dark:border-gray-700">
                        <!-- Coluna do ID -->
                        <td class="py-3 px-2 text-gray-900 dark:text-gray-100">${category.id}</td>

                        <!-- Coluna do nome -->
                        <td class="py-3 px-2 text-gray-900 dark:text-gray-100">${category.name}</td>

                        <!-- Coluna do slug -->
                        <td class="py-3 px-2 text-gray-900 dark:text-gray-100">${category.slug ?? ''}</td>

                        <!-- Coluna das ações -->
                        <td class="py-3 px-2">
                            <div class="flex gap-2">
                                <!-- Link para editar a categoria -->
                                <a href="{{ route('admin.categories.edit') }}?id=${category.id}"
                                    class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                        Editar
                                </a>

                                <!-- Botão para excluir a categoria -->
                                <button onclick="deleteCategory(${category.id})"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                    Excluir
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                // Em caso de erro, mostra a mensagem dentro da tabela
                categoriesTable.innerHTML = `
                    <tr>
                        <td colspan="4" class="py-4 px-2 text-red-500">${error.message}</td>
                    </tr>
                `;
            }
        }

        //Exclui uma categoria pelo ID
        // @param {number} id -ID da 
        
        async function deleteCategory(id) {
            // Pede confirmação antes de excluir
            if (!confirm('Deseja realmente excluir esta categoria?')) return;

            try {
                // Faz a requisição DELETE para remover a categoria
                const response = await fetch(`/admin-api/categories/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        // Token CSRF necessário no Laravel
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                // Converte a resposta para JSON
                const json = await response.json();

                // Se a resposta não for OK, lança erro
                if (!response.ok) {
                    throw new Error(json.message || 'Erro ao excluir categoria.');
                }

                // Mostra mensagem de sucesso
                showMessage(json.message || 'Categoria removida com sucesso.');

                // Recarrega a tabela após excluir
                loadCategories();
            } catch (error) {
                // Mostra mensagem de erro
                showMessage(error.message, 'error');
            }
        }

        //Carrega as categorias assim que a página abre
        loadCategories();

      </script>
</x-app-layout>
