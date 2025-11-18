@pushOnce('scripts')
    <script type="module">
        app.component('v-mondial-relay-selector', {
                data() {
                    return {
                        showSelector: false,
                        selectedMethod: null,
                        postcode: '',
                        searching: false,
                        points: [],
                        selectedPoint: null,
                        error: null,
                    }
                },

                mounted() {
                    // Écouter les changements de shipping method
                    document.addEventListener('change', (e) => {
                        if (e.target.name === 'shipping_method') {
                            const method = e.target.value;

                        // Afficher le sélecteur pour Point Relais et Locker
                        if (method.includes('mondialrelay') &&
                            (method.includes('point_relais') || method.includes('locker'))) {
                            this.showSelector = true;
                            this.selectedMethod = method;
                            this.loadPostcodeFromAddress();
                        } else {
                            this.showSelector = false;
                            this.selectedPoint = null;
                        }
                    }
                });
            },

            methods: {
                loadPostcodeFromAddress() {
                    // Récupérer le code postal de l'adresse de livraison
                    const postcodeInput = document.querySelector('[name="shipping[postcode]"]');
                    if (postcodeInput && postcodeInput.value) {
                        this.postcode = postcodeInput.value;
                        this.searchPoints();
                    }
                },

                searchPoints() {
                    if (!this.postcode || this.postcode.length < 4) {
                        this.error = 'Code postal invalide';
                        return;
                    }

                    this.searching = true;
                    this.error = null;

                    this.$axios.get("{{ route('mondialrelay.search') }}", {
                            params: {
                                postcode: this.postcode,
                                country: 'FR'
                            }
                        })
                        .then(response => {
                            if (response.data.success) {
                                this.points = response.data.points;

                                // Filtrer selon le type (24R ou 24L)
                                if (this.selectedMethod.includes('locker')) {
                                    this.points = this.points.filter(p => p.type === '24L');
                                } else {
                                    this.points = this.points.filter(p => p.type === '24R');
                                }

                                if (this.points.length === 0) {
                                    this.error = 'Aucun point relais trouvé pour ce code postal';
                                }
                            } else {
                                this.error = response.data.message || 'Erreur de recherche';
                            }
                        })
                        .catch(error => {
                            this.error = 'Erreur lors de la recherche des points relais';
                            console.error(error);
                        })
                        .finally(() => {
                            this.searching = false;
                        });
                },

                selectPoint(point) {
                    this.selectedPoint = point;

                    // Sauvegarder en session via API
                    this.$axios.post("{{ route('mondialrelay.save_point') }}", {
                            point: point
                        })
                        .then(response => {
                            console.log('Point relais sauvegardé');
                        })
                        .catch(error => {
                            console.error('Erreur sauvegarde point relais', error);
                        });
                },
            },

            template: `
                <div v-show="showSelector" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold mb-3">Sélectionnez votre point relais</h3>

                    <!-- Recherche -->
                    <div class="mb-4">
                        <div class="flex gap-2">
                            <input
                                type="text"
                                v-model="postcode"
                                placeholder="Code postal"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md"
                                @keyup.enter="searchPoints"
                            />
                            <button
                                @click="searchPoints"
                                :disabled="searching"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                            >
                                <span v-if="!searching">Rechercher</span>
                                <span v-else>Recherche...</span>
                            </button>
                        </div>
                        <p v-if="error" class="mt-2 text-sm text-red-600">@{{ error }}</p>
                    </div>

                    <!-- Liste des points -->
                    <div v-if="points.length > 0" class="max-h-96 overflow-y-auto space-y-2">
                        <div
                            v-for="point in points"
                            :key="point.id"
                            @click="selectPoint(point)"
                            class="p-3 border rounded-lg cursor-pointer transition"
                            :class="selectedPoint?.id === point.id ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-gray-300'"
                        >
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-sm">@{{ point.name }}</h4>
                                    <p class="text-xs text-gray-600 mt-1">
                                        @{{ point.address }}<br>
                                        @{{ point.postcode }} @{{ point.city }}
                                    </p>
                                </div>
                                <span
                                    v-if="selectedPoint?.id === point.id"
                                    class="text-blue-600 text-xl"
                                >✓</span>
                            </div>
                        </div>
                    </div>

                    <p v-if="selectedPoint" class="mt-3 text-sm text-green-600">
                        ✓ Point relais sélectionné : <strong>@{{ selectedPoint.name }}</strong>
                    </p>
                </div>
            `
        });
    </script>
@endPushOnce

@once
<!-- Composant rendu une seule fois -->
<v-mondial-relay-selector></v-mondial-relay-selector>
@endonce
