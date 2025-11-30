Mondial Relay Dual Carrier Web Service: A Comprehensive Integration Guide (v2.7.1)

The Mondial Relay Dual Carrier Web Service is a REST API designed to provide merchants with a powerful and direct method for integrating core shipping functionalities into their own systems. This service streamlines the logistics process by enabling programmatic access to the Mondial Relay network, allowing for the automated routing and generation of shipping labels for both outbound and return parcels. By leveraging this API, merchants can commission shipments without needing in-depth knowledge of the underlying business and technical details of the shipping network.

The service provides a focused set of essential functions for managing parcel shipments:

* Parcel Routing: Automatically determines the correct routing for parcels through the extensive Mondial Relay network, covering all offered destination countries.
* Outbound Label Generation: Creates and delivers compliant shipping labels for parcels being sent from the merchant to a recipient.
* Return Label Generation: Facilitates customer returns by generating the necessary labels for parcels being sent back to the merchant.

This document, Version 2.7.1 (dated June 2024), serves as a comprehensive technical specification for developers. It provides all the necessary information to successfully integrate the Dual Carrier Web Service, covering initial setup and authentication, request and response structures, error handling, and reference data.

Understanding the initial configuration is the first critical step toward a successful integration, which is detailed in the following section.


--------------------------------------------------------------------------------


2.0 Getting Started: Prerequisites and Authentication

Before you can make any calls to the Mondial Relay API, you must first configure your environment and obtain unique credentials. This process ensures that your requests are properly authenticated and directed to the correct service endpoint. This section outlines the critical first steps for establishing a connection and preparing for a successful integration.

2.1 API Environments

Mondial Relay provides distinct environments for testing and production use. It is essential to use the appropriate URL for each phase of your development lifecycle.

Environment	Service URL
Test Environment	https://connect-api-sandbox.mondialrelay.com/api/shipment
Production Environment	https://connect-api.mondialrelay.com/api/shipment

2.2 Generating API Credentials

Your unique API credentials must be generated from the Mondial Relay Connect portal. Follow these steps to access or create them:

1. Log in to the Mondial Relay Connect portal at https://connect.mondialrelay.com.
2. Navigate to the Administration / Gestion des Utilisateurs menu option.
3. Select the user account for which you want to configure API access.
4. In the user rights management screen, add the Configuration des API permission.
5. Log out of the portal and log back in. This step is necessary to refresh your user session and load the newly assigned permissions.
6. Navigate to the newly available Administration / Configuration des API menu option.
7. From the dropdown menu, select API Version V2.0. You can now view or generate the required credentials for accessing the web service.

With your environment configured and credentials in hand, it is important to understand the fundamental technical protocols and usage policies that govern the API.


--------------------------------------------------------------------------------


3.0 Core Concepts and Usage Policies

The Mondial Relay Web Service is built on a specific technical foundation and is governed by mandatory usage policies. Adherence to these standards and rules is essential for ensuring all API requests are processed successfully and the integration remains compliant.

3.1 Technical Specifications

The API operates according to the following technical standards:

Protocol : The service is a REST web service provided exclusively over HTTPS, ensuring secure data transmission.

Data Format : All data exchange, for both requests and responses, is performed using XML. All XML documents must be encoded in UTF-8 without BOM.

HTTP Headers : When making API calls, the following HTTP headers are required:

Header Name	Value
Accept	application/xml
Content-Type	text/xml

3.2 Software Use Policies

To ensure the stability and reliability of the service for all users, the following policies are strictly enforced:

* No Batch Scripts The use of batch scripts to call this web service is not permitted.
* XML Validation All XML requests must be validated against standard XML rules before being sent to the Mondial Relay API. It is recommended to use a validator, such as the one available at: http://www.w3schools.com/xml/xml_validator.asp.
* Label Validation Before sending any physical parcels through the network, the full set of labels printed by the merchant's hardware must be submitted to and validated by Mondial Relay. This step confirms that the labels are scannable and meet all network requirements.

Now that the foundational rules have been established, the next section will detail the specific structure and components of an API request.


--------------------------------------------------------------------------------


4.0 Deconstructing the API Request

Every API call to the Mondial Relay Web Service is an XML document with a specific, hierarchical structure. Understanding this structure is key to providing the correct data for successful shipment creation. The request and response structures are formally defined by XSD schema files, which are available at the following URLs:

* Request XSD: https://www.mondialrelay.fr/media/51911/Mondial-Relay-Shipment-API-.Request.1.0.xsd
* Response XSD: https://www.mondialrelay.fr/media/51914/Mondial-Relay-Shipment-API-.Response.1.0.xsd

This section breaks down each major component of the XML request.

4.1 The <Context> Block

The <Context> element is mandatory in every request. It contains the authentication credentials and API version information required to validate the call.

Attribute	Type	Mandatory?	Description
Login	String	M	The login (email address) of the merchant account calling the service.
Password	String	M	The password associated with the merchant account.
CustomerId	String	M	The unique Customer ID of the merchant. Must match the regex ^[0-9A-Z]{2}[0-9A-Z]{6}$.
Culture	String	M	The culture code (e.g., fr-FR) used to process the request and format the output. Must match the regex ^[a-z]{2}-[A-Z]{2}$.
VersionAPI	Enumeration	M	The version of the API being called.

Important Note: While this documentation is for the overall service version 2.7.1, the VersionAPI parameter within the XML request must be set to 1.0.

4.2 The <OutputOptions> Block

The <OutputOptions> element is mandatory and specifies the desired format for the shipping label that will be returned in the API response.

Attribute	Type	Mandatory?	Description
outputFormat	Enumeration	M	The size or printer model for the label. The available values depend on the outputType. For PDF labels, supported values are A4, A5, 10x15. For ZPL/IPL printing languages, set the printer model (e.g., Monarch9855, MiniMonarch9416XL).
outputType	Enumeration	M	The expected format for the label output (e.g., a PDF link or printer code).

Output Format Compatibility

The available outputFormat values are directly linked to the chosen outputType.

outputType	Available outputFormat
PdfUrl	10x15, A4, A5
ZplCode	Generic_ZPL_10x15_200dpi
IplCode	Generic_IPL_10x15_204dpi
QRCode	(No outputFormat required)

4.3 The <Shipment> Block

The <Shipment> element encapsulates all data related to a single shipment, including parcel details, delivery and collection modes, and sender/recipient addresses.

Shipment Information

These parameters define the overall shipment characteristics.

Attribute	Type	Mandatory/Optional	Max length	Regular expression	Description
Options.key	Enumeration	O	-	-	A key for a special option, such as "ASS" for insurance.
Options.value	String	O	-	-	The value associated with the option key.
OrderNo	String	O	15	`^(	[0-9A-Z_-]{0,15})$`
CustomerNo	String	O	9	`^(	[0-9A-Z]{0,9})$`
parcelCount	Integer	M	2	^[0-9]{1,2}$	The total number of parcels in the shipment. Multi-parcel is only compatible with modes 24L, LD1, and LDS.
shipmentValue.amount	Float	O	10	^[0-9]{0,10}$	The value of the shipment content in EUR (e.g., 20.50).
shipmentValue.currency	Enumeration	O	-	`^(	[A-Z]{3})$`
DeliveryMode.mode	String	M	3	^[0-9A-Z]{3}$	The product code for the delivery method.
DeliveryMode.location	String	O	10	^[0-9A-Z]{0,10}$	The location code of the delivery point. Mandatory for modes 24R, 24L, XOH.
CollectionMode.mode	String	M	3	^[0-9A-Z]{3}$	The product code for the collection method.
CollectionMode.location	String	O	10	^[0-9A-Z]{0,10}$	The location code for collection.

Parcel Information

These parameters describe the physical attributes of each parcel in the shipment.

Attribute	Type	Mandatory/Optional	Max length	Description
Content	String	O	40	A brief description of the parcel's contents.
Length.Value	Integer	O/M	10	The length of the parcel. Mandatory for Poland (max 64*39*38 cm).
Length.Unit	Enum	O/M	2	The unit for length, cm.
Width.Value	Integer	O/M	10	The width of the parcel. Mandatory for Poland (max 64*39*38 cm).
Width.Unit	Enum	O/M	2	The unit for width, cm.
Depth.Value	Integer	O/M	10	The depth of the parcel. Mandatory for Poland (max 64*39*38 cm).
Depth.Unit	Enum	O/M	2	The unit for depth, cm.
Weight.Value	Integer	M	10	The weight of the parcel in grams (minimum 10g).
Weight.Unit	Enum	O	2	The unit for weight, gr.

4.4 The Sender and Recipient Address Blocks

Both the <Sender> and <Recipient> blocks utilize an identical <Address> structure to define their details.

Attribute	Type	Mandatory/Optional	Max length	Regular expression	Description
Title	String	O	32 (concatenated)	^[0-9A-Z_\-'., /]{0,32}$	Salutation (e.g., Mr, Mme).
Firstname	String	M(1)	-	-	First name of the person or company.
Lastname	String	M(1)	-	-	Last name of the person or company.
streetname	String	M	40 (concatenated)	^[0-9A-Z_\-'., /]{0,40}$	Street name. Must not exceed 40 chars when combined with houseNo. Note for Netherlands: the house number must be filled at the end of this field.
houseNo	String	O	10	^[0-9A-Z_\-'., /]{0,10}$	The house or building number.
countryCode	String	M	2	^[A-Z]{2}$	The two-letter country code (ISO 3166-1).
postcode	String	M	10	^[A-Za-z_\-']{2,10}$	The postal code.
city	String	M	30	^[A-Za-z_\-']{2,30}$	The city name.
addressAdd1	String	M(1)	30	^[0-9A-Z_\-'., /]{0,30}$	Name field. Use if Firstname and Lastname are not provided.
addressAdd2	String	O	30	^[0-9A-Z_\-'., /]{0,30}$	Additional name information.
addressAdd3	String	O	30	^[0-9A-Z_\-'., /]{0,30}$	Additional address information (e.g., locality).
phoneNo	String	M/O	20	(See reference)	Phone number. Mandatory for home delivery/collection and for all shipments to Poland.
mobileNo	String	O	20	(See reference)	Mobile phone number.
email	String	O	70	^[\w\-\.\@_]{7,70}$	Email address.

Note on Mandatory Fields M(1): Either the combination of Firstname and Lastname OR the addressAdd1 field is mandatory. If all are provided, addressAdd1 will be ignored.

The address information is printed on the label in the following format:

[Firstname] [Lastname] or [addressAdd1] [addressAdd2] [HouseNo] [StreetName] [addressAdd3] [postcode] [City] [Country]

Once the request is correctly constructed and sent, the API will return a structured response, which is detailed in the next section.


--------------------------------------------------------------------------------


5.0 Interpreting the API Response

Upon a successful API call, the Mondial Relay Web Service returns a structured XML response containing the shipment confirmation, the unique shipment number, and the generated label data in the format you requested. This section details the key elements within a successful response, enabling you to parse the information and complete the shipping workflow.

5.1 Structure of a Successful Response

A successful response mirrors parts of your request and adds the newly generated shipment data.

* The <Context> and <OutputOptions> blocks are echoed back exactly as they were sent in the request, allowing for verification.
* The main <Shipment> element is returned with a critical new attribute: ShipmentNumber. This number (e.g., ShipmentNumber="96408887") is the unique Mondial Relay identifier for the shipment you just created. It is essential for tracking and reference.

5.2 The <LabelList> and <Label> Elements

Inside the <Shipment> block, a <LabelList> contains one or more <Label> elements, each corresponding to a generated shipping label for a parcel in the shipment. Each <Label> element contains two primary components: <RawContent> and <Output>.

Deconstructing <RawContent>

This block contains all the structured, human-readable data that was used to generate the visual label. It is broken down into several sub-elements for clarity and parsing:

* Sender / Recipient: These elements contain the fully formatted address lines for both parties as they appear on the label. The zoneTitle attribute specifies the area title (e.g., "Expediteur"), and each AddressLines element represents a single line of the address block.
* Routing arguments: This provides detailed routing information used by the carrier network. The CarrierCode (e.g., MR for Mondial Relay) identifies the carrier, and Role specifies if it's for collection or delivery. The Key and Value pairs contain specific routing data that is printed on the label (e.g., agency code, tour number).
* Barcodes: This contains the necessary barcode data for each carrier involved. The Type attribute specifies the symbology (e.g., code128), DisplayedValue is the human-readable text printed below the barcode, and Value is the machine-readable string to be encoded.

Understanding <Output>

The <Output> element contains the final, usable label artifact. The content of this element depends directly on the outputType you specified in your request:

* If PdfUrl was requested, the <Output> element will contain a URL pointing to the generated PDF label file. You can then retrieve this file for printing or storage.
* If ZplCode or IplCode was requested, the <Output> element will contain a Base64 encoded string of the raw printer commands. This string must be decoded before being sent directly to a compatible label printer.
* If QRCode was requested for a label-less return, this block will contain the relevant QR code data.

While a successful response confirms shipment creation, it is equally important to be prepared for responses that indicate a problem. The next section details how to handle errors returned by the API.


--------------------------------------------------------------------------------


6.0 Robust Error Handling

Mondial Relay's API provides a clear error handling strategy to help you diagnose and resolve issues efficiently. The service differentiates between two primary types of errors: immediate technical errors that prevent a response, and business errors or warnings that are returned within the body of an otherwise successful API response.

6.1 Recommended Strategy for Interpreting Errors

To build a robust integration, it is recommended to follow these steps when processing each API call:

1. Check for Technical Exceptions: First, ensure your code can handle technical failures, such as a connection timeout or an HTTP error, which would prevent an XML response from being received at all. In these cases, you can attempt to retry the call later.
2. Inspect the <StatusList>: If an XML response is received, immediately check for a <StatusList> element. If this list contains one or more <Status> elements with a level of Error or Critical Error, the shipment creation has failed. The associated error codes and messages will explain why.
3. Differentiate Errors from Warnings:
* Errors (Critical Error, Error) prevent the successful execution of the request. No shipment or label will be created. You must correct the issue described in the error message before retrying.
* Warnings (Warning) indicate a non-critical issue with the data provided (often in an optional field). The request was still processed successfully, but the warning should be reviewed to improve data quality in future calls.

6.2 Error Code Reference

Each <Status> element in the response contains a code, a level, and a descriptive message. The level provides critical context for how to handle the status:

* Critical Error: Indicates a general service problem that is not specific to your request.
* Error: Indicates a problem linked to specific, often mandatory, elements in your request.
* Warning: Indicates a problem with specific, non-mandatory elements in your request.

The following table provides a comprehensive list of all possible error codes.

Error Code	Error Type	Description
-1	Critical Error	Severe System Error. Please, contact the Service Center.
10000	Critical Error	A general error occurred during authentication. Check that the login or/and password are correctly filled.
10001	Critical Error	Invalid user and/or password. Check the authentication information.
10002	Critical Error	A general error occurred while checking configuration. Check that the customerId field is correctly filled.
10003	Critical Error	A general error occurred while checking configuration. Check that the culture field is correctly filled.
10004	Critical Error	A general error occurred while checking configuration. Check that the VersionAPI field is correctly filled.
10005	Critical Error	A general error occurred while checking configuration. Unknown customer Id.
10006	Critical Error	A general error occurred while checking configuration. Unknown culture.
10007	Critical Error	A general error occurred while checking configuration. Unknown VersionAPI.
10008	Warning	Unknown outputFormat. Statement ignored.
10009	Error	No output type defined in the output options.
10010	Error	Invalid output type defined in the output options.
10011	Error	A general error occurred while checking shipments List. No shipment entity defined in the request. A request must contain at least one return element.
10012	Error	No sender information defined in the shipment request.
10013	Error	No receiver information defined in the shipment request.
10014	Warning	Invalid order number. Statement ignored.
10015	Warning	Invalid customer reference defined in the shipment entity. Statement ignored.
10016	Error	No parcel count defined in the shipment entity.
10017	Error	Invalid parcel count.
10018	Warning	Invalid amount defined in the shipment. Statement ignored.
10019	Warning	Invalid shipmentValue defined in the shipment. Statement ignored.
10020	Warning	Invalid currency. Statement ignored.
10021	Warning	Invalid option key. Statement ignored.
10022	Warning	Invalid option value. Statement ignored.
10023	Error	No delivery mode defined in the request.
10024	Error	Invalid delivery mode defined in the request.
10025	Warning	Invalid location for the delivery mode. Statement ignored.
10026	Error	No Collection Mode defined in the request.
10027	Error	Invalid Collection Mode defined in the request.
10028	Warning	Invalid location for the collection mode. Statement ignored.
10029	Warning	Invalid content. Statement ignored.
10030	Warning	Invalid length. Statement ignored.
10031	Warning	Invalid width. Statement ignored.
10032	Warning	Invalid depth. Statement ignored.
10033	Error	No weight defined in the parcel element.
10034	Error	Invalid weight.
10035	Warning	Invalid delivery Instruction. Statement ignored.
10036	Warning	Invalid Title defined in the address. Statement ignored.
10037	Warning	Invalid first name defined in the address. Statement ignored.
10038	Warning	Invalid last name defined in the address. Statement ignored.
10039	Error	Invalid street name defined in the address.
10040	Error	No street name defined in the address.
10041	Warning	Invalid house Number defined in the address. Statement ignored.
10042	Error	Invalid country code defined in the address.
10043	Error	No country code defined in the address.
10044	Error	Invalid postcode defined in the address.
10045	Error	No postcode defined in the address.
10046	Error	Invalid city defined in the address.
10047	Error	No city defined in the address.
10048	Warning	Invalid Additional address field 1 defined in the address. Statement ignored.
10049	Warning	Invalid Additional address field 2 defined in the address. Statement ignored.
10050	Warning	Invalid Additional address field 3 defined in the address. Statement ignored.
10051	Warning	Invalid phone number defined in the address. Statement ignored.
10052	Warning	Invalid mobile number defined in the address. Statement ignored.
10053	Warning	Invalid email defined in the address. Statement ignored.
10054	Error	Unknown address.
10055	Error	Unable to determine transportation plan for this sender address.
10056	Error	Unable to determine transportation plan for this receiver address.
10057	Error	Routing is not needed. No routing will be created.
10058	Error	Routing not completed.
10059	Error	Routing denied.
10060	Error	Label could not be generated for this request.
10061	Error	Not Well-formed XML request.
10062	Warning	Title + FirstName + LastName should not be greater than 30 characters.
10063	Error	HouseNo + StreetName should not be greater than 30 characters.
10065	Error	The number of parcel elements is different from the parcelCount defined in the shipment.
10066	Error	A general error occurred while checking configuration. No access right.
10067	Error	No configuration for your business.
10068	Error	Unable to get the partner barcode.
10069	Warning	Postal code modified by the partner for routing purpose.
10070	Error	Multi parcels forbidden for this product code.
10071	Error	Collection location not found.
10072	Error	Location not allowed for your business. Please refer to your binding agreement.
10073	Error	Location not allowed for this shipment.
10074	Error	No allowed location for this product code.
10075	Error	Location not allowed for this product code.
10076	Error	Unauthorized option for this product code.
10077	Error	No compatible label for this printer.
10078	Error	No available label for this shipment.
10079	Error	Invalid country code for your customer settings.
10080	Error	PDF File unavailable.
10081	Error	Unable to join the partner.
10085	Error	A Mandatory phone number is missing
10090	Error	ParcelSize : Dimensions are missing or are incorrect
99998	Error	XML Parse error. This error will return the specific reason of the reject. You can check your XML request via the XML validator link specify in the policy part.
99999	Critical Error	An error occurred. Please contact the Service Center.

Understanding the request, response, and error structures is best solidified with a practical, end-to-end example, which is provided next.


--------------------------------------------------------------------------------


7.0 End-to-End Examples

This section provides a complete, practical example of a full API interaction. It showcases the XML for both a sample request to create a shipment and the corresponding successful XML response returned by the API.

7.2.1 Sample XML Request

7.3.1 Sample XML Response

To complete your integration, you will need to use specific codes and formats, which are compiled for easy access in the final reference section.


--------------------------------------------------------------------------------


8.0 Reference Data

This final section contains useful reference tables for codes, formats, and regular expressions that are required when constructing valid API requests.

8.2.1 Mode Codes

Use the following codes for the <DeliveryMode> and <CollectionMode> elements.

Code	Description
CCC	Merchant collection
REL	Point Relais® collection
LCC	Merchant delivery
HOM	Home delivery
24R	Point Relais® delivery
24L	Point Relais® XL delivery
XOH	D+1 Delivery. Note: For this delivery mode, a specific action is mandatory by EDI in addition to the creation of the shipment and label by this API. So, please refer to the EDI specification file and the file “leaving warehouse”.

8.2.2 Phone Number Formats by Country

The following table lists the required regular expressions for phone numbers, which are mandatory for home delivery/collection and all shipments to Poland.

Country	Country Code	Regular Expression
France	FR / +33	^[1-9][0-9]{8}$
Espagne	ES / +34	^[1-9][0-9]{8}$
Belgique	BE / +32	^[4]?[0-9]{8}$
Allemagne	DE / +49	^[0-9]{5,11}$
Luxembourg	LU / +352	^[0-9]{5,9}$
Portugal	PT / +351	^[0-9]{5,9}$
Autriche	AT / +43	^[0-9]{4,13}$
Angleterre	UK / +44	^[0-9]{7,10}$
Italie	IT / +39	^[0-9]{9,10}$
Guyane	GF / +594	^[1-9][0-9]{8}$
Saint Martin	MF / +590	^[1-9][0-9]{8}$
Martinique	MQ / +596	^[1-9][0-9]{8}$
Mayotte	YT / +262	^[1-9][0-9]{8}$
Pays Bas	NL / +31	^[0-9]{9}$
Irlande	IE / +353	^[0-9]{9}$
Monaco	FR / +377	^[0-9]{5,9}$
Suisse	CH / +41	^[0-9]{9}$
Poland	PL / +48	\d{9}
