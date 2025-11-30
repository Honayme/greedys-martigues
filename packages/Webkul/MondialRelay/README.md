# Mondial Relay pour Bagisto 2.2

Intégration complète de Mondial Relay dans Bagisto : Point Relais, Locker, Domicile avec génération d'étiquettes.

**Important** : Les Lockers et Points Relais utilisent le même code service Mondial Relay (24R). La différence se fait via le Location ID du point sélectionné.

## 🎯 Big Picture : Comment ça marche

### Flow complet

```
1. CLIENT CHECKOUT
   ├─ Sélectionne "Mondial Relay" comme méthode de livraison
   ├─ Choisit mode : Point Relais / Locker / Domicile
   ├─ Si Point Relais/Locker : Widget recherche par code postal
   │  └─ API WSI3_PointRelais_Recherche → Liste des points
   └─ Sélectionne un point → Sauvegarde en session

2. COMMANDE CRÉÉE
   ├─ OrderObserver écoute la création de commande
   ├─ Si méthode = "mondialrelay_*" → Sauvegarde dans table order_mondial_relay
   │  ├─ delivery_mode (24R/24L/LD1)
   │  ├─ point_relais_id + adresse complète du point
   │  └─ Liaison avec order_id
   └─ Session nettoyée

3. ADMIN : GÉNÉRATION ÉTIQUETTE
   ├─ Page détail commande → Section "Informations Mondial Relay" affichée
   ├─ Bouton "Générer l'étiquette" → LabelService
   │  ├─ Récupère adresse client (pour nom/tel/email)
   │  ├─ Selon mode :
   │  │  ├─ Point Relais/Locker : adresse = point relais
   │  │  └─ Domicile : adresse = client
   │  ├─ Calcule poids total (qty_ordered × weight)
   │  └─ API WSI2_CreationEtiquette
   ├─ Sauvegarde tracking_number + label_url
   └─ Bouton "Télécharger l'étiquette" activé
```

### Architecture technique

**📦 Composants principaux**

- **MondialRelay (Carrier)** : Calcule les tarifs au checkout (3 modes)
- **MondialRelayApi (Service)** : Communication SOAP avec API V1 (recherche points relais)
- **MondialRelayRestApi (Service)** : Communication REST avec API V2 (création étiquettes)
- **LabelService** : Orchestration génération d'étiquettes
- **OrderObserver** : Capture création commande → sauvegarde données MR
- **OrderMondialRelay (Model)** : Table BDD pour infos livraison MR

**🎨 Vues**

- **Frontend** : `point-relais-selector.blade.php` (widget Alpine.js)
- **Admin** : `mondial-relay-info.blade.php` (section dans page commande)

**📡 API Routes**

- `GET /mondialrelay/search-points` → Recherche points relais
- `POST /mondialrelay/save-point` → Sauvegarde point en session
- `POST /admin/mondial-relay/orders/{id}/generate-label` → Génère étiquette
- `GET /admin/mondial-relay/orders/{id}/download-label` → Télécharge PDF

**⚙️ Points techniques clés**

1. **Dual API** : API V1 SOAP (recherche) + API V2 REST (étiquettes)
2. **Validation stricte** : Téléphone obligatoire (regex France), poids 10g-30kg
3. **Adresse intelligente** : Le système switche automatiquement entre adresse client et adresse point relais selon le mode
4. **Poids Bagisto** : Utilise `qty_ordered` (pas `quantity`) et `weight` des produits
5. **Events Bagisto** : Injection des vues via `bagisto.shop.checkout.onepage.shipping.after` et `bagisto.admin.sales.order.left_component.after`

## Installation

Le package est déjà installé. Il suffit de configurer.

## Configuration

### Configuration Admin

Allez dans **Admin > Configuration > Sales > Shipping Methods > Mondial Relay**

Remplissez :

**API V1 (SOAP) - Recherche Points Relais**
- **Code Enseigne** : `CC23JOIN`
- **Clé Privée** : `QaMz6mTT`
- **Code Marque** : `CC`
- **URL API V1** : `https://api.mondialrelay.com/WebService.asmx`

**API V2 (REST) - Création Étiquettes**
- **URL API V2** : `https://connect-api.mondialrelay.com/api/shipment`
- **Login API V2** : `CC23JOIN@business-api.mondialrelay.com`
- **Password API V2** : `-jTqc7ps1>wAbBYuh3p3`
- **Brand ID** : `CC23JOIN`

**Options**
- **Format étiquette** : 10x15, A4 ou A5
- Cochez les modes que vous voulez activer (Point Relais / Locker / Domicile)

### Configuration expéditeur

Dans **Admin > Configuration > Sales > Shipping Settings > Origin**, renseignez votre adresse expéditeur :

- **Store Name** : Nom de votre boutique
- **Address** : Adresse complète
- **City** : Ville
- **Postcode** : Code postal
- **Country** : FR
- **Contact Number** : ⚠️ **OBLIGATOIRE** - Téléphone au format `0XXXXXXXXX` ou `+33XXXXXXXXX`

**Important** : Le numéro de téléphone expéditeur est obligatoire pour l'API V2. Sans lui, la génération d'étiquette échouera.

## Tarification

Le package utilise les tarifs **0-9 colis/mois** par défaut :
- **Locker** : 2,99 €
- **Point Relais** : 3,49 € / 3,58 € / 4,49 € (selon poids)

Pour changer de tranche tarifaire, éditez `packages/Webkul/MondialRelay/src/Config/carriers.php`.

## Utilisation

### Frontend

Au checkout, quand le client choisit Mondial Relay :
1. Les 3 options s'affichent (selon ce que vous avez activé)
2. Si Point Relais ou Locker → widget de recherche s'affiche
3. Le client sélectionne son point → sauvegarde automatique

### Admin

Sur une commande utilisant Mondial Relay :
1. Allez dans la page de détail de la commande
2. Bouton **"Générer l'étiquette"** → appelle l'API Mondial Relay
3. Bouton **"Télécharger l'étiquette"** → récupère le PDF

## API Mondial Relay utilisée

**API V1 (SOAP)**
- `WSI3_PointRelais_Recherche` : Recherche de points relais

**API V2 (REST)**
- `POST /api/shipment` : Création d'expédition et génération d'étiquettes (format XML)

## Structure BDD

Nouvelle table `order_mondial_relay` :
- `order_id` : Lien vers la commande
- `delivery_mode` : 24R (Point Relais & Locker) / HOM (Domicile)
- `point_relais_id` : ID du point sélectionné (distingue Locker vs Point Relais)
- `point_relais_*` : Adresse complète du point relais ou locker
- `tracking_number` : Numéro de suivi (après génération)
- `label_url` : URL de l'étiquette PDF

## Validations et Contraintes

**Téléphone**
- Format obligatoire : `0XXXXXXXXX` ou `+33XXXXXXXXX`
- Requis pour expéditeur ET destinataire
- Validation par regex selon documentation MR

**Poids**
- Minimum : 10 grammes
- Maximum : 30 kg
- Calculé automatiquement depuis les produits

**Codes service Mondial Relay**
- `24R` : Point Relais et Locker standard (jusqu'à 30kg)
- `24L` : Point Relais XL (20-30kg, nécessite autorisation spéciale) - **NON utilisé dans ce package**
- `HOM` : Livraison domicile
- `LD1` : Code tiers non officiel - **NON utilisé**

## TODO

1. ✅ Migration API V2 REST
2. ✅ Validation téléphone stricte
3. ✅ Gestion formats étiquette
4. ⏳ Tester création étiquette Point Relais
5. ⏳ Tester création étiquette Locker
6. ⏳ Tester création étiquette Domicile
7. ⏳ Vérifier impression étiquettes PDF

## Support

Si un problème survient :
1. Vérifier les logs Laravel : `storage/logs/laravel.log`
2. Vérifier que l'extension PHP SOAP est activée : `php -m | grep soap`
3. Tester l'API directement avec SoapUI
