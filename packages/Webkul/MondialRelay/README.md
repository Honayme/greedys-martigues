# Mondial Relay pour Bagisto 2.2

Intégration complète de Mondial Relay dans Bagisto : Point Relais, Locker, Domicile avec génération d'étiquettes.

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
- **MondialRelayApi (Service)** : Communication SOAP avec API Mondial Relay
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

1. **Signature MD5** : Tous les appels API nécessitent une signature calculée avec les paramètres + clé privée
2. **Adresse intelligente** : Le système switche automatiquement entre adresse client et adresse point relais selon le mode
3. **Poids Bagisto** : Utilise `qty_ordered` (pas `quantity`) et `weight` des produits
4. **Events Bagisto** : Injection des vues via `bagisto.shop.checkout.onepage.shipping.after` et `bagisto.admin.sales.order.left_component.after`

## Installation

Le package est déjà installé. Il suffit de configurer.

## Configuration

### Configuration Admin

Allez dans **Admin > Configuration > Sales > Shipping Methods > Mondial Relay**

Remplissez :
- **Status** : Activer
- **URL API** : `https://api.mondialrelay.com/WebService.asmx`
- **Code Enseigne** : Fourni par Mondial Relay
- **Clé Privée** : Fournie par Mondial Relay
- **Code Marque** : Fourni par Mondial Relay
- Cochez les modes que vous voulez activer (Point Relais / Locker / Domicile)

**⚠️ Credentials de test** :
- Code Enseigne : `TTMRSDBX`
- Clé Privée : `9ytnxVCC`
- Code Marque : `TT`

**Ces credentials sont pour les TESTS uniquement. Remplacez-les par vos vrais credentials en production.**

### Configuration expéditeur

Dans **Admin > Configuration > Sales > Shipping Settings > Origin**, renseignez votre adresse expéditeur (obligatoire pour les étiquettes).

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

- `WSI3_PointRelais_Recherche` : Recherche de points relais
- `WSI2_CreationEtiquette` : Génération d'étiquettes

## Structure BDD

Nouvelle table `order_mondial_relay` :
- `order_id` : Lien vers la commande
- `delivery_mode` : 24R (Point Relais) / 24L (Locker) / LD1 (Domicile)
- `point_relais_id` : ID du point sélectionné
- `tracking_number` : Numéro de suivi (après génération)
- `label_url` : URL de l'étiquette PDF

## TODO avant prod

1. ✅ Tester l'API en mode test
2. ❌ Remplacer les credentials test par les vrais
3. ❌ Tester une vraie commande
4. ❌ Imprimer une étiquette
5. ❌ Renseigner les vrais tarifs domicile (LD1)

## Problèmes connus

- Les tarifs **Domicile (LD1)** sont temporaires (5€ / 5,50€ / 6€). À remplacer par les vrais tarifs.
- L'adresse expéditeur est récupérée depuis la config Bagisto. Vérifiez qu'elle est complète.

## Support

Si un problème survient :
1. Vérifier les logs Laravel : `storage/logs/laravel.log`
2. Vérifier que l'extension PHP SOAP est activée : `php -m | grep soap`
3. Tester l'API directement avec SoapUI
