<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
    .mondial-relay-container {
        max-width: 100%;
        overflow-x: hidden;
        box-sizing: border-box;
    }

    .mondial-relay-search {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .mondial-relay-search input {
        min-width: 0;
        flex: 1 1 auto;
    }

    .mondial-relay-search button {
        flex-shrink: 0;
        white-space: nowrap;
    }

    .mondial-relay-split-view {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        min-height: 500px;
    }

    .mondial-relay-map-wrapper {
        min-height: 500px;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    #map-container {
        width: 100%;
        min-height: 500px;
    }

    /* Responsive sur mobile */
    @media (max-width: 768px) {
        .mondial-relay-split-view {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .mondial-relay-split-view > div:first-child {
            max-height: 400px;
        }

        .mondial-relay-map-wrapper,
        #map-container {
            min-height: 300px;
        }
    }

    @media (max-width: 480px) {
        .mondial-relay-search {
            gap: 0.25rem;
        }

        .mondial-relay-search input,
        .mondial-relay-search button {
            padding: 0.5rem 0.75rem;
            font-size: 14px;
        }

        .mondial-relay-split-view > div:first-child {
            max-height: 350px;
        }

        .mondial-relay-map-wrapper,
        #map-container {
            min-height: 250px;
        }
    }
</style>

@pushOnce('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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
                        map: null,
                        markers: [],
                        openedHorairesId: null, // ID du point dont les horaires sont ouverts
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

                            const previousMethod = this.selectedMethod;
                            this.showSelector = true;
                            this.selectedMethod = method;

                            // Si on change de type (Locker ↔ Point Relais) et qu'on a déjà des résultats
                            // → Relancer automatiquement la recherche
                            if (previousMethod && previousMethod !== method && this.postcode && this.points.length > 0) {
                                this.selectedPoint = null; // Réinitialiser la sélection
                                this.searchPoints();
                            } else {
                                // Sinon, charger le code postal depuis l'adresse (comportement initial)
                                this.loadPostcodeFromAddress();
                            }
                        } else {
                            this.showSelector = false;
                            this.selectedPoint = null;
                            this.points = [];
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
                    this.openedHorairesId = null; // Fermer les horaires ouverts lors d'une nouvelle recherche

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
                                } else {
                                    // Initialiser la carte après avoir chargé les points
                                    this.initMap();
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

                initMap() {
                    console.log('initMap() appelé, points:', this.points.length);
                    console.log('Leaflet disponible:', typeof L !== 'undefined');

                    this.$nextTick(() => {
                        const container = document.getElementById('map-container');
                        console.log('map-container trouvé:', !!container);
                        console.log('map-container dimensions:', container?.offsetWidth, container?.offsetHeight);

                        if (this.map) {
                            this.map.remove();
                        }

                        // Créer la carte centrée sur les points
                        const centerLat = this.points.reduce((sum, p) => sum + parseFloat(p.latitude), 0) / this.points.length;
                        const centerLng = this.points.reduce((sum, p) => sum + parseFloat(p.longitude), 0) / this.points.length;

                        console.log('Centre carte:', centerLat, centerLng);

                        try {
                            this.map = L.map('map-container').setView([centerLat, centerLng], 12);
                            console.log('Carte créée avec succès');
                        } catch (e) {
                            console.error('Erreur création carte:', e);
                        }

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(this.map);

                        // Ajouter les marqueurs
                        this.markers = [];
                        this.points.forEach(point => {
                            const isSelected = this.selectedPoint?.id === point.id;

                            // Icône personnalisée selon sélection
                            const icon = L.divIcon({
                                className: 'custom-marker',
                                html: `<div style="background: ${isSelected ? '#2563eb' : '#ef4444'}; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">${this.points.indexOf(point) + 1}</div>`,
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            });

                            const marker = L.marker([parseFloat(point.latitude), parseFloat(point.longitude)], { icon })
                                .addTo(this.map)
                                .bindPopup(`
                                    <div style="min-width: 200px;">
                                        <strong>${point.name}</strong><br>
                                        ${point.address}<br>
                                        ${point.postcode} ${point.city}
                                    </div>
                                `);

                            // Cliquer sur un marqueur sélectionne le point
                            marker.on('click', () => {
                                this.selectPoint(point);
                            });

                            this.markers.push({ id: point.id, marker, icon });
                        });
                    });
                },

                selectPointAndFocusMap(point) {
                    this.selectPoint(point);

                    // Ouvrir automatiquement les horaires du point sélectionné
                    this.openedHorairesId = point.id;

                    // Centrer la carte sur ce point et ouvrir sa popup
                    if (this.map && point.latitude && point.longitude) {
                        this.map.setView([parseFloat(point.latitude), parseFloat(point.longitude)], 15);

                        // Mettre à jour les icônes
                        this.markers.forEach(m => {
                            const isSelected = m.id === point.id;
                            const pointIndex = this.points.findIndex(p => p.id === m.id);

                            const newIcon = L.divIcon({
                                className: 'custom-marker',
                                html: `<div style="background: ${isSelected ? '#2563eb' : '#ef4444'}; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">${pointIndex + 1}</div>`,
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            });

                            m.marker.setIcon(newIcon);
                            if (isSelected) {
                                m.marker.openPopup();
                            }
                        });
                    }
                },

                formatHoraires(horaires) {
                    if (!horaires || Object.keys(horaires).length === 0) {
                        return 'Horaires non disponibles';
                    }
                    return Object.entries(horaires)
                        .map(([jour, horaire]) => `${jour}: ${horaire || 'Fermé'}`)
                        .join('<br>');
                },
            },

            template: `
                <div v-show="showSelector" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 mondial-relay-container">
                    <h3 class="text-lg font-semibold mb-3">Sélectionnez votre point relais</h3>

                    <!-- Recherche -->
                    <div class="mb-4">
                        <div class="mondial-relay-search">
                            <input
                                type="text"
                                v-model="postcode"
                                placeholder="Code postal"
                                class="px-3 py-2 border border-gray-300 rounded-md"
                                @keyup.enter="searchPoints"
                            />
                            <button
                                @click="searchPoints"
                                :disabled="searching"
                                style="background-color: #2563eb; margin-left: 30px; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 500; border: none; cursor: pointer;"
                                :style="searching ? 'opacity: 0.5;' : ''"
                            >
                                <span v-if="!searching">Rechercher</span>
                                <span v-else>Rechercher...</span>
                            </button>
                        </div>
                        <p v-if="error" class="mt-2 text-sm text-red-600">@{{ error }}</p>
                    </div>

                    <!-- Split View : Liste + Carte -->
                    <div v-if="points.length > 0" class="mondial-relay-split-view" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; height: 500px;">

                        <!-- Liste des points (gauche) -->
                        <div style="overflow-y: auto; padding-right: 0.5rem; min-width: 0; max-height: 500px;">
                            <div
                                v-for="(point, index) in points"
                                :key="point.id"
                                @click="selectPointAndFocusMap(point)"
                                class="p-3 mb-3 border rounded-lg cursor-pointer transition"
                                :style="selectedPoint?.id === point.id ? 'border-color: #2563eb; background-color: #eff6ff; border-width: 2px;' : 'border-color: #e5e7eb;'"
                            >
                                <div class="flex items-start gap-3">
                                    <!-- Numéro du point -->
                                    <div :style="'background: ' + (selectedPoint?.id === point.id ? '#2563eb' : '#ef4444') + '; color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; flex-shrink: 0;'">
                                        @{{ index + 1 }}
                                    </div>

                                    <!-- Infos du point -->
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-sm mb-1">@{{ point.name }}</h4>
                                        <p class="text-xs text-gray-600 mb-2">
                                            📍 @{{ point.address }}<br>
                                            @{{ point.postcode }} @{{ point.city }}
                                        </p>

                                        <!-- Horaires avec toggle automatique -->
                                        <div class="mt-1">
                                            <div
                                                @click.stop="openedHorairesId = openedHorairesId === point.id ? null : point.id"
                                                class="text-xs text-blue-600 cursor-pointer hover:text-blue-700 select-none"
                                            >
                                                🕐 Horaires @{{ openedHorairesId === point.id ? '▼' : '▶' }}
                                            </div>
                                            <div
                                                v-show="openedHorairesId === point.id"
                                                class="mt-1 text-xs text-gray-600 pl-3 leading-relaxed"
                                                v-html="formatHoraires(point.horaires)"
                                            ></div>
                                        </div>
                                    </div>

                                    <!-- Checkmark -->
                                    <span
                                        v-if="selectedPoint?.id === point.id"
                                        style="color: #2563eb; font-size: 1.5rem; flex-shrink: 0;"
                                    >✓</span>
                                </div>
                            </div>
                        </div>

                        <!-- Carte (droite) -->
                        <div class="mondial-relay-map-wrapper" style="min-height: 500px; height: 500px; border-radius: 0.5rem; overflow: hidden; border: 1px solid #e5e7eb;">
                            <div id="map-container" style="width: 100%; height: 100%;"></div>
                        </div>
                    </div>

                    <p v-if="selectedPoint" class="mt-3 text-sm" style="color: #059669; font-weight: 500;">
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
