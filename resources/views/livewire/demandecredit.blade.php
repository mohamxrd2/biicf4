<div>
    <div class="relative md:static  bg-white rounded-lg shadow-lg">
        <ol
            class="items-center flex w-full max-w-2xl text-center text-sm font-medium text-gray-500 dark:text-gray-400 sm:text-base  p-5">
            <li
                class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                <span
                    class="flex items-center after:mx-2 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                    <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Details
                </span>
            </li>

            <li
                class="after:border-1 flex items-center text-primary-700 after:mx-6 after:hidden after:h-1 after:w-full after:border-b after:border-gray-200 dark:text-primary-500 dark:after:border-gray-700 sm:after:inline-block sm:after:content-[''] md:w-full xl:after:mx-10">
                <span
                    class="flex items-center after:mx-2  after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                    <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Achat
                </span>
            </li>

            <li>
                <span
                    class="flex items-center after:mx-2 text-blue-600 after:text-gray-200 after:content-['/'] dark:after:text-gray-500 sm:after:hidden">
                    <svg class="me-2 h-4 w-4 sm:h-5 sm:w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Procedure
                </span>
            </li>
        </ol>

        <h2 class="mb-4 text-xl text-center font-bold text-gray-900 dark:text-white">Formulaire De Demande Crédit</h2>
        <form action="#">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 rounded-lg shadow-md dark:bg-gray-800">
                <!-- Titre -->
                <div class="sm:col-span-2">
                    <label for="name" class="block mb-3 text-lg font-semibold text-gray-900 dark:text-white">Demande
                        ID (Demande):</label>
                    <label for="brand" class="block mb-3 text-lg font-semibold text-gray-900 dark:text-white">Objet
                        du financement:</label>
                </div>

                <!-- Montant recherché -->
                <div class="w-full">
                    <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Montant
                        recherché</label>
                    <input type="number" name="price" id="price"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="10.000 XOF" required>
                </div>

                <!-- Type de financement -->
                <div>
                    <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type de
                        financement</label>
                    <select id="category"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option selected>Choisir un type</option>
                        <option value="TV">Demande Directe</option>
                        <option value="PC">Offre composite (groupée)</option>
                    </select>
                </div>

                <!-- Ciblage du bailleur -->
                <div>
                    <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ciblez un
                        bailleur ou entrez son username</label>
                    <select id="category"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option selected>Choisir un bailleur</option>
                        <option value="Bank">Bank/IFD</option>
                        <option value="Pgm">Pgm Public/Para-Public</option>
                        <option value="Fonds">Fonds d’investissement</option>
                        <option value="Particulier">Particulier</option>
                    </select>
                </div>

                <!-- Durée du crédit -->
                <div>
                    <label for="item-weight" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Durée
                        du crédit (mois)</label>
                    <input type="number" name="item-weight" id="item-weight"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="12" required>
                </div>

                <!-- Dates de début et de fin -->
                <div>
                    <label for="start-date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date de
                        début</label>
                    <input type="date" name="start-date" id="start-date"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        required>
                </div>

                <div>
                    <label for="end-date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date de
                        fin</label>
                    <input type="date" name="end-date" id="end-date"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        required>
                </div>

                <!-- Heures de début et de fin -->
                <div>
                    <label for="start-time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Heure
                        de début</label>
                    <input type="time" name="start-time" id="start-time"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        required>
                </div>

                <div>
                    <label for="end-time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Heure de
                        fin</label>
                    <input type="time" name="end-time" id="end-time"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        required>
                </div>

                <!-- Retour sur investissement -->
                <div class="sm:col-span-2">
                    <label for="roi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Retour
                        sur investissement</label>
                    <input type="number" name="roi" id="roi"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-3 transition duration-150 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="12%" required>
                </div>
            </div>

        </form>
    </div>
</div>
