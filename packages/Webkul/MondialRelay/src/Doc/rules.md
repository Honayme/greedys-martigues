Contexte et options de sortie
Culture

Type: String

Max: 5

Format: ^[a-z]{2}-[A-Z]{2}$ (ex. fr-FR)

CustomerId

Type: String

Max: 8

Format: ^[0-9A-Z]{2}[0-9A-Z]{6}$

outputFormat (PDF)

Valeurs: A4, A5, 10x15

outputType

Valeurs: PdfUrl, ZplCode, IplCode

Shipment (niveau expédition)
OrderNo

Type: String

Max: 15

Format: ^(|[0-9A-Z_-]{0,15})$

CustomerNo

Type: String

Max: 9

Format: ^(|[0-9A-Z]{0,9})$

parcelCount

Type: Integer

Max digits: 2

Format: ^[0-9]{1,2}$

shipmentValue.amount

Type: Float

Max digits: 10

Format: ^[0-9]{0,10}$ (valeur en EUR, ex. 20.50)

shipmentValue.currency

Type: Enum/String

Max: 3

Format: ^(|[A-Z]{3})$ (actuellement EUR)

DeliveryMode.mode

Type: String

Max: 3

Format: ^[0-9A-Z]{3}$

Valeurs possibles: CCC, REL, LCC, HOM, 24R, 24L, XOH

DeliveryMode.location

Type: String

Max: 10

Format: ^[0-9A-Z]{0,10}$

Obligatoire si Mode ∈ {24R, 24L, XOH}

CollectionMode.mode

Type: String

Max: 3

Format: ^[0-9A-Z]{3}$

CollectionMode.location

Type: String

Max: 10

Format: ^[0-9A-Z]{0,10}$

deliveryInstruction

Type: String

Max: 30

Format: ^[0-9A-Z_\\-\'., /]{0,30}$

Parcel (colis)
Parcel.content

Type: String

Max: 40

Format: ^[0-9A-Z_\\-\'., /]{0,40}$

Parcel.length.Value / Parcel.width.Value / Parcel.depth.Value

Type: Integer

Max digits: 10

Format: ^[0-9]{0,10}$

Parcel.length.Unit, Parcel.width.Unit, Parcel.depth.Unit

Type: Enum

Valeur: cm

Parcel.weight.Value

Type: Integer

Max digits: 10

Format: ^[0-9]{0,10}$ (en grammes, min 10)

Parcel.weight.Unit

Type: Enum

Valeur: gr

Adresse (Sender / Recipient)
Title

Type: String

Max concat (Title+Firstname+Lastname): 32

Format: ^[0-9A-Z_\\-\'., /]{0,32}$

Firstname

Type: String

Obligation: Firstname + Lastname OU addressAdd1 (M(1))

Lastname

Type: String

Idem Firstname

streetname

Type: String

Max: 40 (et HouseNo + Streetname ≤ 40)

Format: ^[0-9A-Z_\\-\'., /]{0,40}$

houseNo

Type: String

Max: 10

Format: ^[0-9A-Z_\\-\'., /]{0,10}$

countryCode

Type: String

Max: 2

Format: ^[A-Z]{2}$

postcode

Type: String

Max: 10

Format: ^[A-Za-z_\\-']{2,10}$

city

Type: String

Max: 30

Format: ^[A-Za-z_\\-']{2,30}$

addressAdd1

Type: String

Max: 30

Format: ^[0-9A-Z_\\-\'., /]{0,30}$

addressAdd2 / addressAdd3

Type: String

Max: 30

Format: ^[0-9A-Z_\\-\'., /]{0,30}$

phoneNo (général, exemple France)

Type: String

Max: 20

France: ^((00|\\+)33|0)[0-9][0-9]{8}$

mobileNo (France)

Type: String

Max: 20

France: ^((00|\\+)33|0)[0-9][0-9]{8}$

email

Type: String

Max: 70

Format: ^[\\w\\-\\.\\@_]{7,70}$

Regex téléphones internationaux (format « +CC » suivi du numéro)
France (FR): préfixe +33, numéro: ^[1-9][0-9]{8}$

Espagne (ES): +34 puis ^[1-9][0-9]{8}$

Belgique (BE): +32 puis ^[4]?[0-9]{8}$

Allemagne (DE): +49 puis ^[0-9]{5,11}$

Luxembourg (LU): +352 puis ^[0-9]{5,9}$

Portugal (PT): +351 puis ^[0-9]{5,9}$

Autriche (AT): +43 puis ^[0-9]{4,13}$

Royaume-Uni (UK): +44 puis ^[0-9]{7,10}$

Italie (IT): +39 puis ^[0-9]{9,10}$

Guyane (GF): +594 puis ^[1-9][0-9]{8}$

Saint Martin (MF): +590 puis ^[1-9][0-9]{8}$

Martinique (MQ): +596 puis ^[1-9][0-9]{8}$

Mayotte (YT): +262 puis ^[1-9][0-9]{8}$

Pays-Bas (NL): +31 puis ^[0-9]{9}$

Irlande (IE): +353 puis ^[0-9]{9}$

Monaco (FR côté code): +377 puis ^[0-9]{5,9}$

Suisse (CH): +41 puis ^[0-9]{9}$

Pologne (PL): +48 puis ^\\d{9}$

Contraintes techniques requête / réponse
Encodage XML: UTF‑8 sans BOM.

HTTP headers:

Accept: application/xml

Content-Type: text/xml



# Structure de la requêtes pour création d'étiquette

<ShipmentCreationRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns="http://www.example.org/Request">
<Context>
<Login>...</Login>
<Password>...</Password>
<CustomerId>...</CustomerId>
<Culture>fr-FR</Culture>
<VersionAPI>1.0</VersionAPI>
</Context>

  <OutputOptions>
    <OutputFormat>10x15</OutputFormat>
    <OutputType>PdfUrl</OutputType>
  </OutputOptions>

  <ShipmentsList>
    <Shipment>
      <OrderNo>...</OrderNo>
      <CustomerNo>...</CustomerNo>
      <ParcelCount>1</ParcelCount>
      <shipmentValue.amount>...</shipmentValue.amount>
      <shipmentValue.currency>EUR</shipmentValue.currency>
      <DeliveryMode Mode="24R" Location="FR-xxxxx" />
      <CollectionMode Mode="CCC" Location="" />

      <Parcels>
        <Parcel>
          <Content>...</Content>
          <Length Value="..." Unit="cm" />
          <Width  Value="..." Unit="cm" />
          <Depth  Value="..." Unit="cm" />
          <Weight Value="..." Unit="gr" />
        </Parcel>
      </Parcels>

      <DeliveryInstruction>...</DeliveryInstruction>

      <Sender>
        <Address>
          <Title>...</Title>
          <Firstname>...</Firstname>
          <Lastname>...</Lastname>
          <Streetname>...</Streetname>
          <HouseNo>...</HouseNo>
          <CountryCode>FR</CountryCode>
          <PostCode>...</PostCode>
          <City>...</City>
          <AddressAdd1>...</AddressAdd1>
          <AddressAdd2>...</AddressAdd2>
          <AddressAdd3>...</AddressAdd3>
          <PhoneNo>...</PhoneNo>
          <MobileNo>...</MobileNo>
          <Email>...</Email>
        </Address>
      </Sender>

      <Recipient>
        <Address>
          <Title>...</Title>
          <Firstname>...</Firstname>
          <Lastname>...</Lastname>
          <Streetname>...</Streetname>
          <HouseNo>...</HouseNo>
          <CountryCode>FR</CountryCode>
          <PostCode>...</PostCode>
          <City>...</City>
          <AddressAdd1>...</AddressAdd1>
          <AddressAdd2>...</AddressAdd2>
          <AddressAdd3>...</AddressAdd3>
          <PhoneNo>...</PhoneNo>
          <MobileNo>...</MobileNo>
          <Email>...</Email>
        </Address>
      </Recipient>
    </Shipment>
  </ShipmentsList>
</ShipmentCreationRequest>


# Exemple de requête :

XML REQUEST EXAMPLE <?xml version="1.0" encoding="utf-8"?> <ShipmentCreationRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.example.org/Request">   <Context>     <Login>BDTEST@business-api.mondialrelay.com</Login>     <Password>CCCCCCSSSSSS</Password>     <CustomerId>BDTEST</CustomerId>     <Culture>fr-FR</Culture>     <VersionAPI>1.0</VersionAPI>   </Context>   <OutputOptions>     <OutputFormat>10x15</OutputFormat>     <OutputType>PdfUrl</OutputType>   </OutputOptions>   <ShipmentsList>     <Shipment>       <OrderNo>KDZ-9999</OrderNo>       <CustomerNo>CUS1234</CustomerNo>       <ParcelCount>1</ParcelCount>       <DeliveryMode Mode="24R" Location="FR-66974" />       <CollectionMode Mode="CCC" Location="" />       <Parcels>         <Parcel>           <Content>Livres</Content>           <Weight Value="1000" Unit="gr" />                               <Length Value="1" Unit="cm" />           <Width Value="31" Unit="cm" />           <Depth Value="41" Unit="cm" />         </Parcel>       </Parcels>       <DeliveryInstruction>Livrer au fond a droite</DeliveryInstruction>       <Sender>         <Address>           <Title />           <Firstname />           <Lastname />           <Streetname>Avenue Antoine Pinay</Streetname>           <HouseNo>4</HouseNo>           <CountryCode>FR</CountryCode>           <PostCode>59510</PostCode>           <City>HEM</City>           <AddressAdd1>Mondial Relay</AddressAdd1>           <AddressAdd2 />           <AddressAdd3>Mondial Relay</AddressAdd3>           <PhoneNo />           <MobileNo>+33320202020</MobileNo>           <Email>contact@mondialrelay.fr</Email>         </Address>       </Sender>       <Recipient>         <Address>           <Title>Mr</Title>           <Firstname>John</Firstname>           <Lastname>THETESTER</Lastname>           <Streetname>test street</Streetname>           <HouseNo>10</HouseNo>           <CountryCode>FR</CountryCode>           <PostCode>75001</PostCode>           <City>Paris 1</City>           <AddressAdd1 />           <AddressAdd2 />           <AddressAdd3 />           <PhoneNo>+33320202020</PhoneNo>           <MobileNo />           <Email>contact@mondialrelay.fr</Email>         </Address>       </Recipient>     </Shipment>   </ShipmentsList> </ShipmentCreationRequest> 
