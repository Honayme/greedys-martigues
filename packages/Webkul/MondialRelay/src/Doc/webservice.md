Voici une restructuration de votre documentation, optimisée pour être ingérée par une IA (LLM). Elle utilise une hiérarchie stricte, des contraintes explicites et une séparation claire des contextes techniques.

Cette structure permet à une IA de comprendre la distinction critique entre les deux API (SOAP vs REST) et d'implémenter la logique métier sans ambiguïté.

-----

# Spécification Technique : Module Mondial Relay pour Bagisto 2.2

**Contexte :** Développement d'un module d'expédition pour Bagisto (Laravel/PHP).
**Règle d'Or :** Séparation stricte des responsabilités entre API1 (SOAP) et API2 (REST).

-----

## 1\. Architecture Globale & Responsabilités

Cette intégration repose sur l'utilisation de deux API distinctes. Il est impératif de ne pas croiser les usages.

| Caractéristique | API 1 (SOAP) | API 2 (REST/Connect) |
| :--- | :--- | :--- |
| **Usage** | **Recherche & Consultation** | **Transactionnel & Documents** |
| **Fonctions** | • Recherche Points Relais / Lockers<br>• Tracking ponctuel<br>• Récupération détails Point Relais | • Création d'expédition<br>• Génération d'étiquettes (PDF/ZPL)<br>• Modes de livraison et collecte |
| **Moment clé** | Front-End (Checkout) | Back-Office (Traitement commande) |

> **Avertissement :** Les étiquettes générées par l'API 2 sont certifiées. Elles ne doivent jamais être redimensionnées ou modifiées par le module.

-----

## 2\. Workflow Fonctionnel (Logique Métier)

### Phase 1 : Front-Office (Checkout)

* **Action :** Le client choisit son mode de livraison.
* **Protocole :** Appel **API 1 (SOAP)**.
* **Logique :**
    1.  Recherche de 10 à 30 Points Relais/Lockers via GPS ou Code Postal.
    2.  Sélection par le client.
    3.  **Persistance :** Stocker l'ID du Point Relais (ex: `FR-66974`) dans la session/commande Bagisto.

### Phase 2 : Back-Office (Création de commande)

* **Action :** L'administrateur génère l'expédition.
* **Protocole :** Appel **API 2 (REST)**.
* **Logique :**
    1.  Construction de la `ShipmentCreationRequest` (XML).
    2.  Envoi vers l'endpoint REST.
    3.  Récupération de la réponse.

### Phase 3 : Stockage & Base de Données

Le module doit sauvegarder les éléments suivants liés à l'ordre `shipment` de Bagisto :

* `pickup_point_id` (ID Point Relais sélectionné).
* `tracking_number` (Numéro d'expédition MR).
* `label_url` (Lien PDF) OU `label_content` (Base64 ZPL/IPL).
* `label_format` (A4, 10x15, etc.).

-----

## 3\. Spécifications Techniques API 2 (REST)

### Endpoints

* **Test :** `https://connect-api-sandbox.mondialrelay.com/api/shipment`
* **Production :** `https://connect-api.mondialrelay.com/api/shipment`

### Authentification & Headers

* **Headers HTTP :**
    * `Accept: application/xml`
    * `Content-Type: text/xml`
* **Encodage :** UTF-8 sans BOM.
* **Bloc Auth (dans le XML) :** Doit contenir `Login`, `Password`, `CustomerId`, `Culture` (ex: `fr-FR`), `VersionAPI` (`1.0`).

### Structure de la Requête (`ShipmentCreationRequest`)

La racine XML est `<ShipmentCreationRequest>`. Elle contient trois blocs principaux :

#### A. `Context`

Informations d'authentification (voir ci-dessus).

#### B. `OutputOptions`

Définit le format de l'étiquette retournée.

* `OutputType` : `PdfUrl` (recommandé web), `ZplCode`, ou `IplCode`.
* `OutputFormat` : `10x15`, `A4`, `A5`.

#### C. `ShipmentsList` (Liste des expéditions)

Contient un ou plusieurs blocs `<Shipment>`.

* **Identification :** `OrderNo` (Réf commande Bagisto), `CustomerNo`.
* **Colis (`Parcels`) :**
    * `ParcelCount` (1..n).
    * `Parcel` : Contient `Content`, `Weight` (Grammes, min 10), `Unit="gr"`.
    * Dimensions optionnelles mais recommandées (`Length`, `Width`, `Depth` en cm).
* **Valeur :** `shipmentValue.amount` (Decimal), `shipmentValue.currency` (EUR).
* **Instructions :** `DeliveryInstruction` (Commentaire court).

-----

## 4\. Mapping des Modes de Livraison & Adresses

### Mapping des Codes (DeliveryMode / CollectionMode)

Tu dois mapper les méthodes de livraison Bagisto vers ces codes MR :

| Code MR | Description | Usage Bagisto | Note Technique |
| :--- | :--- | :--- | :--- |
| **24R** | Livraison Point Relais | Standard | Requiert `Location` (ID du point) |
| **24L** | Livraison Locker / XL | Gros colis / 24h | Requiert `Location` (ID du locker) |
| **HOM** | Livraison Domicile | Standard Domicile | Adresse client complète requise |
| **REL** | Collecte Point Relais | Retour client | - |
| **CCC** | Collecte chez Marchand | Expédition standard | Adresse boutique expéditeur |
| **LCC** | Livraison chez Marchand | - | - |
| **XOH** | Livraison Spéciale J+1 | Express | Requiert flux EDI complémentaire |

**Logique Location :** Pour `24R` et `24L`, le champ `Location` dans la requête XML doit impérativement contenir l'ID récupéré via l'API 1 (ex: `FR-39807`).

### Formatage des Adresses & Contacts

Blocs `Sender/Address` et `Recipient/Address`.

* **Champs Requis :** `Firstname` + `Lastname` (ou `addressAdd1`), `Streetname`, `CountryCode` (ISO 3166-1 alpha-2), `PostCode`, `City`.
* **Téléphones (MobileNo / PhoneNo) :**
    * **Obligatoire** pour Domicile et certains pays (Pologne).
    * **Regex France :** `^((00|\+)33|0)[0-9][0-9]{8}$`
    * Si autre pays : Appliquer la regex correspondante au CountryCode.
* **Contraintes :**
    * `CountryCode` : 2 lettres obligatoires.
    * `PostCode` / `City` : Respecter les regex de longueur (max 10/30 chars).

-----

## 5\. Traitement de la Réponse

### Succès (`ShipmentCreationResponse`)

Analyser `LabelList/Label` pour chaque shipment.

* Si `OutputType=PdfUrl` : Récupérer l'URL dans la balise `Output`.
* Si `OutputType=ZplCode` : Récupérer le hash Base64 dans `Output`.
* Les infos de debug (barcodes, routing) sont disponibles dans `RawContent` mais ne servent pas à l'utilisateur final.

### Gestion des Erreurs (`StatusList`)

Le système renvoie un code dans `Status.code`.

* **10xxx (Auth/Config) :** Vérifier Login/Pass/ID Client.
* **10011–10065 (Requête) :** Champ manquant, format XML invalide, poids \< 10g.
* **10054–99999 (Métier) :**
    * Problème d'adresse (Code postal incohérent avec Ville).
    * Point Relais invalide ou fermé.
    * Mode de livraison incompatible avec le pays.

-----

### Prochaine étape pour toi

https://storage.mondialrelay.fr/PrÃ©sentation%20des%20WebServices.pdf

https://storage.mondialrelay.fr/web-service-dual-carrier-v-271.pdf
