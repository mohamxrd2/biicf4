<div>
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-8">Ajouter un produit & Service</h1>

        <form>
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Entrez le nom de la catégorie</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Entrez le nom de la catégorie">
                    <select multiple class="w-full p-2 border border-gray-300 rounded-md mt-2">
                        <option value="alimentaire">Produit alimentaire</option>
                        <!-- Ajoutez d'autres options ici si nécessaire -->
                    </select>

                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Entrez le nom du produit</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Entrez le nom du produit">

                    <select multiple class="w-full p-2 border border-gray-300 rounded-md mt-2">
                        <option value="alimentaire">Banane plantain</option>
                        <!-- Ajoutez d'autres options ici si nécessaire -->
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center space-x-2">
                        Référence(
                        <span>Générer</span>
                        <input type="checkbox" class="w-4 h-4 border border-gray-300 rounded-md" />
                        )
                    </label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select class="w-full p-2 border border-gray-300 rounded-md">
                        <option>Choisissez votre type</option>
                        <option>Produit</option>
                        <option>Service</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nom du Produit & Service</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>
            </div>


            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Conditionnement</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Particularité</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Origine</label>
                    <select class="w-full p-2 border border-gray-300 rounded-md">
                        <option>Choisissez une origine</option>
                        <option>Locale</option>
                        <option>Importé</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantité Maximal</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantité Minimal</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prix Unitaire</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Spécification</label>
                    <input type="text" class="w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Tapez ici...">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input type="file" class="w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input type="file" class="w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input type="file" class="w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input type="file" class="w-full p-2 border border-gray-300 rounded-md">
                </div>


            </div>

            <div class="text-right">
                <button type="submit" class="bg-red-500 text-white p-2 rounded-md">Annuler</button>
                <button type="submit" class="bg-green-500 text-white p-2 rounded-md">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
